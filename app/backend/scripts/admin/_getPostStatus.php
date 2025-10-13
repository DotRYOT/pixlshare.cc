<?php
session_start();

require_once "../../connect/MainConnect.php";
require_once "../../_auth.php";
require_once "../../_adminCheck.php";

adminCheck($conn_main);

header('Content-Type: application/json');

$PUID = $_GET['PUID'] ?? $_POST['PUID'] ?? null;

if (!$PUID) {
  echo json_encode(['error' => 'PUID is required']);
  exit;
}

$PUID = mysqli_real_escape_string($conn_main, $PUID);

$sql_post_info = "SELECT p.UUID AS postOwnerUUID, p.postState FROM posts p WHERE p.PUID = ?";
$stmt_post_info = mysqli_prepare($conn_main, $sql_post_info);

if (!$stmt_post_info) {
  echo json_encode(['error' => 'Failed to prepare post info query']);
  exit;
}

mysqli_stmt_bind_param($stmt_post_info, "s", $PUID);
mysqli_stmt_execute($stmt_post_info);
$result_post_info = mysqli_stmt_get_result($stmt_post_info);

if ($row_post_info = mysqli_fetch_assoc($result_post_info)) {
  $postOwnerUUID = $row_post_info['postOwnerUUID'];
  $currentPostState = $row_post_info['postState'];

  // Now get the post owner's userState
  $sql_user_info = "SELECT u.userState FROM users u WHERE u.UUID = ?";
  $stmt_user_info = mysqli_prepare($conn_main, $sql_user_info);

  if (!$stmt_user_info) {
    echo json_encode(['error' => 'Failed to prepare user info query']);
    exit;
  }

  mysqli_stmt_bind_param($stmt_user_info, "s", $postOwnerUUID);
  mysqli_stmt_execute($stmt_user_info);
  $result_user_info = mysqli_stmt_get_result($stmt_user_info);

  if ($row_user_info = mysqli_fetch_assoc($result_user_info)) {
    $currentUserState = $row_user_info['userState'];

    // Successfully retrieved both states
    echo json_encode([
      'success' => true,
      'PUID' => $PUID,
      'postOwnerUUID' => $postOwnerUUID,
      'currentUserState' => $currentUserState,
      'currentPostState' => $currentPostState
    ]);
  } else {
    echo json_encode(['error' => 'Post owner not found']);
  }
  mysqli_stmt_close($stmt_user_info);

} else {
  echo json_encode(['error' => 'Post not found']);
}
mysqli_stmt_close($stmt_post_info);

mysqli_close($conn_main);
exit;
