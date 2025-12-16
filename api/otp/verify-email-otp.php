<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode(['success' => false, 'message' => 'Method not allowed']);
  exit;
}

$email = trim((string)($_POST['email'] ?? ''));
$code  = trim((string)($_POST['code'] ?? ''));
$scope = trim((string)($_POST['scope'] ?? 'default')); // ✅ 'cod' or 'cancel'

if ($email === '' || $code === '') {
  echo json_encode(['success' => false, 'message' => 'Missing email or code.']);
  exit;
}

$sessionEmail   = $_SESSION["otp_email_$scope"] ?? null;
$sessionCode    = $_SESSION["otp_code_$scope"] ?? null;
$sessionExpires = $_SESSION["otp_expires_$scope"] ?? null;

if (!$sessionEmail || !$sessionCode || !$sessionExpires) {
  echo json_encode(['success' => false, 'message' => 'No active verification code. Please request a new one.']);
  exit;
}

if (time() > (int)$sessionExpires) {
  $_SESSION["otp_verified_$scope"] = false;
  unset($_SESSION["otp_code_$scope"], $_SESSION["otp_expires_$scope"]);
  echo json_encode(['success' => false, 'message' => 'Your code has expired. Please request a new one.']);
  exit;
}

if (strcasecmp($email, (string)$sessionEmail) !== 0) {
  echo json_encode(['success' => false, 'message' => 'This code does not match the email used.']);
  exit;
}

if ($code !== (string)$sessionCode) {
  echo json_encode(['success' => false, 'message' => 'Incorrect code.']);
  exit;
}

$_SESSION["otp_verified_$scope"] = true;
unset($_SESSION["otp_code_$scope"], $_SESSION["otp_expires_$scope"]);

$debug = [
  'sid' => session_id(),
  'cookie' => $_COOKIE[session_name()] ?? null,
];

echo json_encode(['success' => true, 'message' => 'OTP verified.', 'debug' => $debug]);


