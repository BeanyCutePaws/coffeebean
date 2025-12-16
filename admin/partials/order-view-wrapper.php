<?php
// admin/partials/order-view-wrapper.php
// expects: $order, $items, $history, $status, $ns

if (!function_exists('fmtDT')) {
  function fmtDT($dt) {
    if (!$dt) return "—";
    $ts = strtotime($dt);
    return $ts ? date("M d, Y • g:i A", $ts) : (string)$dt;
  }
}

// Admin cancel rule:
$canCancel = in_array((string)$status, ['pending', 'preparing'], true);
?>

<div class="row g-4">

  <!-- LEFT: summary + actions -->
  <div class="col-lg-5">
    <div class="panel-card rounded-4 p-4 mb-4">
      <div class="d-flex justify-content-between align-items-start gap-2">
        <div>
          <div class="small text-white-50 mb-1">Tracking Code</div>
          <div class="fw-bold font-monospace fs-5"><?= h($order['order_code']) ?></div>
          <div class="small text-white-50 mt-1">
            Placed: <span class="text-white"><?= h(fmtDT($order['created_at'] ?? '')) ?></span>
          </div>
        </div>

        <div class="text-end">
          <div class="small text-white-50 mb-1">Status</div>
          <span class="badge <?= h(badge($status)) ?>"><?= h(statusNice($status)) ?></span>
        </div>
      </div>

      <hr class="border-secondary my-3">

      <div class="d-flex gap-2 flex-wrap">

        <?php if ($ns): ?>
          <button class="btn btn-primary fw-bold flex-grow-1 js-next-status"
                  data-order-id="<?= (int)$order['order_id'] ?>">
            <i class="fa-solid fa-forward-step me-2"></i>
            Next: <?= h(statusNice($ns)) ?>
          </button>
        <?php else: ?>
          <button class="btn btn-outline-light fw-bold flex-grow-1" disabled>
            No next step
          </button>
        <?php endif; ?>

        <?php if ($canCancel): ?>
          <button class="btn btn-outline-danger fw-bold js-cancel-order"
                  data-order-id="<?= (int)$order['order_id'] ?>">
            <i class="fa-solid fa-ban me-2"></i>Cancel
          </button>
        <?php else: ?>
          <button class="btn btn-outline-danger fw-bold" disabled title="Cancellation not allowed at this status">
            <i class="fa-solid fa-ban me-2"></i>Cancel
          </button>
        <?php endif; ?>

        <a class="btn btn-outline-light"
           href="../track-order.php?order_code=<?= urlencode($order['order_code']) ?>&order_phone=<?= urlencode($order['customer_phone']) ?>">
          <i class="fa-solid fa-up-right-from-square me-2"></i>Track Page
        </a>
      </div>

      <div class="small text-white-50 mt-2">
        Auto-refresh is ON while you stay here.
      </div>
    </div>

    <!-- customer -->
    <div class="panel-card rounded-4 p-4 mb-4">
      <div class="fw-bold mb-2">Customer</div>
      <div class="fw-semibold"><?= h($order['customer_name']) ?></div>
      <div class="text-white-50 small"><?= h($order['customer_phone']) ?></div>

      <hr class="border-secondary my-3">

      <div class="fw-bold mb-1">Delivery Address</div>
      <div class="text-white-50 small"><?= h($order['delivery_address']) ?></div>

      <?php if (!empty($order['notes'])): ?>
        <hr class="border-secondary my-3">
        <div class="fw-bold mb-1">Notes</div>
        <div class="text-white-50 small"><?= h($order['notes']) ?></div>
      <?php endif; ?>
    </div>

    <!-- branch + payment -->
    <div class="panel-card rounded-4 p-4">
      <div class="fw-bold mb-2">Branch</div>
      <div class="fw-semibold"><?= h($order['branch_name'] ?? '—') ?></div>
      <div class="text-white-50 small"><?= h($order['branch_address'] ?? '—') ?></div>

      <hr class="border-secondary my-3">

      <div class="d-flex justify-content-between">
        <div>
          <div class="small text-white-50">Payment</div>
          <div class="fw-semibold">
            <?= h(strtoupper((string)($order['pay_method'] ?? 'cod'))) ?>
            <span class="text-white-50">•</span>
            <?= h(ucfirst((string)($order['pay_status'] ?? 'unpaid'))) ?>
          </div>
          <?php if (!empty($order['pay_ref'])): ?>
            <div class="small text-white-50">Ref: <?= h($order['pay_ref']) ?></div>
          <?php endif; ?>
          <?php if (!empty($order['pay_paid_at'])): ?>
            <div class="small text-white-50">Paid: <?= h(fmtDT($order['pay_paid_at'])) ?></div>
          <?php endif; ?>
        </div>

        <div class="text-end">
          <div class="small text-white-50">Total</div>
          <div class="fw-bold text-success fs-5">₱<?= money($order['total_amount'] ?? 0) ?></div>
          <div class="small text-white-50">Subtotal: ₱<?= money($order['subtotal'] ?? 0) ?></div>
          <div class="small text-white-50">Delivery: ₱<?= money($order['delivery_fee'] ?? 0) ?></div>
        </div>
      </div>
    </div>
  </div>

  <!-- RIGHT: items + history -->
  <div class="col-lg-7">

    <div class="panel-card rounded-4 p-4 mb-4">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <div class="fw-bold">Items</div>
        <div class="text-white-50 small"><?= (int)count($items) ?> line(s)</div>
      </div>

      <?php if (empty($items)): ?>
        <div class="text-white-50">No item details found.</div>
      <?php else: ?>
        <div class="table-responsive">
          <table class="table table-dark table-borderless align-middle mb-0">
            <thead style="opacity:.9;">
              <tr>
                <th>Item</th>
                <th class="text-center">Coffee</th>
                <th class="text-end">Qty</th>
                <th class="text-end">Total</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($items as $it): ?>
                <tr style="border-top:1px solid rgba(255,255,255,.08);">
                  <td class="fw-semibold"><?= h($it['item_name']) ?></td>
                  <td class="text-center small text-white-50">
                    <?= ((int)$it['with_coffee'] === 1) ? "With" : "No" ?>
                  </td>
                  <td class="text-end"><?= (int)$it['qty'] ?></td>
                  <td class="text-end fw-bold">₱<?= money($it['line_total']) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>

    <div class="panel-card rounded-4 p-4">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <div class="fw-bold">Status History</div>
        <div class="text-white-50 small"><?= (int)count($history) ?> record(s)</div>
      </div>

      <?php if (empty($history)): ?>
        <div class="text-white-50">No history yet.</div>
      <?php else: ?>
        <ul class="list-group list-group-flush">
          <?php foreach ($history as $hr): ?>
            <li class="list-group-item bg-transparent text-white d-flex justify-content-between gap-3">
              <div>
                <div class="fw-semibold">
                  <?= h(statusNice($hr['status'])) ?>
                  <?php if (!empty($hr['changed_by_name'])): ?>
                    <span class="text-white-50"> • <?= h($hr['changed_by_name']) ?></span>
                  <?php endif; ?>
                </div>
                <?php if (!empty($hr['note'])): ?>
                  <div class="text-white-50 small"><?= h($hr['note']) ?></div>
                <?php endif; ?>
              </div>
              <div class="text-white-50 small text-end" style="white-space:nowrap;">
                <?= h(date("m-d H:i", strtotime($hr['changed_at']))) ?>
              </div>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </div>

  </div>
</div>
