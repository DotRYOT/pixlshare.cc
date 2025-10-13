<?php
session_start();

require "../_auth.php";
require "../_include.php";
require "../connect/MainConnect.php";

checkUserAuth($conn_main, "auth");

$user = isset($_SESSION['user']['UUID']) ? mysqli_real_escape_string($conn_main, $_SESSION['user']['UUID']) : null;
$following = isset($_GET['UUID']) ? mysqli_real_escape_string($conn_main, $_GET['UUID']) : null;

header('Content-Type: application/json');

if ($following === null) {
  echo json_encode(['error' => 'Invalid UUID']);
  exit;
}

if ($user === null) {
  echo json_encode(['error' => 'User not authenticated']);
  exit;
}

$sql = "SELECT following FROM followers WHERE user = ? AND following = ?";
$stmt = mysqli_prepare($conn_main, $sql);

if ($stmt === false) {
  echo json_encode(['error' => 'Database error']);
  exit;
}

mysqli_stmt_bind_param($stmt, "ss", $user, $following);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

if (mysqli_stmt_num_rows($stmt) > 0) {
  echo json_encode(['isFollowing' => true]);
} else {
  echo json_encode(['isFollowing' => false]);
}
exit;
