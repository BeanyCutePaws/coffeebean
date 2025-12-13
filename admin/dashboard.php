<?php
// admin/dashboard.php
require_once __DIR__ . "/../config.php";
require_once __DIR__ . "/includes/admin-auth.php";

$page_title  = "Dashboard";
$active_page = "dashboard";

include __DIR__ . "/includes/admin-head.php";
include __DIR__ . "/includes/admin-navbar.php";

function statusNice($s) {
  return ucwords(str_replace('_', ' ', (string)$s));
}

// ---------------------- Branch restriction ----------------------
$where  = "1=1";
$params = [];
$types  = "";

if (($ADMIN_ROLE ?? '') === 'branch_admin' && !empty($ADMIN_BRANCH_ID)) {
  $where .= " AND branch_id = ?";
  $types .= "i";
  $params[] = (int)$ADMIN_BRANCH_ID;
}

// ---------------------- Counts ----------------------
$counts = [
  'all'            => 0,
  'pending'        => 0,
  'preparing'      => 0,
  'out_for_delivery' => 0,
  'completed'      => 0,
  'cancelled'      => 0,
];

$stmt = $mysqli->prepare("
  SELECT status, COUNT(*) AS c
  FROM orders
  WHERE $where
  GROUP BY status
");
if ($stmt) {
  if ($types) $stmt->bind_param($types, ...$params);
  $stmt->execute();
  $res = $stmt->get_result();
  while ($res && ($row = $res->fetch_assoc())) {
    $st = (string)$row['status'];
    $c  = (int)$row['c'];
    $counts['all'] += $c;
    if (array_key_exists($st, $counts)) $counts[$st] = $c;
  }
  $stmt->close();
}

// ---------------------- Recent orders ----------------------
$recent = [];
$stmt = $mysqli->prepare("
  SELECT order_id, order_code, customer_name, total_amount, status, created_at
  FROM orders
  WHERE $where
  ORDER BY created_at DESC
  LIMIT 8
");
if ($stmt) {
  if ($types) $stmt->bind_param($types, ...$params);
  $stmt->execute();
  $res = $stmt->get_result();
  while ($res && ($row = $res->fetch_assoc())) $recent[] = $row;
  $stmt->close();
}
?>

<!-- HERO (ADMIN STYLE) -->
<section class="py-4">
  <div class="container">
    <div class="panel-card rounded-4 p-4 text-white"
         style="background:rgba(0,0,0,.45);">
      <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
          <div class="text-white-50 small mb-1">DON MACCHIATOS • Admin Panel</div>
          <h2 class="fw-bold mb-1">Dashboard ☕</h2>
          <div class="text-white-50 small">
            <?= ($ADMIN_ROLE ?? '') === 'super_admin'
              ? "Managing all branches. Updates here reflect on Track Order."
              : "Managing Branch #" . (int)$ADMIN_BRANCH_ID . ". Updates reflect on Track Order." ?>
          </div>
        </div>
        <div class="d-flex gap-2">
          <span class="badge bg-primary px-3 py-2">Live Orders</span>
          <span class="badge bg-dark bg-opacity-75 px-3 py-2">
            Total: <?= (int)$counts['all'] ?>
          </span>
        </div>
      </div>
    </div>
  </div>
</section>

<div class="container py-4">

  <!-- STAT CARDS -->
  <div class="row g-3 mb-4">
    <?php
      $stats = [
        ['All Orders',     'fa-layer-group',  'text-white',   $counts['all']],
        ['Pending',        'fa-clock',        'text-warning', $counts['pending']],
        ['Preparing',      'fa-fire-burner',  'text-warning', $counts['preparing']],
        ['Out / Ready',    'fa-motorcycle',   'text-primary', $counts['out_for_delivery']],
        ['Completed',      'fa-circle-check', 'text-success', $counts['completed']],
        ['Cancelled',      'fa-circle-xmark', 'text-danger',  $counts['cancelled']],
      ];
      foreach ($stats as $s):
    ?>
      <div class="col-6 col-lg-2">
        <div class="panel-card rounded-4 p-3 text-center h-100">
          <i class="fa-solid <?= h($s[1]) ?> fs-3 mb-2"></i>
          <div class="small text-white-50"><?= h($s[0]) ?></div>
          <div class="fw-bold fs-4 <?= h($s[2]) ?>"><?= (int)$s[3] ?></div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <!-- RECENT -->
  <div class="panel-card rounded-4 p-4">
    <div class="d-flex justify-content-between align-items-center mb-2">
      <h5 class="fw-bold mb-0">Recent Orders</h5>
      <div class="text-white-50 small">Latest 8</div>
    </div>

    <?php if (empty($recent)): ?>
      <div class="text-white-50">No orders yet.</div>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table table-dark table-borderless align-middle mb-0">
          <thead style="opacity:.9;">
            <tr>
              <th>Code</th>
              <th>Customer</th>
              <th class="text-end">Total</th>
              <th>Status</th>
              <th class="text-end">Placed</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($recent as $r): ?>
              <tr style="border-top:1px solid rgba(255,255,255,.08);">
                <td class="fw-semibold font-monospace"><?= h($r['order_code']) ?></td>
                <td><?= h($r['customer_name']) ?></td>
                <td class="text-end fw-bold">₱<?= money($r['total_amount']) ?></td>
                <td>
                  <span class="badge <?= h(badge($r['status'])) ?>">
                    <?= h(statusNice($r['status'])) ?>
                  </span>
                </td>
                <td class="text-end text-white-50 small">
                  <?= h(date("m-d H:i", strtotime($r['created_at']))) ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>

</div>

<?php include __DIR__ . "/includes/admin-footer.php"; ?>
