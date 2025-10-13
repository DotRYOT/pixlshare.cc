<?php

//
// This is the submit comment API
//

session_start();
require "../../_auth.php";
require "../../_include.php";
require "../../connect/MainConnect.php";

// Check user authentication and rate limit
checkUserAuth($conn_main, "auth");
RateLimitUser();

// Sanitize POST data
$postBody = htmlspecialchars($_POST['postBody'], ENT_QUOTES, 'UTF-8');
$PUID = randStringGen(26, 'numbers');
$OG_UUID = htmlspecialchars($_POST['OG_UUID'], ENT_QUOTES, 'UTF-8');
$OG_PUID = htmlspecialchars($_POST['OG_PUID'], ENT_QUOTES, 'UTF-8');
$UUID = mysqli_real_escape_string($conn_main, $_SESSION['user']['UUID']);
$CatFilterOption = "999";
$Time = time();
$postState = "0";
$image_id = null;
$media_id = null;
$youtubeEmbed = htmlspecialchars($_POST['commentLinkData'] ?? '', ENT_QUOTES, 'UTF-8');

$page_url = "/profile/u/" . $OG_UUID . "/post/" . $OG_PUID . "/";

// Cloudflare Turnstile verification
if ($_SERVER['HTTP_HOST'] === 'pixlshare.cc') {
  require "../../connect/_cloudFlareAPI.php";
  $secretKey = $CloudFlairAPI;
  $CFTurnstileResponse = $_POST['cf-turnstile-response'] ?? '';

  $curl = curl_init();
  curl_setopt_array($curl, [
    CURLOPT_URL => "https://challenges.cloudflare.com/turnstile/v0/siteverify",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => http_build_query([
      'secret' => $secretKey,
      'response' => $CFTurnstileResponse,
    ]),
  ]);
  $response = curl_exec($curl);
  $err = curl_error($curl);
  curl_close($curl);

  if ($err) {
    exit("Turnstile cURL Error: $err");
  }

  $responseData = json_decode($response, true);
  if (empty($responseData['success'])) {
    exit("Turnstile verification failed: " . json_encode($responseData['error-codes']));
  }
}

// Validate post content
if (empty($postBody)) {
  $Error = generateErrorUrl("Missing Text!");
  $url = $page_url . $Error;
  redirectTo($url);
  echo $url;
  exit;
}

if (strlen($postBody) > 800) {
  $Error = generateErrorUrl("Post exceeds 800 characters!");
  echo $Error;
  exit;
}

// Check for excessive line breaks
$lineBreakCount = substr_count($postBody, "\n");
if ($lineBreakCount > 15) {
  $Error = generateErrorUrl("Too many line breaks in post!");
  echo $Error;
  exit;
}

// Create post directory
$postDir = "../../../profile/u/{$UUID}/post/{$PUID}";
if (!is_dir($postDir)) {
  if (!mkdir($postDir, 0755, true)) {
    error_log("Failed to create directory: $postDir");
    echo "Error creating post directory.";
    exit;
  }
}

// Insert into `posts` table
$sql = "INSERT INTO posts (PUID, UUID, CatFilterOption, content, Time, postState, image_id, media_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = mysqli_prepare($conn_main, $sql);
mysqli_stmt_bind_param($stmt, "ssssssss", $PUID, $UUID, $CatFilterOption, $postBody, $Time, $postState, $image_id, $media_id);
$success = mysqli_stmt_execute($stmt);

// Insert into `comments` table
$sql = "INSERT INTO comments (OG_PUID, COM_PUID, Time) VALUES (?, ?, ?)";
$stmt = mysqli_prepare($conn_main, $sql);
mysqli_stmt_bind_param($stmt, "sss", $OG_PUID, $PUID, $Time);
$success = mysqli_stmt_execute($stmt);

if ($success) {
  // Only handle media if uploaded
  if (!empty($youtubeEmbed)) {
    $Imagesql = "UPDATE posts SET image_id = ? WHERE PUID = ?";
    $ImageStmt = mysqli_prepare($conn_main, $Imagesql);
    mysqli_stmt_bind_param($ImageStmt, "ss", $youtubeEmbed, $PUID);
    mysqli_stmt_execute($ImageStmt);
    mysqli_stmt_close($ImageStmt);
  } elseif (isset($_FILES['images']) && $_FILES['images']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['images'];
    $fileOriginalName = basename($file['name']);
    $fileExtension = strtolower(pathinfo($fileOriginalName, PATHINFO_EXTENSION));
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'mp4'];

    if (in_array($fileExtension, $allowedExtensions)) {
      $newFileName = "img_$PUID.$fileExtension";
      $uploadFilePath = "$postDir/$newFileName";

      if (move_uploaded_file($file['tmp_name'], $uploadFilePath)) {
        if ($fileExtension === 'mp4') {
          $frameFileName = "frame_$PUID.jpg";
          $frameFilePath = "$postDir/$frameFileName";
          $ffmpegCommand = "ffmpeg -i " . escapeshellarg($uploadFilePath) . " -vf 'select=eq(n\\,0)' -q:v 3 " . escapeshellarg($frameFilePath);
          exec($ffmpegCommand, $output, $returnVar);

          if ($returnVar !== 0) {
            error_log("Error generating thumbnail: " . implode("\n", $output));
            echo "Error generating thumbnail.";
            exit;
          }

          $image_id = "/profile/u/{$UUID}/post/{$PUID}/" . $frameFileName;
        }

        $media_id = "/profile/u/{$UUID}/post/{$PUID}/" . $newFileName;

        if ($fileExtension !== 'mp4') {
          $image_id = $media_id;
        }

        // Update media paths
        $Imagesql = "UPDATE posts SET media_id = ?, image_id = ? WHERE PUID = ?";
        $ImageStmt = mysqli_prepare($conn_main, $Imagesql);
        mysqli_stmt_bind_param($ImageStmt, "sss", $media_id, $image_id, $PUID);
        mysqli_stmt_execute($ImageStmt);
        mysqli_stmt_close($ImageStmt);
      } else {
        error_log("Error moving file to $uploadFilePath");
        echo "Error moving uploaded file.";
        exit;
      }
    } else {
      echo "Invalid file type: $fileExtension";
      exit;
    }
  }

  // Create index.php
  $indexContent = '<?php require "../../../../../backend/Files/repoPages/userPostPage/userPostPage.php";';
  file_put_contents($postDir . "/index.php", $indexContent);

  // Create index.json
  $Data = [
    'PUID' => $PUID,
    'UUID' => $UUID,
    'CatFilterOption' => $CatFilterOption,
    'content' => $postBody,
    'postState' => "0",
    'link' => $youtubeEmbed,
    'image_id' => $image_id,
    'media_id' => $media_id
  ];
  file_put_contents($postDir . "/index.json", json_encode($Data));

  // Redirect
  $success = generateGetUrl("posted");
  redirectTo("/profile/u/$OG_UUID/post/$OG_PUID/#$PUID");
  echo $success;
  exit;
} else {
  echo "Database error while posting comment.";
}

// Close database connection
mysqli_close($conn_main);
?>