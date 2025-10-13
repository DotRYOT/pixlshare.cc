<?php
session_start();

require "../_auth.php";
require "../_include.php";
require "../connect/MainConnect.php";

header('Content-Type: application/json');

if (!isset($_GET['PUID'])) {
  echo "PUID is not set!";
  exit;
}

if (!isset($_SESSION['user'])) {
  echo "Session not set!";
  exit;
}

$PUID = mysqli_real_escape_string($conn_main, $_GET['PUID']);
$UUID = mysqli_real_escape_string($conn_main, $_SESSION['user']['UUID']);

// Check if the current user has liked the post
$sql = "SELECT * FROM likes WHERE PUID = ? AND UUID = ?";
$stmt = mysqli_prepare($conn_main, $sql);
mysqli_stmt_bind_param($stmt, "ss", $PUID, $UUID);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$isLiked = mysqli_num_rows($result) > 0 ? "liked" : "not liked";

// Count total likes for the post
$sql_count = "SELECT COUNT(*) as like_count FROM likes WHERE PUID = ?";
$stmt_count = mysqli_prepare($conn_main, $sql_count);
mysqli_stmt_bind_param($stmt_count, "s", $PUID);
mysqli_stmt_execute($stmt_count);
$result_count = mysqli_stmt_get_result($stmt_count);
$row = mysqli_fetch_assoc($result_count);
$like_count = $row['like_count'];

// Return both the like status and the total like count
echo json_encode(["status" => $isLiked, "count" => $like_count]);
