<?php

session_start();

require "../_auth.php";
require "../_include.php";
require "../connect/MainConnect.php";

checkUserAuth($conn_main, "auth");

header('Content-Type: application/json');

if (!isset($_GET['UUID'])) {
  echo "error no UUID!";
  exit;
}

$UUID = mysqli_real_escape_string($conn_main, $_GET['UUID']);

$sql = "SELECT COUNT(user) AS follower_count FROM followers WHERE following = ?";
$stmt = mysqli_prepare($conn_main, $sql);
mysqli_stmt_bind_param($stmt, "s", $UUID);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);

echo $row['follower_count'];
