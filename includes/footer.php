<?php // includes/footer.php ?>
<footer class="site-footer">
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <h5>DON MACCHIATOS</h5>
                <p>✆+639614168392<br>DonMacchiatos@gmail.com<br>
                    Mh aznar street sambag 2 urgello, <br>
                    Cebu City, Philippines, 6000
                </p>
            </div>

            <div class="col-md-3">
                <h5>⏱︎Working Days⏱︎</h5>
                <p>Mon-Saturday 9AM - 10PM</p>
            </div>

            <div class="col-md-3">
                <h5>Quick Links</h5>
                <ul class="list-unstyled">
                    <li><a href="about.php" class="text-light">About</a></li>
                    <li><a href="contact.php" class="text-light">Contact</a></li>
                    <li><a href="track-order.php" class="text-light">Track Order</a></li>
                </ul>
            </div>

            <div class="col-md-3">
                <h5>Subscribe to our newsletter</h5>
                <form action="#" method="post">
                    <div class="input-group">
                        <input type="email" class="form-control" placeholder="Email Address" required>
                        <button class="btn btn-primary" type="submit">➤</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="text-center mt-3">&copy; 2025 Don Macchiatos. © All Rights Reserved.</div>
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
