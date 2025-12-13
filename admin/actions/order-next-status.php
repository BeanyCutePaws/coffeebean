<?php
// admin/actions/order-next-status.php
require_once __DIR__ . "/../../config.php";
require_once __DIR__ . "/../includes/admin-auth.php";

header('Content-Type: application/json');

function json_fail($msg, $code = 400) {
  http_response_code($code);
  echo json_encode(['success' => false, 'message' => $msg]);
  exit;
}

function statusNice($s) {
  return ucwords(str_replace('_', ' ', (string)$s));
}

function nextStatus($s) {
  return match((string)$s) {
    'pending'         => 'preparing',
    'preparing'       => 'out_for_delivery',
    'out_for_delivery'=> 'completed',
    default           => null,
  };
}

$orderId = (int)($_POST['order_id'] ?? 0);
if ($orderId <= 0) json_fail("Invalid order.");

// Track transaction ourselves (mysqli has no in_transaction property)
$txStarted = false;

try {
  // Fetch order (and enforce branch access)
  $stmt = $mysqli->prepare("SELECT order_id, branch_id, status FROM orders WHERE order_id=? LIMIT 1");
  if (!$stmt) json_fail("DB error.");
  $stmt->bind_param("i", $orderId);
  $stmt->execute();
  $res = $stmt->get_result();
  $order = $res ? $res->fetch_assoc() : null;
  $stmt->close();

  if (!$order) json_fail("Order not found.");

  // Branch access restriction
  if (($ADMIN_ROLE ?? '') === 'branch_admin' && !empty($ADMIN_BRANCH_ID)) {
    if ((int)$order['branch_id'] !== (int)$ADMIN_BRANCH_ID) {
      json_fail("Not allowed.", 403);
    }
  }

  $cur = (string)$order['status'];
  $ns  = nextStatus($cur);
  if (!$ns) json_fail("No next step for this order.");

  // Transaction
  $mysqli->begin_transaction();
  $txStarted = true;

  // Update order status
  $stmt = $mysqli->prepare("UPDATE orders SET status=?, updated_at=NOW() WHERE order_id=? LIMIT 1");
  if (!$stmt) throw new Exception("Update failed");
  $stmt->bind_param("si", $ns, $orderId);
  $stmt->execute();
  $stmt->close();

  // Write history
  $note = "Moved to " . statusNice($ns) . " by admin";
  $changedBy = (int)($_SESSION['admin_id'] ?? 0);

  $stmt = $mysqli->prepare("
    INSERT INTO order_status_history (order_id, status, note, changed_by)
    VALUES (?,?,?,?)
  ");
  if (!$stmt) throw new Exception("History failed");
  $stmt->bind_param("issi", $orderId, $ns, $note, $changedBy);
  $stmt->execute();
  $stmt->close();

  $mysqli->commit();
  $txStarted = false;

  echo json_encode([
    'success'    => true,
    'message'    => "Updated to " . statusNice($ns),
    'new_status' => $ns
  ]);
  exit;

} catch (Throwable $e) {
  if ($txStarted) {
    $mysqli->rollback();
  }
  json_fail("Could not update status right now.", 500);
}
