<?php
// order-confirmed.php (Don Macchiatos)
// Displays a completed order by order_code.

session_start();
require_once __DIR__ . "/config.php";

function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function money($n) { return number_format((float)$n, 2); }

$code = trim((string)($_GET['code'] ?? ''));
if ($code === '') {
  http_response_code(400);
  $error = "Missing order code.";
}

$order = null;
$items = [];
$branch = null;
$payment = null;

if (empty($error)) {
  // Order
  $stmt = $mysqli->prepare("SELECT * FROM orders WHERE order_code=? LIMIT 1");
  $stmt->bind_param("s", $code);
  $stmt->execute();
  $res = $stmt->get_result();
  $order = $res ? $res->fetch_assoc() : null;
  $stmt->close();

  if (!$order) {
    $error = "We couldn't find that order.";
  }
}

if (empty($error)) {
  // Branch info
  $stmt = $mysqli->prepare("SELECT branch_id, name, address FROM branches WHERE branch_id=? LIMIT 1");
  $bid = (int)$order['branch_id'];
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
  $oid = (int)$order['order_id'];
  $stmt->bind_param("i", $oid);
  $stmt->execute();
  $res = $stmt->get_result();
  while ($res && ($row = $res->fetch_assoc())) $items[] = $row;
  $stmt->close();

  // Payment
  $stmt = $mysqli->prepare("SELECT method, status FROM payments WHERE order_id=? LIMIT 1");
  $stmt->bind_param("i", $oid);
  $stmt->execute();
  $res = $stmt->get_result();
  $payment = $res ? $res->fetch_assoc() : null;
  $stmt->close();
}

$page_title = "Order Confirmed";
$active_page = "";
include __DIR__ . "/includes/head.php";
include __DIR__ . "/includes/navbar.php";
?>

<section class="py-5">
  <div class="container text-white">
    <?php if (!empty($error)): ?>
      <div class="panel-card rounded-4 p-4">
        <h3 class="fw-bold mb-2">Order not available</h3>
        <div class="text-white-50 mb-3"><?= h($error) ?></div>
        <a class="btn btn-primary rounded-pill" href="menu.php">Back to Menu</a>
      </div>
    <?php else: ?>

      <div class="row g-4">
        <div class="col-lg-4">
          <div class="panel-card rounded-4 p-4 h-100">
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

            <div class="small text-white-50">Tracking code</div>
            <div class="d-flex align-items-center gap-2 mt-1 mb-3">
              <span class="badge bg-dark bg-opacity-75 font-monospace px-3 py-2"><?= h($order['order_code']) ?></span>
              <button class="btn btn-sm btn-outline-light rounded-pill" type="button" id="btnCopyCode">
                Copy
              </button>
            </div>

            <div class="small text-white-50 mb-1">Branch</div>
            <div class="fw-bold"><?= h($branch['name'] ?? 'Selected branch') ?></div>
            <div class="small text-white-50 mb-3"><?= h($branch['address'] ?? '—') ?></div>

            <div class="small text-white-50 mb-1">Status</div>
            <div class="fw-bold text-warning"><?= h(ucwords(str_replace('_',' ', $order['status'] ?? 'pending'))) ?></div>

            <hr class="border-secondary my-4">

            <div class="d-grid gap-2">
              <a class="btn btn-primary fw-bold" href="menu.php">Back to Menu</a>
              <!-- we’ll wire track-order later -->
              <button class="btn btn-outline-light" type="button" disabled
                      title="Track page will be added next">
                Track Order (soon)
              </button>
            </div>
          </div>
        </div>

        <div class="col-lg-8">
          <div class="panel-card rounded-4 p-4">
            <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
              <div>
                <h3 class="fw-bold mb-1">Receipt</h3>
                <div class="text-white-50 small">
                  Placed: <?= h($order['created_at'] ?? '') ?>
                </div>
              </div>
              <div class="text-end">
                <div class="small text-white-50">Payment</div>
                <div class="fw-bold">
                  <?= h(strtoupper($payment['method'] ?? 'cod')) ?>
                  <span class="text-white-50">•</span>
                  <?= h(ucfirst($payment['status'] ?? 'unpaid')) ?>
                </div>
              </div>
            </div>

            <div class="row g-3 mb-3">
              <div class="col-md-6">
                <div class="small text-white-50">Customer</div>
                <div class="fw-bold"><?= h($order['customer_name']) ?></div>
                <div class="text-white-50 small"><?= h($order['customer_phone']) ?></div>
              </div>

              <div class="col-md-6">
                <div class="small text-white-50">Address / Fulfillment</div>
                <div class="fw-bold"><?= h($order['delivery_address']) ?></div>
                <?php if (!empty($order['notes'])): ?>
                  <div class="text-white-50 small mt-1">Notes: <?= h($order['notes']) ?></div>
                <?php endif; ?>
              </div>
            </div>

            <hr class="border-secondary my-3">

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

            <hr class="border-secondary my-3">

            <div class="d-flex justify-content-end">
              <div style="min-width:280px;">
                <div class="d-flex justify-content-between text-white-50">
                  <span>Subtotal</span>
                  <span>₱<?= money($order['subtotal']) ?></span>
                </div>
                <div class="d-flex justify-content-between text-white-50">
                  <span>Delivery fee</span>
                  <span>₱<?= money($order['delivery_fee']) ?></span>
                </div>
                <div class="d-flex justify-content-between fw-bold mt-2" style="font-size:1.05rem;">
                  <span>Total</span>
                  <span class="text-success">₱<?= money($order['total_amount']) ?></span>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>

      <script>
        // Clear client cart after success (safe)
        try { sessionStorage.removeItem("dm_cart_v1"); } catch(e) {}
        // Copy code
        document.getElementById("btnCopyCode")?.addEventListener("click", async () => {
          const code = <?= json_encode($order['order_code']) ?>;
          try { await navigator.clipboard.writeText(code); } catch(e) {}
        });
      </script>

    <?php endif; ?>
  </div>
</section>

<?php include __DIR__ . "/includes/footer.php"; ?>
