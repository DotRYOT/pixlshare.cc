<?php
session_start();

require "../../../backend/_include.php";
require "../../../backend/_auth.php";

require "../../../backend/connect/MainConnect.php";

checkUserAuth($conn_main, "auth");

// $UUID = filter_user_input($_SESSION['user']['UUID']);

// $userJsonPath = "../../../profile/u/$UUID/userData.json";
// $userJsonData = json_decode(file_get_contents($userJsonPath), true);

// $PFPImageLink = $userJsonData['pfp_image_link'];

// if (empty($PFPImageLink)) {
//   $PFPImageLink = filePath("/assets/logos/") . " pixlshareLogo_white_128.png";
// }

// if (empty($BGImageLink)) {
//   $BGImageLink = filePath("/assets/logos/") . " pixlshareLogo_white_128.png";
// }


$VUID = mysqli_real_escape_string($conn_main, $_SESSION['user']['UUID']);
$username = mysqli_real_escape_string($conn_main, $_SESSION['user']['username']);

$ViewjsonUrl = "../../../profile/u/" . $VUID . "/userData.json";
$ViewjsonData = file_get_contents($ViewjsonUrl);
$ViewdataArray = json_decode($ViewjsonData, true);

if (empty($ViewdataArray['pfp_image_link'])) {
  $PFPImageLink = filePath("/assets/logos/") . " pixlshareLogo_white_128.png";
} else {
  $PFPImageLink = filePath("/") . $ViewdataArray['pfp_image_link'];
}

if (empty($ViewdataArray['bg_image_link'])) {
  $BGImageLink = filePath("/assets/logos/") . " pixlshareLogo_white_128.png";
} else {
  $BGImageLink = filePath("/") . $ViewdataArray['bg_image_link'];
}

if (!isset($_GET['tag'])) {
  header('Location: ../../../?error=');
  $Error = generateErrorUrl("No UUID!");
  echo $Error;
  redirectTo("/home/$Error");
}

$pageTag = $_GET['tag'];

$GET_UUID = mysqli_real_escape_string($conn_main, $pageTag);

