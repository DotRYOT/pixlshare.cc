<?php
session_start();

require "../../../backend/_include.php";
require "../../../backend/_auth.php";
require "../../../backend/connect/MainConnect.php";

if (!isset($_GET['uuid']) || !isset($_GET['puid'])) {
  echo "Error: no post info!";
  exit;
}

$URL_UUID = $_GET['uuid'];
$URL_PUID = $_GET['puid'];

$sql = "SELECT PUID, UUID FROM posts WHERE PUID = ?";
$stmt = mysqli_prepare($conn_main, $sql);

mysqli_stmt_bind_param($stmt, "s", $URL_PUID);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $PUID, $UUID);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

// Check to see if the user is the post owner
if ($URL_UUID === $UUID) {
  $isPostOwner = true;
} else {
  $isPostOwner = false;
}

// https://localhost/pixlshare.cc/app/profile/support/suspendedpost/?puid=38566932737449372947702203&uuid=
// https://localhost/pixlshare.cc/app/profile/support/suspendedpost/?puid=38566932737449372947702203&uuid=91660396659457448435599709