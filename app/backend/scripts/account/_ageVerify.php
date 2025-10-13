<?php

session_start();

require "../../_include.php";
require "../../connect/MainConnect.php";

RateLimitUser();
$UUID = mysqli_real_escape_string($conn_main, $_SESSION['user']['UUID']);
$dob = mysqli_real_escape_string($conn_main, $_POST['age']);

$age = date_diff(date_create($dob), date_create('today'))->y;
$is_over_18 = ($age >= 18);
$user_data = [
  'dob' => $dob,
  'age' => $age,
  'is_over_18' => $is_over_18
];

$_SESSION['user']['ageCheck'] = $user_data;

$json_user_data = json_encode($user_data);

if (empty($dob)) {
  $Error = generateErrorUrl("Bad Data!");
  redirectTo("/profile/settings/$Error");
}

$sql = "UPDATE users SET userAge = ? WHERE UUID = ?";
$stmt = mysqli_prepare($conn_main, $sql);
mysqli_stmt_bind_param($stmt, "ss", $json_user_data, $UUID);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);


$success = generateGetUrl("changed");
// echo $success;
redirectTo("/profile/settings/$success");
