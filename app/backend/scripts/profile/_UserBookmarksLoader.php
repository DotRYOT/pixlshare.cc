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

// Get filter parameter
$filter = isset($_GET['filter']) ? (int) $_GET['filter'] : null;

// Get user's bookmarks from users table
$userQuery = "SELECT bookmark FROM users WHERE UUID = ?";
$stmt = mysqli_prepare($conn_main, $userQuery);
if (!$stmt) {
  http_response_code(500);
  echo json_encode(['error' => 'Database error: Failed to prepare user query']);
  exit;
}
mysqli_stmt_bind_param($stmt, 's', $user);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$userData = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt); // Close user query statement

$bookmarks = json_decode($userData['bookmark'] ?? '[]', true);

if (empty($bookmarks)) {
  echo json_encode(['posts' => []]);
  exit;
}

// Modify posts query to use bookmarks and add pagination
$placeholders = str_repeat('?,', count($bookmarks) - 1) . '?';
$types = str_repeat('s', count($bookmarks)); // PUIDs are strings

$postsQuery = "SELECT posts.*, users.username, users.pfp_image_link 
               FROM posts 
               LEFT JOIN users ON posts.UUID = users.UUID 
               WHERE posts.PUID IN ($placeholders)";

if ($filter !== null && $filter >= 0) {
  $postsQuery .= " AND CatFilterOption = ?";
  $types .= 'i'; // Filter is integer
}

$postsQuery .= " ORDER BY Time DESC LIMIT ? OFFSET ?";
$types .= 'ii'; // limit and offset are integers

// Prepare the statement for posts query
$stmt = mysqli_prepare($conn_main, $postsQuery);
if (!$stmt) {
  http_response_code(500);
  echo json_encode(['error' => 'Database error: Failed to prepare posts query']);
  exit;
}

// Build parameters for posts query
$params = $bookmarks;
if ($filter !== null && $filter >= 0) {
  $params[] = $filter;
}
$params[] = $limit;
$params[] = $offset;

// Bind parameters for posts query
if (!mysqli_stmt_bind_param($stmt, $types, ...$params)) {
  http_response_code(500);
  echo json_encode(['error' => 'Database error: Failed to bind parameters']);
  exit;
}

// Execute the posts query
if (!mysqli_stmt_execute($stmt)) {
  http_response_code(500);
  echo json_encode(['error' => 'Database error: Failed to execute posts query']);
  exit;
}

$result = mysqli_stmt_get_result($stmt);

if (!$result) {
  http_response_code(500);
  echo json_encode(['error' => 'Database error: Failed to fetch posts results']);
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