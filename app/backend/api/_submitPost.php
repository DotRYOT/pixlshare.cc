<?php

//
// This is being replaced with a script in the post dir
//

session_start();
require "../_auth.php";
require "../_include.php";
require "../connect/MainConnect.php";

// Check user authentication and rate limit
checkUserAuth($conn_main, "auth");
RateLimitUser();

// Sanitize POST data
$postBody = htmlspecialchars($_POST['postBody'], ENT_QUOTES, 'UTF-8');
$PUID = randStringGen(26, 'numbers');
$UUID = mysqli_real_escape_string($conn_main, $_SESSION['user']['UUID']);
$CatFilterOption = filter_user_input($_POST['PostFilterOptions'], 'int');
$Time = time();
$postState = "0";
$image_id = "null";
$media_id = "null";
$youtubeEmbed = htmlspecialchars($_POST['linkData']);

// Cloudflare Turnstile verification
if ($_SERVER['HTTP_HOST'] == 'pixlshare.cc') {
  require "../connect/_cloudFlareAPI.php";
  $secretKey = $CloudFlairAPI;
  $CFTurnstileResponse = $_POST['cf-turnstile-response'];
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
  $Error = generateErrorUrl("Bad Data!");
  echo $Error;
  exit;
  // redirectTo("/account/signin/$Error");
}
if (strlen($postBody) > 800) {
  $Error = generateErrorUrl("Post exceeds 500 characters!");
  echo $Error;
  exit;
  // redirectTo("/home/$Error");
}

// Check for excessive line breaks
$lineBreakCount = substr_count($postBody, "\n");
if ($lineBreakCount > 15) {
  $Error = generateErrorUrl("Too many line breaks in post!");
  echo $Error;
  exit;
  // redirectTo("/home/$Error");
}

// Create post directory
$postDir = "../../profile/u/{$UUID}/post/{$PUID}";
if (!is_dir($postDir)) {
  if (!mkdir($postDir, 0755, true)) {
    error_log("Failed to create directory: $postDir");
    echo "Error creating post directory. Please contact support.";
    exit;
  }
}

// Insert post into the database
$sql = "INSERT INTO posts (PUID, UUID, CatFilterOption, content, Time, postState, image_id, media_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = mysqli_prepare($conn_main, $sql);
mysqli_stmt_bind_param($stmt, "ssssssss", $PUID, $UUID, $CatFilterOption, $postBody, $Time, $postState, $image_id, $media_id);
$success = mysqli_stmt_execute($stmt);

if ($success) {
  // Handle file uploads
  if (empty($youtubeEmbed) && isset($_FILES['images'])) {
    $file = $_FILES['images'];
    $uploadDir = "$postDir/";
    if ($file['error'] === UPLOAD_ERR_OK) {
      $fileTmpPath = $file['tmp_name'];
      $fileOriginalName = basename($file['name']);
      $fileExtension = strtolower(pathinfo($fileOriginalName, PATHINFO_EXTENSION));
      $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'mp4'];

      /* 
      This was un-needed dont know why I had this
      */
      // Validate file type
      // $finfo = finfo_open(FILEINFO_MIME_TYPE);
      // $mime = finfo_file($finfo, $fileTmpPath);
      // finfo_close($finfo);
      // if (!in_array($mime, ['image/jpeg', 'image/png', 'image/gif', 'video/mp4'])) {
      //   echo "Invalid file type.";
      //   exit;
      // }

      if (in_array($fileExtension, $allowedExtensions)) {
        $newFileName = "img_$PUID.$fileExtension";
        $uploadFilePath = $uploadDir . $newFileName;

        if (move_uploaded_file($fileTmpPath, $uploadFilePath)) {
          if ($fileExtension === 'mp4') {
            $frameFileName = "frame_$PUID.jpg";
            $frameFilePath = $uploadDir . $frameFileName;
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

          // Update database with media paths
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
    } else {
      echo "Image upload error: " . $file['error'];
      exit;
    }
  }

  // Create index.php in the post directory
  $indexContent = '<?php require "../../../../../backend/Files/repoPages/userPostPage/userPostPage.php";';
  file_put_contents($postDir . "/index.php", $indexContent);

  // Handle YouTube embed link
  if (!empty($youtubeEmbed)) {
    $Imagesql = "UPDATE posts SET image_id = ? WHERE PUID = ?";
    $ImageStmt = mysqli_prepare($conn_main, $Imagesql);
    mysqli_stmt_bind_param($ImageStmt, "ss", $youtubeEmbed, $PUID);
    mysqli_stmt_execute($ImageStmt);
    mysqli_stmt_close($ImageStmt);
  }

  // Create index.json with post data
  $Data = [
    'PUID' => $PUID,
    'UUID' => $UUID,
    'CatFilterOption' => $CatFilterOption,
    'content' => $postBody,
    'postState' => "0",
    'link' => $youtubeEmbed,
    'image_id' => isset($image_id) ? $image_id : null,
    'media_id' => isset($media_id) ? $media_id : null
  ];
  file_put_contents($postDir . "/index.json", json_encode($Data));

  // Redirect to the new post's page
  $success = generateGetUrl("posted");
  redirectTo("/profile/u/$UUID/post/$PUID/");
  echo $success;
  exit;
} else {
  echo "Error DB or no file uploaded";
}

// Close database connection
mysqli_close($conn_main);
?>