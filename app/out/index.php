<?php
// https://pixlshare.cc/out/?link=https%3A%2F%2Fx.com%2FPixlShare&return=https://pixlshare.cc

require "../backend/_include.php";

// Generate a random nonce value
$nonce = base64_encode(random_bytes(16));

// Debugging output to log the nonce
error_log("Generated nonce: " . $nonce);

// Use Content Security Policy (CSP) headers with a nonce for secure inline scripts
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'nonce-{$nonce}';");

if (!isset($_GET["link"]) || !isset($_GET["return"])) {
  // header("Location: ../home/");
  echo "error";
  exit;
}

$link = urldecode($_GET['link']);
$return = urldecode($_GET['return']);

if (empty($link) || empty($return)) {
  echo "error";
  exit;
}

// Sanitize the input to prevent XSS attacks
$sanitized_link = filter_var($link, FILTER_SANITIZE_URL);
$sanitized_return = filter_var($return, FILTER_SANITIZE_URL);

// Validate the URLs to ensure they're well-formed
if (filter_var($sanitized_link, FILTER_VALIDATE_URL) && filter_var($sanitized_return, FILTER_VALIDATE_URL)) {
  ?>
  <!DOCTYPE html>
  <html lang="en">

  <head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redirecting...</title>
    <link rel="shortcut icon" href="<?= filePath("/assets/logos/"); ?>pixlshareLogo_color_128.png" type="image/x-icon">
    <link rel="stylesheet" href="./css/index.min.css">
  </head>

  <body>
    <div class="container">
      <div class="card">
        <div class="icon">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960">
            <path
              d="M480-281q14 0 24.5-10.5T515-316q0-14-10.5-24.5T480-351q-14 0-24.5 10.5T445-316q0 14 10.5 24.5T480-281Zm-30-144h60v-263h-60v263ZM330-120 120-330v-300l210-210h300l210 210v300L630-120H330Zm25-60h250l175-175v-250L605-780H355L180-605v250l175 175Zm125-300Z" />
          </svg>
        </div>
        <h1>You are leaving PixlShare</h1>
        <p class="subtitle">Opening link in new tab shortly</p>

        <div class="countdown-container">
          <div class="countdown-circle">
            <span id="countdown">6</span>
            <svg>
              <circle class="countdown-track" cx="60" cy="60" r="54" />
              <circle class="countdown-progress" cx="60" cy="60" r="54" />
            </svg>
          </div>
        </div>

        <div class="links">
          <a href="<?= htmlspecialchars($sanitized_link, ENT_QUOTES, 'UTF-8'); ?>" target="_blank"
            rel="noopener noreferrer" class="btn btn-primary">Continue to Link Now</a>
          <a href="<?= htmlspecialchars($sanitized_return, ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-secondary">Return to
            Previous Page</a>
        </div>
      </div>
    </div>

    <script nonce="<?= $nonce; ?>">
      const link = <?= json_encode($sanitized_link); ?>;
      const returnUrl = <?= json_encode($sanitized_return); ?>;
      const countdownElement = document.getElementById('countdown');

      let seconds = 6;
      const totalSeconds = 6;

      // Update countdown every second
      const countdownInterval = setInterval(() => {
        seconds--;
        countdownElement.textContent = seconds;

        if (seconds <= 0) {
          clearInterval(countdownInterval);
          // Open the target link in a new tab
          window.open(link, '_blank', 'noopener,noreferrer');
          console.log("opening in new tab:", link);
        }
      }, 1000);

      // Add progress ring animation
      const progressRing = document.querySelector('.countdown-progress');
      const trackRing = document.querySelector('.countdown-track');
      const radius = progressRing.r.baseVal.value;
      const circumference = 2 * Math.PI * radius;

      progressRing.style.strokeDasharray = `${circumference} ${circumference}`;
      progressRing.style.strokeDashoffset = circumference;

      // Set track ring color
      trackRing.style.stroke = getComputedStyle(document.documentElement).getPropertyValue('--border-color').trim();

      function setProgress(percent) {
        const offset = circumference - (percent / 100) * circumference;
        progressRing.style.strokeDashoffset = offset;
      }

      // Update progress ring every second
      const progressInterval = setInterval(() => {
        const percent = ((totalSeconds - seconds) / totalSeconds) * 100;
        setProgress(percent);
      }, 1000);
    </script>
  </body>

  </html>
  <?php
} else {
  // Debugging output
  error_log("Sanitized link: " . $sanitized_link);
  error_log("Sanitized return URL: " . $sanitized_return);
  echo "Invalid link or return URL.";
}
?>