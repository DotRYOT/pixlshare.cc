<?php
session_start();

require "../backend/_include.php";

$profileDataJson = "../profile/u/73286726839558497900756239/userData.json";
$profileDataString = file_get_contents($profileDataJson);

$profileData = json_decode($profileDataString, true);

$profileUsername = $profileData["username"];
$pfp_image_link = $profileData["pfp_image_link"];

$postImage1 = "../profile/u/73286726839558497900756239/post/76690232056892732706354728/img_76690232056892732706354728.png";
$postImage2 = "../profile/u/73286726839558497900756239/post/34311040595920128509506591/frame_34311040595920128509506591.jpg";
$postImage3 = "../profile/u/73286726839558497900756239/post/03348131906580482017792049/frame_03348131906580482017792049.jpg";

// if (isset($_SESSION["user"]["UUID"])) {
//   header("Location: ../home/");
//   exit;
// } else {
//   header("Location: ./");
//   exit;
// }