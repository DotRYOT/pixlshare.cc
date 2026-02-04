<?php

session_set_cookie_params([
  'lifetime' => 0,
  'path' => '/account/',
  'domain' => 'pixlshare.cc',
  'secure' => true,
  'httponly' => true,
  'samesite' => 'Strict'
]);
session_start([
  'cookie_lifetime' => 0,
  'cookie_secure' => true,
  'cookie_httponly' => true,
  'cookie_samesite' => 'Strict'
]);

require "../../_include.php";
require "../../connect/MainConnect.php";

// Generate or validate CSRF token
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
    $Error = generateErrorUrl("Invalid CSRF token! Please refresh the page and try again.");
    redirectTo("/account/signin/$Error");
    exit;
  }
} else {
  // Generate CSRF token only once per session
  if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(50));
  }
}

RateLimitUser();

if ($_SERVER['HTTP_HOST'] == 'pixlshare.cc') {
  require "../../connect/_cloudFlareAPI.php";
  $secretKey = $CloudFlairAPI;
  $CFTurnstileResponse = $_POST['cf-turnstile-response'];
  $curl = curl_init();
  curl_setopt_array($curl, [
    CURLOPT_URL => "https://challenges.cloudflare.com/turnstile/v0/siteverify",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => http_build_query([
      'secret' => $secretKey,
      'response' => $CFTurnstileResponse,
    ]),
  ]);
  $response = curl_exec($curl);
  $err = curl_error($curl);
  curl_close($curl);

  if ($err) {
    exit("Turnstile cURL Error: $err");
  }
  $responseData = json_decode($response, true);
  if (empty($responseData['success'])) {
    logLoginAttempt(null, 'Failed_turnstile');
    $Error = generateErrorUrl("Turnstile verification failed: " . json_encode($responseData['error-codes']));
    redirectTo("/account/signin/$Error");
  }
}

// Sanitize inputs
$UserName = filter_user_input($_POST['username'], 'string');
$UserPassword = $_POST['password'];

if (empty($UserName) || empty($UserPassword)) {
  logLoginAttempt(null, 'missing_credentials');
  $Error = generateErrorUrl("Invalid username or password");
  redirectTo("/account/signin/$Error");
}

// Prepare SQL query
$sql = "SELECT uuid, user_password FROM users WHERE username = ?";
$stmt = mysqli_prepare($conn_main, $sql);

if (!$stmt) {
  logLoginAttempt(null, 'db_error');
  exit('Database error.');
}

mysqli_stmt_bind_param($stmt, "s", $UserName);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

if (mysqli_stmt_num_rows($stmt) === 0) {
  logLoginAttempt($UserName, 'user_not_found');
  $Error = generateErrorUrl("Invalid username or password");
  redirectTo("/account/signin/$Error");
}

mysqli_stmt_bind_result($stmt, $UUID, $hashedPassword);
mysqli_stmt_fetch($stmt);

if (!password_verify($UserPassword, $hashedPassword)) {
  logLoginAttempt($UserName, 'wrong_password');
  $Error = generateErrorUrl("Invalid username or password");
  redirectTo("/account/signin/$Error");
}

// User authenticated — regenerate session ID
session_regenerate_id(true);

// Get additional user data
$adminCheck = "SELECT userLevel, userAge, user_token FROM users WHERE UUID = ?";
$adminstmt = mysqli_prepare($conn_main, $adminCheck);
mysqli_stmt_bind_param($adminstmt, "s", $UUID);
mysqli_stmt_execute($adminstmt);
mysqli_stmt_bind_result($adminstmt, $userLevel, $ageCheck, $userTokenArray);
mysqli_stmt_fetch($adminstmt);
mysqli_stmt_close($adminstmt);

$userTokenArray = json_decode($userTokenArray, true);

$userToken = $userTokenArray['Token'] ?? '';
$hashedToken = $userTokenArray['hashToken'] ?? '';

$authTokenArray = array(
  'UUID' => $UUID,
  'Token' => $userToken
);

$authTokenArray = json_encode($authTokenArray);

setcookie("authToken", $authTokenArray, time() + (7 * 24 * 60 * 60), "/", '', true, true);

$_SESSION['user'] = array(
  'UUID' => $UUID,
  'token' => $userToken,
  'username' => $UserName,
  'userLevel' => $userLevel,
  'ageCheck' => $ageCheck,
  'profile_setup_complete' => true
);

logLoginAttempt($UserName, 'success');

mysqli_close($conn_main);
redirectTo("/home/");
exit;