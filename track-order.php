<?php
// track-order.php (Don Macchiatos)
session_start();
require_once __DIR__ . "/config.php";

function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function money($n) { return number_format((float)$n, 2); }
function fmtDT($dt) {
  if (!$dt) return "—";
  $ts = strtotime($dt);
  return $ts ? date("M d, Y • g:i A", $ts) : h($dt);
}
function phoneDigits($s) { return preg_replace('/\D+/', '', (string)$s); }

function statusLabel($s) {
  return match((string)$s) {
    'pending' => 'Pending',
    'preparing' => 'Preparing',
    'out_for_delivery' => 'Out for Delivery / Ready for Pickup',
    'completed' => 'Completed',
    'cancelled' => 'Cancelled',
    default => ucwords(str_replace('_',' ', (string)$s)),
  };
}
function statusBadgeClass($s) {
  return match((string)$s) {
    'completed' => 'bg-success',
    'out_for_delivery' => 'bg-primary',
    'preparing' => 'bg-warning text-dark',
    'cancelled' => 'bg-danger',
    'pending' => 'bg-secondary',
    default => 'bg-secondary',
  };
}

$statusOrder = [
  'pending' => 0,
  'preparing' => 1,
  'out_for_delivery' => 2,
  'completed' => 3,
  'cancelled' => -1
];

$steps = [
  ['key' => 'pending',          'icon' => 'fa-receipt',     'label' => 'Order Placed'],
  ['key' => 'preparing',        'icon' => 'fa-fire-burner', 'label' => 'Preparing'],
  ['key' => 'out_for_delivery', 'icon' => 'fa-motorcycle',  'label' => 'On the Way / Ready'],
  ['key' => 'completed',        'icon' => 'fa-circle-check','label' => 'Completed'],
];

// ---------------------- inputs ----------------------
$orderCodeInput  = trim((string)($_GET['order_code'] ?? $_GET['code'] ?? ''));
$orderPhoneInput = trim((string)($_GET['order_phone'] ?? ''));

$hasSearched = ($orderCodeInput !== '' || $orderPhoneInput !== '');

$order   = null;
$items   = [];
$branch  = null;
$history = [];
$payment = null;

$errorMsg  = "";
$cancelMsg = "";
$cancelErr = "";

// Helper for phone match (works on MySQL 5/8)
function phoneMatchSql(): string {
  return "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(customer_phone,' ',''),'-',''),'+',''),'(',''),')','')";
}

// ---------------------- cancel (OTP gated) ----------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_action'])) {
  $code    = trim((string)($_POST['cancel_order_code'] ?? ''));
  $phoneIn = (string)($_POST['cancel_phone'] ?? '');
  $phone   = phoneDigits($phoneIn);
  $confirm = (string)($_POST['confirm'] ?? '');
  $emailIn = trim((string)($_POST['cancel_email'] ?? ''));

  if ($confirm !== 'yes') {
    $cancelErr = "Cancellation not confirmed.";
  } elseif ($emailIn === '' || !filter_var($emailIn, FILTER_VALIDATE_EMAIL)) {
    $cancelErr = "Please enter a valid email to receive the cancellation code.";
  } else {
    // Require OTP verified in session (set by api/otp/verify-email-otp.php)
    $otpOk   = !empty($_SESSION['otp_verified']);
    $otpMail = (string)($_SESSION['otp_email'] ?? '');

    if (!$otpOk || strcasecmp($otpMail, $emailIn) !== 0) {
      $cancelErr = "Please verify the OTP code first (email must match).";
    } else {
      // lookup order again (avoid tampering)
      $sql = "SELECT * FROM orders WHERE order_code = ? AND " . phoneMatchSql() . " = ? LIMIT 1";
      $stmt = $mysqli->prepare($sql);

      if (!$stmt) {
        $cancelErr = "DB error. Please try again.";
      } else {
        $stmt->bind_param("ss", $code, $phone);
        $stmt->execute();
        $res = $stmt->get_result();
        $found = $res ? $res->fetch_assoc() : null;
        $stmt->close();

        if (!$found) {
          $cancelErr = "Order not found or phone does not match.";
        } else {
          $oid = (int)$found['order_id'];
          $cur = (string)$found['status'];

          if (!in_array($cur, ['pending'], true)) {
            $cancelErr = "This order can no longer be cancelled.";
          } else {
            $mysqli->begin_transaction();
            try {
              $stmt = $mysqli->prepare("UPDATE orders SET status='cancelled', cancelled_at=NOW() WHERE order_id=? LIMIT 1");
              $stmt->bind_param("i", $oid);
              $stmt->execute();
              $stmt->close();

              $note = "Cancelled by customer (OTP verified)";
              $stmt = $mysqli->prepare("INSERT INTO order_status_history (order_id, status, note, changed_by) VALUES (?, 'cancelled', ?, NULL)");
              $stmt->bind_param("is", $oid, $note);
              $stmt->execute();
              $stmt->close();

              $mysqli->commit();
              $cancelMsg = "Your order has been cancelled.";

              // consume OTP session so it can’t be reused
              unset($_SESSION['otp_verified'], $_SESSION['otp_code'], $_SESSION['otp_expires']);
            } catch (Throwable $e) {
              $mysqli->rollback();
              $cancelErr = "We couldn't cancel your order right now.";
            }
          }
        }
      }
    }
  }

  // keep fields after POST
  $orderCodeInput  = $code ?: $orderCodeInput;
  $orderPhoneInput = $orderPhoneInput ?: $phoneIn;
  $hasSearched = true;
}

// ---------------------- search (GET) ----------------------
if ($hasSearched) {
  $code  = $orderCodeInput;
  $phone = phoneDigits($orderPhoneInput);

  if ($code === '' && $phone === '') {
    $errorMsg = "Please enter your tracking code and mobile number.";
  } elseif ($code === '' || $phone === '') {
    $errorMsg = "Please enter both tracking code and mobile number.";
  } else {
    $sql = "SELECT * FROM orders WHERE order_code = ? AND " . phoneMatchSql() . " = ? LIMIT 1";
    $stmt = $mysqli->prepare($sql);

    if ($stmt) {
      $stmt->bind_param("ss", $code, $phone);
      $stmt->execute();
      $res = $stmt->get_result();
      $order = $res ? $res->fetch_assoc() : null;
      $stmt->close();
    }

    if (!$order) {
      $errorMsg = "We couldn't find an order matching that code and mobile number.";
    }
  }
}

// ---------------------- load details if found ----------------------
if ($order) {
  $oid = (int)$order['order_id'];
  $bid = (int)$order['branch_id'];

  // items
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

  // branch
  $stmt = $mysqli->prepare("SELECT name, address, phone, facebook_url FROM branches WHERE branch_id=? LIMIT 1");
  $stmt->bind_param("i", $bid);
  $stmt->execute();
  $res = $stmt->get_result();
  $branch = $res ? $res->fetch_assoc() : null;
  $stmt->close();

  // payment
  $stmt = $mysqli->prepare("SELECT method, status, provider_ref, paid_at FROM payments WHERE order_id=? LIMIT 1");
  $stmt->bind_param("i", $oid);
  $stmt->execute();
  $res = $stmt->get_result();
  $payment = $res ? $res->fetch_assoc() : null;
  $stmt->close();

  // history
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

$page_title = "Track Order";
$active_page = "track-order";
include __DIR__ . "/includes/head.php";
include __DIR__ . "/includes/navbar.php";

$status = $order['status'] ?? '';
$stepIdx = $statusOrder[$status] ?? -1;
$progressPercent = match(true) {
  $stepIdx <= 0 => 20,
  $stepIdx === 1 => 50,
  $stepIdx === 2 => 80,
  $stepIdx >= 3 => 100,
  default => 0
};

$mode = $order['fulfillment_mode'] ?? '';
$isDelivery = ($mode === 'delivery');

// If your orders table has customer_email later, this will auto-fill; else it stays blank.
$prefillEmail = '';
if ($order && isset($order['customer_email'])) {
  $prefillEmail = trim((string)$order['customer_email']);
}
?>

<section class="py-5">
  <div class="container text-white">
    <div class="row g-4">

      <!-- LEFT -->
      <div class="col-lg-4">
        <div class="panel-card rounded-4 p-4">
          <h4 class="fw-bold mb-1">Track Order</h4>
          <div class="text-white-50 small mb-3">Enter your tracking code and mobile number.</div>

          <form method="get" action="track-order.php">
            <div class="mb-3">
              <label class="form-label small text-white-50">Tracking Code</label>
              <input type="text" class="form-control" name="order_code"
                     placeholder="DM-000001"
                     value="<?= h($orderCodeInput) ?>" required>
            </div>

            <div class="mb-3">
              <label class="form-label small text-white-50">Mobile Number</label>
              <input type="tel" class="form-control" name="order_phone"
                     placeholder="09XX XXX XXXX"
                     value="<?= h($orderPhoneInput) ?>" required>
            </div>

            <?php if ($hasSearched && $errorMsg): ?>
              <div class="alert alert-danger py-2 small mb-3">
                <i class="fa-solid fa-circle-exclamation me-1"></i><?= h($errorMsg) ?>
              </div>
            <?php endif; ?>

            <div class="d-grid">
              <button class="btn btn-primary fw-bold" type="submit">
                <i class="fa-solid fa-location-crosshairs me-2"></i>Track
              </button>
            </div>
          </form>

          <div class="panel-card rounded-4 p-3 mt-3">
            <div class="small text-white-50">
              <i class="fa-solid fa-shield-halved me-1"></i>
              For privacy, we only show orders when the code and phone match.
            </div>
          </div>
        </div>
      </div>

      <!-- RIGHT -->
      <div class="col-lg-8">
        <div id="js-track-status-wrapper">

          <?php if ($order): ?>
            <?php $itemsCount = array_sum(array_map(fn($x) => (int)$x['qty'], $items)); ?>

            <div class="panel-card rounded-4 p-4">

              <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
                <div>
                  <div class="small text-white-50 mb-1">Tracking Code</div>
                  <div class="fw-bold font-monospace"><?= h($order['order_code']) ?></div>
                  <div class="small text-white-50 mt-1">
                    Placed: <span class="text-white"><?= h(fmtDT($order['created_at'] ?? '')) ?></span>
                    <span class="text-white-50">•</span>
                    Items: <span class="text-white"><?= (int)$itemsCount ?></span>
                  </div>
                </div>

                <div class="text-end">
                  <div class="small text-white-50 mb-1">Current Status</div>
                  <span class="badge <?= h(statusBadgeClass($status)) ?>">
                    <?= h(statusLabel($status)) ?>
                  </span>
                </div>
              </div>

              <?php if ($cancelErr): ?>
                <div class="alert alert-danger py-2 small mb-3">
                  <i class="fa-solid fa-circle-exclamation me-1"></i><?= h($cancelErr) ?>
                </div>
              <?php endif; ?>

              <?php if ($cancelMsg): ?>
                <div class="alert alert-success py-2 small mb-3">
                  <i class="fa-solid fa-circle-check me-1"></i><?= h($cancelMsg) ?>
                </div>
              <?php endif; ?>

              <?php if ($status === 'cancelled'): ?>
                <div class="alert alert-danger py-2 small mb-3">
                  <i class="fa-solid fa-circle-xmark me-1"></i>
                  This order has been cancelled.
                </div>
              <?php else: ?>

                <div class="panel-card rounded-4 p-3 mb-3">
                  <div class="d-flex justify-content-between text-center gap-2 flex-wrap">
                    <?php foreach ($steps as $i => $st):
                      $active = ($stepIdx >= $i);
                      $badge = $active ? 'bg-success' : 'bg-dark bg-opacity-50';
                    ?>
                      <div class="flex-fill" style="min-width:140px;">
                        <div class="badge rounded-pill <?= $badge ?> px-3 py-2">
                          <i class="fa-solid <?= h($st['icon']) ?>"></i>
                        </div>
                        <div class="small mt-2 fw-semibold"><?= h($st['label']) ?></div>
                        <div class="small text-white-50"><?= $active ? 'Done' : 'Pending' ?></div>
                      </div>
                    <?php endforeach; ?>
                  </div>

                  <div class="progress mt-3" style="height: 6px;">
                    <div class="progress-bar bg-success" style="width: <?= (int)$progressPercent ?>%;"></div>
                  </div>

                  <div class="small text-white-50 mt-2">
                    This panel auto-refreshes while you’re on this page.
                  </div>
                </div>

                <div class="panel-card rounded-4 p-3 mb-3">
                  <div class="fw-bold mb-1">Cancellation (OTP)</div>
                  <div class="small text-white-50">
                    To cancel, we’ll email you a 6-digit confirmation code.
                  </div>

                  <?php if (in_array($status, ['pending'], true)): ?>
                    <hr class="border-secondary my-3">

                    <div class="mb-2">
                      <label class="form-label small text-white-50">Email for OTP</label>
                      <input type="email" class="form-control" id="cancel_email" value="<?= h($prefillEmail) ?>" placeholder="you@email.com">
                      <div class="small text-white-50 mt-2" id="cancelOtpStatus"></div>
                    </div>

                    <div class="d-flex gap-2 flex-wrap">
                      <button type="button" class="btn btn-outline-light btn-sm" id="btnSendCancelOtp">
                        <i class="fa-solid fa-envelope me-2"></i>Send code
                      </button>
                    </div>

                    <div class="mt-3 d-none" id="cancelVerifyWrap">
                      <label class="form-label small text-white-50">6-digit code</label>
                      <div class="d-flex gap-2">
                        <input type="text" class="form-control text-center fw-bold" id="cancel_otp_code"
                               maxlength="6" inputmode="numeric" placeholder="______">
                        <button type="button" class="btn btn-primary btn-sm" id="btnVerifyCancelOtp">Verify</button>
                      </div>
                      <div class="small mt-2" id="cancelVerifyStatus"></div>
                    </div>

                    <form method="post" class="mt-3"
                          action="track-order.php?order_code=<?= urlencode($orderCodeInput) ?>&order_phone=<?= urlencode($orderPhoneInput) ?>">
                      <input type="hidden" name="cancel_action" value="1">
                      <input type="hidden" name="cancel_order_code" value="<?= h($order['order_code']) ?>">
                      <input type="hidden" name="cancel_phone" value="<?= h($orderPhoneInput) ?>">
                      <input type="hidden" name="confirm" value="yes">
                      <input type="hidden" name="cancel_email" id="h_cancel_email" value="">
                      <button type="submit" class="btn btn-outline-danger" id="btnCancelOrderFinal" disabled>
                        <i class="fa-solid fa-ban me-2"></i>Cancel Order
                      </button>
                      <div class="small text-white-50 mt-2">
                        Button unlocks only after OTP verification.
                      </div>
                    </form>

                  <?php else: ?>
                    <div class="small text-white-50 mt-2">
                      Cancellation is only available while the order is still <strong>Pending</strong>.
                    </div>
                  <?php endif; ?>
                </div>

              <?php endif; ?>

              <div class="row g-3">
                <div class="col-md-6">
                  <div class="panel-card rounded-4 p-3 h-100">
                    <div class="small text-white-50">Customer</div>
                    <div class="fw-bold"><?= h($order['customer_name']) ?></div>
                    <div class="text-white-50 small"><?= h($order['customer_phone']) ?></div>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="panel-card rounded-4 p-3 h-100">
                    <div class="small text-white-50">Fulfillment</div>
                    <div class="fw-bold"><?= $isDelivery ? "Delivery" : "Pickup" ?></div>
                    <div class="text-white-50 small">
                      <?= $isDelivery ? h($order['delivery_address'] ?? '—') : h($branch['address'] ?? '—') ?>
                    </div>
                  </div>
                </div>

                <div class="col-12">
                  <div class="panel-card rounded-4 p-3">
                    <div class="d-flex justify-content-between align-items-start gap-3">
                      <div>
                        <div class="small text-white-50">Branch</div>
                        <div class="fw-bold"><?= h($branch['name'] ?? '—') ?></div>
                        <div class="small text-white-50"><?= h($branch['address'] ?? '—') ?></div>
                      </div>
                      <div class="text-end">
                        <div class="small text-white-50">Total</div>
                        <div class="fw-bold text-success">₱<?= money($order['total_amount'] ?? 0) ?></div>
                        <div class="small text-white-50">
                          <?= h(strtoupper($payment['method'] ?? 'cod')) ?> • <?= h(ucfirst($payment['status'] ?? 'unpaid')) ?>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="panel-card rounded-4 p-3 mt-3">
                <div class="fw-bold mb-2">Items</div>
                <?php if (!empty($items)): ?>
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
                            <td class="text-center small text-white-50"><?= ((int)$it['with_coffee'] === 1) ? "With" : "No" ?></td>
                            <td class="text-end"><?= (int)$it['qty'] ?></td>
                            <td class="text-end fw-bold">₱<?= money($it['line_total']) ?></td>
                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                    </table>
                  </div>
                <?php else: ?>
                  <div class="text-white-50 small">No item details found.</div>
                <?php endif; ?>
              </div>

              <?php if (!empty($history)): ?>
                <div class="panel-card rounded-4 p-3 mt-3">
                  <div class="fw-bold mb-2">Status History</div>
                  <ul class="list-group list-group-flush">
                    <?php foreach ($history as $hr): ?>
                      <li class="list-group-item bg-transparent text-white d-flex justify-content-between">
                        <span>
                          <?= h(statusLabel($hr['status'])) ?>
                          <?php if (!empty($hr['note'])): ?>
                            <span class="text-white-50"> • <?= h($hr['note']) ?></span>
                          <?php endif; ?>
                        </span>
                        <span class="text-white-50 small"><?= h(date("m-d H:i", strtotime($hr['changed_at']))) ?></span>
                      </li>
                    <?php endforeach; ?>
                  </ul>
                </div>
              <?php endif; ?>

            </div>

          <?php elseif (!$hasSearched): ?>
            <div class="panel-card rounded-4 p-4">
              <h5 class="fw-bold mb-2">Waiting for a tracking request</h5>
              <div class="text-white-50">Enter your tracking code + the same phone number you used during checkout.</div>
            </div>
          <?php else: ?>
            <div class="panel-card rounded-4 p-4">
              <h5 class="fw-bold mb-2">No matching order found</h5>
              <div class="text-white-50">Double-check your code and phone number.</div>
            </div>
          <?php endif; ?>

        </div>
      </div>

    </div>
  </div>
</section>

<!-- Track page JS (moved out) -->
<script src="assets/js/track/track.page.js" defer></script>

<?php include __DIR__ . "/includes/footer.php"; ?>
