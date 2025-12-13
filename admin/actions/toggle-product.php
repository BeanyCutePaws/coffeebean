<?php
require_once __DIR__ . "/../../config.php";
require_once __DIR__ . "/../includes/admin-auth.php";

header('Content-Type: application/json');

$productId = (int)($_POST['product_id'] ?? 0);
$branchId  = (int)($_POST['branch_id'] ?? 0);
$enabled   = (int)($_POST['enabled'] ?? 0);

if ($productId <= 0 || $branchId <= 0) {
  echo json_encode(['success' => false]);
  exit;
}

// Branch admin protection
if ($ADMIN_ROLE === 'branch_admin' && (int)$ADMIN_BRANCH_ID !== $branchId) {
  echo json_encode(['success' => false]);
  exit;
}

$stmt = $mysqli->prepare("
  INSERT INTO branch_product_availability
    (branch_id, product_id, is_available, updated_by)
  VALUES (?, ?, ?, ?)
  ON DUPLICATE KEY UPDATE
    is_available = VALUES(is_available),
    updated_by = VALUES(updated_by),
    updated_at = NOW()
");

$adminId = (int)$_SESSION['admin_id'];
$stmt->bind_param("iiii", $branchId, $productId, $enabled, $adminId);
$stmt->execute();
$stmt->close();

echo json_encode(['success' => true]);
