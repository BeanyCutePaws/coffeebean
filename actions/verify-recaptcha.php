<?php
// actions/verify-recaptcha.php
// Verifies reCAPTCHA token server-side and sets a session flag for checkout gating.

session_start();
header("Content-Type: application/json; charset=utf-8");

require_once __DIR__ . "/../config/keys.php";

if (!defined('RECAPTCHA_SECRET_KEY') || empty(RECAPTCHA_SECRET_KEY)) {
  echo json_encode([
    "ok" => false,
    "message" => "reCAPTCHA secret key not configured."
  ]);
  exit;
}

$raw  = file_get_contents("php://input");
$data = json_decode($raw, true);

$token = $data['token'] ?? '';
$token = is_string($token) ? trim($token) : '';

if ($token === '') {
  echo json_encode(["ok" => false, "message" => "Missing token."]);
  exit;
}

// Optional: include IP (not required)
$remoteIp = $_SERVER['REMOTE_ADDR'] ?? null;

$payload = http_build_query([
  "secret"   => RECAPTCHA_SECRET_KEY,
  "response" => $token,
  "remoteip" => $remoteIp
]);

$ch = curl_init("https://www.google.com/recaptcha/api/siteverify");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$res = curl_exec($ch);
$err = curl_error($ch);
curl_close($ch);

if ($res === false) {
  echo json_encode(["ok" => false, "message" => "cURL error: " . $err]);
  exit;
}

$out = json_decode($res, true);
$success = (bool)($out['success'] ?? false);

// Optional: restrict to your domain (recommended)
$expectedHost = defined('RECAPTCHA_EXPECTED_HOST') ? RECAPTCHA_EXPECTED_HOST : '';
if ($success && $expectedHost) {
  $host = (string)($out['hostname'] ?? '');
  if ($host !== $expectedHost) {
    $success = false;
    $out['error-codes'][] = 'hostname-mismatch';
  }
}

if ($success) {
  // Session flag that checkout.php can require
  $_SESSION['dm_recaptcha_ok'] = true;
  $_SESSION['dm_recaptcha_ok_at'] = time();
}

echo json_encode([
  "ok" => $success,
  "message" => $success ? "OK" : "Verification failed.",
  "errors" => $success ? [] : ($out['error-codes'] ?? [])
]);
