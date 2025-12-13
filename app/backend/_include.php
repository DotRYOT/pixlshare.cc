<?php
$defaultImage = "pixlshareLogo_white_4k.png";
$defaultImageBG = "pxl_logo_1350_white.png";
function randStringGen($length, $type = 'normal')
{
  switch ($type) {
    case 'normal':
    default:
      $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
      $charactersLength = strlen($characters);
      $randomString = '';
      for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[random_int(0, $charactersLength - 1)];
      }
      return $randomString;

    case 'numbers':
      $characters = '0123456789';
      $charactersLength = strlen($characters);
      $randomString = '';
      for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[random_int(0, $charactersLength - 1)];
      }
      return $randomString;

    case 'letters':
      $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
      $charactersLength = strlen($characters);
      $randomString = '';
      for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[random_int(0, $charactersLength - 1)];
      }
      return $randomString;
  }
}
function getUserIP()
{
  // Check for headers used by proxy servers or load balancers
  $headers = [
    'HTTP_CLIENT_IP',
    'HTTP_X_FORWARDED_FOR',
    'HTTP_X_FORWARDED',
    'HTTP_FORWARDED_FOR',
    'HTTP_FORWARDED'
  ];

  foreach ($headers as $header) {
    if (isset($_SERVER[$header])) {
      return $_SERVER[$header];
    }
  }

  return $_SERVER['REMOTE_ADDR'];
}
function filter_user_input($input, $type = 'string', $allow_html = false, $max_length = 255)
{
  if ($input === null) {
    error_log("Null input detected in filter_user_input");
    $input = '';
  }

  $input = trim($input);

  switch ($type) {
    case 'email':
      $input = filter_var($input, FILTER_SANITIZE_EMAIL);
      if (!filter_var($input, FILTER_VALIDATE_EMAIL)) {
        error_log("Invalid email input: $input");
        return false;
      }
      break;

    case 'url':
      $input = filter_var($input, FILTER_SANITIZE_URL);
      if (!filter_var($input, FILTER_VALIDATE_URL)) {
        error_log("Invalid URL input: $input");
        return false;
      }
      break;

    case 'int':
      $input = filter_var($input, FILTER_SANITIZE_NUMBER_INT);
      if (filter_var($input, FILTER_VALIDATE_INT) === false) {
        error_log("Invalid integer input: $input");
        return false;
      }
      break;

    case 'float':
      $input = filter_var($input, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
      if (filter_var($input, FILTER_VALIDATE_FLOAT) === false) {
        error_log("Invalid float input: $input");
        return false;
      }
      break;

    case 'string':
    default:
      // Remove HTML tags unless explicitly allowed
      if (!$allow_html) {
        $input = strip_tags($input);
      }

      $input = htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');
      $input = str_replace(["\r", "\n", "%0a", "%0d"], '', $input);
      $input = preg_replace('/[^\p{L}\p{N}\s\-_,\.]/u', '', $input);
      $input = substr($input, 0, $max_length);
      break;
  }

  return $input;
}
function RateLimitUser()
{
  // Attempt at rate limiting if the password is wrong
  $limit = 5;
  $interval = 60;
  $now = time();
  if (!isset($_SESSION['rate_limit'])) {
    $_SESSION['rate_limit'] = array(
      'count' => 1,
      'timestamp' => $now
    );
  } else {
    if ($now - $_SESSION['rate_limit']['timestamp'] > $interval) {
      $_SESSION['rate_limit']['count'] = 1;
      $_SESSION['rate_limit']['timestamp'] = $now;
    } else {
      $_SESSION['rate_limit']['count']++;
    }
  }

  // Check if the request count exceeds the limit
  if ($_SESSION['rate_limit']['count'] > $limit) {
    redirectTo("/");
    exit;
  }
}
function redirectTo($url)
{
  // Add validation for malformed URLs
  if (strpos($url, '?') !== false) {
    $urlParts = explode('?', $url, 2);
    $url = rtrim($urlParts[0], '/') . '?' . $urlParts[1];
  }

  // Existing domain handling
  $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
  $domain = $_SERVER['HTTP_HOST'];
  $redirecturl = $protocol . $domain . $url;

  if ($domain == "localhost") {
    $redirecturl = $protocol . "localhost/pixlshare.cc/app" . $url;
  }

  if ($domain === "192.168.1.108") {
    $redirecturl = $protocol . $domain . "/pixlshare.cc/app" . $url;
  }

  header("Location: $redirecturl");
  exit;
}
function filePath($url)
{
  $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
  $domain = $_SERVER['HTTP_HOST'];
  $redirecturl = $protocol . $domain . $url;

  if ($domain == "localhost") {
    $redirecturl = $protocol . "localhost/pixlshare.cc/app" . $url;
  }

  if ($domain === "192.168.1.170") {
    $redirecturl = $protocol . $domain . "/pixlshare.cc/app" . $url;
  }

  return $redirecturl;
}
function generateErrorUrl($errorMessage)
{
  $encodedError = urlencode($errorMessage);
  return "?error=" . $encodedError;
}
function generateGetUrl($Message)
{
  $encodedUrl = urlencode($Message);

  $fullUrl = "?status={$encodedUrl}";

  return $fullUrl;
}
function extractNumbersFromUrl($url)
{
  $path = parse_url($url, PHP_URL_PATH);
  preg_match('/\d+/', $path, $matches);
  return $matches[0] ?? null;
}
function loadUserPosts($UUID, $conn_main)
{
  $UUID = mysqli_real_escape_string($conn_main, $UUID);

  $sql_post = "SELECT P.PUID, P.content, P.image_id, U.username, U.pfp_image_link 
              FROM posts P 
              JOIN users U ON P.UUID = U.UUID 
              WHERE P.UUID = ?";
  $stmt = mysqli_prepare($conn_main, $sql_post);
  mysqli_stmt_bind_param($stmt, "s", $UUID);
  mysqli_stmt_execute($stmt);
  mysqli_stmt_bind_result($stmt, $PUID, $Content, $image_id, $UserName, $pfp_image_link);

  $posts = [];
  while (mysqli_stmt_fetch($stmt)) {
    $posts[] = array(
      'PUID' => $PUID,
      'content' => $Content,
      'image_id' => $image_id,
      'username' => $UserName,
      'pfp_image_link' => $pfp_image_link,
      'UUID' => $UUID,
    );
  }
  mysqli_stmt_close($stmt);

  return $posts;
}
function handleError($message)
{
  global $stmt;
  $Error = generateErrorUrl($message);
  if ($stmt)
    mysqli_stmt_close($stmt);
  mysqli_close($GLOBALS['conn_main']);
  redirectTo("/home?$Error");
  exit;
}
function handleSignupError($message)
{
  $Error = generateErrorUrl($message);
  redirectTo("/account/signup$Error");
  exit;
}
function handleSigninError($message)
{
  $Error = generateErrorUrl($message);
  redirectTo("/account/signin?$Error");
  exit;
}
function getNonce()
{
  return bin2hex(random_bytes(16));
}
function displayError()
{
  if (isset($_GET['error'])) {
    $error = $_GET['error'];
    echo '
  <div class="errorHeader">
    <div>
        <ion-icon name="help-outline"></ion-icon>
    </div>
    <h2>' . $error . '</h2>
    <div>
        <a href="./">
            <ion-icon name="close-circle-outline"></ion-icon>
        </a>
    </div>
  </div>
';
  }
}
function logLoginAttempt($username, $status)
{
  global $conn_main;

  // Validate status length to prevent truncation
  if (strlen($status) > 50) {
    $status = substr($status, 0, 50); // Truncate if too long
  }

  $ip = $_SERVER['REMOTE_ADDR'];
  $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';

  try {
    $stmt = mysqli_prepare($conn_main, "
          INSERT INTO login_attempts (username, ip_address, user_agent, attempt_time, status)
          VALUES (?, ?, ?, NOW(), ?)
      ");
    if ($stmt) {
      mysqli_stmt_bind_param($stmt, "ssss", $username, $ip, $user_agent, $status);
      mysqli_stmt_execute($stmt);
      mysqli_stmt_close($stmt);
    }
  } catch (Exception $e) {
    // Log the error but don't break the login process
    error_log("Failed to log login attempt: " . $e->getMessage());
  }
}

// Always run suspended user check
function checkSuspendedUser($conn_main, $UUID)
{
  $sql = "SELECT userState FROM users WHERE UUID = ?";
  $stmt = mysqli_prepare($conn_main, $sql);
  mysqli_stmt_bind_param($stmt, "s", $UUID);
  mysqli_stmt_execute($stmt);
  mysqli_stmt_bind_result($stmt, $userState);
  mysqli_stmt_fetch($stmt);
  mysqli_stmt_close($stmt);

  if ($userState === 500) {
    echo "Your account has been suspended. Please contact support for more information.";
    exit;
  }
}