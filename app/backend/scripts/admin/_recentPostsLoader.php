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

// Pagination parameters
$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 10;
$offset = isset($_GET['offset']) ? (int) $_GET['offset'] : 0;

// Get filter parameter
$filter = isset($_GET['filter']) ? (int) $_GET['filter'] : null;

// Query to get the latest posts from ALL users (including current user)
$postsQuery = "
    SELECT posts.*, users.username, users.pfp_image_link 
    FROM posts 
    LEFT JOIN users ON posts.UUID = users.UUID
";

$whereAdded = false;

if ($filter !== null && $filter >= 0) {
  $postsQuery .= " WHERE CatFilterOption = ?";
  $whereAdded = true;
}

$postsQuery .= " ORDER BY Time DESC LIMIT ? OFFSET ?";

$stmt = mysqli_prepare($conn_main, $postsQuery);
if (!$stmt) {
  echo json_encode(['error' => 'Database error']);
  exit;
}

// Parameters
$params = [];
$types = '';

if ($filter !== null && $filter >= 0) {
  $params[] = $filter;
  $types .= 'i';
}

$params[] = $limit;
$params[] = $offset;
$types .= 'ii';

// Bind parameters dynamically
$refs = [];
foreach ($params as $i => $val) {
  $refs[$i] = &$params[$i];
}
array_unshift($refs, $types);
call_user_func_array([$stmt, 'bind_param'], $refs);

// Execute query
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$posts = [];
while ($row = mysqli_fetch_assoc($result)) {
  $posts[] = $row;
}

// Close resources
mysqli_stmt_close($stmt);
mysqli_close($conn_main);

echo json_encode(['posts' => $posts]);