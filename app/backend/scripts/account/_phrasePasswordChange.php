<?php

session_start();

require "../../_include.php";
require "../../connect/MainConnect.php";
require "../../_auth.php";

RateLimitUser();

$newPassword = $_POST['newPassword'];
$confirmPassword = $_POST['confirmPassword'];

if ($newPassword != $confirmPassword) {
  $Error = generateErrorUrl("Passwords do not match!");
  redirectTo("/account/forgot/reset/$Error");
}

// Trim whitespace from session values
$sessionEmail = trim($_SESSION['password_reset_email']);
$sessionToken = trim($_SESSION['password_reset_token']);

if (empty($sessionEmail) || empty($sessionToken)) {
  $Error = generateErrorUrl("Invalid session. Please start the password reset process again.");
  redirectTo("/account/forgot/$Error");
}

// Also trim the email before querying
$cleanEmail = trim($sessionEmail);

$sql = "SELECT id FROM users WHERE email=?";
$stmt = mysqli_prepare($conn_main, $sql);
if ($stmt) {
  mysqli_stmt_bind_param($stmt, "s", $cleanEmail);
  mysqli_stmt_execute($stmt);
  mysqli_stmt_store_result($stmt);

  if (mysqli_stmt_num_rows($stmt) > 0) {
    mysqli_stmt_close($stmt);

    $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

    $sql = "UPDATE users SET user_password = ? WHERE email = ?";
    $stmt = mysqli_prepare($conn_main, $sql);

    if ($stmt) {
      mysqli_stmt_bind_param($stmt, "ss", $hashedPassword, $cleanEmail);
      mysqli_stmt_execute($stmt);
      mysqli_stmt_close($stmt);
    }

    unset($_SESSION['password_reset_email']);
    unset($_SESSION['password_reset_token']);

    redirectTo("/account/signin/");
    exit;
  } else {
    $Error = generateErrorUrl("Account does not exist");
    redirectTo("/account/forgot/$Error");
    mysqli_stmt_close($stmt);
  }
} else {
  $Error = generateErrorUrl("Database error: " . mysqli_error($conn_main));
  redirectTo("/account/forgot/$Error");
}