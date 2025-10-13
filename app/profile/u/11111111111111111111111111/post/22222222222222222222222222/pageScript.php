<?php
session_start();

require "../../../../../backend/_include.php";
require "../../../../../backend/_auth.php";
require "../../../../../backend/connect/MainConnect.php";

// Use this instead of the redirect for not member viewers
if (!isset($_SESSION['user'])) {
  $_SESSION['user'] = array(
    'UUID' => null,
    'token' => null,
    'username' => null,
    'userLevel' => 0
  );
}

$jsonUrl = "./index.json";
$jsonData = file_get_contents($jsonUrl);
$dataArray = json_decode($jsonData, true);

// 
// Poster Variables
// 

$PUID = filter_user_input($dataArray['PUID'], 'string');
$UUID = filter_user_input($dataArray['UUID'], 'srting');
$Poster_UUID = filter_user_input($dataArray['UUID'], 'string');
$Post_YT_Link = filter_user_input($dataArray['link'], 'string');
$content = htmlspecialchars_decode($dataArray['content'], ENT_QUOTES);

if (empty($dataArray['media_id'])) {
  $media_id = $dataArray['image_id'];
} else {
  $media_id = $dataArray['media_id'];
}

$postState = $dataArray['postState'];
$CatFilterOption = isset($_SESSION['CatFilterOption']) ? $_SESSION['CatFilterOption'] : null;

$PosterUUID = $UUID;

// Get the first few words for the title
$words = explode(' ', $content);
$firstFiveWords = implode(' ', array_slice($words, 0, 5));
$firstFiveWords = mysqli_real_escape_string($conn_main, $firstFiveWords);

$postURL = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

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

$SessionRedirectUUID = filter_user_input($_SESSION['user']['UUID'], 'string');

if (!$PageAdmin && !$PostOwner) {
  $pageSuspended = true;
  if ($postState === "500") {
    redirectTo("/profile/support/suspendedpost/?puid=$PUID&uuid=$SessionRedirectUUID");
    exit;
  }
} else {
  $pageSuspended = false;
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

$previewImage = "";

if (substr($media_id, -4) === '.mp4') {
  $previewImage = filePath("/profile/u/") . $Poster_UUID . '/post/' . $PUID . '/frame_' . $PUID . '.jpg';
} else {
  $previewImage = $poster_image_link;
}

// 
// Viewer Variables
// 
if (isset($_SESSION['user']['UUID'])) {
  $VUID = mysqli_real_escape_string($conn_main, $_SESSION['user']['UUID']);
  $username = mysqli_real_escape_string($conn_main, $_SESSION['user']['username']);

  $ViewjsonUrl = "../../../../../profile/u/" . $VUID . "/userData.json";
  $ViewjsonData = file_get_contents($ViewjsonUrl);
  $ViewdataArray = json_decode($ViewjsonData, true);

  if (empty($ViewdataArray['pfp_image_link'])) {
    $PFPImageLink = filePath("/assets/logos/") . " pixlshareLogo_white_128.png";
  } else {
    $PFPImageLink = filePath("/") . $ViewdataArray['pfp_image_link'];
  }
}