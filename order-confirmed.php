<?php
// order-confirmed.php (Don Macchiatos)

session_start();
require_once __DIR__ . "/config.php";

function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function money($n) { return number_format((float)$n, 2); }
function fmtDT($dt) {
  if (!$dt) return "—";
  $ts = strtotime($dt);
  return $ts ? date("M d, Y • g:i A", $ts) : h($dt);
}
function statusLabel($s) { return ucwords(str_replace('_',' ', (string)$s)); }
function statusClass($s) {
  return match((string)$s) {
    'pending' => 'text-warning',
    'preparing' => 'text-info',
    'out_for_delivery' => 'text-primary',
    'completed' => 'text-success',
    'cancelled' => 'text-danger',
    default => 'text-warning',
  };
}

$code = trim((string)($_GET['code'] ?? ''));
$error = "";

if ($code === '') {
  http_response_code(400);
  $error = "Missing order code.";
}

$order = null;
$items = [];
$branch = null;
$payment = null;
$history = [];

if ($error === "") {
  // Order
  $stmt = $mysqli->prepare("SELECT * FROM orders WHERE order_code=? LIMIT 1");
  $stmt->bind_param("s", $code);
  $stmt->execute();
  $res = $stmt->get_result();
  $order = $res ? $res->fetch_assoc() : null;
  $stmt->close();

  if (!$order) $error = "We couldn't find that order.";
}

if ($error === "") {
  $bid = (int)$order['branch_id'];
  $oid = (int)$order['order_id'];

  // Branch info (add phone/fb)
  $stmt = $mysqli->prepare("SELECT branch_id, name, address, phone, facebook_url FROM branches WHERE branch_id=? LIMIT 1");
  $stmt->bind_param("i", $bid);
  $stmt->execute();
  $res = $stmt->get_result();
  $branch = $res ? $res->fetch_assoc() : null;
  $stmt->close();

  // Items
  $stmt = $mysqli->prepare("
    SELECT item_name, unit_price, qty, with_coffee, line_total
    FROM order_items
    WHERE order_id=?
    ORDER BY order_item_id ASC
  ");
  $stmt->bind_param("i", $oid);
  $stmt->execute();
  $res = $stmt->get_result();
  while ($res && ($row = $res->fetch_assoc())) $items[] = $row;
  $stmt->close();

  // Payment (add ref/paid_at)
  $stmt = $mysqli->prepare("SELECT method, status, provider_ref, paid_at FROM payments WHERE order_id=? LIMIT 1");
  $stmt->bind_param("i", $oid);
  $stmt->execute();
  $res = $stmt->get_result();
  $payment = $res ? $res->fetch_assoc() : null;
  $stmt->close();

  // Status history (for mini timeline)
  $stmt = $mysqli->prepare("
    SELECT status, note, changed_at
    FROM order_status_history
    WHERE order_id=?
    ORDER BY changed_at ASC
  ");
  $stmt->bind_param("i", $oid);
  $stmt->execute();
  $res = $stmt->get_result();
  while ($res && ($row = $res->fetch_assoc())) $history[] = $row;
  $stmt->close();
}

$page_title = "Order Confirmed";
$active_page = "";
include __DIR__ . "/includes/head.php";
include __DIR__ . "/includes/navbar.php";

$mode = $order['fulfillment_mode'] ?? 'pickup';
$isDelivery = ($mode === 'delivery');
$itemsCount = array_sum(array_map(fn($x) => (int)$x['qty'], $items));
?>

<section class="py-5">
  <div class="container text-white">
    <?php if ($error !== ""): ?>
      <div class="panel-card rounded-4 p-4">
        <h3 class="fw-bold mb-2">Order not available</h3>
        <div class="text-white-50 mb-3"><?= h($error) ?></div>
        <a class="btn btn-primary rounded-pill" href="menu.php">Back to Menu</a>
      </div>
<?php else: ?>

  <div class="row g-4">

    <!-- LEFT -->
    <div class="col-lg-4">
      <div class="panel-card rounded-4 p-4 h-100">

        <!-- top success -->
        <div class="d-flex align-items-center gap-3 mb-3">
          <div class="rounded-circle d-flex align-items-center justify-content-center"
               style="width:46px;height:46px;background:rgba(34,197,94,.18);border:1px solid rgba(34,197,94,.35);">
            <i class="fa-solid fa-check text-success"></i>
          </div>
          <div>
            <div class="fw-bold">Order placed successfully</div>
            <div class="small text-white-50">Save your tracking code.</div>
          </div>
        </div>

        <!-- tracking card -->
        <div class="panel-card rounded-4 p-3 mb-3">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <div class="small text-white-50">Tracking code</div>
              <div class="mt-1 d-flex align-items-center gap-2 flex-wrap">
                <span class="badge bg-dark bg-opacity-75 font-monospace px-3 py-2">
                  <?= h($order['order_code']) ?>
                </span>
                <button class="btn btn-sm btn-outline-light rounded-pill" type="button" id="btnCopyCode">
                  <i class="fa-regular fa-copy me-1"></i>Copy
                </button>
              </div>
            </div>

            <div class="text-end">
              <div class="small text-white-50">Status</div>
              <div class="fw-bold <?= h(statusClass($order['status'] ?? 'pending')) ?>">
                <?= h(statusLabel($order['status'] ?? 'pending')) ?>
              </div>
            </div>
          </div>

          <hr class="border-secondary my-3">

          <!-- meta chips -->
          <div class="d-flex gap-2 flex-wrap">
            <span class="badge bg-dark bg-opacity-50">
              <i class="fa-regular fa-clock me-1"></i><?= h(fmtDT($order['created_at'] ?? '')) ?>
            </span>
            <span class="badge bg-dark bg-opacity-50">
              <i class="fa-solid fa-bag-shopping me-1"></i><?= (int)$itemsCount ?> item(s)
            </span>
            <span class="badge bg-dark bg-opacity-50">
              <i class="fa-solid fa-truck me-1"></i><?= $isDelivery ? "Delivery" : "Pickup" ?>
            </span>
          </div>
        </div>

        <!-- fulfillment + branch card -->
        <div class="panel-card rounded-4 p-3 mb-3">
          <div class="small text-white-50 mb-1">Fulfillment</div>
          <div class="fw-bold mb-2"><?= $isDelivery ? "Delivery" : "Pickup" ?></div>

          <div class="small text-white-50 mb-1">Branch</div>
          <div class="fw-bold"><?= h($branch['name'] ?? 'Selected branch') ?></div>
          <div class="small text-white-50"><?= h($branch['address'] ?? '—') ?></div>

          <?php if (!empty($branch['phone'])): ?>
            <div class="small text-white-50 mt-2">
              <i class="fa-solid fa-phone me-1"></i><?= h($branch['phone']) ?>
            </div>
          <?php endif; ?>
        </div>

        <div class="d-grid gap-2">
          <a class="btn btn-primary fw-bold" href="menu.php">
            <i class="fa-solid fa-rotate-left me-2"></i>Order Again
          </a>

          <a class="btn btn-outline-light"
            href="track-order.php?code=<?= urlencode($order['order_code']) ?>&order_phone=<?= urlencode($order['customer_phone']) ?>">
            Track Order
          </a>


          <?php if (!empty($branch['facebook_url'])): ?>
            <a class="btn btn-outline-light" target="_blank" rel="noopener" href="<?= h($branch['facebook_url']) ?>">
              <i class="fa-brands fa-facebook-messenger me-2"></i>Message Branch
            </a>
          <?php endif; ?>
        </div>

      </div>
    </div>

    <!-- RIGHT -->
    <div class="col-lg-8">
      <div class="panel-card rounded-4 p-4">

        <!-- header -->
        <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
          <div>
            <h3 class="fw-bold mb-1">Receipt</h3>
            <div class="text-white-50 small">
              <?= $isDelivery ? "Delivery Order" : "Pickup Order" ?>
            </div>
          </div>
          <div class="text-end">
            <span class="badge bg-dark bg-opacity-50 px-3 py-2">
              <i class="fa-solid fa-receipt me-2"></i><?= h($order['order_code']) ?>
            </span>
          </div>
        </div>

        <!-- info cards row -->
        <div class="row g-3 mb-3">

          <!-- payment card -->
          <div class="col-md-6">
            <div class="panel-card rounded-4 p-3 h-100">
              <div class="d-flex justify-content-between align-items-start">
                <div>
                  <div class="small text-white-50">Payment</div>
                  <div class="fw-bold">
                    <?= h(strtoupper($payment['method'] ?? 'cod')) ?>
                    <span class="text-white-50">•</span>
                    <?= h(ucfirst($payment['status'] ?? 'unpaid')) ?>
                  </div>
                </div>
                <div class="text-white-50">
                  <i class="fa-solid fa-credit-card"></i>
                </div>
              </div>

              <?php if (!empty($payment['provider_ref'])): ?>
                <div class="small text-white-50 mt-2">Ref: <?= h($payment['provider_ref']) ?></div>
              <?php endif; ?>

              <?php if (!empty($payment['paid_at'])): ?>
                <div class="small text-white-50">Paid: <?= h(fmtDT($payment['paid_at'])) ?></div>
              <?php endif; ?>
            </div>
          </div>

          <!-- customer card -->
          <div class="col-md-6">
            <div class="panel-card rounded-4 p-3 h-100">
              <div class="d-flex justify-content-between align-items-start">
                <div>
                  <div class="small text-white-50">Customer</div>
                  <div class="fw-bold"><?= h($order['customer_name']) ?></div>
                  <div class="text-white-50 small"><?= h($order['customer_phone']) ?></div>
                </div>
                <div class="text-white-50">
                  <i class="fa-solid fa-user"></i>
                </div>
              </div>
            </div>
          </div>

          <!-- address card full -->
          <div class="col-12">
            <div class="panel-card rounded-4 p-3">
              <div class="d-flex justify-content-between align-items-start">
                <div>
                  <div class="small text-white-50">
                    <?= $isDelivery ? "Delivery Address" : "Pickup Location" ?>
                  </div>
                  <div class="fw-bold">
                    <?= $isDelivery ? h($order['delivery_address']) : h($branch['address'] ?? '—') ?>
                  </div>

                  <?php if (!empty($order['notes'])): ?>
                    <div class="text-white-50 small mt-1">
                      <i class="fa-regular fa-note-sticky me-1"></i>Notes: <?= h($order['notes']) ?>
                    </div>
                  <?php endif; ?>
                </div>
                <div class="text-white-50">
                  <i class="fa-solid fa-location-dot"></i>
                </div>
              </div>
            </div>
          </div>

        </div>

        <!-- items table in its own card -->
        <div class="panel-card rounded-4 p-3">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="fw-bold">Items</div>
            <div class="small text-white-50"><?= (int)$itemsCount ?> item(s)</div>
          </div>

          <div class="table-responsive">
            <table class="table table-dark table-borderless align-middle mb-0">
              <thead style="opacity:.9;">
                <tr>
                  <th>Item</th>
                  <th class="text-center">Coffee</th>
                  <th class="text-end">Qty</th>
                  <th class="text-end">Unit</th>
                  <th class="text-end">Total</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($items as $it): ?>
                  <tr style="border-top:1px solid rgba(255,255,255,.08);">
                    <td class="fw-semibold"><?= h($it['item_name']) ?></td>
                    <td class="text-center small text-white-50">
                      <?= ((int)$it['with_coffee'] === 1) ? "With coffee" : "No coffee" ?>
                    </td>
                    <td class="text-end"><?= (int)$it['qty'] ?></td>
                    <td class="text-end">₱<?= money($it['unit_price']) ?></td>
                    <td class="text-end fw-bold">₱<?= money($it['line_total']) ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>

        <!-- totals in its own card -->
        <div class="d-flex justify-content-end mt-3">
          <div class="panel-card rounded-4 p-3" style="min-width:300px;">
            <div class="d-flex justify-content-between text-white-50">
              <span>Subtotal</span>
              <span>₱<?= money($order['subtotal']) ?></span>
            </div>
            <div class="d-flex justify-content-between text-white-50">
              <span>Delivery fee</span>
              <span>₱<?= money($order['delivery_fee']) ?></span>
            </div>
            <hr class="border-secondary my-2">
            <div class="d-flex justify-content-between fw-bold" style="font-size:1.05rem;">
              <span>Total</span>
              <span class="text-success">₱<?= money($order['total_amount']) ?></span>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>

  <script>
    // Clear client cart after success
    try { sessionStorage.removeItem("dm_cart_v1"); } catch(e) {}

    // Copy code
    document.getElementById("btnCopyCode")?.addEventListener("click", async () => {
      const code = <?= json_encode($order['order_code']) ?>;
      try {
        await navigator.clipboard.writeText(code);
        const btn = document.getElementById("btnCopyCode");
        if (btn) { btn.innerHTML = '<i class="fa-solid fa-check me-1"></i>Copied'; setTimeout(()=>btn.innerHTML='<i class="fa-regular fa-copy me-1"></i>Copy', 900); }
      } catch(e) {}
    });
  </script>

<?php endif; ?>

  </div>
</section>

<?php include __DIR__ . "/includes/footer.php"; ?>
