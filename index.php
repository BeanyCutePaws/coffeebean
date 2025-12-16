<?php
$page_title = "Home";
$active_page = "home";
include "includes/head.php";
include "includes/navbar.php";
?>

<!-- Hero Section -->
<section id="hero" class="hero-section d-flex align-items-end justify-content-start">
    <img src="assets/img/bg.jpg" alt="Background" class="hero-bg">

    <div class="container text-white text-start mb-5">
        <h4 class="hero-subtitle mb-3">CEBU’S FIRST 39 PESOS COFFEE ☕️</h4>
        <h1 class="hero-heading display-3 fw-bold">DON <span>MACCHIATOS</span></h1>
        <p class="hero-text my-4" style="max-width: 600px;">
            Enjoy high-quality coffee for just 39 pesos! We believe everyone deserves a great<br>
            cup without the high price. Savor expertly brewed coffee made from the finest<br>
            beans, affordable and delicious!
        </p>
        <a class="btn btn-primary btn-lg" href="menu.php">ORDER NOW</a>
    </div>
</section>

<!-- Hot Selection -->
<section id="hot" class="py-5">
    <div class="container">
        <h4 class="mb-4 text-center">☕ TRY OUR HOT SELECTION ☕</h4>
        <div class="row g-4">

            <div class="col-md-4 feature-card">
                <img src="assets/img/hotcaramel.png" class="img-fluid" alt="Hot Caramel">
                <h5>Hot Caramel</h5>
                <a href="menu.php" class="btn btn-outline-primary">ORDER NOW</a>
            </div>

            <div class="col-md-4 feature-card">
                <img src="assets/img/hotdarko.png" class="img-fluid" alt="Hot Darko">
                <h5>Hot Darko</h5>
                <a href="menu.php" class="btn btn-outline-primary">ORDER NOW</a>
            </div>

            <div class="col-md-4 feature-card">
                <img src="assets/img/hotdonbarako.png" class="img-fluid" alt="Don Barako">
                <h5>Don Barako</h5>
                <a href="menu.php" class="btn btn-outline-primary">ORDER NOW</a>
            </div>

        </div>
    </div>
</section>

<!-- Iced Selection Carousel -->
<section id="iced" class="py-5">
    <div class="container">
        <h4 class="mb-4 text-center">☕ TRY OUR ICED SELECTION ☕</h4>

        <div id="featureCarousel" class="carousel slide carousel-fade"
             data-bs-ride="carousel" data-bs-interval="3000" data-bs-pause="hover">

            <div class="carousel-indicators">
                <button type="button" data-bs-target="#featureCarousel" data-bs-slide-to="0" class="active"></button>
                <button type="button" data-bs-target="#featureCarousel" data-bs-slide-to="1"></button>
                <button type="button" data-bs-target="#featureCarousel" data-bs-slide-to="2"></button>
            </div>

            <div class="carousel-inner">

                <!-- SLIDE 1 -->
                <div class="carousel-item active">
                    <div class="row g-4">
                        <div class="col-md-4 feature-card">
                            <img src="assets/img/icedcaramacchiato.png" class="img-fluid" alt="Iced Caramel Macchiato">
                            <h5>Iced Caramel Macchiato</h5>
                            <a href="menu.php" class="btn btn-outline-primary">ORDER NOW</a>
                        </div>

                        <div class="col-md-4 feature-card">
                            <img src="assets/img/icedspanlatte.png" class="img-fluid" alt="Spanish Latte">
                            <h5>Spanish Latte</h5>
                            <a href="menu.php" class="btn btn-outline-primary">ORDER NOW</a>
                        </div>

                        <div class="col-md-4 feature-card">
                            <img src="assets/img/iceddonpistachio.png" class="img-fluid" alt="Don Pistachio">
                            <h5>Don Pistachio</h5>
                            <a href="menu.php" class="btn btn-outline-primary">ORDER NOW</a>
                        </div>
                    </div>
                </div>

                <!-- SLIDE 2 -->
                <div class="carousel-item">
                    <div class="row g-4">
                        <div class="col-md-4 feature-card">
                            <img src="assets/img/iceddonberry.png" class="img-fluid" alt="Donya Berry">
                            <h5>Donya Berry</h5>
                            <a href="menu.php" class="btn btn-outline-primary">ORDER NOW</a>
                        </div>

                        <div class="col-md-4 feature-card">
                            <img src="assets/img/iceddonmatcha.png" class="img-fluid" alt="Don Matcha">
                            <h5>Don Matcha</h5>
                            <a href="menu.php" class="btn btn-outline-primary">ORDER NOW</a>
                        </div>

                        <div class="col-md-4 feature-card">
                            <img src="assets/img/icedmatchaberry.png" class="img-fluid" alt="Matcha Berry">
                            <h5>Matcha Berry</h5>
                            <a href="menu.php" class="btn btn-outline-primary">ORDER NOW</a>
                        </div>
                    </div>
                </div>

                <!-- SLIDE 3 -->
                <div class="carousel-item">
                    <div class="row g-4">
                        <div class="col-md-4 feature-card">
                            <img src="assets/img/icedoreocoffee.png" class="img-fluid" alt="Oreo Coffee">
                            <h5>Oreo Coffee</h5>
                            <a href="menu.php" class="btn btn-outline-primary">ORDER NOW</a>
                        </div>

                        <div class="col-md-4 feature-card">
                            <img src="assets/img/iceddondarko.png" class="img-fluid" alt="Don Darko">
                            <h5>Don Darko</h5>
                            <a href="menu.php" class="btn btn-outline-primary">ORDER NOW</a>
                        </div>

                        <div class="col-md-4 feature-card">
                            <img src="assets/img/icedblackforest.png" class="img-fluid" alt="Black Forest">
                            <h5>Black Forest</h5>
                            <a href="menu.php" class="btn btn-outline-primary">ORDER NOW</a>
                        </div>
                    </div>
                </div>

            </div>

            <button class="carousel-control-prev" type="button"
                    data-bs-target="#featureCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>

            <button class="carousel-control-next" type="button"
                    data-bs-target="#featureCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>

        </div>
    </div>
</section>

<!-- Platforms -->
<section id="app" class="app-section">
    <div class="container">
        <h3>ORDER FROM PLATFORMS!</h3>
        <p>Our services can also be availed in other platforms!</p>
        <div class="d-flex justify-content-center gap-3 mt-3">
            <a href="https://www.foodpanda.ph"><img src="assets/img/foodpanda.jpg" alt="Food Panda" class="img-fluid"></a>
            <a href="https://www.grab.com/ph/"><img src="assets/img/grab.jpg" alt="Grab" class="img-fluid"></a>
        </div>
    </div>
</section>

<?php include "includes/footer.php"; ?>
