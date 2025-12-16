<?php
// actions/place-order.php

if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}

require_once __DIR__ . "/../config.php";
require_once __DIR__ . "/../config/keys.php";

function projectBasePath(): string {
  $dir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');

  if ($dir !== '' && str_ends_with($dir, '/actions')) {
    $dir = substr($dir, 0, -strlen('/actions'));
  }

  return $dir; // '' at domain root, '/subfolder' when deployed in a folder
}


function fail($msg, $code = 400) {
  http_response_code($code);
  echo "<h3>Checkout Error</h3>";
  echo "<p>" . htmlspecialchars($msg) . "</p>";
  $base = projectBasePath();
  echo "<p><a href='" . htmlspecialchars($base . "/checkout.php") . "'>Back to checkout</a></p>";
  exit;
}

function money2($n) {
  return (float) number_format((float)$n, 2, '.', '');
}

function isRecaptchaEnabled() {
  return defined('RECAPTCHA_SITE_KEY') && !empty(RECAPTCHA_SITE_KEY)
      && defined('RECAPTCHA_SECRET_KEY') && !empty(RECAPTCHA_SECRET_KEY);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  fail("Invalid request method.", 405);
}

// --- Inputs ---
$branch_id      = (int)($_POST['branch_id'] ?? 0);
$order_mode     = trim((string)($_POST['order_mode'] ?? '')); // pickup|delivery
$customer_lat   = (string)($_POST['customer_lat'] ?? '');
$customer_lng   = (string)($_POST['customer_lng'] ?? '');

$customer_name  = trim((string)($_POST['customer_name'] ?? ''));
$customer_phone = trim((string)($_POST['customer_phone'] ?? ''));

$delivery_address = trim((string)($_POST['delivery_address'] ?? ''));
$notes            = trim((string)($_POST['notes'] ?? ''));
$payment_method   = trim((string)($_POST['payment_method'] ?? 'cod'));

$cart_json     = (string)($_POST['cart_json'] ?? '[]');
$subtotal_post = (float)($_POST['subtotal'] ?? 0);
$delivery_fee  = (float)($_POST['delivery_fee'] ?? 0);
$total_post    = (float)($_POST['total_amount'] ?? 0);

// --- Validation ---
if ($branch_id <= 0) fail("Missing branch.");
if (!in_array($order_mode, ['pickup','delivery'], true)) fail("Invalid order mode.");
if ($customer_name === '') fail("Customer name required.");
if ($customer_phone === '') fail("Customer phone required.");
if (!in_array($payment_method, ['cod','paymongo'], true)) fail("Invalid payment method.");

$cart = json_decode($cart_json, true);
if (!is_array($cart) || empty($cart)) fail("Cart is empty.");

if ($order_mode === 'delivery') {
  if ($delivery_address === '') fail("Delivery address required.");
} else {
  if ($delivery_address === '') {
    $delivery_address = "PICKUP (see branch details)";
  }
}

// --- Validate branch ---
$stmt = $mysqli->prepare("SELECT is_active FROM branches WHERE branch_id=? LIMIT 1");
$stmt->bind_param("i", $branch_id);
$stmt->execute();
$res = $stmt->get_result();
$row = $res ? $res->fetch_assoc() : null;
$stmt->close();

if (!$row || (int)$row['is_active'] !== 1) {
  fail("Selected branch is unavailable.");
}

// --- Server-side totals ---
$subtotal_calc = 0.0;
$normalized = [];

foreach ($cart as $it) {
  $pid = (int)($it['product_id'] ?? 0);
  $qty = (int)($it['qty'] ?? 0);
  $price = (float)($it['price'] ?? 0);
  $withCoffee = !empty($it['with_coffee']) ? 1 : 0;

  if ($pid <= 0 || $qty <= 0 || $price < 0) {
    fail("Invalid cart item.");
  }

  $line = $price * $qty;
  $subtotal_calc += $line;

  $normalized[] = compact('pid','qty','price','withCoffee');
}

$total_calc = $subtotal_calc + $delivery_fee;

// --- Transaction ---
$mysqli->begin_transaction();

try {
  $tmpCode = 'DM-TMP-' . bin2hex(random_bytes(6));

  $sub = money2($subtotal_calc);
  $df  = money2($delivery_fee);
  $tot = money2($total_calc);

  // Insert order
  $ins = $mysqli->prepare("
    INSERT INTO orders (
      order_code, branch_id,
      customer_name, customer_phone,
      fulfillment_mode, delivery_address, notes,
      subtotal, delivery_fee, total_amount
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
  ");

  $ins->bind_param(
    "sisssssddd",
    $tmpCode,
    $branch_id,
    $customer_name,
    $customer_phone,
    $order_mode,
    $delivery_address,
    $notes,
    $sub,
    $df,
    $tot
  );

  if (!$ins->execute()) {
    throw new Exception("Order insert failed.");
  }

  $order_id = (int)$ins->insert_id;
  $ins->close();

  // Final order code
  $order_code = 'DM-' . str_pad((string)$order_id, 6, '0', STR_PAD_LEFT);
  $up = $mysqli->prepare("UPDATE orders SET order_code=? WHERE order_id=?");
  $up->bind_param("si", $order_code, $order_id);
  $up->execute();
  $up->close();

  // Status history
  $hist = $mysqli->prepare("
    INSERT INTO order_status_history (order_id, status, note)
    VALUES (?, 'pending', 'Order placed')
  ");
  $hist->bind_param("i", $order_id);
  $hist->execute();
  $hist->close();

  // --- Payments row ---
  $payment_status = 'unpaid';
  $provider_ref   = null;
  $paid_at        = null;

  // If PayMongo return said paid, accept it (testing mode)
  if ($payment_method === 'paymongo' && (($_POST['payment_status'] ?? '') === 'paid')) {
    $payment_status = 'paid';
    $paid_at = date('Y-m-d H:i:s');
    // If you stored the checkout session id in session, keep it
    $provider_ref = $_SESSION['dm_pending_checkout_session_id'] ?? null;
  }

  $pay = $mysqli->prepare("
    INSERT INTO payments (order_id, method, status, provider_ref, paid_at)
    VALUES (?, ?, ?, ?, ?)
  ");
  $pay->bind_param("issss", $order_id, $payment_method, $payment_status, $provider_ref, $paid_at);
  $pay->execute();
  $pay->close();

  // Order items
  $prod = $mysqli->prepare("SELECT name, is_active FROM products WHERE product_id=?");
  $item = $mysqli->prepare("
    INSERT INTO order_items
      (order_id, product_id, item_name, unit_price, qty, with_coffee, line_total)
    VALUES (?, ?, ?, ?, ?, ?, ?)
  ");

  foreach ($normalized as $it) {
    $prod->bind_param("i", $it['pid']);
    $prod->execute();
    $pr = $prod->get_result()->fetch_assoc();

    if (!$pr || (int)$pr['is_active'] !== 1) {
      throw new Exception("Product unavailable.");
    }

    $line = $it['price'] * $it['qty'];

    $item->bind_param(
      "iisdiid",
      $order_id,
      $it['pid'],
      $pr['name'],
      $it['price'],
      $it['qty'],
      $it['withCoffee'],
      $line
    );
    $item->execute();
  }

  $prod->close();
  $item->close();

  $mysqli->commit();

  // Clear recaptcha flags (and optionally any paymongo pending snapshot)
  unset($_SESSION['dm_recaptcha_ok'], $_SESSION['dm_recaptcha_ok_at']);

  $base = projectBasePath();
  header("Location: " . $base . "/order-confirmed.php?code=" . urlencode($order_code));
  exit;

} catch (Exception $e) {
  $mysqli->rollback();
  fail($e->getMessage(), 500);
}
