<?php
session_start();
require "../../_auth.php";
require "../../_include.php";
require "../../connect/MainConnect.php";



checkUserAuth($conn_main, "auth");

$user = $_SESSION['user']['UUID'] ?? null;

header('Content-Type: application/json');

if (!$user) {
  echo json_encode(['error' => 'Authentication required']);
  exit;
}

// Pagination parameters with validation
$limit = max(1, min(100, intval($_GET['limit'] ?? 10)));
$offset = max(0, intval($_GET['offset'] ?? 0));

// Modify posts query to include filter condition and pagination
$postsQuery = "SELECT posts.*, users.username, users.pfp_image_link, 
                      IF(likes.UUID IS NOT NULL, 1, 0) AS liked 
               FROM posts 
               LEFT JOIN users ON posts.UUID = users.UUID 
               INNER JOIN likes ON posts.PUID = likes.PUID AND likes.UUID = ? 
               WHERE likes.UUID IS NOT NULL 
               ORDER BY likes.Time DESC
               LIMIT ? OFFSET ?";

// Prepare the statement
$stmt = mysqli_prepare($conn_main, $postsQuery);

if (!$stmt) {
  http_response_code(500);
  echo json_encode(['error' => 'Database error: Failed to prepare statement']);
  exit;
}

// Bind parameters (user UUID, limit, offset)
mysqli_stmt_bind_param($stmt, 'sii', $user, $limit, $offset);

// Execute the query
if (!mysqli_stmt_execute($stmt)) {
  http_response_code(500);
  echo json_encode(['error' => 'Database error: Failed to execute query']);
  exit;
}

$result = mysqli_stmt_get_result($stmt);

if (!$result) {
  http_response_code(500);
  echo json_encode(['error' => 'Database error: Failed to fetch results']);
  exit;
}

$posts = [];
while ($row = mysqli_fetch_assoc($result)) {
  $posts[] = $row;
}

// Close remaining resources
mysqli_stmt_close($stmt);
mysqli_close($conn_main);

echo json_encode(['posts' => $posts]);
?>