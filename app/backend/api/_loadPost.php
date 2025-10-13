<?php

session_start();

require "../connect/MainConnect.php";
require "../_include.php";

header('Content-Type: application/json');

// 
// Post Variables
// 

if (!isset($_GET['UUID'])) {
  echo "Error: no UUID!";
  exit;
}

if (!isset($_GET['PUID'])) {
  echo "Error: no PUID!";
  exit;
}

$getPUID = $_GET['PUID'];
$getUUID = $_GET['UUID'];

$PUID = mysqli_real_escape_string($conn_main, $getPUID);
$Poster_UUID = mysqli_real_escape_string($conn_main, $getUUID);

$sql = "SELECT PUID, UUID, content, image_id FROM posts WHERE PUID = ?";
$stmt = mysqli_prepare($conn_main, $sql);

mysqli_stmt_bind_param($stmt, "s", $PUID);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $PUID, $UUID, $content, $image_id);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

if ($UUID === $_SESSION['user']['UUID']) {
  $PostOwner = true;
} else {
  $PostOwner = false;
}

if ($_SESSION['user']['userLevel'] === 1) {
  $PageAdmin = true;
} else {
  $PageAdmin = false;
}

$sql = "SELECT username, pfp_image_link FROM users WHERE UUID = ?";
$stmt = mysqli_prepare($conn_main, $sql);
mysqli_stmt_bind_param($stmt, "s", $Poster_UUID);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $poster_username, $poster_image_link);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

if (empty($poster_image_link)) {
  $poster_image_link = filePath("/assets/logos/") . " pixlshareLogo_white_128.png";
}

// Prepare the data to be output as JSON
$response = [
    'PUID' => $PUID,
    'UUID' => $UUID,
    'content' => $content,
    'image_id' => $image_id,
    'poster_username' => $poster_username,
    'poster_image_link' => $poster_image_link,
    'PostOwner' => $PostOwner,
    'PageAdmin' => $PageAdmin
];

// Output the data as a JSON array
header('Content-Type: application/json');
echo json_encode($response);

?>
