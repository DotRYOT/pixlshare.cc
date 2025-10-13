<?php
session_start();

require "../_auth.php";
require "../_include.php";
require "../connect/MainConnect.php";

if (!isset($_GET['uuid'])) {
  echo json_encode("error no UUID!");
  exit;
}

$UUID = mysqli_real_escape_string($conn_main, $_GET['uuid']);

$sql = "SELECT follower_id FROM followers WHERE followee_id = ?";
$stmt = mysqli_prepare($conn_main, $sql);
mysqli_stmt_bind_param($stmt, "s", $UUID);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$data = [];

while ($row = mysqli_fetch_assoc($result)) {
  $data[] = $row['follower_id'];
}

$data = array_values($data);

// var_dump($data);

echo json_encode($data);
