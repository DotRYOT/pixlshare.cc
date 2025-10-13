<?php
session_start();
require "../_auth.php";
require "../connect/MainConnect.php";

$user = $_SESSION['user']['UUID'] ?? null;

header('Content-Type: application/json');

if (!$user) {
  echo json_encode(['error' => 'Authentication required']);
  exit;
}

// Pagination parameters with validation
$limit = max(1, min(100, intval($_GET['limit'] ?? 5)));
$offset = max(0, intval($_GET['offset'] ?? 0));

// Get all users being followed
$followQuery = "SELECT following FROM followers WHERE user = ?";
$stmt = mysqli_prepare($conn_main, $followQuery);
if (!$stmt) {
  echo json_encode(['error' => 'Database error']);
  exit;
}
mysqli_stmt_bind_param($stmt, "s", $user);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$followedUsers = [];
while ($row = mysqli_fetch_assoc($result)) {
  $followedUsers[] = $row['following'];
}

mysqli_stmt_close($stmt);

if (empty($followedUsers)) {
  echo json_encode(['posts' => []]);
  exit;
}

// Get filter parameter
$filter = isset($_GET['filter']) ? (int) $_GET['filter'] : null;

// Build placeholders for IN clause
$placeholders = str_repeat('?,', count($followedUsers) - 1) . '?';
$types = str_repeat('s', count($followedUsers)) . 's'; // +1 for $user exclusion

// Construct query
$postsQuery = "
    SELECT posts.*, users.username, users.pfp_image_link 
    FROM posts 
    LEFT JOIN users ON posts.UUID = users.UUID 
    WHERE posts.UUID IN ($placeholders) 
    AND posts.UUID != ?
";

if ($filter !== null && $filter >= 0) {
  $postsQuery .= " AND CatFilterOption = ?";
  $types .= 'i';
}

$postsQuery .= " ORDER BY Time DESC LIMIT ? OFFSET ?";
$types .= 'ii';

// Prepare statement
$stmt = mysqli_prepare($conn_main, $postsQuery);
if (!$stmt) {
  echo json_encode(['error' => 'Database prepare failed']);
  exit;
}

// Build parameters
$params = array_merge($followedUsers, [$user]);
if ($filter !== null && $filter >= 0) {
  $params[] = $filter;
}
$params[] = $limit;
$params[] = $offset;

// Bind parameters safely
mysqli_stmt_bind_param($stmt, $types, ...$params);

// Execute and fetch results
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$posts = [];
while ($row = mysqli_fetch_assoc($result)) {
  // Check if the post is a comment (CatFilterOption = 999)
  if ($row['CatFilterOption'] == 999) {
    // Fetch original post details using OG_PUID from the comments table
    $commentQuery = "SELECT OG_PUID FROM comments WHERE COM_PUID = ?";
    $commentStmt = mysqli_prepare($conn_main, $commentQuery);
    if ($commentStmt) {
      mysqli_stmt_bind_param($commentStmt, "s", $row['PUID']);
      mysqli_stmt_execute($commentStmt);
      $commentResult = mysqli_stmt_get_result($commentStmt);
      $commentData = mysqli_fetch_assoc($commentResult);
      mysqli_stmt_close($commentStmt);

      // If OG_PUID exists in the comments table, fetch the original post details
      if ($commentData && isset($commentData['OG_PUID'])) {
        $originalPostQuery = "SELECT UUID AS OG_Posters_UUID, PUID AS OG_PUID FROM posts WHERE PUID = ?";
        $originalPostStmt = mysqli_prepare($conn_main, $originalPostQuery);
        if ($originalPostStmt) {
          mysqli_stmt_bind_param($originalPostStmt, "s", $commentData['OG_PUID']);
          mysqli_stmt_execute($originalPostStmt);
          $originalPostResult = mysqli_stmt_get_result($originalPostStmt);
          $originalPostData = mysqli_fetch_assoc($originalPostResult);
          mysqli_stmt_close($originalPostStmt);

          // Append OG_Posters_UUID and OG_PUID to the post data
          $row['OG_Posters_UUID'] = $originalPostData['OG_Posters_UUID'] ?? null;
          $row['OG_PUID'] = $originalPostData['OG_PUID'] ?? null;
        } else {
          $row['OG_Posters_UUID'] = null;
          $row['OG_PUID'] = null;
        }
      } else {
        // Set to null if no original post details are found
        $row['OG_Posters_UUID'] = null;
        $row['OG_PUID'] = null;
      }
    } else {
      $row['OG_Posters_UUID'] = null;
      $row['OG_PUID'] = null;
    }
  } else {
    // Set OG_Posters_UUID and OG_PUID to null for non-comment posts
    $row['OG_Posters_UUID'] = null;
    $row['OG_PUID'] = null;
  }

  $posts[] = $row;
}

// Close resources
mysqli_stmt_close($stmt);
mysqli_close($conn_main);

echo json_encode(['posts' => $posts]);
?>