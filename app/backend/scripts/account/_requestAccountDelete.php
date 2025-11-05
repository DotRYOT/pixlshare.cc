<?php
session_start();

require "../../_auth.php";
require "../../_include.php";
require "../../connect/MainConnect.php";

checkUserAuth($conn_main, "auth");

$UUID = mysqli_real_escape_string($conn_main, $_SESSION['user']['UUID']);
$Context = 999;
$Time = time();

$sql = "SELECT id FROM adminrequest WHERE UUID = ? AND context = ?";
$stmt = mysqli_prepare($conn_main, $sql);
mysqli_stmt_bind_param($stmt, "ss", $UUID, $Context);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

if (mysqli_stmt_num_rows($stmt)) {
  echo "Request already under review!";
  exit;
}

$sql = "INSERT INTO adminrequest (UUID, context, Time) VALUES (?, ?, ?)";
$stmt = mysqli_prepare($conn_main, $sql);
mysqli_stmt_bind_param($stmt, "sss", $UUID, $Context, $Time);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

$userState = 500;

$sql = "UPDATE users SET userState = ? WHERE UUID = ?";
$stmt = mysqli_prepare($conn_main, $sql);
mysqli_stmt_bind_param($stmt, "ss", $userState, $UUID);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);
mysqli_close($conn_main);

echo "successfully";
exit;
