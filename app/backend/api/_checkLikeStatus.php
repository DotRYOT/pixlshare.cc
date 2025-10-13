<?php
session_start();

require "../_auth.php";
require "../_include.php";
require "../connect/MainConnect.php";

header('Content-Type: application/json');

// Check authentication
if (!isset($_SESSION['user']) || $_SESSION['user']['UUID'] === null) {
  echo json_encode(['error' => 'User not authenticated']);
  exit;
}

$UUID = $_SESSION['user']['UUID'];
$PUID = mysqli_real_escape_string($conn_main, $_GET['PUID'] ?? '');

// Validate required parameters
if (empty($PUID)) {
  echo json_encode(['error' => 'Missing PUID']);
  exit;
}

// Check if user has liked this post
$sql = "SELECT 1 FROM likes WHERE PUID = ? AND UUID = ?";
$stmt = mysqli_prepare($conn_main, $sql);
mysqli_stmt_bind_param($stmt, "ss", $PUID, $UUID);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);
$userLiked = mysqli_stmt_num_rows($stmt) > 0;
mysqli_stmt_close($stmt);

// Get total like count for the post
$sql = "SELECT COUNT(*) FROM likes WHERE PUID = ?";
$stmt = mysqli_prepare($conn_main, $sql);
mysqli_stmt_bind_param($stmt, "s", $PUID);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $totalLikes);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

// Return response
echo json_encode([
  'liked' => $userLiked,
  'count' => (int) $totalLikes
]);
?>