<?php
session_start();

require "../../_auth.php";
require "../../connect/MainConnect.php";
require "../../_adminCheck.php";

adminCheck($conn_main);

$user = $_SESSION['user']['UUID'] ?? null;
header('Content-Type: application/json');

if (!$user) {
  echo json_encode(['error' => 'Authentication required']);
  exit;
}

// Fetch reports with latest userState from users table
$sql = "SELECT 
            r.PUID,
            r.UUID AS reporterUUID,
            u.username AS reporterName,
            u.userState AS reporterUserState,
            r.CONTEXT,
            p.UUID AS defendantUUID,
            du.username AS defendantName,
            p.postState,
            du.userState AS defendantUserState,
            r.reason,
            r.extra_info
        FROM adminrequest r
        JOIN users u ON r.UUID = u.UUID
        JOIN posts p ON r.PUID = p.PUID
        JOIN users du ON p.UUID = du.UUID
        ORDER BY r.Time DESC
        LIMIT 50";

$stmt = mysqli_prepare($conn_main, $sql);
if (!$stmt) {
  echo json_encode(['error' => 'Query preparation failed']);
  exit;
}

mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $PUID, $reporterUUID, $reporterName, $reporterUserState, $context, $defendantUUID, $defendantName, $postState, $defendantUserState, $reson, $extra_info);

$results = [];

while (mysqli_stmt_fetch($stmt)) {
  // Interpret context
  switch ($context) {
    case '999':
      $contextText = "Request for account removal.";
      break;
    case '555':
      $contextText = "reported a post from";
      break;
    default:
      $contextText = $context;
  }

  $results[] = [
    'PUID' => $PUID,
    'reporterUUID' => $reporterUUID,
    'reporterName' => $reporterName,
    'context' => $contextText,
    'defendantUUID' => $defendantUUID,
    'defendantName' => $defendantName,
    'postState' => $postState,
    'userState' => $defendantUserState,
    'reason' => $reson,
    'extra_info' => $extra_info
  ];
}

echo json_encode([
  'total' => count($results),
  'reports' => $results
]);
exit;