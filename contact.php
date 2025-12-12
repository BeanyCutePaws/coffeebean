<?php
$page_title  = "Contact";
$active_page = "contact";

include "includes/head.php";
include "includes/navbar.php";

$branches = require __DIR__ . "/data/branches.php";
?>

<section class="py-5" style="min-height: 70vh;">
    <div class="container text-white">
        <div class="row g-4 align-items-start">

            <!-- LEFT: MESSAGE FORM (keep old vibe) -->
            <div class="col-lg-5">
                <h1 class="fw-bold mb-3">Contact Us</h1>
                <p class="mb-4">
                    Got questions, feedback, or concerns? Message us and we’ll get back to you.
                </p>

                <div class="p-4 rounded-4 bg-dark bg-opacity-75">
                    <h5 class="fw-bold mb-3">Send a Message</h5>

                    <form action="#" method="post">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" placeholder="Your name" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" placeholder="you@example.com" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Message</label>
                            <textarea class="form-control" rows="4" placeholder="Type your message..." required></textarea>
                        </div>

                        <button class="btn btn-primary w-100" type="submit">
                            <i class="fa-solid fa-paper-plane me-2"></i> Send
                        </button>

                        <small class="d-block text-light mt-3">
                            Note: This form is for demo UI only
                        </small>
                    </form>
                </div>
            </div>

            <!-- RIGHT: BRANCH SEARCH + MAP -->
            <div class="col-lg-7">
                <h3 class="fw-bold mb-3">Branches & Locations</h3>
                <p class="text-white-50 mb-4">
                    Search for a branch to view its location and operating hours.
                </p>

                <div class="p-4 rounded-4 bg-dark bg-opacity-75">
                    <label class="form-label fw-bold">Find a branch</label>

                    <!-- ONE INPUT: search + select -->
                    <div class="position-relative">
                        <input
                            id="branchSearch"
                            type="text"
                            class="form-control"
                            placeholder="Type: Dasma, Taguig, Manila..."
                            autocomplete="off"
                        >

                        <!-- Suggestions dropdown -->
                        <div
                            id="branchSuggestions"
                            class="list-group position-absolute w-100 mt-2 shadow d-none"
                            style="z-index: 9999; max-height: 280px; overflow:auto;"
                        ></div>
                    </div>

                    <!-- Selected branch info -->
                    <div id="branchInfo" class="mt-4 d-none">
                        <h5 id="branchName" class="fw-bold mb-2"></h5>

                        <div class="small text-white-50 mb-3">
                            <div class="mb-1">
                                <i class="fa-solid fa-location-dot me-2"></i>
                                <span id="branchAddress"></span>
                            </div>

                            <div class="mb-1" id="branchPlusCodeRow" style="display:none;">
                                <i class="fa-solid fa-hashtag me-2"></i>
                                <span id="branchPlusCode"></span>
                            </div>

                            <div class="mb-1" id="branchPhoneRow" style="display:none;">
                                <i class="fa-solid fa-phone me-2"></i>
                                <span id="branchPhone"></span>
                            </div>

                            <div class="mb-1" id="branchCoordsRow" style="display:none;">
                                <i class="fa-solid fa-location-crosshairs me-2"></i>
                                <span id="branchCoords"></span>
                            </div>
                        </div>

                        <!-- Buttons (match Beany style: outline-light + primary) -->
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <a id="btnMaps" class="btn btn-outline-light btn-sm" target="_blank" rel="noopener">
                                <i class="fa-solid fa-map-location-dot me-2"></i> View Map
                            </a>

                            <a id="btnFacebook" class="btn btn-outline-light btn-sm d-none" target="_blank" rel="noopener">
                                <i class="fa-brands fa-facebook me-2"></i> Facebook
                            </a>
                        </div>

                        <!-- Hours -->
                        <div class="mt-3">
                            <h6 class="fw-bold mb-2">Operating Hours</h6>
                            <div id="branchHours" class="small text-white-50"></div>
                        </div>

                        <!-- Embed -->
                        <div class="mt-4 rounded-4 overflow-hidden bg-dark bg-opacity-50 p-2">
                            <div id="mapEmbed" class="ratio ratio-4x3"></div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 p-3 rounded-4 bg-dark bg-opacity-50">
                    <div class="small text-white-50">
                        Tip: Updating branches is just editing <code class="text-white">data/branches.php</code>.
                        Later we can migrate to DB without changing this page layout.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Branch data for JS -->
    <script id="branches-data" type="application/json"><?php
        echo json_encode($branches, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    ?></script>
</section>

<?php include "includes/footer.php"; ?>
