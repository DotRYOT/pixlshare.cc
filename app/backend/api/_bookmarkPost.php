<?php
session_start();

require "../_auth.php";
require "../_include.php";
require "../connect/MainConnect.php";

// Get user ID and action from session and request
$UUID = isset($_SESSION['user']['UUID']) ? $_SESSION['user']['UUID'] : null; // Ensure user is logged in
$puid = isset($_GET['PUID']) ? $_GET['PUID'] : null; // Get the PUID from the request
$action = isset($_GET['action']) ? $_GET['action'] : null; // Get the action (bookmark or unbookmark)

// Validate inputs
if (!$UUID || !$puid || !in_array($action, ['bookmark', 'unbookmark'])) {
  http_response_code(400);
  echo json_encode(['error' => 'Invalid input']);
  exit;
}

// Fetch current bookmarks
$sql = "SELECT bookmark FROM users WHERE UUID = ?";
$stmt = mysqli_prepare($conn_main, $sql);
mysqli_stmt_bind_param($stmt, "s", $UUID);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $currentBookmarksJson);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

// Decode the JSON into an array
$currentBookmarks = $currentBookmarksJson ? json_decode($currentBookmarksJson, true) : [];

// Modify the bookmarks array based on the action
if ($action === 'bookmark') {
  // Add PUID if not already in the array
  if (!in_array($puid, $currentBookmarks)) {
    $currentBookmarks[] = $puid; // Add PUID to bookmarks
  }
} elseif ($action === 'unbookmark') {
  // Remove PUID if it exists in the array
  $currentBookmarks = array_values(array_diff($currentBookmarks, [$puid])); // Remove PUID from bookmarks
}

// Encode the array back to JSON
$new_bookmark_json = json_encode(array_values($currentBookmarks)); // Re-index the array

// Update the database with the new bookmarks
$sql = "UPDATE users SET bookmark = ? WHERE UUID = ?";
$stmt = mysqli_prepare($conn_main, $sql);
mysqli_stmt_bind_param($stmt, "ss", $new_bookmark_json, $UUID);
$success = mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

// Check if the update was successful
if ($success) {
  echo json_encode(['message' => 'Bookmarks updated successfully.']);
} else {
  echo json_encode(['error' => 'Failed to update bookmarks.']);
}

// Close the database connection
mysqli_close($conn_main);
