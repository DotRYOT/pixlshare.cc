<?php
session_start();

require "../backend/_include.php";
require "../backend/_auth.php";
require "../backend/connect/MainConnect.php";

checkUserAuth($conn_main, "auth");

$VUID = mysqli_real_escape_string($conn_main, $_SESSION['user']['UUID']);
$username = mysqli_real_escape_string($conn_main, $_SESSION['user']['username']);

$ViewjsonUrl = "../profile/u/" . $VUID . "/userData.json";
$ViewjsonData = file_get_contents($ViewjsonUrl);
$ViewdataArray = json_decode($ViewjsonData, true);

if (empty($ViewdataArray['pfp_image_link'])) {
  $PFPImageLink = filePath("/assets/logos/") . $defaultImage;
} else {
  $PFPImageLink = filePath("/") . $ViewdataArray['pfp_image_link'];
}

if (isset($_GET['filter'])) {
  $filter = mysqli_real_escape_string($conn_main, $_GET['filter']);
} else {
  $filter = 0;
}