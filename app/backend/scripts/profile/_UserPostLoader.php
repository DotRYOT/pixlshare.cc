<?php
// Start the session
session_start();

// Include required files
require "../../_auth.php";
require "../../_include.php";
require "../../connect/MainConnect.php";

checkUserAuth($conn_main, "auth");

// Ensure the request method is GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
  http_response_code(405); // Method Not Allowed
  echo json_encode(['error' => 'Only GET requests are allowed']);
  exit;
}

// Check if UUID parameter is provided
if (isset($_GET['UUID'])) {
  $user = $_GET['UUID'];
} else {
  // If no UUID passed, use session user
  $user = $_SESSION['user']['UUID'] ?? null;
  checkUserAuth($conn_main, "auth");
}

// Validate user is authenticated
if (!$user) {
  http_response_code(401); // Unauthorized
  echo json_encode(['error' => 'Authentication required']);
  exit;
}

// Pagination parameters with validation
$limit = max(1, min(100, intval($_GET['limit'] ?? 10)));
$offset = max(0, intval($_GET['offset'] ?? 0));

// Prepare SQL query to fetch user's posts, excluding comments (CatFilterOption = 999)
$postsQuery = "
  SELECT posts.*, users.username, users.pfp_image_link 
  FROM posts 
  LEFT JOIN users ON posts.UUID = users.UUID 
  WHERE posts.UUID = ? 
    AND posts.CatFilterOption != 999 
  ORDER BY posts.Time DESC
  LIMIT ? OFFSET ?";

$stmt = mysqli_prepare($conn_main, $postsQuery);

if (!$stmt) {
  http_response_code(500); // Internal Server Error
  echo json_encode(['error' => 'Database error: Failed to prepare statement']);
  exit;
}

// Bind user UUID and pagination parameters to the query
mysqli_stmt_bind_param($stmt, 'sii', $user, $limit, $offset);

// Execute the query
if (!mysqli_stmt_execute($stmt)) {
  http_response_code(500); // Internal Server Error
  echo json_encode(['error' => 'Database error: Failed to execute query']);
  exit;
}

$result = mysqli_stmt_get_result($stmt);

// Check if query executed successfully
if (!$result) {
  http_response_code(500); // Internal Server Error
  echo json_encode(['error' => 'Database error: Failed to fetch results']);
  exit;
}

// Fetch all posts
$posts = [];
while ($row = mysqli_fetch_assoc($result)) {
  // Handle comment posts (CatFilterOption = 999) - though they're excluded in WHERE clause
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

// Clean up resources
mysqli_stmt_close($stmt);
mysqli_close($conn_main);

// Return JSON response
header('Content-Type: application/json');
echo json_encode(['posts' => $posts]);
?>