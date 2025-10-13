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

$sql = "SELECT following FROM followers WHERE user = ?";
$stmt = mysqli_prepare($conn_main, $sql);
mysqli_stmt_bind_param($stmt, "s", $UUID);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$data = [];

while ($row = mysqli_fetch_assoc($result)) {
  $data[] = $row['following'];
}

if (empty($data)) {
  echo json_encode("No followers found for this UUID.");
  exit;
}

// New code to fetch user names and profile pictures for each follower ID
$userDetails = [];
foreach ($data as $follower_id) {
  $sql_user = "SELECT UUID, username, pfp_image_link FROM users WHERE UUID = ?";
  $stmt_user = mysqli_prepare($conn_main, $sql_user);
  mysqli_stmt_bind_param($stmt_user, "s", $follower_id);
  mysqli_stmt_execute($stmt_user);

  $result_user = mysqli_stmt_get_result($stmt_user);
  if ($row_user = mysqli_fetch_assoc($result_user)) {
    if ($row_user['pfp_image_link'] === null) {
      $userPFP = "assets/logos/pixlshareLogo_white_4k.png";
    } else {
      $userPFP = $row_user['pfp_image_link'];
    }
    $userDetails[] = [
      'uuid' => $row_user['UUID'],
      'name' => $row_user['username'],
      'profilePicture' => $userPFP
    ];
  }
}

echo json_encode($userDetails);
