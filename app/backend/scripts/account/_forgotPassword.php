<?php

session_start();

require "../../_include.php";
require "../../connect/MainConnect.php";

RateLimitUser();

if (isset($_POST['emailSubmit'])) {
  $email = mysqli_real_escape_string($conn_main, $_POST['email']);

  if (empty($email)) {
    $Error = generateErrorUrl("Invalid Email");
    redirectTo("/account/forgot/$Error");
  }

  $sql = "SELECT id FROM users WHERE email=?";
  $stmt = mysqli_prepare($conn_main, $sql);
  mysqli_stmt_bind_param($stmt, "s", $email);
  mysqli_stmt_execute($stmt);
  mysqli_stmt_store_result($stmt);
  if (mysqli_stmt_num_rows($stmt) > 0) {
    // Generate a unique token to unlock password reset
    $token = bin2hex(random_bytes(16));
    $_SESSION['password_reset_token'] = $token;
    $_SESSION['password_reset_email'] = $email;

    redirectTo("/account/forgot/passwordchange/");
  } else {
    $Error = generateErrorUrl("Email Not Found");
    redirectTo("/account/forgot/$Error");
  }
}

if (isset($_POST['recoverAccount'])) {
  $phrase1 = trim(mysqli_real_escape_string($conn_main, $_POST['word1']));
  $phrase2 = trim(mysqli_real_escape_string($conn_main, $_POST['word2']));
  $phrase3 = trim(mysqli_real_escape_string($conn_main, $_POST['word3']));
  $phrase4 = trim(mysqli_real_escape_string($conn_main, $_POST['word4']));
  $phrase5 = trim(mysqli_real_escape_string($conn_main, $_POST['word5']));
  $phrase6 = trim(mysqli_real_escape_string($conn_main, $_POST['word6']));
  $phrase7 = trim(mysqli_real_escape_string($conn_main, $_POST['word7']));
  $phrase8 = trim(mysqli_real_escape_string($conn_main, $_POST['word8']));
  $phrase9 = trim(mysqli_real_escape_string($conn_main, $_POST['word9']));
  $phrase10 = trim(mysqli_real_escape_string($conn_main, $_POST['word10']));

  $emailCheck = trim(mysqli_real_escape_string($conn_main, $_POST['emailCheck']));

  // Build the recovered phrase with exactly one space between words
  $recoveredPhrase = trim("$phrase1 $phrase2 $phrase3 $phrase4 $phrase5 $phrase6 $phrase7 $phrase8 $phrase9 $phrase10");

  if (empty($recoveredPhrase)) {
    $Error = generateErrorUrl("Invalid Recovery Phrase");
    redirectTo("/account/forgot/$Error");
  }

  // Fetch the stored phrase
  $sql = "SELECT user_phrase FROM users WHERE email=?";
  $stmt = mysqli_prepare($conn_main, $sql);
  mysqli_stmt_bind_param($stmt, "s", $emailCheck);
  mysqli_stmt_execute($stmt);
  mysqli_stmt_bind_result($stmt, $storedPhrase);
  mysqli_stmt_fetch($stmt);
  mysqli_stmt_close($stmt);

  // Normalize whitespace for comparison
  $normalizedStored = preg_replace('/\s+/', ' ', trim($storedPhrase));
  $normalizedRecovered = preg_replace('/\s+/', ' ', $recoveredPhrase);

  // Compare the phrases after normalizing whitespace
  if ($normalizedStored === $normalizedRecovered) {
    // Recovery phrase is correct, allow password reset
    $_SESSION['password_reset_verified'] = true;
    redirectTo("/account/forgot/reset/");
  } else {
    $Error = generateErrorUrl("Incorrect Recovery Phrase");
    redirectTo("/account/forgot/$Error");
  }
}