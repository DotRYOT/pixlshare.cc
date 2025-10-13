<?php
header("Content-Type: application/json");
session_start();

require "../../_include.php";
require "../../connect/MainConnect.php";
require "../../_auth.php";
require "../../_adminCheck.php";

checkUserAuth($conn_main, "auth");
adminCheck($conn_main);

$response = ['success' => false];

if (isset($_GET['query'])) {
  $searchQuery = mysqli_real_escape_string($conn_main, trim($_GET['query']));

  if (empty($searchQuery)) {
    echo json_encode(['success' => false, 'error' => 'Please enter a search query']);
    exit;
  }

  $page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
  $limit = isset($_GET['limit']) ? max(1, min(100, (int) $_GET['limit'])) : 10;
  $offset = ($page - 1) * $limit;

  $filterBy = isset($_GET['filter_by']) ? mysqli_real_escape_string($conn_main, $_GET['filter_by']) : '';
  $filterValue = isset($_GET['filter_value']) ? mysqli_real_escape_string($conn_main, $_GET['filter_value']) : '';

  // Check if query is a valid 26-digit numeric UUID
  $isUUID = preg_match('/^\d{26}$/', $searchQuery); // Matches exactly 26 digits

  // Build WHERE clause
  if ($isUUID) {
    // Exact match on UUID
    $whereClause = "WHERE UUID = ?";
  } else {
    // Fuzzy match on username/email
    $whereClause = "WHERE username LIKE ? OR email LIKE ?";
  }

  if ($filterBy && $filterValue) {
    $whereClause .= " AND $filterBy = ?";
  }

  // Count total rows
  $countSql = "SELECT COUNT(*) as total FROM users $whereClause";
  $countStmt = mysqli_prepare($conn_main, $countSql);

  if ($countStmt) {
    if ($isUUID) {
      mysqli_stmt_bind_param($countStmt, "s", $searchQuery);
    } else {
      $searchParam = "%" . $searchQuery . "%";
      if ($filterBy && $filterValue) {
        mysqli_stmt_bind_param($countStmt, "sss", $searchParam, $searchParam, $filterValue);
      } else {
        mysqli_stmt_bind_param($countStmt, "ss", $searchParam, $searchParam);
      }
    }

    mysqli_stmt_execute($countStmt);
    $countResult = mysqli_stmt_get_result($countStmt);
    $totalRows = mysqli_fetch_assoc($countResult)['total'];
    mysqli_stmt_close($countStmt);
  }

  // Fetch results
  $sql = "SELECT UUID, username, email, pfp_image_link, bg_image_link, profile_bio, user_ip, userAge, userState FROM users $whereClause ORDER BY username LIMIT ? OFFSET ?";
  $stmt = mysqli_prepare($conn_main, $sql);

  if ($stmt) {
    if ($isUUID) {
      mysqli_stmt_bind_param($stmt, "sii", $searchQuery, $limit, $offset);
    } else {
      $searchParam = "%" . $searchQuery . "%";
      if ($filterBy && $filterValue) {
        mysqli_stmt_bind_param($stmt, "sssi", $searchParam, $searchParam, $filterValue, $limit, $offset);
      } else {
        mysqli_stmt_bind_param($stmt, "ssii", $searchParam, $searchParam, $limit, $offset);
      }
    }

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $users = [];
    while ($row = mysqli_fetch_assoc($result)) {
      $users[] = $row;
    }

    $response = [
      'success' => true,
      'data' => $users,
      'pagination' => [
        'total' => $totalRows ?? 0,
        'page' => $page,
        'limit' => $limit,
        'total_pages' => $totalRows > 0 ? ceil($totalRows / $limit) : 0
      ],
      'filters' => [
        'filter_by' => $filterBy,
        'filter_value' => $filterValue
      ]
    ];

    mysqli_stmt_close($stmt);
  } else {
    $response['error'] = "Error preparing the statement.";
  }

  mysqli_close($conn_main);
} else {
  $response['error'] = "No search query provided.";
}

echo json_encode($response, JSON_PRETTY_PRINT);