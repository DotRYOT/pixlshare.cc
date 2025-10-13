<?php
session_start();

require "../_auth.php";
require "../_include.php";
require "../connect/MainConnect.php";

header('Content-Type: application/json');

try {
  if (!isset($_SESSION['user']['UUID'])) {
    throw new Exception('Invalid session - please login again', 401);
  }

  if (empty($_GET['PUID']) || !preg_match('/^\d{1,32}$/', $_GET['PUID'])) {
    throw new Exception('Invalid PUID format - expected numeric string', 400);
  }

  $PUID = mysqli_real_escape_string($conn_main, $_GET['PUID']);
  $UUID = $_SESSION['user']['UUID'];

  // Verify post existence
  $stmt = mysqli_prepare($conn_main, "SELECT PUID FROM posts WHERE PUID = ?");
  if (!$stmt || !mysqli_stmt_bind_param($stmt, "s", $PUID) || !mysqli_stmt_execute($stmt)) {
    throw new Exception('Database verification failed', 500);
  }

  mysqli_stmt_store_result($stmt);
  if (mysqli_stmt_num_rows($stmt) === 0) {
    throw new Exception('Post not found', 404);
  }
  mysqli_stmt_close($stmt);

  // Check bookmark status in JSON array
  $stmt = mysqli_prepare($conn_main, "SELECT bookmark FROM users WHERE UUID = ?");
  if (!$stmt || !mysqli_stmt_bind_param($stmt, "s", $UUID) || !mysqli_stmt_execute($stmt)) {
    throw new Exception('Database query failed', 500);
  }

  mysqli_stmt_bind_result($stmt, $bookmarkJson);
  mysqli_stmt_fetch($stmt);

  $bookmarks = json_decode($bookmarkJson ?? '[]', true) ?: [];
  $isBookmarked = in_array($PUID, $bookmarks) ? 'bookmarked' : 'not_bookmarked';

  echo json_encode([
    'status' => $isBookmarked,
    'count' => 0
  ]);

} catch (Exception $e) {
  http_response_code($e->getCode() ?: 500);
  echo json_encode([
    'error' => $e->getMessage(),
    'code' => $e->getCode()
  ]);
  error_log("BookmarkCheck Error: " . $e->getMessage());
}
