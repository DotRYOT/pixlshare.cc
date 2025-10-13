<?php
session_start();
require "../_auth.php";
require "../_include.php";
require "../connect/MainConnect.php";

checkUserAuth($conn_main, "auth");
RateLimitUser();

if (!isset($_POST['PostTextFilterOptions']) || !isset($_POST['postText']) || !isset($_POST['PUID']) || !isset($_POST['PosterUUID'])) {
  echo "Error: empty post header!";
  exit;
}

$CatFilterOption = filter_user_input($_POST['PostTextFilterOptions'], 'int');
$content = htmlspecialchars($_POST['postText'], ENT_QUOTES, 'UTF-8');
$PUID = $_POST['PUID'];
$UUID = $_POST['PosterUUID'];

// Mark the post as edited
$content = $content . " (edited)";

$postData = "../../profile/u/" . $UUID . "/post/" . $PUID . "/index.json";

if (file_exists($postData)) {
  $jsonData = file_get_contents($postData);
  $dataArray = json_decode($jsonData, true);

  $PosterUUID = filter_user_input($dataArray['UUID'], 'string');

  if ($_SESSION['user']['userLevel'] === 1) {
    $Admin = true;
  } else {
    $Admin = false;
  }

  if ($UUID === $PosterUUID || $Admin === true) {
    $sql = "UPDATE posts SET content = ?, CatFilterOption = ? WHERE PUID = ?";
    $stmt = mysqli_prepare($conn_main, $sql);
    mysqli_stmt_bind_param($stmt, "sss", $content, $CatFilterOption, $PUID);
    if (mysqli_stmt_execute($stmt)) {
      echo "Success";
    }
    $dataArray['content'] = $content;
    $dataArray['CatFilterOption'] = $CatFilterOption;
    $jsonData = json_encode($dataArray);

    if (file_put_contents($postData, $jsonData) === false) {
      echo "Failed to write to file.";
    }
    mysqli_stmt_close($stmt);
    $success = generateGetUrl("edited");
    echo $success;
    redirectTo("/profile/u/$UUID/post/$PUID/$success");
  } else {
    $Error = generateErrorUrl("No access!");
    echo $Error;
    redirectTo("/account/signin/$Error");
  }
} else {
  echo "Error: File does not exist.";
}
