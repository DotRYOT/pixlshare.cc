<?php

session_start();

require "../../_auth.php";
require "../../_include.php";
require "../../_adminCheck.php";
require "../../connect/MainConnect.php";

checkUserAuth($conn_main, "auth");

adminCheck($conn_main);

$requesterUUID = mysqli_real_escape_string($conn_main, $_POST['VarOne']);
$Status = mysqli_real_escape_string($conn_main, $_POST['Status']);
$AdminUUID = mysqli_real_escape_string($conn_main, $_SESSION['user']['UUID']);

echo $requesterUUID . "<br>" . $Status;

$sql = "UPDATE users SET userState = ? WHERE UUID = ?";
$stmt = mysqli_prepare($conn_main, $sql);

mysqli_stmt_bind_param($stmt, "ss", $Status, $requesterUUID);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

$success = generateErrorUrl("Account marked to be deleted!");
echo "<br>";
echo $success;
redirectTo("/admin/$Error");