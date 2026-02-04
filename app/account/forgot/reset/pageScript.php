<?php
session_start();

require "../../../backend/_include.php";

if (!isset($_SESSION['password_reset_token']) || !isset($_SESSION['password_reset_email'])) {
  $Error = generateErrorUrl("Unauthorized Access");
  echo $Error;
  redirectTo("/account/forgot/$Error");
}

$userEmail = $_SESSION['password_reset_email'];
$userToken = $_SESSION['password_reset_token'];

// echo $userEmail;
// echo "<br>";
// echo $userToken;
// exit;