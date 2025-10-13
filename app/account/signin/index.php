<?php
require "../../backend/_include.php";
session_start();
$_SESSION['csrf_token'] = bin2hex(random_bytes(50));
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sign in - PIXLSHARE</title>
  <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
  <link rel="shortcut icon" href="<?= filePath("/assets/logos/"); ?>pixlshareLogo_color_128.png" type="image/x-icon">
  <link rel="stylesheet" href="./css/index.min.css">
  <style>
    .caps-warning {
      color: red;
      display: none;
    }
  </style>
</head>

<body>
  <?php
  displayError();
  ?>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script type="module" src="https://cdn.jsdelivr.net/npm/ldrs/dist/auto/lineWobble.js"></script>
  <!-- %@@@@@@@@@@@@@@@@%@@@@%@%@@@@@@@@@@@@@@@@@@@@%@@@@@@ -->
  <!-- @@@@@@@@@@@%@%%@@@@@@@@@@@@@@@@@@@%@%@@@@@@@@@@@@@@@ -->
  <!-- @@@@@@@@#::::::::::::-+#%%@%%::::::::::::::::::+%@@% -->
  <!-- @@@@@@@%-               .*%%+                 .*%%@@ -->
  <!-- @@@@@@@%.                 %%:                 :%@@@@ -->
  <!-- @@@@@@@*                  ##                  =%@@@@ -->
  <!-- @@@@@#:      *%%%=      -#%%%%%*.     .*%%%%%%%@@@@@ -->
  <!-- @@@@%=       ===-       #%@@@@%=      =%@@@@@%@@@@@@ -->
  <!-- @@@@%-                 +%@@%@%#:      *%@@%@@@@@@%@@ -->
  <!-- @@@%*.               :#%@@@@@%*      :#@@@@@@@@@%@@@ -->
  <!-- @@@%*-              :+%%%@%@@%#=      -%%@@@@@@@@@@@ -->
  <!-- @@@@%=      .%#:      *%@%@@%@%=      =%@@@@@@@@@@@@ -->
  <!-- @%%@%-      =@%=      =%@@@@@@#:      *%@@%@@@@@@@@@ -->
  <!-- @@@@*.      #@%*      .#%@@@@%*      :#%@@@@@@@@@@%@ -->
  <!-- @@@@#*******%%%%#******%%@@@@%#******#%@@@@@@@@@@@@@ -->
  <!-- @@@@%@@@@@@@%@@@@%@@@@@@%@@@@@@@@@@@@@@@@@@@@%%@@@@@ -->
  <!-- @@@@@%@@@@@@@@@%@@@@@@%@@@%@@@@%@@@@@@@@@@%@@@@@@@@@ -->
  <nav id="topNav">
    <div class="topNavLeft">
      <p class="logoFont">PXL</p>
    </div>
    <div class="topNavRight">
      <h2>Sign in</h2>
    </div>
  </nav>
  <nav id="bottomNav">
    <div class="navButton">
      <button type="button" name="signup" onclick="window.location.href='../signup'">
        <ion-icon size="large" name="log-in-outline"></ion-icon>
      </button>
    </div>
    <div class="navButton">
      <button type="button" name="signup" onclick="window.location.href='../../'">
        <ion-icon name="home-outline"></ion-icon>
      </button>
    </div>
    <div class="navButton">
      <button type="button" name="signin" onclick="window.location.href='./'">
        <ion-icon size="large" name="create-outline"></ion-icon>
      </button>
    </div>
  </nav>
  <div class="formContainer">
    <form action="../../backend/scripts/account/_signin.php" method="post">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']); ?>">
      <input type="text" name="username" id="username" placeholder="Username" autocomplete="username" required />
      <input type="password" name="password" id="password" placeholder="Password" autocomplete="current-password"
        required />
      <p id="capsWarning" class="caps-warning">Caps Lock is ON!</p>
      <div class="info_container">
        <div style="display: block; flex-flow: row;">
          <div class="cf-turnstile" data-sitekey="0x4AAAAAAA27XzOGlxRe70Bl" data-size="flexible"></div>
        </div>
        <!-- <p>
          <a href="../forgot/">Forgot Password?</a>
        </p> -->
      </div>
      <input type="submit" value="Sign in" />
    </form>
  </div>
  <script>
    const passwordInput = document.getElementById('password');
    const capsWarning = document.getElementById('capsWarning');

    function checkCapsLock(event) {
      const capsLockOn = event.getModifierState && event.getModifierState('CapsLock');
      capsWarning.style.display = capsLockOn ? 'block' : 'none';
    }

    passwordInput.addEventListener('keydown', checkCapsLock);
    passwordInput.addEventListener('keyup', checkCapsLock);

    // Check Caps Lock on focus
    passwordInput.addEventListener('focus', function (event) {
      const dummyEvent = new KeyboardEvent('keydown', {
        'key': 'a'
      });
      checkCapsLock(dummyEvent);
    });
  </script>
  <script type="module" src="https://cdn.jsdelivr.net/npm/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
  <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js" crossorigin></script>
</body>

</html>