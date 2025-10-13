<?php
session_start();

require "../_auth.php";
require "../_include.php";
require "../connect/MainConnect.php";

RateLimitUser();

if (!isset($_SESSION['user'])) {
  echo "Session not set!";
  exit;
}

// Using this instead of the auth check for not account memebers
if ($_SESSION['user']['UUID'] === null) {
  echo "No account!";
  exit;
}

if (!isset($_GET['UUID'])) {
  echo "UUID is not set!";
  exit;
}

$user = mysqli_real_escape_string($conn_main, $_SESSION['user']['UUID']);
$following = mysqli_real_escape_string($conn_main, $_GET['UUID']);

$sql = "SELECT user FROM followers WHERE user = ? AND following = ?";
$stmt = mysqli_prepare($conn_main, $sql);
mysqli_stmt_bind_param($stmt, "ss", $user, $following);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

header('Content-Type: application/json');

if (mysqli_stmt_num_rows($stmt) > 0) {
  echo json_encode(['success' => false, 'message' => 'Already following']);
  exit;
}

$insert_sql = "INSERT INTO followers (user, following) VALUES (?, ?)";
$insert_stmt = mysqli_prepare($conn_main, $insert_sql);
mysqli_stmt_bind_param($insert_stmt, "ss", $user, $following);
$success = mysqli_stmt_execute($insert_stmt);

if ($success) {
  echo json_encode(['success' => true, 'message' => 'Followed successfully']);
  exit;
}
