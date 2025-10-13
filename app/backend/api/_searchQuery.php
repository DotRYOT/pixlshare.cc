<?php

session_start();

require "../_auth.php";
require "../_include.php";
require "../connect/MainConnect.php";

header('Content-Type: application/json');

if (isset($_GET['query'])) {
  $searchQuery = trim($_GET['query']);

  if (empty($searchQuery)) {
    echo json_encode([
      "error" => "Please enter a search query"
    ]);
    exit;
  }

  $results = [];

  // Search posts by content with correct join
  $sqlPosts = "SELECT p.id, p.content, p.Time, p.UUID as post_uuid, p.PUID, p.image_id,
               u.id as user_id, u.username, u.UUID as user_uuid, u.pfp_image_link 
               FROM posts p 
               LEFT JOIN users u ON p.UUID = u.UUID 
               WHERE p.content LIKE ? 
               ORDER BY p.Time DESC";
  $stmtPosts = mysqli_prepare($conn_main, $sqlPosts);

  if ($stmtPosts) {
    $searchParam = "%" . $searchQuery . "%";
    mysqli_stmt_bind_param($stmtPosts, "s", $searchParam);
    mysqli_stmt_execute($stmtPosts);

    $resultPosts = mysqli_stmt_get_result($stmtPosts);
    $results['posts'] = [];

    while ($row = mysqli_fetch_assoc($resultPosts)) {
      $results['posts'][] = [
        "id" => $row['id'],
        "content" => $row['content'],
        "time" => $row['Time'],
        "PUID" => $row['PUID'],
        "post_uuid" => $row['post_uuid'],
        "image_id" => $row['image_id'] ?? null, // Added image_id
        "user_id" => $row['user_id'] ?? null,
        "username" => $row['username'] ?? null,
        "user_uuid" => $row['user_uuid'] ?? null,
        "pfp_image_link" => $row['pfp_image_link'] ?? null // Added PFP link
      ];
    }

    mysqli_stmt_close($stmtPosts);
  }

  // Search users by username
  $sqlUsers = "SELECT id, username, UUID, pfp_image_link, bg_image_link FROM users WHERE username LIKE ?";
  $stmtUsers = mysqli_prepare($conn_main, $sqlUsers);

  if ($stmtUsers) {
    $searchParam = "%" . $searchQuery . "%";
    mysqli_stmt_bind_param($stmtUsers, "s", $searchParam);
    mysqli_stmt_execute($stmtUsers);

    $resultUsers = mysqli_stmt_get_result($stmtUsers);
    $results['users'] = [];

    while ($row = mysqli_fetch_assoc($resultUsers)) {
      $results['users'][] = [
        "id" => $row['id'],
        "username" => $row['username'],
        "uuid" => $row['UUID'],
        "pfp_image_link" => $row['pfp_image_link'],
        "bg_image_link" => $row['bg_image_link']
      ];
    }

    mysqli_stmt_close($stmtUsers);
  }

  echo json_encode($results);

  mysqli_close($conn_main);
} else {
  echo json_encode([
    "error" => "No search query provided."
  ]);
}
?>