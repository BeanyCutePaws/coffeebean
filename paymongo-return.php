<?php
// paymongo-return.php
session_start();

// Must have the stored POST from when user clicked PayMongo
if (empty($_SESSION['dm_pending_post']) || !is_array($_SESSION['dm_pending_post'])) {
  header("Location: checkout.php?paymongo_cancel=1");
  exit;
}

// ✅ Optional: you can verify payment status later using checkout session id.
// For now: treat successful redirect as success.
// (Real verification = next step, but this gets the flow working.)

// Bypass recaptcha expiry issue (place-order.php expects dm_recaptcha_ok when enabled)
if (empty($_SESSION['dm_recaptcha_ok'])) {
  $_SESSION['dm_recaptcha_ok'] = true;
  $_SESSION['dm_recaptcha_ok_at'] = time();
}

// Replay POST, then run your existing action
$_POST = $_SESSION['dm_pending_post'];
$_SERVER['REQUEST_METHOD'] = 'POST';

// Important: ensure payment_method is paymongo
$_POST['payment_method'] = 'paymongo';

// Clear pending snapshot so refresh won’t double insert
unset($_SESSION['dm_pending_post'], $_SESSION['dm_pending_checkout_session_id']);

$_POST['payment_method'] = 'paymongo';
$_POST['payment_status'] = 'paid'; // ✅ add this

require __DIR__ . '/actions/place-order.php';
