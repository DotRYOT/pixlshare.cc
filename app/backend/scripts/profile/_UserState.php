<?php
header("Content-Type: application/json");
session_start();

require "../../_auth.php";
require "../../_include.php";
require "../../connect/MainConnect.php";

$UUID = $_GET['UUID'];

checkUserAuth($conn_main, "auth");

$sql = "SELECT userState FROM users WHERE UUID = ?";
$stmt = mysqli_prepare($conn_main, $sql);

if ($stmt) {
  mysqli_stmt_bind_param($stmt, "s", $UUID);
  mysqli_stmt_execute($stmt);
  mysqli_stmt_bind_result($stmt, $userState);
  mysqli_stmt_fetch($stmt);
  mysqli_stmt_close($stmt);
}

echo json_encode(['userState' => $userState]);
