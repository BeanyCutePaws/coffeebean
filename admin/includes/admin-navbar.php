<?php
// admin/includes/admin-navbar.php

require_once __DIR__ . "/admin-helpers.php"; // gives us h(), money(), statusNice(), etc.

$adminName = $_SESSION['admin_name'] ?? 'Admin';
$adminRole = $_SESSION['admin_role'] ?? '';
$adminBid  = $_SESSION['admin_branch_id'] ?? null;

if (!function_exists('adminRoleLabel')) {
  function adminRoleLabel($role) {
    return match((string)$role) {
      'super_admin'  => 'Beeg Admin',
      'branch_admin' => 'Branch Admin',
      default        => ucwords(str_replace('_',' ', (string)$role)),
    };
  }
}
?>

<nav class="navbar navbar-expand-lg navbar-dark sticky-top"
     style="background:rgba(0,0,0,.85); backdrop-filter: blur(6px);">
  <div class="container">
    <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="dashboard.php">
      <i class="fa-solid fa-mug-hot"></i>
      DON MACCHIATOS
      <span class="badge bg-primary ms-2">ADMIN</span>
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="adminNav">
      <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">

        <li class="nav-item">
          <a class="nav-link <?= ($active_page ?? '') === 'dashboard' ? 'active' : '' ?>"
             href="dashboard.php">
            <i class="fa-solid fa-gauge me-1"></i> Dashboard
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link <?= ($active_page ?? '') === 'orders' ? 'active' : '' ?>"
             href="orders.php">
            <i class="fa-solid fa-receipt me-1"></i> Orders
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link <?= ($active_page ?? '') === 'menu' ? 'active' : '' ?>"
             href="menu.php">
            <i class="fa-solid fa-list-check me-1"></i> Menu
          </a>
        </li>

        <!-- Admin identity -->
        <li class="nav-item ms-lg-3 mt-2 mt-lg-0">
          <span class="badge bg-dark bg-opacity-75 px-3 py-2 d-inline-flex align-items-center gap-2 flex-wrap">
            <span class="d-inline-flex align-items-center gap-2">
              <span class="rounded-circle"
                    style="width:8px;height:8px;background:#22c55e;display:inline-block;"></span>
              <i class="fa-solid fa-user-gear"></i>
            </span>

            <span class="fw-semibold"><?= h($adminName) ?></span>

            <span class="text-white-50">•</span>
            <span class="text-white-50"><?= h(adminRoleLabel($adminRole)) ?></span>

            <?php if ($adminRole === 'branch_admin' && !empty($adminBid)): ?>
              <span class="text-white-50">•</span>
              <span class="text-white-50">Branch #<?= (int)$adminBid ?></span>
            <?php endif; ?>
          </span>
        </li>

        <li class="nav-item ms-lg-3 mt-2 mt-lg-0">
          <a class="btn btn-outline-light btn-sm" href="../menu.php" target="_blank">
            <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> View Site
          </a>
        </li>

        <li class="nav-item ms-lg-2 mt-2 mt-lg-0">
          <a class="btn btn-danger btn-sm" href="logout.php">
            <i class="fa-solid fa-right-from-bracket me-1"></i> Logout
          </a>
        </li>

      </ul>
    </div>
  </div>
</nav>
