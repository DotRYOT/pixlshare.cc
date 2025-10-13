<?php
session_start();

// require "../_auth.php";
require "../_include.php";
require "../connect/MainConnect.php";

header('Content-Type: application/json');

// Add authentication check
if (!isset($_SESSION['user'])) {
  echo json_encode(['error' => 'User not authenticated']);
  exit;
}

// Validate PUID parameter
if (empty($_GET['PUID'])) {
  echo json_encode(['error' => 'PUID is required']);
  exit;
}

$PUID = mysqli_real_escape_string($conn_main, $_GET['PUID']);

// Prepare and execute query
$sql_comments = "SELECT c.PUID, c.content, c.UUID, c.Time, u.username, u.pfp_image_link 
                FROM comments c 
                JOIN users u ON c.UUID = u.UUID 
                WHERE c.PUID = ? 
                ORDER BY c.Time DESC";
$stmt = mysqli_prepare($conn_main, $sql_comments);

if (!$stmt || !mysqli_stmt_bind_param($stmt, "s", $PUID) || !mysqli_stmt_execute($stmt)) {
  echo json_encode(['error' => 'Database query failed']);
  exit;
}

$result = mysqli_stmt_get_result($stmt);
$comments = [];

while ($row = mysqli_fetch_assoc($result)) {
  // Set default profile image if empty
  if (empty($row['pfp_image_link'])) {
    $row['pfp_image_link'] = filePath("/assets/logos/") . " pixlshareLogo_white_128.png";
  }
  $comments[] = $row;
}

mysqli_stmt_close($stmt);
mysqli_close($conn_main);

echo json_encode([
  'comments' => $comments,
  'isAdmin' => ($_SESSION['user']['userLevel'] ?? 0) === 1
]);
