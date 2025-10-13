<?php
function adminCheck($conn_main)
{
  if (isset($_SESSION['user']['userLevel'])) {
    $userLevel = $_SESSION['user']['userLevel'];
    $UUID = $_SESSION['user']['UUID'];

    $sql = "SELECT userLevel FROM users WHERE UUID = ?";
    $stmt = mysqli_prepare($conn_main, $sql);

    mysqli_stmt_bind_param($stmt, "s", $UUID);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $DBuserLevel);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    if ($userLevel === $DBuserLevel) {
      if ($userLevel === 1) {
        return true;
      } else {
        $Error = generateErrorUrl("Bad Creds!");
        redirectTo("/account/signin/$Error");
        mysqli_stmt_close($stmt);
      }
    } else {
      $Error = generateErrorUrl("Bad Data!");
      redirectTo("/account/signin/$Error");
      mysqli_stmt_close($stmt);
    }

  } else {
    $Error = generateErrorUrl("Session Error!");
    redirectTo("/account/signin/$Error");
  }
}
