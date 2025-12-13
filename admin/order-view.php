<?php
// admin/order-view.php
require_once __DIR__ . "/../config.php";
require_once __DIR__ . "/includes/admin-auth.php";
require_once __DIR__ . "/includes/admin-helpers.php"; // MUST contain h(), money(), statusNice(), badge(), nextStatus(), fmtDT() (or equivalents)

$orderId = (int)($_GET['order_id'] ?? 0);
$isAjax  = (isset($_GET['ajax']) && $_GET['ajax'] === '1');

$page_title  = "Order Details";
$active_page = "orders";

// ---------------------- Guard ----------------------
if ($orderId <= 0) {
  if (!$isAjax) {
    include __DIR__ . "/includes/admin-head.php";
    include __DIR__ . "/includes/admin-navbar.php";
  }
  ?>
  <section class="py-4">
    <div class="container">
      <div class="panel-card rounded-4 p-4" style="background:rgba(0,0,0,.45);">
        <div class="text-white-50 small mb-1">DON MACCHIATOS • Admin Panel</div>
        <h2 class="fw-bold mb-1">Order Details ☕</h2>
        <div class="text-white-50">Invalid order. Go back to Orders.</div>
        <div class="mt-3">
          <a class="btn btn-outline-light" href="orders.php">
            <i class="fa-solid fa-arrow-left me-2"></i>Back to Orders
          </a>
        </div>
      </div>
    </div>
  </section>
  <?php
  if (!$isAjax) include __DIR__ . "/includes/admin-footer.php";
  exit;
}

// ---------------------- Load Order (with access control) ----------------------
$order = null;

$sql = "
  SELECT
    o.*,
    b.name AS branch_name, b.address AS branch_address, b.phone AS branch_phone, b.facebook_url AS branch_facebook,
    p.method AS pay_method, p.status AS pay_status, p.provider_ref AS pay_ref, p.paid_at AS pay_paid_at
  FROM orders o
  LEFT JOIN branches b ON b.branch_id = o.branch_id
  LEFT JOIN payments p ON p.order_id = o.order_id
  WHERE o.order_id = ?
  LIMIT 1
";

$stmt = $mysqli->prepare($sql);
if ($stmt) {
  $stmt->bind_param("i", $orderId);
  $stmt->execute();
  $res = $stmt->get_result();
  $order = $res ? $res->fetch_assoc() : null;
  $stmt->close();
}

if (!$order) {
  if (!$isAjax) {
    include __DIR__ . "/includes/admin-head.php";
    include __DIR__ . "/includes/admin-navbar.php";
  }
  ?>
  <section class="py-4">
    <div class="container">
      <div class="panel-card rounded-4 p-4" style="background:rgba(0,0,0,.45);">
        <div class="text-white-50 small mb-1">DON MACCHIATOS • Admin Panel</div>
        <h2 class="fw-bold mb-1">Order Details ☕</h2>
        <div class="text-white-50">Order not found.</div>
        <div class="mt-3">
          <a class="btn btn-outline-light" href="orders.php">
            <i class="fa-solid fa-arrow-left me-2"></i>Back to Orders
          </a>
        </div>
      </div>
    </div>
  </section>
  <?php
  if (!$isAjax) include __DIR__ . "/includes/admin-footer.php";
  exit;
}

// branch_admin restriction
if (($ADMIN_ROLE ?? '') === 'branch_admin' && !empty($ADMIN_BRANCH_ID)) {
  if ((int)$order['branch_id'] !== (int)$ADMIN_BRANCH_ID) {
    http_response_code(403);
    if ($isAjax) {
      echo "<div class='text-white-50 small'>Not allowed.</div>";
      exit;
    }
    include __DIR__ . "/includes/admin-head.php";
    include __DIR__ . "/includes/admin-navbar.php";
    ?>
    <section class="py-4">
      <div class="container">
        <div class="panel-card rounded-4 p-4" style="background:rgba(0,0,0,.45);">
          <div class="text-white-50 small mb-1">DON MACCHIATOS • Admin Panel</div>
          <h2 class="fw-bold mb-1">Order Details ☕</h2>
          <div class="text-white-50">You don’t have access to this order.</div>
          <div class="mt-3">
            <a class="btn btn-outline-light" href="orders.php">
              <i class="fa-solid fa-arrow-left me-2"></i>Back to Orders
            </a>
          </div>
        </div>
      </div>
    </section>
    <?php
    include __DIR__ . "/includes/admin-footer.php";
    exit;
  }
}

// ---------------------- Items ----------------------
$items = [];
$stmt = $mysqli->prepare("
  SELECT item_name, unit_price, qty, with_coffee, line_total
  FROM order_items
  WHERE order_id = ?
  ORDER BY order_item_id ASC
");
if ($stmt) {
  $stmt->bind_param("i", $orderId);
  $stmt->execute();
  $res = $stmt->get_result();
  while ($res && ($r = $res->fetch_assoc())) $items[] = $r;
  $stmt->close();
}

// ---------------------- History ----------------------
$history = [];
$stmt = $mysqli->prepare("
  SELECT h.status, h.note, h.changed_at, a.full_name AS changed_by_name
  FROM order_status_history h
  LEFT JOIN admin_users a ON a.admin_id = h.changed_by
  WHERE h.order_id = ?
  ORDER BY h.changed_at ASC
");
if ($stmt) {
  $stmt->bind_param("i", $orderId);
  $stmt->execute();
  $res = $stmt->get_result();
  while ($res && ($r = $res->fetch_assoc())) $history[] = $r;
  $stmt->close();
}

$status = (string)($order['status'] ?? '');
$ns     = function_exists('nextStatus') ? nextStatus($status) : null;

// ---------------------- Ajax-only render (wrapper) ----------------------
if ($isAjax) {
  include __DIR__ . "/partials/order-view-wrapper.php";
  exit;
}

// Normal full page render
include __DIR__ . "/includes/admin-head.php";
include __DIR__ . "/includes/admin-navbar.php";
?>

<!-- HERO -->
<section class="py-4">
  <div class="container">
    <div class="panel-card rounded-4 p-4 text-white" style="background:rgba(0,0,0,.45);">
      <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
          <div class="text-white-50 small mb-1">DON MACCHIATOS • Admin Panel</div>
          <h2 class="fw-bold mb-1">Order Details ☕</h2>
          <div class="text-white-50 small">
            View + update order status. Changes reflect on <strong>Track Order</strong>.
          </div>
        </div>
        <div class="d-flex gap-2 flex-wrap">
          <a class="btn btn-outline-light" href="orders.php">
            <i class="fa-solid fa-arrow-left me-2"></i>Back
          </a>
          <span class="badge bg-dark bg-opacity-75 px-3 py-2">
            <span class="text-white-50">Code:</span>
            <span class="fw-semibold font-monospace"><?= h($order['order_code']) ?></span>
          </span>
        </div>
      </div>
    </div>
  </div>
</section>

<div class="container py-4">
  <div id="js-order-view-wrapper">
    <?php include __DIR__ . "/partials/order-view-wrapper.php"; ?>
  </div>
</div>

<script src="../assets/js/admin-order-view.js" defer></script>
<script>
  window.ADMIN_ORDER_VIEW = {
    ajaxUrl: "order-view.php?order_id=<?= (int)$orderId ?>",
    nextStatusUrl: "actions/order-next-status.php",
    orderId: <?= (int)$orderId ?>,
  };
</script>

<?php include __DIR__ . "/includes/admin-footer.php"; ?>
