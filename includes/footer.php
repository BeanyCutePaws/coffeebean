<?php // includes/footer.php ?>
<footer class="site-footer mt-5">
  <div class="container py-1">
    <div class="row g-4">

      <!-- BRAND -->
      <div class="col-md-4">
        <h5 class="fw-bold mb-2">DON MACCHIATOS</h5>
        <p class="text-white-50 mb-2">
          An online ordering platform for handcrafted coffee drinks,
          designed for fast, convenient, and reliable service.
        </p>
        <div class="text-white-50 small">
          <div><i class="fa-solid fa-phone me-2"></i>Customer Support</div>
          <div><i class="fa-solid fa-envelope me-2"></i>support@donmacchiatos.com</div>
          <div class="mt-1">
            <i class="fa-solid fa-location-dot me-2"></i>
            Serving selected locations
          </div>
        </div>
      </div>

      <!-- HOURS -->
      <div class="col-md-3">
        <h6 class="fw-bold mb-2">Operating Hours</h6>
        <p class="text-white-50 mb-1">Monday – Saturday</p>
        <p class="fw-semibold mb-0">9:00 AM – 10:00 PM</p>
      </div>

      <!-- QUICK LINKS -->
      <div class="col-md-2">
        <h6 class="fw-bold mb-2">Quick Links</h6>
        <ul class="list-unstyled footer-links mb-0">
          <li><a href="menu.php">Menu</a></li>
          <li><a href="about.php">About</a></li>
          <li><a href="contact.php">Contact</a></li>
          <li><a href="track-order.php">Track Order</a></li>
        </ul>
      </div>

      <!-- CONNECT -->
      <div class="col-md-3">
        <h6 class="fw-bold mb-2">Stay Connected</h6>
        <p class="text-white-50 small mb-2">
          Subscribe for updates and announcements.
        </p>

        <form action="#" method="post" class="mb-2">
          <div class="input-group input-group-sm">
            <input type="email" class="form-control" placeholder="Email address" required>
            <button class="btn btn-primary" type="submit">
              <i class="fa-solid fa-paper-plane"></i>
            </button>
          </div>
        </form>

        <div class="d-flex gap-2">
          <a
            href="https://www.facebook.com/donmacchiatosphilippines/"
            class="footer-social"
            aria-label="Facebook"
            target="_blank"
            rel="noopener noreferrer"
          >
            <i class="fa-brands fa-facebook-f"></i>
          </a>

          <a
            href="https://www.instagram.com/donmacchiatos.official/?hl=en"
            class="footer-social"
            aria-label="Instagram"
            target="_blank"
            rel="noopener noreferrer"
          >
            <i class="fa-brands fa-instagram"></i>
          </a>
        </div>  
      </div>

    </div>

    <hr class="border-secondary my-3">

    <div class="text-center text-white-50 small">
        © <?= date('Y') ?> Don Macchiatos. All rights reserved.
        <span class="mx-2">•</span>
        <a href="admin/login.php"
            class="text-white-50 text-decoration-none"
            style="opacity:.6;">
            Admin
        </a>
    </div>

  </div>
</footer>

<!-- Bootstrap Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<!-- App entry -->
<script src="assets/js/main.js" defer></script>

<?php
// Load reCAPTCHA only if key exists
if (defined('RECAPTCHA_SITE_KEY') && !empty(RECAPTCHA_SITE_KEY)) {
  echo '<script src="https://www.google.com/recaptcha/api.js" async defer></script>';
}
?>
</body>
</html>
