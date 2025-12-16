<?php
// checkout.php — Don Macchiatos

session_start();
$page_title  = "Checkout";
$active_page = "menu";

require_once __DIR__ . "/config.php";
require_once __DIR__ . "/config/keys.php";

include __DIR__ . "/includes/head.php";
include __DIR__ . "/includes/navbar.php";

// Load branches for display (branch name/address on checkout)
$branches = [];
$res = $mysqli->query("SELECT branch_id, name, address, lat, lng, is_active FROM branches WHERE is_active=1");
if ($res) { while ($row = $res->fetch_assoc()) $branches[] = $row; $res->free(); }
?>

<section class="py-5" style="min-height: 70vh;">
  <div class="container text-white">

    <div class="mb-4">
      <h1 class="fw-bold mb-1">Checkout</h1>
      <div class="text-white-50">
        Confirm your details and choose payment — your cart comes from the Menu page.
      </div>
    </div>

    <div id="checkout-root" class="row g-4">

      <!-- LEFT: Details -->
      <div class="col-lg-7">
        <div class="panel-card rounded-4 p-4">
          <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
            <div>
              <h4 class="fw-bold mb-1">Your details</h4>
              <div class="text-white-50 small">Required fields first, then payment.</div>
            </div>
            <a href="menu.php" class="btn btn-outline-light btn-sm rounded-pill">
              <i class="fa-solid fa-pen-to-square me-2"></i>Edit order
            </a>
          </div>

          <div id="checkoutGateBanner" class="alert alert-warning bg-warning bg-opacity-10 border-warning text-warning d-none">
            Missing required info. Redirecting back to Menu…
          </div>

          <form id="checkoutForm" action="actions/place-order.php" method="post" novalidate>
            <!-- Filled by assets/js/checkout/checkout.page.js -->
            <input type="hidden" name="branch_id"     id="h_branch_id">
            <input type="hidden" name="order_mode"    id="h_order_mode"> <!-- pickup|delivery -->
            <input type="hidden" name="customer_lat"  id="h_customer_lat">
            <input type="hidden" name="customer_lng"  id="h_customer_lng">
            <input type="hidden" name="cart_json"     id="h_cart_json">
            <input type="hidden" name="subtotal"      id="h_subtotal">
            <input type="hidden" name="delivery_fee"  id="h_delivery_fee" value="0">
            <input type="hidden" name="total_amount"  id="h_total_amount">

            <!-- OTP gate (COD only) -->
            <input type="hidden" name="customer_email" id="h_customer_email" value="">
            <input type="hidden" name="otp_verified"   id="h_otp_verified" value="0">

            <!-- Branch / Mode summary -->
            <div class="rounded-4 p-3 mb-4" style="background: rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.12);">
              <div class="small text-white-50">Branch</div>
              <div class="fw-bold" id="ui_branch_name">—</div>
              <div class="small text-white-50" id="ui_branch_addr">—</div>

              <div class="mt-3 d-flex flex-wrap gap-2">
                <span class="badge bg-dark bg-opacity-75">Mode: <span id="ui_mode">—</span></span>
                <span class="badge bg-dark bg-opacity-75">Pinned location: <span id="ui_loc">—</span></span>
              </div>
            </div>

            <!-- Customer info -->
            <div class="row g-3">
              <div class="col-md-7">
                <label class="form-label">Full name</label>
                <input class="form-control" name="customer_name" id="customer_name"
                       required maxlength="120" placeholder="Juan Dela Cruz">
                <div class="invalid-feedback">Please enter your name.</div>
              </div>
              <div class="col-md-5">
                <label class="form-label">Mobile number</label>
                <input class="form-control" name="customer_phone" id="customer_phone"
                       required maxlength="30" placeholder="09xxxxxxxxx">
                <div class="invalid-feedback">Please enter your mobile number.</div>
              </div>
            </div>

            <!-- Delivery fields (shown only when mode=delivery) -->
            <div id="deliveryFields" class="mt-4 d-none">
              <h5 class="fw-bold mb-2">Delivery details</h5>
              <div class="text-white-50 small mb-3">
                You pinned your location — now add a readable address/instructions for the rider.
              </div>

              <div class="mb-3">
                <label class="form-label">Full delivery address</label>
                <textarea class="form-control" name="delivery_address" id="delivery_address"
                          rows="3" placeholder="House no., street, barangay, city…"></textarea>
                <div class="invalid-feedback">Delivery address is required for delivery orders.</div>
              </div>

              <div class="mb-3">
                <label class="form-label">Landmark / notes for rider <span class="text-white-50">(optional)</span></label>
                <input class="form-control" name="delivery_landmark" id="delivery_landmark"
                       placeholder="Near gate, blue roof, call upon arrival…">
              </div>
            </div>

            <!-- Pickup note (shown when mode=pickup) -->
            <div id="pickupFields" class="mt-4 d-none">
              <h5 class="fw-bold mb-2">Pickup</h5>
              <div class="text-white-50 small">
                You’ll pick up your order at the selected branch. No delivery address needed.
              </div>
            </div>

            <!-- Payment -->
            <div class="mt-4">
              <h5 class="fw-bold mb-2">Payment</h5>
              <div class="text-white-50 small mb-3">Choose one payment method.</div>

              <div class="d-grid gap-2">
                <label class="pay-card">
                  <input type="radio" name="payment_method" value="cod" checked>
                  <div>
                    <div class="fw-bold">Cash</div>
                    <div class="small text-white-50">Pay at pickup or upon delivery.</div>
                  </div>
                  <i class="fa-solid fa-money-bill-wave"></i>
                </label>

                <label class="pay-card">
                  <input type="radio" name="payment_method" value="paymongo">
                  <div>
                    <div class="fw-bold">Card / e-wallet</div>
                    <div class="small text-white-50">PayMongo (placeholder for now).</div>
                  </div>
                  <i class="fa-solid fa-credit-card"></i>
                </label>
              </div>

              <div id="payNote" class="small text-white-50 mt-2">
                COD requires email OTP verification. PayMongo does not.
              </div>

              <!-- COD OTP block -->
              <div id="codOtpBlock" class="mt-3 d-none">
                <div class="panel-card rounded-4 p-3">
                  <div class="fw-bold mb-1">Cash verification (Email OTP)</div>
                  <div class="small text-white-50 mb-3">
                    Before we place a cash order, we’ll send a 6-digit code to your email.
                  </div>

                  <div class="mb-2">
                    <label class="form-label small text-white-50">Email address</label>
                    <input type="email" class="form-control" id="cod_email" placeholder="you@email.com">
                    <div class="invalid-feedback" id="cod_email_err">Please enter a valid email.</div>
                  </div>

                  <div class="d-flex gap-2 flex-wrap align-items-center">
                    <button type="button" class="btn btn-outline-light btn-sm" id="btnSendCodOtp">
                      <i class="fa-solid fa-envelope me-2"></i>Send code
                    </button>
                    <div class="small text-white-50" id="codOtpStatus"></div>
                  </div>

                  <div class="mt-3 d-none" id="codOtpVerifyWrap">
                    <label class="form-label small text-white-50">6-digit code</label>
                    <div class="d-flex gap-2">
                      <input type="text" class="form-control text-center fw-bold" id="cod_otp_code"
                             maxlength="6" inputmode="numeric" placeholder="______">
                      <button type="button" class="btn btn-primary btn-sm" id="btnVerifyCodOtp">
                        Verify
                      </button>
                    </div>
                    <div class="small mt-2" id="codVerifyStatus"></div>
                  </div>
                </div>
              </div>

            </div>

            <!-- Order notes -->
            <div class="mt-4">
              <label class="form-label">Order notes <span class="text-white-50">(optional)</span></label>
              <!-- IMPORTANT: backend expects name="notes" -->
              <textarea class="form-control" name="notes" id="order_notes" rows="3"
                        placeholder="Less sugar, no ice, call when outside, etc."></textarea>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-4">
              <a href="menu.php" class="btn btn-outline-light rounded-pill">
                <i class="fa-solid fa-arrow-left me-2"></i>Back to Menu
              </a>

              <button id="btnPlaceOrder" type="submit" class="btn btn-primary fw-bold rounded-pill px-4">
                Place Order <i class="fa-solid fa-check ms-2"></i>
              </button>
            </div>

            <div id="checkoutError" class="small text-danger mt-3 d-none"></div>
          </form>
        </div>
      </div>

      <!-- RIGHT: Summary (sticky) -->
      <div class="col-lg-5">
        <div class="position-sticky" style="top: 92px;">
          <div class="panel-card rounded-4 p-4">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <h4 class="fw-bold mb-0">Order Summary</h4>
              <span class="badge bg-dark bg-opacity-75">
                Items: <span id="ui_item_count">0</span>
              </span>
            </div>
            <div class="text-white-50 small mb-3">Review your cart before confirming.</div>

            <div id="summaryList" class="d-grid gap-2 mb-4"></div>

            <div class="d-flex justify-content-between">
              <div class="text-white-50">Subtotal</div>
              <div class="fw-bold">₱<span id="ui_subtotal">0.00</span></div>
            </div>

            <div class="d-flex justify-content-between mt-2">
              <div class="text-white-50">Delivery fee</div>
              <div class="fw-bold">₱<span id="ui_delivery_fee">0.00</span></div>
            </div>

            <hr class="border-secondary my-3">

            <div class="d-flex justify-content-between align-items-center">
              <div class="fw-bold">Total</div>
              <div class="fw-bold" style="font-size: 1.15rem;">
                ₱<span id="ui_total">0.00</span>
              </div>
            </div>

            <div class="small text-white-50 mt-3">
              Tip: If something looks wrong, hit “Edit order” to go back to Menu.
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- Branch list for JS lookup -->
<script id="branches-data" type="application/json"><?php
echo json_encode($branches, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
?></script>

<?php include __DIR__ . "/includes/footer.php"; ?>
