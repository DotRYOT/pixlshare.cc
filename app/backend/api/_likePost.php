<?php
session_start();

require "../_auth.php";
require "../_include.php";
require "../connect/MainConnect.php";
require "../scripts/account/_notificationService.php";

header('Content-Type: application/json');

// Check session and authentication
if (!isset($_SESSION['user'])) {
  echo json_encode(['error' => 'Session not set!']);
  exit;
}

if ($_SESSION['user']['UUID'] === null) {
  echo json_encode(['error' => 'No account!']);
  exit;
}

$PUID = mysqli_real_escape_string($conn_main, $_GET['PUID'] ?? '');
$UUID = mysqli_real_escape_string($conn_main, $_SESSION['user']['UUID']);
$Time = time();

// Validate required parameters
if (empty($PUID)) {
  echo json_encode(['error' => 'Missing PUID!']);
  exit;
}

if (!isset($_GET['action']) || ($_GET['action'] !== 'like' && $_GET['action'] !== 'unlike')) {
  echo json_encode(['error' => 'Invalid action!']);
  exit;
}

$sql = "SELECT UUID, CatFilterOption FROM posts WHERE PUID = ?";
$stmt = mysqli_prepare($conn_main, $sql);
mysqli_stmt_bind_param($stmt, "s", $PUID);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $OwnerUUID, $CatFilterOption);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

if (empty($OwnerUUID)) {
  echo json_encode(['error' => 'Post not found.']);
  exit;
}

// Rate limiting
$rate_limit_seconds = 1;
$sql = "SELECT Time FROM likes WHERE PUID = ? AND UUID = ? ORDER BY Time DESC LIMIT 1";
$stmt = mysqli_prepare($conn_main, $sql);
mysqli_stmt_bind_param($stmt, "ss", $PUID, $UUID);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $last_action_time);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

if ($last_action_time && ($Time - $last_action_time < $rate_limit_seconds)) {
  echo json_encode(['error' => 'Rate limit exceeded. Please wait a few seconds before trying again.']);
  exit;
}

$action = $_GET['action'];

if ($action === 'like') {
  $shouldNotify = ($OwnerUUID !== $UUID);

  $sql = "INSERT INTO likes (PUID, UUID, Time) VALUES (?, ?, ?) 
            ON DUPLICATE KEY UPDATE Time = VALUES(Time)";
  $stmt = mysqli_prepare($conn_main, $sql);
  mysqli_stmt_bind_param($stmt, "sss", $PUID, $UUID, $Time);
  $success = mysqli_stmt_execute($stmt);

  if (!$success) {
    echo json_encode(['error' => 'Error liking post.']);
    exit;
  }

  if ($shouldNotify) {
    if ($CatFilterOption === 999) {
      $Type = 'comment';
    } else {
      $Type = 'like';
    }
    AddNewLike($OwnerUUID, $PUID, $conn_main, $Type);
  }

} else { // unlike
  $sql = "DELETE FROM likes WHERE PUID = ? AND UUID = ?";
  $stmt = mysqli_prepare($conn_main, $sql);
  mysqli_stmt_bind_param($stmt, "ss", $PUID, $UUID);
  $success = mysqli_stmt_execute($stmt);

  if (!$success) {
    echo json_encode(['error' => 'Error unliking post.']);
    exit;
  }

  $Path = "../..";
  RemoveLikeNotification($OwnerUUID, $PUID, $conn_main, $Path);
}

// Get updated like count
$sql = "SELECT COUNT(*) FROM likes WHERE PUID = ?";
$stmt = mysqli_prepare($conn_main, $sql);
mysqli_stmt_bind_param($stmt, "s", $PUID);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $likeCount);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

echo json_encode([
  'status' => $action === 'like' ? 'liked' : 'unliked',
  'count' => (int) $likeCount
]);