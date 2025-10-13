<?php
session_start();
require "../../_auth.php";
require "../../_include.php";
require "../../connect/MainConnect.php";

header('Content-Type: application/json');

// Start with error checking
try {
  // Auth check
  checkUserAuth($conn_main, "auth");
  $UUID = $_SESSION['user']['UUID'];
  $OG_PUID = mysqli_real_escape_string($conn_main, $_GET['OG_PUID'] ?? '');

  if (!$OG_PUID) {
    throw new Exception("Missing PUID");
  }

  // Query with error handling
  $sql = "
        SELECT 
            p.PUID AS COM_PUID,
            p.content,
            p.media_id,
            p.image_id,
            p.Time,
            u.UUID,
            u.username,
            u.pfp_image_link,
            IF(l.UUID IS NOT NULL, 1, 0) AS liked,
            (SELECT COUNT(*) FROM likes WHERE likes.PUID = p.PUID) AS likeCount
        FROM comments c
        JOIN posts p ON c.COM_PUID = p.PUID
        JOIN users u ON p.UUID = u.UUID
        LEFT JOIN likes l ON p.PUID = l.PUID AND l.UUID = ?
        WHERE c.OG_PUID = ?
        ORDER BY c.Time DESC
    ";

  $stmt = mysqli_prepare($conn_main, $sql);
  if (!$stmt) {
    throw new Exception("SQL prepare failed: " . mysqli_error($conn_main));
  }

  mysqli_stmt_bind_param($stmt, "ss", $UUID, $OG_PUID);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);

  $comments = [];
  while ($row = mysqli_fetch_assoc($result)) {
    $comments[] = $row;
  }

  echo json_encode(['comments' => $comments, 'isAdmin' => false]);

} catch (Exception $e) {
  http_response_code(500);
  echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
?>