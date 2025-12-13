<?php
// admin/orders.php
require_once __DIR__ . "/../config.php";
require_once __DIR__ . "/includes/admin-auth.php";
require_once __DIR__ . "/includes/admin-helpers.php";

$isAjax = (isset($_GET['ajax']) && $_GET['ajax'] === '1');

$page_title  = "Orders";
$active_page = "orders";

// Only include full layout on non-AJAX
if (!$isAjax) {
  include __DIR__ . "/includes/admin-head.php";
  include __DIR__ . "/includes/admin-navbar.php";
}

// ---------------------- Filters (GET) ----------------------
$q         = trim((string)($_GET['q'] ?? ''));
$statusF   = trim((string)($_GET['status'] ?? ''));
$dateFrom  = trim((string)($_GET['from'] ?? ''));
$dateTo    = trim((string)($_GET['to'] ?? ''));
$page      = max(1, (int)($_GET['page'] ?? 1));
$perPage   = 10;

$branchF = null;
if (($ADMIN_ROLE ?? '') === 'super_admin') {
  $branchF = ($_GET['branch_id'] ?? '');
  $branchF = ($branchF === '' ? null : (int)$branchF);
} else {
  $branchF = !empty($ADMIN_BRANCH_ID) ? (int)$ADMIN_BRANCH_ID : null;
}

// quick "today" helper
if (isset($_GET['today']) && $_GET['today'] === '1') {
  $dateFrom = date('Y-m-d');
  $dateTo   = date('Y-m-d');
}

// ---------------------- WHERE builder ----------------------
$where  = "1=1";
$params = [];
$types  = "";

// branch restriction
if (!empty($branchF)) {
  $where .= " AND o.branch_id = ?";
  $types .= "i";
  $params[] = (int)$branchF;
}

// status
$validStatuses = ['pending','preparing','out_for_delivery','completed','cancelled'];
if ($statusF !== '' && in_array($statusF, $validStatuses, true)) {
  $where .= " AND o.status = ?";
  $types .= "s";
  $params[] = $statusF;
}

// search by order code (or customer name)
if ($q !== '') {
  $where .= " AND (o.order_code LIKE CONCAT('%', ?, '%') OR o.customer_name LIKE CONCAT('%', ?, '%'))";
  $types .= "ss";
  $params[] = $q;
  $params[] = $q;
}

// date range (created_at)
if ($dateFrom !== '') {
  $where .= " AND DATE(o.created_at) >= ?";
  $types .= "s";
  $params[] = $dateFrom;
}
if ($dateTo !== '') {
  $where .= " AND DATE(o.created_at) <= ?";
  $types .= "s";
  $params[] = $dateTo;
}

// ---------------------- Branch dropdown data (beeg admin only) ----------------------
$branches = [];
if (($ADMIN_ROLE ?? '') === 'super_admin') {
  $res = $mysqli->query("SELECT branch_id, name FROM branches WHERE is_active=1 ORDER BY name ASC");
  if ($res) { while ($r = $res->fetch_assoc()) $branches[] = $r; $res->free(); }
}

// ---------------------- Urgent / most recent (top strip) ----------------------
$urgent = [];
$stmt = $mysqli->prepare("
  SELECT o.order_id, o.order_code, o.customer_name, o.total_amount, o.status, o.created_at,
         b.name AS branch_name
  FROM orders o
  LEFT JOIN branches b ON b.branch_id = o.branch_id
  WHERE $where
    AND o.status IN ('pending','preparing','out_for_delivery')
  ORDER BY o.created_at DESC
  LIMIT 6
");
if ($stmt) {
  if ($types) $stmt->bind_param($types, ...$params);
  $stmt->execute();
  $res = $stmt->get_result();
  while ($res && ($row = $res->fetch_assoc())) $urgent[] = $row;
  $stmt->close();
}

// ---------------------- Count for pagination (all matching orders) ----------------------
$totalRows = 0;
$stmt = $mysqli->prepare("SELECT COUNT(*) AS c FROM orders o WHERE $where");
if ($stmt) {
  if ($types) $stmt->bind_param($types, ...$params);
  $stmt->execute();
  $res = $stmt->get_result();
  $row = $res ? $res->fetch_assoc() : null;
  $totalRows = (int)($row['c'] ?? 0);
  $stmt->close();
}

$totalPages = max(1, (int)ceil($totalRows / $perPage));
if ($page > $totalPages) $page = $totalPages;
$offset = ($page - 1) * $perPage;

// ---------------------- Paged list (cards) ----------------------
$orders = [];
$sql = "
  SELECT o.order_id, o.order_code, o.customer_name, o.customer_phone, o.total_amount, o.status, o.created_at,
         b.name AS branch_name
  FROM orders o
  LEFT JOIN branches b ON b.branch_id = o.branch_id
  WHERE $where
  ORDER BY o.created_at DESC
  LIMIT ? OFFSET ?
";
$stmt = $mysqli->prepare($sql);
if ($stmt) {
  if ($types) {
    $bindTypes  = $types . "ii";
    $bindParams = array_merge($params, [$perPage, $offset]);
    $stmt->bind_param($bindTypes, ...$bindParams);
  } else {
    $stmt->bind_param("ii", $perPage, $offset);
  }

  $stmt->execute();
  $res = $stmt->get_result();
  while ($res && ($row = $res->fetch_assoc())) $orders[] = $row;
  $stmt->close();
}

// ---------------------- AJAX: return urgent + list together ----------------------
if ($isAjax) {
  ?>
  <!-- TOP: URGENT / MOST RECENT -->
  <div class="panel-card rounded-4 p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center mb-2">
      <h5 class="fw-bold mb-0">Most Recent (Urgent)</h5>
      <div class="text-white-50 small">Pending / Preparing / Out</div>
    </div>

    <?php if (empty($urgent)): ?>
      <div class="text-white-50">No urgent orders right now.</div>
    <?php else: ?>
      <div class="row g-3">
        <?php foreach ($urgent as $u): ?>
          <?php $ns = nextStatus($u['status']); ?>
          <div class="col-md-6 col-lg-4">
            <div class="panel-card rounded-4 p-3 h-100" style="background:rgba(0,0,0,.28);">
              <div class="d-flex justify-content-between align-items-start gap-2">
                <div>
                  <div class="fw-bold font-monospace"><?= h($u['order_code']) ?></div>
                  <div class="small text-white-50"><?= h($u['customer_name']) ?></div>
                </div>
                <span class="badge <?= h(badge($u['status'])) ?>">
                  <?= h(statusNice($u['status'])) ?>
                </span>
              </div>

              <div class="d-flex justify-content-between mt-2">
                <div class="small text-white-50">
                  <i class="fa-regular fa-clock me-1"></i><?= h(date("m-d H:i", strtotime($u['created_at']))) ?>
                  <div class="small text-white-50">
                    <i class="fa-solid fa-store me-1"></i><?= h($u['branch_name'] ?? 'Branch') ?>
                  </div>
                </div>
                <div class="fw-bold text-success">₱<?= money($u['total_amount']) ?></div>
              </div>

              <div class="d-flex gap-2 mt-3">
                <a class="btn btn-outline-light btn-sm flex-grow-1"
                   href="order-view.php?order_id=<?= (int)$u['order_id'] ?>">
                  View
                </a>

                <?php if ($ns): ?>
                  <button class="btn btn-primary btn-sm flex-grow-1 js-next-status"
                          data-order-id="<?= (int)$u['order_id'] ?>">
                    Next: <?= h(statusNice($ns)) ?>
                  </button>
                <?php else: ?>
                  <button class="btn btn-outline-light btn-sm flex-grow-1" disabled>
                    No next step
                  </button>
                <?php endif; ?>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>

  <!-- LIST AREA -->
  <div id="js-orders-area">
    <?php include __DIR__ . "/partials/orders-list.php"; ?>
  </div>
  <?php
  exit;
}
?>

<!-- HERO / HEADER -->
<section class="py-4">
  <div class="container">
    <div class="panel-card rounded-4 p-4 text-white" style="background:rgba(0,0,0,.45);">
      <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
          <div class="text-white-50 small mb-1">DON MACCHIATOS • Admin Panel</div>
          <h2 class="fw-bold mb-1">Orders ☕</h2>
          <div class="text-white-50 small">
            <?= ($ADMIN_ROLE ?? '') === 'super_admin'
              ? "Manage all branches. Status changes reflect on Track Order."
              : "Manage your branch. Status changes reflect on Track Order." ?>
          </div>
        </div>
        <div class="d-flex gap-2 flex-wrap">
          <span class="badge bg-primary px-3 py-2">Live Updates</span>
          <span class="badge bg-dark bg-opacity-75 px-3 py-2">Total: <?= (int)$totalRows ?></span>
        </div>
      </div>
    </div>
  </div>
</section>

<div class="container py-4">

  <!-- FILTERS -->
  <div class="panel-card rounded-4 p-4 mb-4">
    <form method="get" class="row g-3 align-items-end" id="ordersFilterForm">
      <div class="col-lg-3">
        <label class="form-label small text-white-50">Search (code or name)</label>
        <input class="form-control" name="q" value="<?= h($q) ?>" placeholder="DM-000123 / Juan">
      </div>

      <div class="col-lg-2">
        <label class="form-label small text-white-50">Status</label>
        <select class="form-select" name="status">
          <option value="">All</option>
          <?php foreach (['pending','preparing','out_for_delivery','completed','cancelled'] as $st): ?>
            <option value="<?= h($st) ?>" <?= $statusF===$st ? 'selected' : '' ?>>
              <?= h(statusNice($st)) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <?php if (($ADMIN_ROLE ?? '') === 'super_admin'): ?>
        <div class="col-lg-3">
          <label class="form-label small text-white-50">Branch</label>
          <select class="form-select" name="branch_id">
            <option value="">All branches</option>
            <?php foreach ($branches as $b): ?>
              <option value="<?= (int)$b['branch_id'] ?>" <?= ((int)$branchF === (int)$b['branch_id']) ? 'selected' : '' ?>>
                <?= h($b['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
      <?php else: ?>
        <div class="col-lg-3">
          <label class="form-label small text-white-50">Branch</label>
          <input class="form-control" value="Your branch only" disabled>
        </div>
      <?php endif; ?>

      <div class="col-lg-2">
        <label class="form-label small text-white-50">From</label>
        <input type="date" class="form-control" name="from" value="<?= h($dateFrom) ?>">
      </div>

      <div class="col-lg-2">
        <label class="form-label small text-white-50">To</label>
        <input type="date" class="form-control" name="to" value="<?= h($dateTo) ?>">
      </div>

      <div class="col-12 d-flex gap-2 flex-wrap">
        <button class="btn btn-primary fw-bold" type="submit">
          <i class="fa-solid fa-filter me-2"></i>Apply Filters
        </button>

        <a class="btn btn-outline-light" href="orders.php">Reset</a>

        <button class="btn btn-outline-light" type="submit" name="today" value="1">Today</button>

        <div class="ms-auto small text-white-50 align-self-center">
          Auto-refresh: <strong>ON</strong> (every 5s)
        </div>
      </div>
    </form>
  </div>

  <!-- ✅ ONE SHELL: urgent + list updates together -->
  <div id="js-orders-shell">
    <!-- TOP: URGENT / MOST RECENT -->
    <div class="panel-card rounded-4 p-4 mb-4">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <h5 class="fw-bold mb-0">Most Recent (Urgent)</h5>
        <div class="text-white-50 small">Pending / Preparing / Out</div>
      </div>

      <?php if (empty($urgent)): ?>
        <div class="text-white-50">No urgent orders right now.</div>
      <?php else: ?>
        <div class="row g-3">
          <?php foreach ($urgent as $u): ?>
            <?php $ns = nextStatus($u['status']); ?>
            <div class="col-md-6 col-lg-4">
              <div class="panel-card rounded-4 p-3 h-100" style="background:rgba(0,0,0,.28);">
                <div class="d-flex justify-content-between align-items-start gap-2">
                  <div>
                    <div class="fw-bold font-monospace"><?= h($u['order_code']) ?></div>
                    <div class="small text-white-50"><?= h($u['customer_name']) ?></div>
                  </div>
                  <span class="badge <?= h(badge($u['status'])) ?>">
                    <?= h(statusNice($u['status'])) ?>
                  </span>
                </div>

                <div class="d-flex justify-content-between mt-2">
                  <div class="small text-white-50">
                    <i class="fa-regular fa-clock me-1"></i><?= h(date("m-d H:i", strtotime($u['created_at']))) ?>
                    <div class="small text-white-50">
                      <i class="fa-solid fa-store me-1"></i><?= h($u['branch_name'] ?? 'Branch') ?>
                    </div>
                  </div>
                  <div class="fw-bold text-success">₱<?= money($u['total_amount']) ?></div>
                </div>

                <div class="d-flex gap-2 mt-3">
                  <a class="btn btn-outline-light btn-sm flex-grow-1"
                     href="order-view.php?order_id=<?= (int)$u['order_id'] ?>">
                    View
                  </a>

                  <?php if ($ns): ?>
                    <button class="btn btn-primary btn-sm flex-grow-1 js-next-status"
                            data-order-id="<?= (int)$u['order_id'] ?>">
                      Next: <?= h(statusNice($ns)) ?>
                    </button>
                  <?php else: ?>
                    <button class="btn btn-outline-light btn-sm flex-grow-1" disabled>
                      No next step
                    </button>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

    <!-- LIST AREA -->
    <div id="js-orders-area">
      <?php include __DIR__ . "/partials/orders-list.php"; ?>
    </div>
  </div>

</div>

<!-- Keep your existing JS file if you want, but this guarantees shell refresh from this file only -->
<script>
document.addEventListener("DOMContentLoaded", () => {
  const shell = document.getElementById("js-orders-shell");
  if (!shell) return;

  const nextUrl = "actions/order-next-status.php";

  function withAjax(url) {
    const u = new URL(url, window.location.origin);
    u.searchParams.set("ajax", "1");
    return u.toString();
  }

  async function refreshShell() {
    try {
      const res = await fetch(withAjax(window.location.href), { cache: "no-store" });
      shell.innerHTML = await res.text();
    } catch (e) {}
  }

  // pagination without full refresh
  document.addEventListener("click", async (e) => {
    const a = e.target.closest("#js-orders-shell .pagination a");
    if (!a) return;
    e.preventDefault();

    try {
      const res = await fetch(withAjax(a.href), { cache: "no-store" });
      shell.innerHTML = await res.text();
      history.pushState({}, "", a.href);
    } catch (e) {}
  });

  // next status (urgent + list)
  document.addEventListener("click", async (e) => {
    const btn = e.target.closest(".js-next-status");
    if (!btn) return;

    const orderId = btn.dataset.orderId;
    if (!orderId) return;

    btn.disabled = true;
    const old = btn.innerHTML;
    btn.innerHTML = `<i class="fa-solid fa-spinner fa-spin me-1"></i> Updating...`;

    try {
      const body = new URLSearchParams({ order_id: orderId });
      const res = await fetch(nextUrl, {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body
      });
      const data = await res.json();
      if (!data.success) throw new Error("fail");
      await refreshShell();
    } catch (e) {
      btn.innerHTML = old;
      btn.disabled = false;
    }
  });

  setInterval(refreshShell, 5000);
});
</script>

<?php include __DIR__ . "/includes/admin-footer.php"; ?>
