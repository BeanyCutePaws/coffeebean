<?php
// admin/actions/order-cancel.php
require_once __DIR__ . "/../../config.php";
require_once __DIR__ . "/../includes/admin-auth.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode(['success' => false, 'message' => 'Method not allowed']);
  exit;
}

$orderId = (int)($_POST['order_id'] ?? 0);
$noteIn  = trim((string)($_POST['note'] ?? 'Cancelled by admin'));
$note    = $noteIn !== '' ? $noteIn : 'Cancelled by admin';

if ($orderId <= 0) {
  http_response_code(400);
  echo json_encode(['success' => false, 'message' => 'Invalid order_id']);
  exit;
}

$adminId = (int)($_SESSION['admin_id'] ?? 0);
if ($adminId <= 0) {
  http_response_code(401);
  echo json_encode(['success' => false, 'message' => 'Not authenticated']);
  exit;
}

// Load order (for status + branch access checks)
$stmt = $mysqli->prepare("SELECT order_id, branch_id, status FROM orders WHERE order_id=? LIMIT 1");
if (!$stmt) {
  http_response_code(500);
  echo json_encode(['success' => false, 'message' => 'DB error']);
  exit;
}
$stmt->bind_param("i", $orderId);
$stmt->execute();
$res = $stmt->get_result();
$order = $res ? $res->fetch_assoc() : null;
$stmt->close();

if (!$order) {
  http_response_code(404);
  echo json_encode(['success' => false, 'message' => 'Order not found']);
  exit;
}

// Enforce branch admin restriction (same pattern as order-view.php)
$role = (string)($_SESSION['admin_role'] ?? '');
$adminBranchId = (int)($_SESSION['admin_branch_id'] ?? 0);

if ($role === 'branch_admin' && $adminBranchId > 0) {
  if ((int)$order['branch_id'] !== $adminBranchId) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Not allowed for this branch']);
    exit;
  }
}

$cur = (string)$order['status'];

// Only allow cancel for pending/preparing (change rule if you want)
if (!in_array($cur, ['pending', 'preparing'], true)) {
  http_response_code(400);
  echo json_encode(['success' => false, 'message' => 'This order can no longer be cancelled.']);
  exit;
}

$mysqli->begin_transaction();
try {
  // Update order
  $stmt = $mysqli->prepare("UPDATE orders SET status='cancelled', cancelled_at=NOW() WHERE order_id=? LIMIT 1");
  if (!$stmt) throw new Exception("DB error update");
  $stmt->bind_param("i", $orderId);
  $stmt->execute();
  $stmt->close();

  // Insert history
  $stmt = $mysqli->prepare("
    INSERT INTO order_status_history (order_id, status, note, changed_by)
    VALUES (?, 'cancelled', ?, ?)
  ");
  if (!$stmt) throw new Exception("DB error history");
  $stmt->bind_param("isi", $orderId, $note, $adminId);
  $stmt->execute();
  $stmt->close();

  $mysqli->commit();
  echo json_encode(['success' => true, 'message' => 'Order cancelled']);
} catch (Throwable $e) {
  $mysqli->rollback();
  http_response_code(500);
  echo json_encode(['success' => false, 'message' => 'Cancel failed']);
}
