<?php
function checkUserAuth($conn_main, $type)
{
  if (isset($_COOKIE['authToken'])) {
    $user_token = $_COOKIE['authToken'];
    $user_token = json_decode($user_token, true);

    $UUID = $user_token['UUID'];

    $sql = "SELECT user_token FROM users WHERE UUID = ?";
    $stmt = mysqli_prepare($conn_main, $sql);
    mysqli_stmt_bind_param($stmt, "s", $UUID);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $hashTokenArray);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    $hashTokenArray = json_decode($hashTokenArray, true);

    $token = $hashTokenArray['Token'];
    $hashedToken = $hashTokenArray['hashToken'];

    if (password_verify($user_token['Token'], $hashedToken)) {

      $sql = "SELECT userLevel, userAge, username FROM users WHERE UUID= ? ";
      $stmt = mysqli_prepare($conn_main, $sql);

      mysqli_stmt_bind_param($stmt, "s", $UUID);
      mysqli_stmt_execute($stmt);
      mysqli_stmt_bind_result($stmt, $userLevel, $ageCheck, $UserName);
      mysqli_stmt_fetch($stmt);
      mysqli_stmt_close($stmt);

      $_SESSION['user'] = array(
        'UUID' => $UUID,
        'token' => $token,
        'username' => $UserName,
        'userLevel' => $userLevel,
        'ageCheck' => $ageCheck
      );

      if ($type === "check") {
        header("Location: ./home/");
        exit;
      }
      if ($type === "auth") {
        return;
      }
    } else {
      handleSigninError("No account found!");
    }
  } else {
    handleSigninError("No log of account!");
  }
}