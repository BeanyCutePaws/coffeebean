<?php
$page_title  = "Menu";
$active_page = "menu";

require_once __DIR__ . "/config.php";
require_once __DIR__ . "/config/keys.php";

include __DIR__ . "/includes/head.php";
include __DIR__ . "/includes/navbar.php";

$branches = [];
$res = $mysqli->query("SELECT branch_id, name, address, lat, lng, is_active FROM branches WHERE is_active=1 ORDER BY name ASC");
if ($res) { while ($row = $res->fetch_assoc()) $branches[] = $row; $res->free(); }

$categories = [];
$res = $mysqli->query("SELECT category_id, name FROM categories WHERE is_active=1 ORDER BY sort_order ASC, name ASC");
if ($res) { while ($row = $res->fetch_assoc()) $categories[] = $row; $res->free(); }

$products = [];
$sql = "
  SELECT p.product_id, p.category_id, c.name AS category_name, p.name, p.description, p.image_path, p.price, p.allow_no_coffee
  FROM products p
  LEFT JOIN categories c ON c.category_id=p.category_id
  WHERE p.is_active=1
  ORDER BY c.sort_order ASC, c.name ASC, p.name ASC
";
$res = $mysqli->query($sql);
if ($res) { while ($row = $res->fetch_assoc()) $products[] = $row; $res->free(); }

$availability = [];
$res = $mysqli->query("SELECT branch_id, product_id, is_available FROM branch_product_availability");
if ($res) {
  while ($row = $res->fetch_assoc()) {
    $b = (string)$row['branch_id'];
    $p = (string)$row['product_id'];
    if (!isset($availability[$b])) $availability[$b] = [];
    $availability[$b][$p] = (int)$row['is_available'];
  }
  $res->free();
}

$recaptchaSiteKey = defined('RECAPTCHA_SITE_KEY') ? RECAPTCHA_SITE_KEY : '';
?>

<!-- Leaflet (no API key) -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" defer></script>

<!-- STEP 1: Location + Branch selection -->
<section class="py-5">
  <div class="container text-white">
    <div class="row g-4">
      <div class="col-lg-7">
        <div class="panel-card rounded-4 p-4 h-100">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="fw-bold mb-0">1) Pin your location</h4>
            <button id="btnUseMyLocation" class="btn btn-outline-light btn-sm rounded-pill" type="button">
              Use my location
            </button>
          </div>

          <div class="text-white-50 small mb-3">
            Drag the pin to your exact spot (works on PC too).
          </div>

          <div id="leafletMap" class="menu-map rounded-4"></div>

          <div class="d-flex flex-wrap gap-2 mt-3">
            <span class="badge bg-dark bg-opacity-75">Nearest branch: <span id="nearestBranchName">—</span></span>
            <span class="badge bg-dark bg-opacity-75">Distance: <span id="nearestBranchDist">—</span></span>
            <span class="badge bg-dark bg-opacity-75">Mode: <span id="orderModeBadge">—</span></span>
          </div>
        </div>
      </div>

      <div class="col-lg-5">
        <div class="panel-card rounded-4 p-4 h-100">
          <h4 class="fw-bold mb-2">2) Choose a branch</h4>
          <div class="text-white-50 small mb-3">
            We’ll list branches near you. If you’re too far, we’ll force pickup.
          </div>

          <div id="branchResults" class="branch-results">
            <div class="text-white-50">Move the pin to load nearby branches.</div>
          </div>

          <div class="mt-4">
            <button id="btnConfirmBranch" class="btn btn-primary w-100 fw-bold" type="button" disabled>
              Confirm Branch & View Menu
            </button>
            <div id="branchConfirmMsg" class="small text-danger mt-2 d-none"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- STEP 2: Menu (locked until branch confirm) -->
    <div id="menuLockedWrap" class="mt-5">
      <div class="panel-card rounded-4 p-4">
        <h4 class="fw-bold mb-2">Menu</h4>
        <div class="text-white-50">
          Please confirm your pinned location and branch first.
        </div>
      </div>
    </div>

    <div id="menuWrap" class="mt-5 d-none">
    <div class="row g-4">
    
    <!-- LEFT: MENU -->
    <div class="col-lg-8">
      <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-4">
        <div>
          <h4 class="fw-bold mb-1">Menu</h4>
          <div class="text-white-50 small">
            Now showing availability for: <span id="selectedBranchLabel">—</span>
          </div>
        </div>

        <input id="menuSearch"
               class="form-control form-control-sm menu-search"
               placeholder="Search drinks..." />
      </div>

      <div class="menu-tabs mb-4">
        <button class="btn btn-outline-light btn-sm me-2 mb-2 menu-tab active"
                data-category="all">All</button>
        <?php foreach ($categories as $c): ?>
          <button class="btn btn-outline-light btn-sm me-2 mb-2 menu-tab"
                  data-category="<?= (int)$c['category_id']; ?>">
            <?= htmlspecialchars($c['name']); ?>
          </button>
        <?php endforeach; ?>
      </div>

      <div id="menuGrid" class="row g-4"></div>
    </div>

    <!-- RIGHT: CART -->
    <div class="col-lg-4">
      <aside class="checkout-sticky">
        <div class="panel-card rounded-4 p-4">

          <div id="cartEmpty" class="text-white-50">
            Your cart is empty.
          </div>

          <div id="cartList" class="d-none"></div>

          <div id="cartSummary" class="d-none mt-4">
            <div class="d-flex justify-content-between mb-2">
              <span class="text-white-50">Total</span>
              <span class="fw-bold">₱<span id="cartSubtotal">0.00</span></span>
            </div>

            <!-- CAPTCHA -->
            <?php if (!empty($recaptchaSiteKey)): ?>
              <div class="mt-3">
                <div class="g-recaptcha"
                    data-sitekey="<?= htmlspecialchars($recaptchaSiteKey); ?>"
                    data-callback="onCaptchaSuccess"></div>
              </div>
            <?php endif; ?>

            <button id="btnProceedCheckout" class="btn btn-primary w-100 fw-bold mt-3">
              Verify to Continue
            </button>

            <div id="checkoutGateMsg" class="small text-danger mt-2 d-none"></div>
          </div>

        </div>
      </aside>
    </div>


  </div>
</div>

  </div>
</section>

<script id="menu-data" type="application/json"><?php
echo json_encode([
  "categories"   => $categories,
  "products"     => $products,
  "branches"     => $branches,
  "availability" => $availability,
  "recaptcha"    => ["enabled" => !empty($recaptchaSiteKey)],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
?></script>

<?php include __DIR__ . "/includes/footer.php"; ?>
