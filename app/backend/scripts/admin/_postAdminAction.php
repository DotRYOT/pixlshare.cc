<?php
session_start();

require "../../_auth.php";
require "../../_include.php";
require "../../_adminCheck.php";
require "../../connect/MainConnect.php";

checkUserAuth($conn_main, "auth");
adminCheck($conn_main);

header('Content-Type: application/json');

// Ensure all required fields are present
if (!isset($_POST['options'], $_POST['DEF_UUID'], $_POST['postoptions'], $_POST['PUID'])) {
  echo json_encode([
    'success' => false,
    'error' => 'Missing required fields'
  ]);
  exit;
}

$OPT_Choice = mysqli_real_escape_string($conn_main, $_POST['options']);
$Post_Choice = mysqli_real_escape_string($conn_main, $_POST['postoptions']);
$PUID = mysqli_real_escape_string($conn_main, $_POST['PUID']);
$DEF_UUID = mysqli_real_escape_string($conn_main, $_POST['DEF_UUID']);

if ($OPT_Choice === "null")
  $OPT_Choice = "0";
if ($Post_Choice === "null")
  $Post_Choice = "0";

try {
  if (isset($_POST['submit']) && $_POST['submit'] === "Finish") {
    // Update user state
    $sql = "UPDATE users SET userState = ? WHERE UUID = ?";
    $stmt = mysqli_prepare($conn_main, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $OPT_Choice, $DEF_UUID);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Update post state
    $sql = "UPDATE posts SET postState = ? WHERE PUID = ?";
    $stmt = mysqli_prepare($conn_main, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $Post_Choice, $PUID);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Update userData.json
    $userJsonPath = "../../../profile/u/$DEF_UUID/userData.json";
    if (file_exists($userJsonPath)) {
      $jsonData = file_get_contents($userJsonPath);
      $userData = json_decode($jsonData, true);
      if (json_last_error() === JSON_ERROR_NONE) {
        $userData['userState'] = $OPT_Choice;
        file_put_contents($userJsonPath, json_encode($userData, JSON_PRETTY_PRINT));
      }
    }

    // Update index.json for post
    $postJsonPath = "../../../profile/u/$DEF_UUID/post/$PUID/index.json";
    if (file_exists($postJsonPath)) {
      $jsonData = file_get_contents($postJsonPath);
      $postData = json_decode($jsonData, true);
      if (json_last_error() === JSON_ERROR_NONE) {
        $postData['postState'] = $Post_Choice;
        file_put_contents($postJsonPath, json_encode($postData, JSON_PRETTY_PRINT));
      }
    }

    echo json_encode([
      'success' => true,
      'message' => 'Status updated successfully.',
      'data' => [
        'userState' => $OPT_Choice,
        'postState' => $Post_Choice,
        'PUID' => $PUID,
        'DEF_UUID' => $DEF_UUID
      ]
    ]);

  } elseif (isset($_POST['submit']) && $_POST['submit'] === "CloseTicket") {
    // Delete the report
    $sql = "DELETE FROM adminrequest WHERE PUID = ?";
    $stmt = mysqli_prepare($conn_main, $sql);
    if (!$stmt) {
      throw new Exception("Failed to prepare delete statement.");
    }

    mysqli_stmt_bind_param($stmt, "s", $PUID);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    echo json_encode([
      'success' => true,
      'message' => 'Report ticket closed and deleted.',
      'PUID' => $PUID
    ]);

  } else {
    throw new Exception("Unknown or missing submit action.");
  }

} catch (Exception $e) {
  error_log("Admin Action Error: " . $e->getMessage());
  echo json_encode([
    'success' => false,
    'error' => 'Server error: ' . $e->getMessage()
  ]);
}

exit;