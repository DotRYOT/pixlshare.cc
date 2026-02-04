<?php

session_start();

require "../../_include.php";
require "../../connect/MainConnect.php";
require "../../_auth.php";

RateLimitUser();

$UserPassword = $_POST['CurrentPassword'];
$NewPassword = $_POST['NewPassword'];
$RePassword = $_POST['RePassword'];

if ($NewPassword != $RePassword) {
  $Error = generateErrorUrl("Passwords do not match!");
  echo $Error;
  redirectTo("/account/signin/$Error");
}

checkUserAuth($conn_main, "auth");

$UUID = filter_user_input($_SESSION['user']['UUID'], 'string');

$sql = "SELECT id, user_password FROM users WHERE UUID = ?";
$stmt = mysqli_prepare($conn_main, $sql);

if ($stmt) {
  mysqli_stmt_bind_param($stmt, "s", $UUID);
  mysqli_stmt_execute($stmt);
  mysqli_stmt_store_result($stmt);

  if (mysqli_stmt_num_rows($stmt) > 0) {
    mysqli_stmt_bind_result($stmt, $id, $hashedPassword);
    mysqli_stmt_fetch($stmt);

    if (password_verify($UserPassword, $hashedPassword)) {
      mysqli_stmt_close($stmt);

      $sql = "UPDATE users SET user_password = ? WHERE uuid = ?";
      $stmt = mysqli_prepare($conn_main, $sql);

      if ($stmt) {

        $newPasswordHashed = password_hash($NewPassword, PASSWORD_BCRYPT);

        mysqli_stmt_bind_param($stmt, "ss", $newPasswordHashed, $UUID);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
      }
      redirectTo("/account/signout/");
      echo "success";
      exit;
    } else {
      $Error = generateErrorUrl("Bad Password");
      echo $Error;
      redirectTo("/account/signin/$Error");
      mysqli_stmt_close($stmt);
    }
  } else {
    $Error = generateErrorUrl("Account does not exist");
    echo $Error;
    redirectTo("/account/signin/$Error");
    mysqli_stmt_close($stmt);
  }
} else {
  $Error = generateErrorUrl("Missing account");
  echo $Error;
  redirectTo("/account/signin/$Error");
}
