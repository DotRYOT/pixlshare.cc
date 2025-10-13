<?php
session_start();
require "../../_auth.php";
require "../../connect/MainConnect.php";
require "../../_adminCheck.php";

adminCheck($conn_main);

// Check authentication
$user = $_SESSION['user']['UUID'] ?? null;

header('Content-Type: application/json');

if (!$user) {
  echo json_encode(['error' => 'Authentication required']);
  exit;
}

$sql = "SELECT username, ip_address, user_agent, attempt_time FROM login_attempts ORDER BY attempt_time DESC LIMIT 5";
$stmt = mysqli_prepare($conn_main, $sql);

if (!$stmt) {
  echo json_encode(['error' => 'Failed to prepare statement']);
  exit;
}

mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $UUName, $ip_address, $user_agent, $attempt_time);

$results = [];
while (mysqli_stmt_fetch($stmt)) {
  $results[] = [
    'UUName' => $UUName,
    'ip_address' => $ip_address,
    'user_agent' => $user_agent,
    'attempt_time' => $attempt_time
  ];
}

echo json_encode(['data' => $results]);

mysqli_stmt_close($stmt);
mysqli_close($conn_main);