<?php
session_start();

require "../_auth.php";
require "../_include.php";
require "../connect/MainConnect.php";

header('Content-Type: application/json');

RateLimitUser();

checkUserAuth($conn_main, "auth");

if (!isset($_GET['UUID'])) {
  echo "Error: no UUID!";
  exit;
}

$user = mysqli_real_escape_string($conn_main, $_SESSION['user']['UUID']);
$following = mysqli_real_escape_string($conn_main, $_GET['UUID']);

$sql = "SELECT following FROM followers WHERE user = ? AND following = ?";
$stmt = mysqli_prepare($conn_main, $sql);
mysqli_stmt_bind_param($stmt, "ss", $user, $following);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);


if (mysqli_stmt_num_rows($stmt) === 0) {
  echo json_encode(['success' => false, 'message' => 'Not following']);
  exit;
}

$delete_sql = "DELETE FROM followers WHERE user = ? AND following = ?";
$delete_stmt = mysqli_prepare($conn_main, $delete_sql);
mysqli_stmt_bind_param($delete_stmt, "ss", $user, $following);
$success = mysqli_stmt_execute($delete_stmt);

if ($success) {
  echo json_encode(['success' => true, 'message' => 'Unfollowed successfully']);
  exit;
}
