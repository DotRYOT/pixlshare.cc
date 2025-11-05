<?php
session_start();

require "../backend/connect/MainConnect.php";
require "../backend/_include.php";
require "../backend/_auth.php";

checkSuspendedUser($conn_main, $_SESSION['user']['UUID']);
displayError();

if (isset($_GET['p'])) {
  $RefUser = mysqli_real_escape_string($conn_main, $_GET['p']);

  $check_sql = "SELECT uuid FROM users WHERE username = ?";
  $check_stmt = mysqli_prepare($conn_main, $check_sql);
  mysqli_stmt_bind_param($check_stmt, "s", $RefUser);
  mysqli_stmt_execute($check_stmt);
  mysqli_stmt_store_result($check_stmt);

  if (mysqli_stmt_num_rows($check_stmt) > 0) {
    mysqli_stmt_bind_result($check_stmt, $RefUUID);
    mysqli_stmt_fetch($check_stmt);
    redirectTo("/profile/u/$RefUUID");
  }
}

checkUserAuth($conn_main, "auth");

$UUID = mysqli_real_escape_string($conn_main, $_SESSION['user']['UUID']);
$UserName = mysqli_real_escape_string($conn_main, $_SESSION['user']['username']);

$ViewjsonUrl = "../profile/u/" . $UUID . "/userData.json";
$ViewjsonData = file_get_contents($ViewjsonUrl);
$ViewdataArray = json_decode($ViewjsonData, true);

if (empty($ViewdataArray['pfp_image_link'])) {
  $PFPImageLink = filePath("/assets/logos/") . $defaultImage;
} else {
  $PFPImageLink = filePath("/") . $ViewdataArray['pfp_image_link'];
}

if (empty($ViewdataArray['bg_image_link'])) {
  $BGImageLink = filePath("/assets/logos/") . $defaultImageBG;
} else {
  $BGImageLink = filePath("/") . $ViewdataArray['bg_image_link'];
}

$profile_bio = $ViewdataArray['profile_bio'];

$profile_bio = stripslashes($profile_bio);