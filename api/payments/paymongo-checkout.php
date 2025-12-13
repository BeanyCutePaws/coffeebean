<?php
// api/payments/paymongo-checkout.php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../../config/paymongo.php';

function out($code, $payload) {
  http_response_code($code);
  echo json_encode($payload);
  exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  out(405, ['status' => 'error', 'message' => 'Method not allowed']);
}

if (!defined('PAYMONGO_SECRET_KEY') || !PAYMONGO_SECRET_KEY) {
  out(500, ['status' => 'error', 'message' => 'PayMongo config missing.']);
}

$total_amount = (float)($_POST['total_amount'] ?? 0);
if ($total_amount <= 0) {
  out(400, ['status' => 'error', 'message' => 'Invalid total amount.']);
}

$amount_centavos = (int) round($total_amount * 100);

// ✅ Store ALL checkout POST so we can reuse place-order.php later
$_SESSION['dm_pending_post'] = $_POST;

// Optional: remember this is paymongo
$_SESSION['dm_pending_post']['payment_method'] = 'paymongo';

// Base URL for redirects
$base = (defined('APP_BASE_URL') && APP_BASE_URL) ? APP_BASE_URL : '';
if ($base === '') {
  // adjust folder name if needed
  $base = 'http://localhost/coffeebean';
}

$success_url = $base . '/paymongo-return.php?via=paymongo';
$cancel_url  = $base . '/checkout.php?paymongo_cancel=1';

$payload = [
  'data' => [
    'attributes' => [
      'cancel_url' => $cancel_url,
      'success_url' => $success_url,
      'description' => 'Don Macchiatos Online Order',
      'payment_method_types' => ['card', 'gcash', 'paymaya'],
      'line_items' => [[
        'amount' => $amount_centavos,
        'quantity' => 1,
        'name' => 'Don Macchiatos Order',
        'description' => 'Online order',
        'currency' => 'PHP',
      ]],
      'reference_number' => 'DM-' . time(),
      'send_email_receipt' => false,
      'show_description' => true,
      'show_line_items' => false,
    ],
  ],
];

$ch = curl_init(PAYMONGO_API_BASE . '/checkout_sessions');
curl_setopt_array($ch, [
  CURLOPT_POST => true,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_HTTPHEADER => [
    'Content-Type: application/json',
    'Accept: application/json',
    'Authorization: Basic ' . base64_encode(PAYMONGO_SECRET_KEY . ':'),
  ],
  CURLOPT_POSTFIELDS => json_encode($payload),
]);

$body = curl_exec($ch);
$http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($body === false) {
  out(500, ['status' => 'error', 'message' => 'No response from PayMongo.']);
}

$data = json_decode($body, true);

if ($http >= 200 && $http < 300 && !empty($data['data']['attributes']['checkout_url'])) {
  $_SESSION['dm_pending_checkout_session_id'] = $data['data']['id'] ?? null;

  out(200, [
    'status' => 'ok',
    'checkout_url' => $data['data']['attributes']['checkout_url'],
  ]);
}

out(500, [
  'status' => 'error',
  'message' => 'PayMongo Checkout Session creation failed.',
  'httpCode' => $http,
  'raw' => $data,
]);
