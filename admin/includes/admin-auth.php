<?php
// admin/includes/admin-auth.php
if (session_status() === PHP_SESSION_NONE) session_start();

if (empty($_SESSION['admin_id'])) {
  header("Location: login.php");
  exit;
}

// Convenience vars (safe defaults)
$ADMIN_ID        = (int)($_SESSION['admin_id'] ?? 0);
$ADMIN_NAME      = (string)($_SESSION['admin_name'] ?? 'Admin');
$ADMIN_ROLE      = (string)($_SESSION['admin_role'] ?? 'branch_admin'); // super_admin|branch_admin
$ADMIN_BRANCH_ID = $_SESSION['admin_branch_id'] ?? null; // int|null

