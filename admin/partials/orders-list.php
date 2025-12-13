<?php
// admin/partials/orders-list.php
// expects: $orders, $page, $totalPages, $totalRows

require_once __DIR__ . "/../includes/admin-helpers.php";

if (!function_exists('buildQuery')) {
  function buildQuery(array $overrides = []) {
    $base = [
      'q'      => $_GET['q']      ?? '',
      'status' => $_GET['status'] ?? '',
      'from'   => $_GET['from']   ?? '',
      'to'     => $_GET['to']     ?? '',
      'page'   => $_GET['page']   ?? 1,
    ];
    if (isset($_GET['branch_id'])) $base['branch_id'] = $_GET['branch_id'];
    $base = array_merge($base, $overrides);
    return http_build_query($base);
  }
}

if (!function_exists('pageBtn')) {
  function pageBtn($label, $p, $isActive=false, $disabled=false) {
    $qs = buildQuery(['page' => $p]);
    $cls = "page-item";
    if ($isActive) $cls .= " active";
    if ($disabled) $cls .= " disabled";
    echo '<li class="'.$cls.'"><a class="page-link" href="?'.$qs.'">'.$label.'</a></li>';
  }
}
?>

<div class="panel-card rounded-4 p-4">
  <div class="d-flex justify-content-between align-items-center mb-2">
    <h5 class="fw-bold mb-0">All Orders</h5>
    <div class="text-white-50 small">
      Showing <strong><?= (int)count($orders) ?></strong> of <strong><?= (int)$totalRows ?></strong>
    </div>
  </div>

  <?php if (empty($orders)): ?>
    <div class="text-white-50">No orders found for your filters.</div>
  <?php else: ?>
    <div class="row g-3">
      <?php foreach ($orders as $o): ?>
        <?php $ns = nextStatus($o['status']); ?>
        <div class="col-lg-6">
          <div class="panel-card rounded-4 p-3 h-100" style="background:rgba(0,0,0,.22);">
            <div class="d-flex justify-content-between align-items-start gap-2">
              <div>
                <div class="fw-bold font-monospace"><?= h($o['order_code']) ?></div>
                <div class="small text-white-50"><?= h($o['customer_name']) ?> • <?= h($o['customer_phone']) ?></div>
                <div class="small text-white-50">
                  <i class="fa-regular fa-clock me-1"></i><?= h(date("m-d H:i", strtotime($o['created_at']))) ?>
                  <span class="mx-2">•</span>
                  <i class="fa-solid fa-store me-1"></i><?= h($o['branch_name'] ?? 'Branch') ?>
                </div>
              </div>

              <div class="text-end">
                <div class="fw-bold text-success">₱<?= money($o['total_amount']) ?></div>
                <span class="badge <?= h(badge($o['status'])) ?> mt-1">
                  <?= h(statusNice($o['status'])) ?>
                </span>
              </div>
            </div>

            <hr class="border-secondary my-3">

            <div class="d-flex gap-2 flex-wrap">
              <a class="btn btn-outline-light btn-sm"
                 href="order-view.php?order_id=<?= (int)$o['order_id'] ?>">
                <i class="fa-solid fa-eye me-1"></i> View details
              </a>

              <?php if ($ns): ?>
                <button class="btn btn-primary btn-sm ms-auto js-next-status"
                        data-order-id="<?= (int)$o['order_id'] ?>">
                  <i class="fa-solid fa-forward-step me-1"></i>
                  Next: <?= h(statusNice($ns)) ?>
                </button>
              <?php else: ?>
                <button class="btn btn-outline-light btn-sm ms-auto" disabled>
                  No next step
                </button>
              <?php endif; ?>
            </div>

            <?php if (in_array($o['status'], ['pending','preparing','out_for_delivery'], true)): ?>
              <div class="small text-white-50 mt-2">
                Tip: click <strong>Next</strong> to move it forward (no going backwards).
              </div>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- Pagination -->
    <nav class="mt-4" aria-label="Orders pages">
      <ul class="pagination pagination-sm mb-0 justify-content-center">
        <?php
          $cur = (int)($page ?? 1);
          $tp  = (int)($totalPages ?? 1);

          $win = 2;
          $start = max(1, $cur - $win);
          $end   = min($tp, $cur + $win);

          pageBtn('«', 1, false, $cur <= 1);
          pageBtn('‹', max(1, $cur-1), false, $cur <= 1);

          if ($start > 1) {
            pageBtn('1', 1, $cur===1, false);
            if ($start > 2) echo '<li class="page-item disabled"><span class="page-link">…</span></li>';
          }

          for ($p=$start; $p<=$end; $p++) pageBtn((string)$p, $p, $cur===$p, false);

          if ($end < $tp) {
            if ($end < $tp-1) echo '<li class="page-item disabled"><span class="page-link">…</span></li>';
            pageBtn((string)$tp, $tp, $cur===$tp, false);
          }

          pageBtn('›', min($tp, $cur+1), false, $cur >= $tp);
          pageBtn('»', $tp, false, $cur >= $tp);
        ?>
      </ul>
    </nav>
  <?php endif; ?>
</div>
