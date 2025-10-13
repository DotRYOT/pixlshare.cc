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
  $user = $_GET['UUID']; // Removed mysqli_real_escape_string as we use prepared statements
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
$limit = max(1, min(100, intval($_GET['limit'] ?? 5)));
$offset = max(0, intval($_GET['offset'] ?? 0));

// Prepare SQL query to fetch user's comments (CatFilterOption = 999)
// Join with comments table to get OG_PUID
// Join with posts table (as og_post) to get OG_Posters_UUID
// Join with posts table (as og_post_content) to get a snippet of the original post content
$postsQuery = "
  SELECT 
    posts.*,
    users.username,
    users.pfp_image_link,
    comments.OG_PUID,
    og_post.UUID AS OG_Posters_UUID,
    SUBSTRING(og_post_content.content, 1, 100) AS original_post_snippet
  FROM posts
  LEFT JOIN users ON posts.UUID = users.UUID
  INNER JOIN comments ON posts.PUID = comments.COM_PUID -- Use INNER JOIN for comments
  LEFT JOIN posts AS og_post ON comments.OG_PUID = og_post.PUID
  LEFT JOIN posts AS og_post_content ON comments.OG_PUID = og_post_content.PUID -- For snippet
  WHERE posts.UUID = ?
    AND posts.CatFilterOption = 999 -- Filter for comments
  ORDER BY posts.Time DESC
  LIMIT ? OFFSET ?";

$stmt = mysqli_prepare($conn_main, $postsQuery);

if (!$stmt) {
  http_response_code(500); // Internal Server Error
  echo json_encode(['error' => 'Database error: Failed to prepare statement']);
  exit;
}

// Bind user UUID and pagination parameters to the query
mysqli_stmt_bind_param($stmt, 'sii', $user, $limit, $offset); // 's' for string (UUID), 'i' for integer (limit), 'i' for integer (offset)

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

// Fetch all comments
$comments = [];
while ($row = mysqli_fetch_assoc($result)) {
  $comments[] = $row;
}

// Clean up resources
mysqli_stmt_close($stmt);
mysqli_close($conn_main);

// Return JSON response with 'comments' key
header('Content-Type: application/json');
echo json_encode(['comments' => $comments]);
?>