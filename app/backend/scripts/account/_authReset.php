<?php

session_start();

require "../../_include.php";
require "../../connect/MainConnect.php";

RateLimitUser();

$UUID = mysqli_real_escape_string($conn_main, $_SESSION['user']['UUID']);
$token = bin2hex(random_bytes(32));
$hashedToken = password_hash($token, PASSWORD_BCRYPT);

$user_token = array(
  'Token' => $token,
  'hashToken' => $hashedToken
);

$user_token = json_encode($user_token);

$sql = "UPDATE users SET user_token = ? WHERE UUID = ?";
$stmt = mysqli_prepare($conn_main, $sql);

if ($stmt) {
  mysqli_stmt_bind_param($stmt, "ss", $user_token, $UUID);
  mysqli_stmt_execute($stmt);
  mysqli_stmt_close($stmt);
}

mysqli_close($conn_main);

redirectTo("/account/signout/");