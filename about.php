<?php
$page_title = "About";
$active_page = "about";
include "includes/head.php";
include "includes/navbar.php";
?>

<section class="about-hero-section d-flex align-items-center">
    <div class="container">
        <div class="row align-items-center">

            <div class="col-lg-6 text-white">
                <h4 class="hero-subtitle mb-3">☕️ OUR STORY ☕️</h4>
                <h1 class="hero-heading display-3 fw-bold">
                    DON <span>MACCHIATOS</span>
                </h1>

                <p class="hero-text my-4">
                    started in Cebu, Philippines, in late 2022, founded by Ariel Alegado and Nickie San Juan, who
                    wanted to offer affordable, quality coffee (like their signature P39 Iced Caramel Macchiato) to
                    students and budget-conscious people, inspired by their own experiences of high coffee prices
                    and fueled by their previous success with Pure Leaf Milktea, leading to rapid growth as a "work
                    for fun" project that became a viral sensation.
                </p>
            </div>

            <div class="col-lg-6 d-flex justify-content-center">
                <iframe
                    src="https://www.facebook.com/plugins/video.php?href=https://www.facebook.com/watch/?v=1087623022457828&show_text=false&t=0"
                    width="320" height="570" style="border:none;overflow:hidden" scrolling="no" frameborder="0"
                    allowfullscreen="true">
                </iframe>
            </div>

        </div>
    </div>
</section>

<section id="gallery" class="py-5">
    <div class="container">
        <h2 class="text-center mb-4 text-white fw-bold">✨ Photo Gallery ✨</h2>
        <p class="text-center text-white mb-5">A cozy glimpse into Don Macchiatos ☕</p>

        <div class="row g-4">
            <?php
            // Quick gallery loop so you don't repeat 15 blocks manually
            for ($i = 1; $i <= 15; $i++):
                $src = "assets/img/about{$i}.jpg";
            ?>
                <div class="col-md-4">
                    <div class="gallery-card">
                        <img src="<?php echo $src; ?>" alt="Gallery Image <?php echo $i; ?>" class="img-fluid">
                    </div>
                </div>
            <?php endfor; ?>
        </div>
    </div>
</section>

<?php include "includes/footer.php"; ?>
