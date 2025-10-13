<?php

session_start();

require "../../_auth.php";
require "../../_include.php";
require "../../connect/MainConnect.php";

checkUserAuth($conn_main, "auth");

$UUID = $_SESSION['user']['UUID'];

// Helper function to convert images to WebP using FFmpeg
function convertImageToWebP($sourcePath, $destinationPath, $sourceExtension)
{
  try {
    $sourceExtension = strtolower($sourceExtension);

    switch ($sourceExtension) {
      case 'gif':
        // Convert animated GIF to animated WebP with loop support
        $command = "ffmpeg -i " . escapeshellarg($sourcePath) . " -quality 85 -loop 0 -pix_fmt yuva420p " . escapeshellarg($destinationPath) . " 2>&1";
        break;

      case 'jpg':
      case 'jpeg':
        // Convert JPG to WebP
        $command = "ffmpeg -i " . escapeshellarg($sourcePath) . " -quality 85 " . escapeshellarg($destinationPath) . " 2>&1";
        break;

      case 'png':
        // Convert PNG to WebP
        $command = "ffmpeg -i " . escapeshellarg($sourcePath) . " -quality 85 " . escapeshellarg($destinationPath) . " 2>&1";
        break;

      default:
        return false;
    }

    // Execute the command
    $output = [];
    $returnCode = 0;
    exec($command, $output, $returnCode);

    // Check if file was created successfully
    return $returnCode === 0 && file_exists($destinationPath) && filesize($destinationPath) > 0;

  } catch (Exception $e) {
    error_log("Image conversion error: " . $e->getMessage());
    error_log("FFmpeg command: " . $command);
    return false;
  }
}

// Fallback function using GD if FFmpeg is not available
function convertImageToWebPFallback($sourcePath, $destinationPath, $sourceExtension)
{
  try {
    // Create image resource based on source format
    switch (strtolower($sourceExtension)) {
      case 'jpg':
      case 'jpeg':
        $sourceImage = imagecreatefromjpeg($sourcePath);
        break;
      case 'png':
        $sourceImage = imagecreatefrompng($sourcePath);
        break;
      case 'gif':
        $sourceImage = imagecreatefromgif($sourcePath);
        break;
      default:
        return false;
    }

    if (!$sourceImage) {
      return false;
    }

    // Convert to WebP
    $result = imagewebp($sourceImage, $destinationPath, 85);
    imagedestroy($sourceImage);

    return $result && file_exists($destinationPath) && filesize($destinationPath) > 0;
  } catch (Exception $e) {
    error_log("Image conversion error: " . $e->getMessage());
    return false;
  }
}

if ($_SERVER['REQUEST_METHOD'] === "POST") {
  // Handle profile picture upload
  if (!empty($_FILES['profileImage']['name'])) {
    $file = $_FILES['profileImage'];
    $uploadDir = "../../../profile/u/$UUID/";

    if (!is_dir($uploadDir)) {
      mkdir($uploadDir, 0755, true);
    }

    $existingFiles = glob($uploadDir . "*_profile_*");
    foreach ($existingFiles as $existingFile) {
      if (is_file($existingFile)) {
        unlink($existingFile);
      }
    }

    if ($file['error'] === UPLOAD_ERR_OK) {
      $fileTmpPath = $file['tmp_name'];
      $fileOriginalName = basename($file['name']);
      $fileExtension = strtolower(pathinfo($fileOriginalName, PATHINFO_EXTENSION));

      $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

      if (in_array($fileExtension, $allowedExtensions)) {
        $targetFormat = 'webp';
        $uniqueImageID = uniqid($UUID . "_profile_", true);
        $newFileName = $uniqueImageID . "." . $targetFormat;

        $uploadFilePath = $uploadDir . $newFileName;
        $PFPImageLink = "profile/u/$UUID/" . $newFileName;

        // Try FFmpeg first, fallback to GD
        $conversionSuccess = false;
        if (shell_exec('which ffmpeg')) {
          $conversionSuccess = convertImageToWebP($fileTmpPath, $uploadFilePath, $fileExtension);
        } else {
          $conversionSuccess = convertImageToWebPFallback($fileTmpPath, $uploadFilePath, $fileExtension);
        }

        // Perform conversion to WebP
        if ($conversionSuccess && file_exists($uploadFilePath) && filesize($uploadFilePath) > 0) {
          // Update database
          $Imagesql = "UPDATE users SET pfp_image_link = ? WHERE uuid = ?";
          $ImageStmt = mysqli_prepare($conn_main, $Imagesql);

          if ($ImageStmt) {
            mysqli_stmt_bind_param($ImageStmt, "ss", $PFPImageLink, $UUID);
            mysqli_stmt_execute($ImageStmt);
            mysqli_stmt_close($ImageStmt);
          }

          // Update JSON
          $jsonFilePath = "../../../profile/u/$UUID/userData.json";
          if (file_exists($jsonFilePath)) {
            $jsonData = file_get_contents($jsonFilePath);
            $userData = json_decode($jsonData, true);

            if (json_last_error() === JSON_ERROR_NONE) {
              $userData['pfp_image_link'] = $PFPImageLink;
              file_put_contents($jsonFilePath, json_encode($userData, JSON_PRETTY_PRINT));
            }
          }
        } else {
          error_log("Conversion failed for profile image. File: " . $fileTmpPath . " -> " . $uploadFilePath);
          $Error = generateErrorUrl("Failed to process profile image");
          redirectTo("/profile/$Error");
        }
      } else {
        $Error = generateErrorUrl("Invalid profile image type");
        redirectTo("/profile/$Error");
      }
    } else {
      $Error = generateErrorUrl("Profile image upload error");
      redirectTo("/profile/$Error");
    }
  }

  // Handle background image upload
  if (!empty($_FILES['backgroundImage']['name'])) {
    $file = $_FILES['backgroundImage'];
    $uploadDir = "../../../profile/u/$UUID/";

    if (!is_dir($uploadDir)) {
      mkdir($uploadDir, 0755, true);
    }

    $existingFiles = glob($uploadDir . "*_background_*");
    foreach ($existingFiles as $existingFile) {
      if (is_file($existingFile)) {
        unlink($existingFile);
      }
    }

    if ($file['error'] === UPLOAD_ERR_OK) {
      $fileTmpPath = $file['tmp_name'];
      $fileOriginalName = basename($file['name']);
      $fileExtension = strtolower(pathinfo($fileOriginalName, PATHINFO_EXTENSION));

      $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

      if (in_array($fileExtension, $allowedExtensions)) {
        $targetFormat = 'webp';
        $uniqueImageID = uniqid($UUID . "_background_", true);
        $newFileName = $uniqueImageID . "." . $targetFormat;

        $uploadFilePath = $uploadDir . $newFileName;
        $BGImageLink = "profile/u/$UUID/" . $newFileName;

        // Try FFmpeg first, fallback to GD
        $conversionSuccess = false;
        if (shell_exec('which ffmpeg')) {
          $conversionSuccess = convertImageToWebP($fileTmpPath, $uploadFilePath, $fileExtension);
        } else {
          $conversionSuccess = convertImageToWebPFallback($fileTmpPath, $uploadFilePath, $fileExtension);
        }

        // Perform conversion to WebP
        if ($conversionSuccess && file_exists($uploadFilePath) && filesize($uploadFilePath) > 0) {
          // Update database
          $Imagesql = "UPDATE users SET bg_image_link = ? WHERE uuid = ?";
          $ImageStmt = mysqli_prepare($conn_main, $Imagesql);

          if ($ImageStmt) {
            mysqli_stmt_bind_param($ImageStmt, "ss", $BGImageLink, $UUID);
            mysqli_stmt_execute($ImageStmt);
            mysqli_stmt_close($ImageStmt);
          }

          // Update JSON
          $jsonFilePath = "../../../profile/u/$UUID/userData.json";
          if (file_exists($jsonFilePath)) {
            $jsonData = file_get_contents($jsonFilePath);
            $userData = json_decode($jsonData, true);

            if (json_last_error() === JSON_ERROR_NONE) {
              $userData['bg_image_link'] = $BGImageLink;
              file_put_contents($jsonFilePath, json_encode($userData, JSON_PRETTY_PRINT));
            }
          }
        } else {
          error_log("Conversion failed for background image. File: " . $fileTmpPath . " -> " . $uploadFilePath);
          $Error = generateErrorUrl("Failed to process background image");
          redirectTo("/profile/$Error");
        }
      } else {
        $Error = generateErrorUrl("Invalid background image type");
        redirectTo("/profile/$Error");
      }
    } else {
      $Error = generateErrorUrl("Background image upload error");
      redirectTo("/profile/$Error");
    }
  }

  // Handle bio update
  if (isset($_POST['bioText'])) {
    $bio = htmlspecialchars($_POST['bioText'], ENT_QUOTES, 'UTF-8');
    $bioSql = "UPDATE users SET profile_bio = ? WHERE uuid = ?";
    $bioStmt = mysqli_prepare($conn_main, $bioSql);

    if ($bioStmt) {
      mysqli_stmt_bind_param($bioStmt, "ss", $bio, $UUID);
      mysqli_stmt_execute($bioStmt);
      mysqli_stmt_close($bioStmt);
    }

    $jsonFilePath = "../../../profile/u/$UUID/userData.json";
    if (file_exists($jsonFilePath)) {
      $jsonData = file_get_contents($jsonFilePath);
      $userData = json_decode($jsonData, true);

      if (json_last_error() === JSON_ERROR_NONE) {
        $userData['profile_bio'] = $bio;
        file_put_contents($jsonFilePath, json_encode($userData, JSON_PRETTY_PRINT));
      }
    }
  }

  mysqli_close($conn_main);
  redirectTo("/profile/");
}
