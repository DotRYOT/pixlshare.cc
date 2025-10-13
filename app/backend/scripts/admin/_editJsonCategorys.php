<?php

session_start();

require "../../_auth.php";
require "../../_include.php";
require "../../_adminCheck.php";
require "../../connect/MainConnect.php";

checkUserAuth($conn_main, "auth");

adminCheck($conn_main);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['categories'])) {
    $categories = json_decode($_POST['categories'], true);

    if (json_last_error() === JSON_ERROR_NONE) {
      // Save to JSON file
      $jsonString = json_encode($categories, JSON_PRETTY_PRINT);
      $filePath = '../../json/CategoryOptions.json';

      if (file_put_contents($filePath, $jsonString)) {
        echo json_encode(['success' => true, 'message' => 'Categories saved successfully']);
      } else {
        echo json_encode(['success' => false, 'message' => 'Failed to write to file']);
      }
    } else {
      echo json_encode(['success' => false, 'message' => 'Invalid JSON data']);
    }
  } else {
    echo json_encode(['success' => false, 'message' => 'No categories data provided']);
  }
} else {
  echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>