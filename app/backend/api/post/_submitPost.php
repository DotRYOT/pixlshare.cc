<?php
// 
// This is the normal post script 
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
$UUID = mysqli_real_escape_string($conn_main, $_SESSION['user']['UUID']);
$CatFilterOption = filter_user_input($_POST['PostFilterOptions'], 'int');
$Time = time();
$postState = "0";
$image_id = "null";
$media_id = "null";
$youtubeEmbed = htmlspecialchars($_POST['linkData']);

// Cloudflare Turnstile verification
if ($_SERVER['HTTP_HOST'] == 'pixlshare.cc') {
  require "../../connect/_cloudFlareAPI.php";
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
  // exit;
  redirectTo("/account/signin/$Error");
}
if (strlen($postBody) > 800) {
  $Error = generateErrorUrl("Post exceeds 500 characters!");
  echo $Error;
  // exit;
  redirectTo("/home/$Error");
}

// Check for excessive line breaks
$lineBreakCount = substr_count($postBody, "\n");
if ($lineBreakCount > 15) {
  $Error = generateErrorUrl("Too many line breaks in post!");
  echo $Error;
  // exit;
  redirectTo("/home/$Error");
}

// Create post directory
$postDir = "../../../profile/u/{$UUID}/post/{$PUID}";
if (!is_dir($postDir)) {
  if (!mkdir($postDir, 0755, true)) {
    $Error = generateErrorUrl("Error creating post directory. Please contact support.");
    echo $Error;
    // exit;
    redirectTo("/home/$Error");
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

      if (in_array($fileExtension, $allowedExtensions)) {
        $newFileName = "img_$PUID.$fileExtension";
        $uploadFilePath = $uploadDir . $newFileName;

        // Move uploaded file temporarily to check dimensions
        if (move_uploaded_file($fileTmpPath, $uploadFilePath)) {
          // Handle image conversion to WebP
          if (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            if ($fileExtension === 'webp') {
              // If already WebP, just use the uploaded file
              $media_id = "/profile/u/{$UUID}/post/{$PUID}/" . $newFileName;
              $image_id = $media_id;
            } else {
              // Convert main image to WebP
              $webpFileName = "img_$PUID.webp";
              $webpFilePath = $uploadDir . $webpFileName;
              $ffmpegImageCommand = "ffmpeg -i " . escapeshellarg($uploadFilePath) . " -q:v 85 " . escapeshellarg($webpFilePath);
              exec($ffmpegImageCommand, $output, $returnVar);
              if ($returnVar === 0) {
                // Conversion successful
                unlink($uploadFilePath); // Remove original file
                $media_id = "/profile/u/{$UUID}/post/{$PUID}/" . $webpFileName;
                $image_id = $media_id; // For images, both are the same
              } else {
                error_log("Main image WebP conversion failed: " . implode("\n", $output));
                $Error = generateErrorUrl("Image processing failed.");
                redirectTo("/home/$Error");
                exit;
              }
            }
          }
          // Handle video processing
          else if ($fileExtension === 'mp4') {
            $cmd = "ffprobe -v error -select_streams v:0 -show_entries stream=width,height -of csv=s=x:p=0 " . escapeshellarg($uploadFilePath);
            exec($cmd, $output, $returnVar);

            if ($returnVar !== 0 || empty($output[0])) {
              unlink($uploadFilePath);
              // Delete post from database
              $deleteSql = "DELETE FROM posts WHERE PUID = ?";
              $deleteStmt = mysqli_prepare($conn_main, $deleteSql);
              mysqli_stmt_bind_param($deleteStmt, "s", $PUID);
              mysqli_stmt_execute($deleteStmt);
              mysqli_stmt_close($deleteStmt);

              $Error = generateErrorUrl("Error reading video dimensions.");
              redirectTo("/home/$Error");
              exit;
            }

            // Generate thumbnail - try multiple approaches
            $webpThumbName = "frame_$PUID.webp";
            $webpThumbPath = $uploadDir . $webpThumbName;

            // Approach 1: Direct WebP generation
            $ffmpegCommand1 = "ffmpeg -i " . escapeshellarg($uploadFilePath) . " -vf 'select=eq(n\\,0)' -q:v 3 -f webp " . escapeshellarg($webpThumbPath);
            exec($ffmpegCommand1 . " 2>&1", $output1, $returnVar1);

            if ($returnVar1 === 0 && file_exists($webpThumbPath)) {
              $image_id = "/profile/u/{$UUID}/post/{$PUID}/" . $webpThumbName;
            } else {
              // Approach 2: Generate JPG then convert to WebP
              $tempThumbName = "frame_$PUID.jpg";
              $tempThumbPath = $uploadDir . $tempThumbName;
              $ffmpegCommand2 = "ffmpeg -i " . escapeshellarg($uploadFilePath) . " -vf 'select=eq(n\\,0)' -q:v 3 " . escapeshellarg($tempThumbPath);

              exec($ffmpegCommand2 . " 2>&1", $output2, $returnVar2);

              if ($returnVar2 === 0 && file_exists($tempThumbPath)) {
                // Convert JPG to WebP
                $ffmpegConvertCommand = "ffmpeg -i " . escapeshellarg($tempThumbPath) . " -q:v 80 " . escapeshellarg($webpThumbPath);
                exec($ffmpegConvertCommand . " 2>&1", $convertOutput, $convertReturnVar);

                if ($convertReturnVar === 0 && file_exists($webpThumbPath)) {
                  unlink($tempThumbPath); // Remove temporary JPG
                  $image_id = "/profile/u/{$UUID}/post/{$PUID}/" . $webpThumbName;
                } else {
                  // Use JPG as fallback
                  $image_id = "/profile/u/{$UUID}/post/{$PUID}/" . $tempThumbName;
                }
              } else {
                $image_id = null;
              }
            }

            // Video file remains as MP4
            $media_id = "/profile/u/{$UUID}/post/{$PUID}/" . $newFileName;
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
        $Error = generateErrorUrl("Invalid file type: $fileExtension");
        echo $Error;
        redirectTo("/home/$Error");
      }
    } else {
      $Error = generateErrorUrl("Image upload error: " . $file['error']);
      echo $Error;
      redirectTo("/home/$Error");
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