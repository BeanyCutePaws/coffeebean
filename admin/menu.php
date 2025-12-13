<?php
require_once __DIR__ . "/../config.php";
require_once __DIR__ . "/includes/admin-auth.php";
require_once __DIR__ . "/includes/admin-helpers.php";

$page_title  = "Menu Availability";
$active_page = "menu";

include __DIR__ . "/includes/admin-head.php";
include __DIR__ . "/includes/admin-navbar.php";

/* ---------------- Branch logic ---------------- */
if ($ADMIN_ROLE === 'super_admin') {
  $branchId = (int)($_GET['branch_id'] ?? 0);

  if ($branchId <= 0) {
    $branchId = (int)$mysqli->query(
      "SELECT branch_id FROM branches WHERE is_active=1 ORDER BY name LIMIT 1"
    )->fetch_row()[0];
  }
} else {
  $branchId = (int)$ADMIN_BRANCH_ID;
}

/* ---------------- Branch list (super admin) ---------------- */
$branches = [];
if ($ADMIN_ROLE === 'super_admin') {
  $res = $mysqli->query("SELECT branch_id, name FROM branches WHERE is_active=1 ORDER BY name");
  while ($r = $res->fetch_assoc()) $branches[] = $r;
}

/* ---------------- Products + availability ---------------- */
$items = [];
$stmt = $mysqli->prepare("
  SELECT 
    p.product_id,
    p.name,
    p.image_path,
    p.is_active,
    IFNULL(bpa.is_available, 0) AS is_available
  FROM products p
  LEFT JOIN branch_product_availability bpa
    ON bpa.product_id = p.product_id
   AND bpa.branch_id = ?
  WHERE p.is_active = 1
  ORDER BY p.name ASC
");
$stmt->bind_param("i", $branchId);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) $items[] = $row;
$stmt->close();
?>

<!-- HERO -->
<section class="py-4">
  <div class="container">
    <div class="panel-card rounded-4 p-4" style="background:rgba(0,0,0,.45);">
      <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
          <div class="text-white-50 small mb-1">DON MACCHIATOS • Admin Panel</div>
          <h2 class="fw-bold mb-0">Menu Availability ☕</h2>
          <div class="text-white-50 small">
            Toggle drinks on/off per branch
          </div>
        </div>

        <?php if ($ADMIN_ROLE === 'super_admin'): ?>
          <form method="get">
            <select name="branch_id" class="form-select" onchange="this.form.submit()">
              <?php foreach ($branches as $b): ?>
                <option value="<?= (int)$b['branch_id'] ?>"
                  <?= $branchId === (int)$b['branch_id'] ? 'selected' : '' ?>>
                  <?= h($b['name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </form>
        <?php else: ?>
          <span class="badge bg-dark bg-opacity-75 px-3 py-2">
            Branch #<?= (int)$branchId ?>
          </span>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<div class="container py-4">

  <div class="row g-3">
    <?php foreach ($items as $it): ?>
      <div class="col-md-6 col-lg-4">
        <div class="panel-card rounded-4 p-3 h-100"
             style="background:rgba(0,0,0,.25);">

          <div class="d-flex align-items-center gap-3">
            <img src="../<?= h($it['image_path']) ?>"
                 alt=""
                 style="width:64px;height:64px;object-fit:contain;">

            <div class="flex-grow-1">
              <div class="fw-bold"><?= h($it['name']) ?></div>
              <div class="small text-white-50">₱39 • Coffee option</div>
            </div>

            <div class="form-check form-switch">
              <input class="form-check-input js-toggle-item"
                     type="checkbox"
                     data-product-id="<?= (int)$it['product_id'] ?>"
                     <?= $it['is_available'] ? 'checked' : '' ?>>
            </div>
          </div>

        </div>
      </div>
    <?php endforeach; ?>
  </div>

</div>

<script>
  window.ADMIN_MENU = {
    branchId: <?= (int)$branchId ?>,
    toggleUrl: "actions/toggle-product.php"
  };
</script>
<script src="../assets/js/admin-menu.js" defer></script>

<?php include __DIR__ . "/includes/admin-footer.php"; ?>
