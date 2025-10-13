<?php
function AddNewLike($UUID, $PUID, $conn_main, $Type = 'like')
{
  $UUID = mysqli_real_escape_string($conn_main, $UUID);
  $PUID = mysqli_real_escape_string($conn_main, $PUID);

  $userDir = "../../profile/u/$UUID";
  $notificationsFile = "$userDir/notifications.json";

  if (!is_dir($userDir)) {
    return false;
  }

  if (!file_exists($notificationsFile)) {
    $defaultData = ["notifications" => (object) [], "totalCount" => 0];
    file_put_contents($notificationsFile, json_encode($defaultData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
  }

  $jsonData = file_get_contents($notificationsFile);
  $notificationsData = json_decode($jsonData, true);

  if (json_last_error() !== JSON_ERROR_NONE || !isset($notificationsData['notifications'])) {
    return false;
  }

  // 🔍 Handle different notification types
  $postPreview = "/assets/logos/ascii-pixlshareLogo_color_1padding_.png";
  $actionUrl = "/profile/u/$UUID/post/" . urlencode($PUID);

  if ($Type === 'comment') {
    // Load comment data
    $commentFile = "$userDir/comment/$PUID/index.json";
    if (file_exists($commentFile)) {
      $commentJson = file_get_contents($commentFile);
      $commentData = json_decode($commentJson, true);
      if (!empty($commentData['image_id'])) {
        $postPreview .= '/assets/logos/ascii-pixlshareLogo_color_1padding_.png';
      }
    }
    $actionUrl = "/profile/u/$UUID/post/" . urlencode($PUID);
  } else {
    // Post like (your existing logic)
    $postIndexFile = "$userDir/post/$PUID/index.json";
    if (file_exists($postIndexFile)) {
      $postJson = file_get_contents($postIndexFile);
      $postData = json_decode($postJson, true);
      if (!empty($postData['image_id'])) {
        $postPreview = $postData['image_id'];
      }
    }
  }

  // Check for existing unread notification
  $existingNotifId = null;
  foreach ($notificationsData['notifications'] as $id => $notif) {
    if ($notif['type'] === $Type && $notif['PUID'] === $PUID && !$notif['read']) {
      $existingNotifId = $id;
      break;
    }
  }

  if ($existingNotifId !== null) {
    $notificationsData['notifications'][$existingNotifId]['timestamp'] = gmdate('c');
  } else {
    $notifId = $Type . '_' . bin2hex(random_bytes(8)) . '_' . time();
    $notificationsData['notifications'][$notifId] = [
      "PUID" => $PUID,
      "type" => $Type,
      "postPreview" => $postPreview,
      "timestamp" => gmdate('c'),
      "read" => false,
      "actionUrl" => $actionUrl
    ];
    $notificationsData['totalCount'] = count((array) $notificationsData['notifications']);
  }

  $jsonOutput = json_encode($notificationsData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
  return $jsonOutput !== false && file_put_contents($notificationsFile, $jsonOutput) !== false;
}
function RemoveLikeNotification($UUID, $PUID, $conn_main, $Path)
{
  $UUID = mysqli_real_escape_string($conn_main, $UUID);
  $PUID = mysqli_real_escape_string($conn_main, $PUID);

  $userDir = $Path . "/profile/u/$UUID";
  $notificationsFile = "$userDir/notifications.json";

  if (!file_exists($notificationsFile)) {
    return true;
  }

  $jsonData = file_get_contents($notificationsFile);
  $notificationsData = json_decode($jsonData, true);

  if (json_last_error() !== JSON_ERROR_NONE || !isset($notificationsData['notifications'])) {
    return false;
  }

  $changed = false;
  foreach ($notificationsData['notifications'] as $id => $notif) {
    if ($notif['PUID'] === $PUID && ($notif['type'] === 'like' || $notif['type'] === 'comment')) {
      unset($notificationsData['notifications'][$id]);
      $changed = true;
    }
  }

  if ($changed) {
    $notificationsData['totalCount'] = count((array) $notificationsData['notifications']);
    $jsonOutput = json_encode($notificationsData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    if ($jsonOutput === false) {
      return false;
    }
    return file_put_contents($notificationsFile, $jsonOutput) !== false;
  }

  return true;
}
?>