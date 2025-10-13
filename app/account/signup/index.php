<?php require "./pageScript.php"; ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sign up - PIXLSHARE</title>
  <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
  <link rel="shortcut icon" href="<?= filePath("/assets/logos/"); ?>pixlshareLogo_color_128.png" type="image/x-icon">
  <link rel="manifest" href="../../manifest.json">
  <link rel="stylesheet" href="./css/index.min.css">
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
      <h2>Sign up</h2>
    </div>
  </nav>
  <nav id="bottomNav">
    <div class="navButton">
      <button type="button" name="signup" onclick="window.location.href='./'">
        <ion-icon size="large" name="log-in-outline"></ion-icon>
      </button>
    </div>
    <div class="navButton">
      <button type="button" name="signup" onclick="window.location.href='../../'">
        <ion-icon name="home-outline"></ion-icon>
      </button>
    </div>
    <div class="navButton">
      <button type="button" name="signin" onclick="window.location.href='../signin/'">
        <ion-icon size="large" name="create-outline"></ion-icon>
      </button>
    </div>
  </nav>
  <div class="formContainer">
    <form action="../../backend/scripts/account/_signup.php" method="post" id="signupForm">
      <input type="email" name="email" id="email" placeholder="Email" required />
      <input type="text" name="UserName" id="UserName" placeholder="Username" required />
      <input type="password" name="password" id="password" placeholder="Password" required />
      <input type="password" name="repassword" id="repassword" placeholder="Re-Password" required />
      <div id="passwordMatchError" class="password-match-error">Passwords do not match</div>
      <!-- Date of Birth Input -->
      <div class="date-input-container">
        <label for="dob" class="date-label">Date of Birth</label>
        <div class="date-wrapper">
          <input type="date" id="dob" name="dob" class="date-input" required aria-describedby="dob-error">
        </div>
        <small id="dob-error" class="date-error-message" aria-live="polite"></small>
        <p class="date-hint" title="We require users to be at least 18 years old">Must be 18+ ⓘ</p>
      </div>
      <!-- <p id="capsWarning" class="caps-warning">Caps Lock is ON!</p> -->
      <div class="feedbackAndAuth">
        <div id="passwordStrength" class="password-strength">Password
          strength: </div>
        <div style="display: block; flex-flow: row;">
          <div class="cf-turnstile" data-sitekey="0x4AAAAAAA27XzOGlxRe70Bl" data-size="flexible"></div>
        </div>
      </div>
      <input type="submit" value="Sign up" />
    </form>
  </div>

  <!-- Validation Scripts -->
  <script>
    // Date Validation
    document.addEventListener("DOMContentLoaded", function () {
      const dateInput = document.getElementById("dob");
      const errorElement = document.getElementById("dob-error");

      if (!dateInput || !errorElement) {
        console.warn("DOB elements not found in DOM.");
        return;
      }

      const today = new Date();
      const minDate = new Date();
      minDate.setFullYear(today.getFullYear() - 120);

      const maxDate = new Date();
      maxDate.setFullYear(today.getFullYear() - 18);

      dateInput.min = minDate.toISOString().split("T")[0];
      dateInput.max = maxDate.toISOString().split("T")[0];

      dateInput.addEventListener("input", () => {
        const selectedDate = new Date(dateInput.value);
        let errorMessage = "";

        if (!dateInput.value) {
          errorMessage = "Please select your date of birth";
        } else if (selectedDate > maxDate) {
          errorMessage = "You must be at least 18 years old";
        } else if (selectedDate < minDate) {
          errorMessage = "Please enter a realistic birth date";
        }

        errorElement.textContent = errorMessage;
        dateInput.setCustomValidity(errorMessage);
      });
    });

    // Password & Caps Lock Validation
    const passwordInput = document.getElementById('password');
    const capsWarning = document.getElementById('capsWarning');

    function checkCapsLock(event) {
      const capsLockOn = event.getModifierState && event.getModifierState('CapsLock');
      capsWarning.style.display = capsLockOn ? 'block' : 'none';
    }

    passwordInput.addEventListener('keydown', checkCapsLock);
    passwordInput.addEventListener('keyup', checkCapsLock);

    passwordInput.addEventListener('focus', function (event) {
      const dummyEvent = new KeyboardEvent('keydown', {
        'key': 'CapsLock'
      });
      checkCapsLock(dummyEvent);
    });

    function checkPasswordStrength(password) {
      let strength = 0;

      if (password.length >= 8) strength += 1;
      if (/[A-Z]/.test(password)) strength += 1;
      if (/[0-9]/.test(password)) strength += 1;
      if (/[^A-Za-z0-9]/.test(password)) strength += 1;

      const strengthText = ['Weak', 'Moderate', 'Strong', 'Very Strong'];
      const strengthColors = ['red', 'orange', 'yellow', 'green'];
      const strengthIndex = Math.min(strength, strengthText.length - 1);

      const strengthElement = document.getElementById('passwordStrength');
      strengthElement.textContent = 'Password strength: ' + strengthText[strengthIndex];
      strengthElement.style.color = strengthColors[strengthIndex];
    }

    function checkPasswordMatch() {
      const password = document.getElementById('password').value;
      const repassword = document.getElementById('repassword').value;
      const passwordMatchError = document.getElementById('passwordMatchError');

      if (password !== repassword) {
        passwordMatchError.style.display = 'block';
        return false;
      } else {
        passwordMatchError.style.display = 'none';
        return true;
      }
    }

    passwordInput.addEventListener('input', function () {
      checkPasswordStrength(this.value);
    });

    document.getElementById('repassword').addEventListener('input', function () {
      checkPasswordMatch();
    });

    document.getElementById('signupForm').addEventListener('submit', function (event) {
      if (!checkPasswordMatch()) {
        event.preventDefault();
      }
    });
  </script>

  <!-- Ionicons -->
  <script type="module" src="https://cdn.jsdelivr.net/npm/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
  <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js" crossorigin></script>
</body>

</html>