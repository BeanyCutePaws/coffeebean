<?php
// api/otp/send-email-otp.php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../../config/brevo.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode(['success' => false, 'message' => 'Method not allowed']);
  exit;
}

$email = trim((string)($_POST['email'] ?? ''));
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
  echo json_encode(['success' => false, 'message' => 'Invalid email address.']);
  exit;
}

if (!defined('BREVO_API_KEY') || !BREVO_API_KEY) {
  http_response_code(500);
  echo json_encode(['success' => false, 'message' => 'Brevo API key missing.']);
  exit;
}
if (!defined('BREVO_SENDER_EMAIL') || !BREVO_SENDER_EMAIL) {
  http_response_code(500);
  echo json_encode(['success' => false, 'message' => 'Brevo sender email missing.']);
  exit;
}

// Generate OTP
try { $otp = random_int(100000, 999999); }
catch (Exception $e) { $otp = mt_rand(100000, 999999); }

// Store OTP (5 min)
$_SESSION['otp_email']     = $email;
$_SESSION['otp_code']      = (string)$otp;
$_SESSION['otp_expires']   = time() + 300;
$_SESSION['otp_verified']  = false;

$subject = 'Your Don Macchiatos confirmation code';
$html = '<html><body>'
  . '<p>Hi!</p>'
  . '<p>Your confirmation code is: <strong style="font-size:18px;">' . $otp . '</strong></p>'
  . '<p>This code expires in 5 minutes.</p>'
  . '</body></html>';

$payload = [
  'sender' => ['email' => BREVO_SENDER_EMAIL, 'name' => (defined('BREVO_SENDER_NAME') ? BREVO_SENDER_NAME : 'Don Macchiatos')],
  'to' => [['email' => $email]],
  'subject' => $subject,
  'htmlContent' => $html,
];

$ch = curl_init(BREVO_API_BASE . '/smtp/email');
curl_setopt_array($ch, [
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_POST => true,
  CURLOPT_HTTPHEADER => [
    'accept: application/json',
    'content-type: application/json',
    'api-key: ' . BREVO_API_KEY,
  ],
  CURLOPT_POSTFIELDS => json_encode($payload),
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlErr  = curl_error($ch);
curl_close($ch);

if ($response === false) {
  echo json_encode(['success' => false, 'message' => 'Failed to contact email service: ' . $curlErr]);
  exit;
}
if ($httpCode < 200 || $httpCode >= 300) {
  echo json_encode(['success' => false, 'message' => 'Email service returned an error (HTTP ' . $httpCode . ').', 'raw' => $response]);
  exit;
}

echo json_encode(['success' => true, 'message' => 'OTP sent to ' . $email]);
