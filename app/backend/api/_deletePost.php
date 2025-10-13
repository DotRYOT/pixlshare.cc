<?php
session_start();

require "../_auth.php";
require "../_include.php";
require "../connect/MainConnect.php";

checkUserAuth($conn_main, "auth");

if (!isset($_GET['PUID'])) {
  echo "PUID is not set!";
  exit;
}

if (!isset($_SESSION['user']['UUID'])) {
  echo "UUID is not set!";
  exit;
}

$PUID = mysqli_real_escape_string($conn_main, $_GET['PUID']);
$UUID = mysqli_real_escape_string($conn_main, $_SESSION['user']['UUID']);

$sql = "SELECT UUID FROM posts WHERE PUID = ? AND UUID = ?";
$stmt = mysqli_prepare($conn_main, $sql);
mysqli_stmt_bind_param($stmt, "ss", $PUID, $UUID);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $DB_UUID);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

if ($_SESSION['user']['userLevel'] === 1 || $UUID === $DB_UUID) {
  $Owner = true;
} else {
  echo "Not Owner STOP!";
  exit;
}

if ($Owner) {
  $sql = "DELETE FROM posts WHERE PUID = ?";
  $stmt = mysqli_prepare($conn_main, $sql);
  mysqli_stmt_bind_param($stmt, "s", $PUID);
  $success = mysqli_stmt_execute($stmt);

  if ($success) {
    echo "Post deleted successfully";
  } else {
    echo "Error deleting post";
  }
  mysqli_stmt_close($stmt);
}
