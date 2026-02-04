<?php
session_start();

require "../../../backend/connect/MainConnect.php";
require "../../../backend/_include.php";
require "../../../backend/_auth.php";

// Check if user is authenticated
if (!isset($_SESSION['user']['UUID'])) {
  http_response_code(401);
  echo json_encode(['error' => 'Unauthorized']);
  exit;
}

$UUID = mysqli_real_escape_string($conn_main, $_SESSION['user']['UUID']);

$sql = "SELECT user_phrase FROM users WHERE UUID = ?";
$stmt = mysqli_prepare($conn_main, $sql);
mysqli_stmt_bind_param($stmt, "s", $UUID);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $userPhrase);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

if ($userPhrase) {
  echo $userPhrase;
} else {
  echo "null";
}
?>