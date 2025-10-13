<?php

session_start();

require "../../_include.php";
require "../../connect/MainConnect.php";

RateLimitUser();

$email = trim(strtolower($_POST['email']));
// $UUID = filter_user_input($_SESSION['user']['UUID']);
$Time = time();
$pageKey = rand(100000, 999999);
$Context = "800";

// Get page url
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$domain = $_SERVER['HTTP_HOST'];
$uri = $_SERVER['REQUEST_URI'];
$pageUrl = $protocol . $domain;

if (!$conn_main) {
  die("Connection failed: " . mysqli_connect_error());
}

$sql = "SELECT UUID FROM users WHERE email = ?";
$stmt = mysqli_prepare($conn_main, $sql);

if ($stmt) {
  mysqli_stmt_bind_param($stmt, "s", $email);
  mysqli_stmt_execute($stmt);
  mysqli_stmt_store_result($stmt);

  if (mysqli_stmt_num_rows($stmt) > 0) {
    mysqli_stmt_bind_result($stmt, $UUID);
    mysqli_stmt_fetch($stmt);

    $check_sql = "SELECT id FROM adminrequest WHERE UUID = ? AND context = ?";
    $check_stmt = mysqli_prepare($conn_main, $check_sql);
    mysqli_stmt_bind_param($check_stmt, "ss", $UUID, $Context);
    mysqli_stmt_execute($check_stmt);
    mysqli_stmt_store_result($check_stmt);

    if (mysqli_stmt_num_rows($check_stmt) > 0) {
      $Error = generateErrorUrl("Request already under review!");
      redirectTo("/account/signin/$Error");
      exit;
    }
    mysqli_stmt_close($check_stmt);

    $sql_key = "INSERT INTO adminrequest (UUID, context, pageKey, Time) VALUES (?, ?, ?, ?)";
    $stmt_key = mysqli_prepare($conn_main, $sql_key);
    mysqli_stmt_bind_param($stmt_key, "ssss", $UUID, $Context, $pageKey, $Time);
    mysqli_stmt_execute($stmt_key);
    mysqli_stmt_close($stmt_key);

    $to = $email;
    $subject = "Forgot Password";

    $message = "
    <html>
    <head>
      <title>Forgot Password</title>
      <style>
        .email-container {
          font-family: Arial, sans-serif;
          background-color: #f4f4f4;
          padding: 20px;
          border-radius: 10px;
          color: #333;
        }
        .email-header {
          font-size: 24px;
          color: #4caf50;
          margin-bottom: 20px;
        }
        .email-body {
          font-size: 16px;
          margin-bottom: 20px;
        }
        .email-footer {
          font-size: 12px;
          color: #777;
        }
        .highlight {
          color: #ff5722;
          background-color: #ff5722;
          width: auto;
        }
        .highlight:hover {
          background-color: transparent;
        }
        .btn-reset {
          background-color: #4caf50;
          color: white;
          padding: 10px 15px;
          text-decoration: none;
          border-radius: 5px;
          font-size: 16px;
        }
      </style>
    </head>
    <body>
      <div class='email-container'>
        <div class='email-header'>Reset Your Password</div>
        <div class='email-body'>
          Hello,<br><br>
          It looks like you've forgotten your password. Don't worry, we've generated a temporary link for you:<br><br>
          <a href='" . $pageUrl . "/account/forgot/passwordchange/?key=" . $pageKey . "&uuid=" . $UUID . "' class='btn-reset'>Reset Password</a>
        </div>
        <div class='email-footer'>
          If you didn’t request a password reset, you can safely ignore this email.<br>
          Regards,<br>
          The PixlShare Team
        </div>
      </div>
    </body>
    </html>";

    $headers = "From: admin@pixlshare.cc\r\n";
    $headers .= "Reply-To: admin@pixlshare.cc\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

    if (mail($to, $subject, $message, $headers)) {
      echo "Mail sent";
      $success = generateErrorUrl("Email sent!");
      redirectTo("/account/forgot/$success");
    } else {
      echo "Mail not sent";
    }
  } else {
    echo "No user found with this email";
    $Error = generateErrorUrl("No user found with this email");
    redirectTo("/account/signin/$Error");
  }
  mysqli_stmt_close($stmt);
} else {
  echo "Statement preparation failed: " . mysqli_error($conn_main);
  $Error = generateErrorUrl("Statement preparation failed");
  redirectTo("/account/signin/$Error");
}
