<?php
$page_title = "About";
$active_page = "about";
include "includes/head.php";
include "includes/navbar.php";
?>

<!-- HERO -->
<section class="about-hero-section position-relative py-5">
    <!-- subtle overlay -->
    <div class="position-absolute top-0 start-0 w-100 h-100"
         style="background:linear-gradient(90deg, rgba(0,0,0,.85), rgba(0,0,0,.35));">
    </div>

    <div class="container position-relative">
        <div class="row g-4 align-items-center">

            <!-- TEXT CARD -->
            <div class="col-lg-6">
                <div class="p-4 p-md-5 rounded-4 bg-dark bg-opacity-75 shadow">
                    <div class="d-inline-flex align-items-center gap-2 px-3 py-2 rounded-pill mb-3"
                         style="background:rgba(255,255,255,.08);">
                        <span>☕️</span>
                        <span class="text-white-50 small fw-semibold">OUR STORY</span>
                    </div>

                    <h1 class="display-4 fw-bold text-white mb-3">
                        DON <span class="text-primary">MACCHIATOS</span>
                    </h1>

                    <p class="text-white-50 mb-4" style="line-height:1.7;">
                        Don Macchiatos started in Cebu, Philippines in late 2022, founded by Ariel Alegado and
                        Nickie San Juan. Their goal was simple: offer affordable, quality coffee (like the signature
                        P39 Iced Caramel Macchiato) for students and budget-conscious coffee lovers — a “work for fun”
                        project that quickly became a viral sensation.
                    </p>

                    <div class="d-flex flex-wrap gap-2">
                        <a class="btn btn-primary" href="menu.php">
                            <i class="fa-solid fa-mug-hot me-2"></i> Order Now
                        </a>
                        <a class="btn btn-outline-light" href="contact.php">
                            <i class="fa-solid fa-location-dot me-2"></i> Find a Branch
                        </a>
                    </div>
                </div>
            </div>

            <!-- VIDEO CARD -->
            <div class="col-lg-6">
                <div class="p-3 p-md-4 rounded-4 bg-dark bg-opacity-75 shadow">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="fw-bold text-white">Featured Video</div>
                    <div class="small text-white-50">Facebook</div>
                </div>

                <!-- 9:16 aspect ratio using CSS variable -->
                <div class="ratio rounded-4 overflow-hidden"
                    style="--bs-aspect-ratio: 177.78%; background:rgba(255,255,255,.06);">
                    <iframe
                    src="https://www.facebook.com/plugins/video.php?href=https://www.facebook.com/watch/?v=1087623022457828&show_text=false&t=0"
                    style="border:none;overflow:hidden;width:100%;height:100%;"
                    scrolling="no"
                    frameborder="0"
                    allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share"
                    allowfullscreen="true">
                    </iframe>
                </div>

                <div class="mt-2 small text-white-50">
                    If the video doesn’t load (adblock/cookies), <a class="text-white" target="_blank" rel="noopener"
                    href="https://www.facebook.com/watch/?v=1087623022457828">open it on Facebook</a>.
                </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- GALLERY -->
<section id="gallery" class="py-5">
    <div class="container">
        <div class="text-center mb-4">
            <h2 class="text-white fw-bold mb-2">✨ Photo Gallery ✨</h2>
            <p class="text-white-50 mb-0">A cozy glimpse into Don Macchiatos ☕</p>
        </div>

        <div class="row g-4">
            <?php for ($i = 1; $i <= 15; $i++): $src = "assets/img/about{$i}.jpg"; ?>
                <div class="col-6 col-md-4">
                    <div class="rounded-4 overflow-hidden bg-dark bg-opacity-50 shadow-sm h-100 gallery-tile"
                         style="cursor:pointer;">
                        <img src="<?php echo $src; ?>"
                             alt="Gallery Image <?php echo $i; ?>"
                             class="w-100"
                             style="height:220px; object-fit:cover;">
                    </div>
                </div>
            <?php endfor; ?>
        </div>
    </div>
</section>

<?php include "includes/footer.php"; ?>
