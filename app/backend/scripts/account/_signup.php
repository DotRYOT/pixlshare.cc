<?php

session_start();

require "../../_include.php";
require "../../connect/MainConnect.php";

RateLimitUser();

$UUID = randStringGen(26, 'numbers');
$UserName = filter_user_input($_POST['UserName'], 'string');
$UserIP = getUserIP();
$userPassword = $_POST['password'];
$userRePassword = $_POST['repassword'];
$pfp_image_link = "assets/logos/pixlshareLogo_white_4k.png";
$bg_image_link = "assets/logos/pxl_logo_1350_white.png";
$profile_bio = "";
$userLevel = 0;
$userState = 0;
$bookMark = "[]";
$token = bin2hex(random_bytes(32));
$hashedToken = password_hash($token, PASSWORD_BCRYPT);
$day = filter_user_input($_POST['dob_day'], 'string');
$month = filter_user_input($_POST['dob_month'], 'string');
$year = filter_user_input($_POST['dob_year'], 'string');

// Cloudflare Turnstile verification
if ($_SERVER['HTTP_HOST'] == 'pixlshare.cc') {
  require "../../connect/_cloudFlareAPI.php";
  $secretKey = $CloudFlareAPI;
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
    exit("Turnstile verification failed: " . json_encode($responseData['error-codes']));
  }
}

// Validate that all parts are provided
if (empty($day) || empty($month) || empty($year)) {
  handleSignupError("Date of birth is required");
}

// Validate numeric values
if (!is_numeric($day) || !is_numeric($month) || !is_numeric($year)) {
  handleSignupError("Invalid date format");
}

$day = (int) $day;
$month = (int) $month;
$year = (int) $year;

// Validate date ranges
if ($day < 1 || $day > 31 || $month < 1 || $month > 12 || $year < 1900 || $year > (int) date('Y') - 18) {
  handleSignupError("Invalid date values");
}

// Create a date string in YYYY-MM-DD format
$ageCheck = sprintf('%04d-%02d-%02d', $year, $month, $day);

// Verify the date is valid (e.g., not Feb 30th)
$testDate = DateTime::createFromFormat('Y-m-d', $ageCheck);
if (!$testDate || $testDate->format('Y-m-d') !== $ageCheck) {
  handleSignupError("Please enter a valid date");
}

$authTokenArray = array(
  'UUID' => $UUID,
  'Token' => $token
);

$authTokenArray = json_encode($authTokenArray);

$hashTokenArray = array(
  'Token' => $token,
  'hashToken' => $hashedToken
);

$hashTokenArray = json_encode($hashTokenArray);

$age = date_diff(date_create($ageCheck), date_create('today'))->y;
$is_over_18 = ($age >= 18);
$user_data = [
  'dob' => $ageCheck,
  'age' => $age,
  'is_over_18' => $is_over_18
];
$json_user_data = json_encode($user_data);

if ($userPassword !== $userRePassword) {
  handleSignupError("Passwords don't match!");
}

// Validate email format
if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
  handleSignupError("Invalid email format");
}

$email = mysqli_real_escape_string($conn_main, $_POST['email']);

// Check to see if the username exists 
$check_sql = "SELECT id FROM users WHERE username = ? OR email = ?";
$check_stmt = mysqli_prepare($conn_main, $check_sql);
mysqli_stmt_bind_param($check_stmt, "ss", $UserName, $email);
mysqli_stmt_execute($check_stmt);
mysqli_stmt_store_result($check_stmt);

if (mysqli_stmt_num_rows($check_stmt) > 0) {
  handleSignupError("Username or email taken");
}

// Hash password
$hashedPassword = password_hash($userPassword, PASSWORD_BCRYPT);

// Use transaction for atomic operations
mysqli_begin_transaction($conn_main);

// Random profile backup phrase generation

require "../../json/_wordList.php";

function generateSecureBackupPhrase($wordList, $numWords = 10)
{
  $phraseWords = [];
  $wordListCount = count($wordList);
  for ($i = 0; $i < $numWords; $i++) {
    $randomIndex = random_int(0, $wordListCount - 1);
    $phraseWords[] = $wordList[$randomIndex];
  }
  return $phraseWords;
}

$backupPhrase = generateSecureBackupPhrase($wordList, 10);
$backupPhrase = implode(' ', $backupPhrase);
$backupPhrase = mysqli_real_escape_string($conn_main, $backupPhrase);

try {
  $insert_sql = "INSERT INTO users (uuid, username, user_ip, email, userAge, user_password, profile_bio, bookmark, userLevel, userState, user_token, pfp_image_link, bg_image_link, user_phrase) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
  $insert_stmt = mysqli_prepare($conn_main, $insert_sql);
  mysqli_stmt_bind_param($insert_stmt, "ssssssssssssss", $UUID, $UserName, $UserIP, $email, $json_user_data, $hashedPassword, $profile_bio, $bookMark, $userLevel, $userState, $hashTokenArray, $pfp_image_link, $bg_image_link, $backupPhrase);

  if (!mysqli_stmt_execute($insert_stmt)) {
    throw new Exception("Database error");
  }

  // Add self-follow
  $follow_sql = "INSERT INTO followers (user, following) VALUES (?, ?)";
  $follow_stmt = mysqli_prepare($conn_main, $follow_sql);
  mysqli_stmt_bind_param($follow_stmt, "ss", $UUID, $UUID);
  if (!mysqli_stmt_execute($follow_stmt)) {
    throw new Exception("Failed to create self-follow");
  }
  mysqli_stmt_close($follow_stmt);

  // Combined directory creation
  $userDir = "../../../profile/u/$UUID";
  $dirsToCreate = [
    $userDir,
    "$userDir/post",
    "$userDir/uploads"
  ];

  foreach ($dirsToCreate as $dir) {
    if (!is_dir($dir) && !mkdir($dir, 0755, true)) {
      throw new Exception("Failed to create directories");
    }
  }

  // File creation
  $filesToCreate = [
    "$userDir/index.php" => '<?php require "../../../backend/Files/repoPages/publicUserPage/userHomePage.php"; ?>',
    "$userDir/userData.json" => json_encode([
      'username' => $UserName,
      'UUID' => $UUID,
      'pfp_image_link' => '',
      'bg_image_link' => '',
      'profile_bio' => '',
      'userLevel' => '0',
      'userState' => '0',
      'is_over_18' => $is_over_18
    ])
  ];

  foreach ($filesToCreate as $path => $content) {
    if (file_put_contents($path, $content) === false) {
      throw new Exception("Failed to create files");
    }
  }

  // Set session and cookie
  $_SESSION['user'] = [
    'UUID' => $UUID,
    'token' => $token,
    'username' => $UserName,
    'userLevel' => $userLevel,
    'ageCheck' => $ageCheck,
    'profile_setup_complete' => false
  ];

  setcookie('authToken', $authTokenArray, time() + 3600, '/', '', true, true);
  session_regenerate_id(true);

  mysqli_commit($conn_main);
  redirectTo("/account/signup/style/");

} catch (Exception $e) {
  mysqli_rollback($conn_main);
  handleSignupError("Account creation failed: " . $e->getMessage());
}

mysqli_stmt_close($check_stmt);