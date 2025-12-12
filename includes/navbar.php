<?php
// includes/navbar.php
// Usage: set $active_page = 'home'|'about'|'contact'|'track'|'menu' before include.

$active_page = $active_page ?? '';
function navActive($key, $active_page) {
    return $key === $active_page ? 'active' : '';
}
?>
<header>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">
                ☕ Don Macchiatos ☕
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-lg-center">
                    <li class="nav-item">
                        <a class="nav-link <?php echo navActive('home', $active_page); ?>" href="index.php">Home</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link <?php echo navActive('about', $active_page); ?>" href="about.php">About</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link <?php echo navActive('menu', $active_page); ?>" href="menu.php">Menu</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link <?php echo navActive('contact', $active_page); ?>" href="contact.php">Contact</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link <?php echo navActive('track', $active_page); ?>" href="track-order.php">Track Order</a>
                    </li>

                    <li class="nav-item">
                        <a class="btn btn-primary ms-lg-3" href="menu.php">☕︎ Order Now</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>
