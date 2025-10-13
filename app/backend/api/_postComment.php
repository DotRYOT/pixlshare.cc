<?php

//
// This is being replaced with a script in the post dir
//

session_start();

require "../_auth.php";
require "../_include.php";
require "../connect/MainConnect.php";


if (!isset($_SESSION['user'])) {
  echo json_encode(["error" => "Session not set!"]);
  exit;
}

// Using this instead of the auth check for non-account members
if ($_SESSION['user']['UUID'] === null) {
  echo json_encode(["error" => "No account!"]);
  exit;
}

$PUID = mysqli_real_escape_string($conn_main, $_POST['PUID']);
$UUID = mysqli_real_escape_string($conn_main, $_SESSION['user']['UUID']);
$comment = htmlspecialchars($_POST['comment'], ENT_QUOTES, 'UTF-8');

// Check if the comment exceeds 500 characters
if (strlen($comment) > 500) {
  $Error = generateErrorUrl("Comment exceeds 1000 characters!");
  echo json_encode(["error" => $Error]);
  redirectTo("/home/$Error");
}

// Check for excessive line breaks
$lineBreakCount = substr_count($comment, "\n"); // Count newlines
if ($lineBreakCount > 5) { // Adjust the threshold as needed
  $Error = generateErrorUrl("Too many line breaks in comment!");
  echo json_encode(["error" => $Error]);
  redirectTo("/home/$Error");
}

if (empty($comment)) {
  $Error = generateErrorUrl("Comment empty!");
  echo json_encode(["error" => $Error]);
  redirectTo("/home/$Error");
}

$Time = time();

// Rate limiting logic
$rate_limit_seconds = 30;

// Fetch the last comment time for the user on this post
$sql = "SELECT Time FROM comments WHERE PUID = ? AND UUID = ? ORDER BY Time DESC LIMIT 1";
$stmt = mysqli_prepare($conn_main, $sql);
mysqli_stmt_bind_param($stmt, "ss", $PUID, $UUID);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $last_comment_time);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

// Check if the rate limit is exceeded
if ($last_comment_time && ($Time - $last_comment_time < $rate_limit_seconds)) {
  echo json_encode(["error" => "Rate limit exceeded. Please wait a few seconds before commenting again."]);
  exit;
}

// Insert the comment if rate limit is not exceeded
$sql = "INSERT INTO comments (PUID, UUID, content, Time) VALUES (?, ?, ?, ?)";
$stmt = mysqli_prepare($conn_main, $sql);
mysqli_stmt_bind_param($stmt, "ssss", $PUID, $UUID, $comment, $Time);
$success = mysqli_stmt_execute($stmt);

if ($success) {
  echo json_encode(["success" => "Comment Success!"]);
} else {
  echo json_encode(["error" => "Comment Error"]);
  $Error = generateErrorUrl("Comment Error");
  echo json_encode(["error" => $Error]);
  mysqli_stmt_close($stmt);
  redirectTo("/home/$Error");
}
