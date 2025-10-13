<?php
session_start();

$baseDir = __DIR__ . '../../../../../backend';

require $baseDir . "/_include.php";
require $baseDir . "/_auth.php";
require $baseDir . "/connect/MainConnect.php";

displayError();

// Use this instead of the redirect for not member viewers
if (!isset($_SESSION['user'])) {
  $_SESSION['user'] = array(
    'UUID' => null,
    'token' => null,
    'username' => null,
    'userLevel' => 0
  );
}

//
// Host Data
//

$url = $_SERVER['REQUEST_URI'];
$extractedNumber = extractNumbersFromUrl($url);

$HostUUID = filter_user_input($extractedNumber);

$UserjsonUrl = "../../../profile/u/" . $HostUUID . "/userData.json";
$UserjsonData = file_get_contents($UserjsonUrl);
$UserdataArray = json_decode($UserjsonData, true);

$profile_UserName = isset($UserdataArray['username']) ? $UserdataArray['username'] : null;
$profile_PFPImageLink = isset($UserdataArray['pfp_image_link']) ? $UserdataArray['pfp_image_link'] : null;
$profile_BGImageLink = isset($UserdataArray['bg_image_link']) ? $UserdataArray['bg_image_link'] : null;
$profile_bio = isset($UserdataArray['profile_bio']) ? stripslashes($UserdataArray['profile_bio']) : null;
$userLevel = isset($UserdataArray['userLevel']) ? $UserdataArray['userLevel'] : null;
$userState = isset($UserdataArray['userState']) ? $UserdataArray['userState'] : null;
$isOver18 = isset($UserdataArray['is_over_18']) ? $UserdataArray['is_over_18'] : null;

if (empty($profile_PFPImageLink)) {
  $profile_PFPImageLink = filePath("/assets/logos/") . $defaultImage;
} else {
  $profile_PFPImageLink = filePath("/") . $profile_PFPImageLink;
}

if (empty($profile_BGImageLink)) {
  $profile_BGImageLink = filePath("/assets/logos/") . $defaultImageBG;
} else {
  $profile_BGImageLink = filePath("/") . $profile_BGImageLink;
}

// 
// Viewer Data
// 

if (isset($_SESSION['user']['UUID'])) {
  $ViewUUID = mysqli_real_escape_string($conn_main, $_SESSION['user']['UUID']);
  $username = mysqli_real_escape_string($conn_main, $_SESSION['user']['username']);

  $ViewjsonUrl = "../../../profile/u/" . $ViewUUID . "/userData.json";
  $ViewjsonData = file_get_contents($ViewjsonUrl);
  $ViewdataArray = json_decode($ViewjsonData, true);


  if (empty($ViewdataArray['pfp_image_link'])) {
    $PFPImageLink = filePath("/assets/logos/") . $defaultImage;
  } else {
    $PFPImageLink = filePath("/") . $ViewdataArray['pfp_image_link'];
  }
}