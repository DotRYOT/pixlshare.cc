<?php

session_start();

require "../../_include.php";
require "../../connect/MainConnect.php";
require "../../_auth.php";

RateLimitUser();

$Type = $_POST['type'];

if ($Type == 1) {

  $UserPassword = $_POST['CurrentPassword'];
  $NewPassword = $_POST['NewPassword'];
  $RePassword = $_POST['RePassword'];

  if ($NewPassword != $RePassword) {
    $Error = generateErrorUrl("Passwords do not match!");
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
          mysqli_stmt_bind_param($stmt, "ss", $hashedPassword, $UUID);
          mysqli_stmt_execute($stmt);
          mysqli_stmt_close($stmt);
        }

        redirectTo("/account/signout/");
        exit;
      } else {
        $Error = generateErrorUrl("Bad Password");
        redirectTo("/account/signin/$Error");
        mysqli_stmt_close($stmt);
      }
    } else {
      $Error = generateErrorUrl("Account does not exist");
      redirectTo("/account/signin/$Error");
      mysqli_stmt_close($stmt);
    }
  } else {
    $Error = generateErrorUrl("Missing account");
    redirectTo("/account/signin/$Error");
  }
} elseif ($Type == 2) {
  $UUID = filter_user_input($_POST['uuid'], 'string');
  $userpassword = $_POST['password'];
  $userREpassword = $_POST['repassword'];

  $pageKey = filter_user_input($_POST['pageKey'], 'int');

  $Context = "800";

  if ($userpassword != $userREpassword) {
    $Error = generateErrorUrl("Password does not match!");
    redirectTo("/account/forgot/passwordchange/$Error&key=$pageKey&uuid=$UUID");
  }

  $hashedPassword = password_hash($userpassword, PASSWORD_BCRYPT);

  $check_sql = "SELECT UUID, pageKey FROM adminrequest WHERE UUID = ? AND context = ?";
  $check_stmt = mysqli_prepare($conn_main, $check_sql);
  mysqli_stmt_bind_param($check_stmt, "ss", $UUID, $Context);
  mysqli_stmt_execute($check_stmt);
  mysqli_stmt_store_result($check_stmt);

  if (mysqli_stmt_num_rows($check_stmt) > 0) {
    mysqli_stmt_bind_result($check_stmt, $DBUUID, $DBpageKey);
    mysqli_stmt_fetch($check_stmt);
    if ($DBpageKey == $pageKey && $DBUUID == $UUID) {
      $sql = "UPDATE users SET user_password = ? WHERE uuid = ?";
      $stmt = mysqli_prepare($conn_main, $sql);

      if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ss", $hashedPassword, $UUID);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
      }

      $delete_sql = "DELETE FROM adminrequest WHERE UUID = ? AND context = ?";
      $delete_stmt = mysqli_prepare($conn_main, $delete_sql);
      if ($delete_stmt) {
        mysqli_stmt_bind_param($delete_stmt, "ss", $UUID, $Context);
        mysqli_stmt_execute($delete_stmt);
        mysqli_stmt_close($delete_stmt);
      }

      $success = generateErrorUrl("Password Changed");
      redirectTo("/account/signin/$success");
    }
  } else {
    $Error = generateErrorUrl("No matching request found");
    redirectTo("/account/forgot/$Error");
  }

  mysqli_stmt_close($check_stmt);
}
