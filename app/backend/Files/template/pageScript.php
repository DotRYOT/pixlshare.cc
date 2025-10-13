<?php
session_start();

require "../backend/_include.php";
require "../backend/_auth.php";

require "../backend/connect/MainConnect.php";

checkUserAuth($conn_main, "auth");

$UUID = filter_user_input($_SESSION['user']['UUID']);

$sql = "SELECT pfp_image_link FROM users WHERE UUID = ?";
$stmt = mysqli_prepare($conn_main, $sql);

mysqli_stmt_bind_param($stmt, "s", $UUID);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $PFPImageLink);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

if (empty($PFPImageLink)) {
  $PFPImageLink = filePath("/assets/logos/") . " pixlshareLogo_white_128.png";
}

