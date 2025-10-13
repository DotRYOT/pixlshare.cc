<?php

session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user']['UUID']) || empty($_SESSION['user']['UUID'])) {
  return json_encode([
    "notifications" => (object) [],
    "totalCount" => 0
  ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
}

$UUID = $_SESSION['user']['UUID'];
$UserPath = "../../../profile/u/$UUID";
$NotificationsFile = "$UserPath/notifications.json";

if (!file_exists($NotificationsFile)) {
  return json_encode([
    "notifications" => (object) [],
    "totalCount" => 0
  ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
}

$jsonData = file_get_contents($NotificationsFile);
$data = json_decode($jsonData, true);

if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
  return json_encode([
    "notifications" => (object) [],
    "totalCount" => 0
  ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
}

if (!isset($data['notifications'])) {
  $data['notifications'] = (object) [];
}
if (!isset($data['totalCount'])) {
  $data['totalCount'] = is_array($data['notifications'])
    ? count($data['notifications'])
    : count((array) $data['notifications']);
}

echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
