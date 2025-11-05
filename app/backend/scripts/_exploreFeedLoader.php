<?php
session_start();
require "../_auth.php";
require "../_include.php";
require "../connect/MainConnect.php";

checkUserAuth($conn_main, "auth");

$user = $_SESSION['user']['UUID'] ?? null;
header('Content-Type: application/json');

if (!$user) {
  echo json_encode(['error' => 'Authentication required']);
  exit;
}

// Get all users being followed
$followQuery = "SELECT following FROM followers WHERE user = ?";
$stmt = mysqli_prepare($conn_main, $followQuery);
mysqli_stmt_bind_param($stmt, "s", $user);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$followedUsers = [];
while ($row = mysqli_fetch_assoc($result)) {
  $followedUsers[] = $row['following'];
}
mysqli_stmt_close($stmt);

// Handle the case where no users are followed
if (empty($followedUsers)) {
  $followedUsers = ['00000000-0000-0000-0000-000000000000'];
}

// Create placeholders for IN clause
$placeholders = implode(',', array_fill(0, count($followedUsers), '?'));
$types = str_repeat('s', count($followedUsers));

// Get filter, offset, and limit parameters
$filter = $_GET['filter'] ?? null;
$offset = max(0, (int) ($_GET['offset'] ?? 0));
$limit = min(50, (int) ($_GET['limit'] ?? 5)); // Cap limit to 50

// Modify query for explore page (NOT IN followed users and not self)
$postsQuery = "SELECT posts.*, users.username, users.pfp_image_link, users.userState 
               FROM posts 
               LEFT JOIN users ON posts.UUID = users.UUID 
               WHERE posts.UUID NOT IN ($placeholders) 
               AND posts.UUID != ?" .
  ($filter ? " AND CatFilterOption = ?" : "") .
  " ORDER BY Time DESC
               LIMIT ? OFFSET ?";

// Prepare the statement
$stmt = mysqli_prepare($conn_main, $postsQuery);

// Build parameters and types
$params = array_merge($followedUsers, [$user]);
$types = str_repeat('s', count($followedUsers)) . 's';
if ($filter) {
  $params[] = $filter;
  $types .= 's';
}
$params[] = $limit;
$params[] = $offset;
$types .= 'ii';

// Bind parameters
mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$posts = [];
while ($row = mysqli_fetch_assoc($result)) {
  // Check if the post is a comment (CatFilterOption = 999)
  if ($row['CatFilterOption'] == 999) {
    // Fetch original post details using OG_PUID from the comments table
    $commentQuery = "SELECT OG_PUID FROM comments WHERE COM_PUID = ?";
    $commentStmt = mysqli_prepare($conn_main, $commentQuery);
    mysqli_stmt_bind_param($commentStmt, "s", $row['PUID']);
    mysqli_stmt_execute($commentStmt);
    $commentResult = mysqli_stmt_get_result($commentStmt);
    $commentData = mysqli_fetch_assoc($commentResult);

    // If OG_PUID exists in the comments table, fetch the original post details
    if ($commentData && isset($commentData['OG_PUID'])) {
      $originalPostQuery = "SELECT UUID AS OG_Posters_UUID, PUID AS OG_PUID FROM posts WHERE PUID = ?";
      $originalPostStmt = mysqli_prepare($conn_main, $originalPostQuery);
      mysqli_stmt_bind_param($originalPostStmt, "s", $commentData['OG_PUID']);
      mysqli_stmt_execute($originalPostStmt);
      $originalPostResult = mysqli_stmt_get_result($originalPostStmt);
      $originalPostData = mysqli_fetch_assoc($originalPostResult);

      // Append OG_Posters_UUID and OG_PUID to the post data
      $row['OG_Posters_UUID'] = $originalPostData['OG_Posters_UUID'] ?? null;
      $row['OG_PUID'] = $originalPostData['OG_PUID'] ?? null;
    } else {
      // Set to null if no original post details are found
      $row['OG_Posters_UUID'] = null;
      $row['OG_PUID'] = null;
    }

    mysqli_stmt_close($commentStmt);
    mysqli_stmt_close($originalPostStmt);
  } else {
    // Set OG_Posters_UUID and OG_PUID to null for non-comment posts
    $row['OG_Posters_UUID'] = null;
    $row['OG_PUID'] = null;
  }

  $posts[] = $row;
}

// Close remaining resources
mysqli_stmt_close($stmt);
mysqli_close($conn_main);

echo json_encode(['posts' => $posts]);
?>