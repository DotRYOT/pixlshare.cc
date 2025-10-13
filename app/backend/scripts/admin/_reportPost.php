<?php
session_start();

require "../../_auth.php";
require "../../_include.php";
require "../../connect/MainConnect.php";

checkUserAuth($conn_main, "auth");

// Get PUID from GET
$PUID = mysqli_real_escape_string($conn_main, $_GET['PUID']);

// Get form data from POST
$reason = mysqli_real_escape_string($conn_main, $_POST['reason'] ?? '');
$extraInfo = mysqli_real_escape_string($conn_main, $_POST['extraInfo'] ?? '');
$UUID = mysqli_real_escape_string($conn_main, $_SESSION['user']['UUID']);
$Context = 555; // Your report context code
$Time = time();

// Validate required fields
if (empty($reason)) {
  echo "Reason is required";
  exit;
}

// Check if already reported
$sql = "SELECT id FROM adminrequest WHERE PUID = ? AND context = ?";
$stmt = mysqli_prepare($conn_main, $sql);
mysqli_stmt_bind_param($stmt, "ss", $PUID, $Context);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

if (mysqli_stmt_num_rows($stmt) > 0) {
  echo "Request already under review!";
  mysqli_stmt_close($stmt);
  mysqli_close($conn_main);
  exit;
}

mysqli_stmt_close($stmt);

// Insert new report
$sql = "INSERT INTO adminrequest (PUID, UUID, context, Time, reason, extra_info) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = mysqli_prepare($conn_main, $sql);
mysqli_stmt_bind_param($stmt, "ssssss", $PUID, $UUID, $Context, $Time, $reason, $extraInfo);

if (mysqli_stmt_execute($stmt)) {
  echo "successfully";
} else {
  echo "Database error: " . mysqli_error($conn_main);
}

mysqli_stmt_close($stmt);
mysqli_close($conn_main);
exit;