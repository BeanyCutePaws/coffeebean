<?php
// admin/includes/admin-footer.php

$customer_site_link = $customer_site_link ?? '../index.php';
?>

<footer class="site-footer mt-5">
  <div class="container py-1">
    <div class="row g-4">

      <!-- BRAND -->
      <div class="col-md-4">
        <h5 class="fw-bold mb-2">DON MACCHIATOS</h5>
        <p class="text-white-50 mb-2">
          Admin Panel for managing orders and branch availability — synced with Track Order.
        </p>
        <div class="text-white-50 small">
          <div><i class="fa-solid fa-shield-halved me-2"></i>Internal use only</div>
          <div><i class="fa-solid fa-rotate me-2"></i>Status updates reflect live</div>
        </div>
      </div>

      <!-- QUICK LINKS -->
      <div class="col-md-3">
        <h6 class="fw-bold mb-2">Admin Links</h6>
        <ul class="list-unstyled footer-links mb-0">
          <li><a href="dashboard.php">Dashboard</a></li>
          <li><a href="orders.php">Orders</a></li>
          <li><a href="users.php" style="pointer-events:none;opacity:.6;">Users (soon)</a></li>
          <li><a href="menu.php" style="pointer-events:none;opacity:.6;">Menu (soon)</a></li>
        </ul>
      </div>

      <!-- CUSTOMER SIDE -->
      <div class="col-md-2">
        <h6 class="fw-bold mb-2">Customer Side</h6>
        <ul class="list-unstyled footer-links mb-0">
          <li><a href="<?= htmlspecialchars($customer_site_link) ?>" target="_blank" rel="noopener">View Customer Site</a></li>
          <li><a href="../track-order.php" target="_blank" rel="noopener">Track Order Page</a></li>
          <li><a href="../menu.php" target="_blank" rel="noopener">Menu</a></li>
        </ul>
      </div>

      <!-- ACTIONS / SOCIAL -->
      <div class="col-md-3">
        <h6 class="fw-bold mb-2">Actions</h6>
        <div class="d-grid gap-2">
          <a class="btn btn-outline-light btn-sm" href="<?= htmlspecialchars($customer_site_link) ?>" target="_blank" rel="noopener">
            <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Open Customer Site
          </a>
          <a class="btn btn-danger btn-sm" href="logout.php">
            <i class="fa-solid fa-right-from-bracket me-1"></i> Logout
          </a>
        </div>

        <div class="d-flex gap-2 mt-3">
          <a href="#" class="footer-social" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
          <a href="#" class="footer-social" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
          <a href="#" class="footer-social" aria-label="Messenger"><i class="fa-brands fa-facebook-messenger"></i></a>
        </div>
      </div>

    </div>

    <hr class="border-secondary my-3">

    <div class="text-center text-white-50 small">
      © <?= date('Y') ?> Don Macchiatos • Admin Panel
    </div>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<?php
// Optional per-page script hook
if (!empty($admin_page_script)) {
  echo $admin_page_script;
}
?>
</body>
</html>
