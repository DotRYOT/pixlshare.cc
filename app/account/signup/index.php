<?php require "./pageScript.php"; ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sign up - PIXLSHARE</title>
  <script src="https://challenges.cloudflare.com/turnstile/v0/api.js  " async defer></script>
  <link rel="shortcut icon" href="<?= filePath("/assets/logos/"); ?>pixlshareLogo_color_128.png" type="image/x-icon">
  <link rel="manifest" href="../../manifest.json">
  <link rel="stylesheet" href="./css/index.min.css">
  <style>
    .dob-input-container {
      display: flex;
      gap: 10px;
      margin-top: 10px;
    }

    .dob-select {
      flex: 1;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 4px;
      font-size: 16px;
    }
  </style>
</head>

<body>
  <?php
  displayError();
  ?>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js  "></script>
  <script type="module" src="https://cdn.jsdelivr.net/npm/ldrs/dist/auto/lineWobble.js  "></script>
  <!-- %@@@@@@@@@@@@@@@@%@@@@%@%@@@@@@@@@@@@@@@@@@@@%@@@@@@ -->
  <!-- @@@@@@@@@@@%@%%@@@@@@@@@@@@@@@@@@@%@%@@@@@@@@@@@@@@@ -->
  <!-- @@@@@@@@#::::::::::::-+#%%@%%::::::::::::::::::+%@@% -->
  <!-- @@@@@@@%-               .*%%+                 .*%%@@ -->
  <!-- @@@@@@@%.                 %%:                 :%@@@@ -->
  <!-- @@@@@@@*                  ##                  =%@@@@ -->
  <!-- @@@@@#:      *%%%=      -#%%%%%*.     .*%%%%%%%@@@@@ -->
  <!-- @@@@%=       ===-       #%@@@@%=      =%@@@@@%@@@@@@ -->
  <!-- @@@@%-                 +%@@%@%#:      *%@@%@@@@@@%@@ -->
  <!-- @@@@@*               :#%@@@@@%*      :#@@@@@@@@@%@@@ -->
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
        <svg xmlns="http://www.w3.org/2000/svg" height="40px" viewBox="0 -960 960 960" width="40px" fill="#FFFFFF">
          <path
            d="M480.67-120v-66.67h292.66v-586.66H480.67V-840H840v720H480.67Zm-63.34-176.67-47-48 102-102H120v-66.66h351l-102-102 47-48 184 184-182.67 182.66Z" />
        </svg>
        <p>Sign up</p>
      </button>
    </div>
    <div class="navButton">
      <button type="button" name="signup" onclick="window.location.href='../../'">
        <svg xmlns="http://www.w3.org/2000/svg" height="40px" viewBox="0 -960 960 960" width="40px" fill="#FFFFFF">
          <path
            d="M226.67-186.67h140v-246.66h226.66v246.66h140v-380L480-756.67l-253.33 190v380ZM160-120v-480l320-240 320 240v480H526.67v-246.67h-93.34V-120H160Zm320-352Z" />
        </svg>
      </button>
    </div>
    <div class="navButton">
      <button type="button" name="signin" onclick="window.location.href='../signin/'">
        <svg xmlns="http://www.w3.org/2000/svg" height="40px" viewBox="0 -960 960 960" width="40px" fill="#FFFFFF">
          <path
            d="M80-160v-100q0-33.67 17-62.33Q114-351 146.67-366q65-30 126.33-45.33 61.33-15.34 127-15.34 29.33 0 60.5 3.34Q491.67-420 523.33-412l-56 56q-17-2-33.5-3T400-360q-62.33 0-112.83 12.67-50.5 12.66-112.5 41.33-14.34 7-21.17 20-6.83 13-6.83 26v33.33h296L509.33-160H80Zm544 16L484-284l46.67-46.67L624-237.33l209.33-209.34L880-400 624-144ZM400-481.33q-66 0-109.67-43.67-43.66-43.67-43.66-109.67t43.66-109.66Q334-788 400-788t109.67 43.67q43.66 43.66 43.66 109.66T509.67-525Q466-481.33 400-481.33Zm42.67 254.66ZM400-548q37 0 61.83-24.83 24.84-24.84 24.84-61.84t-24.84-61.83Q437-721.33 400-721.33t-61.83 24.83q-24.84 24.83-24.84 61.83t24.84 61.84Q363-548 400-548Zm0-86.67Z" />
        </svg>
        <p>Sign in</p>
      </button>
    </div>
  </nav>
  <div class="formContainer">
    <form action="../../backend/scripts/account/_signup.php" method="post" id="signupForm">
      <input type="email" name="email" id="email" placeholder="Email" required />
      <input type="text" name="UserName" id="UserName" placeholder="Username" required />
      <input type="password" name="password" id="password" placeholder="Password" minlength="8" required />
      <input type="password" name="repassword" id="repassword" placeholder="Re-Password" minlength="8" required />
      <div id="passwordMatchError" class="password-match-error">Passwords do not match</div>

      <div class="date-input-container">
        <label for="dob" class="date-label">Date of Birth</label>
        <div class="dob-input-container">
          <select id="day" name="dob_day" class="dob-select" required>
            <option value="">Day</option>
          </select>
          <select id="month" name="dob_month" class="dob-select" required>
            <option value="">Month</option>
          </select>
          <select id="year" name="dob_year" class="dob-select" required>
            <option value="">Year</option>
          </select>
        </div>
        <small id="dob-error" class="date-error-message" aria-live="polite"></small>
        <p class="date-hint" title="We require users to be at least 18 years old">Must be 18+ ⓘ</p>
      </div>
      <input type="hidden" name="dob" id="dob-hidden" />

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
    document.addEventListener("DOMContentLoaded", function () {
      const daySelect = document.getElementById("day");
      const monthSelect = document.getElementById("month");
      const yearSelect = document.getElementById("year");
      const errorElement = document.getElementById("dob-error");
      const dobHidden = document.getElementById("dob-hidden");

      // Populate days (1-31)
      for (let i = 1; i <= 31; i++) {
        const option = document.createElement("option");
        option.value = i.toString().padStart(2, '0');
        option.textContent = i;
        daySelect.appendChild(option);
      }

      // Populate months
      const months = [
        "January", "February", "March", "April", "May", "June",
        "July", "August", "September", "October", "November", "December"
      ];
      months.forEach((month, index) => {
        const option = document.createElement("option");
        option.value = (index + 1).toString().padStart(2, '0');
        option.textContent = month;
        monthSelect.appendChild(option);
      });

      // Populate years (from 120 years ago to 18 years ago)
      const currentYear = new Date().getFullYear();
      const startYear = currentYear - 120;
      const endYear = currentYear - 18;
      for (let year = endYear; year >= startYear; year--) {
        const option = document.createElement("option");
        option.value = year;
        option.textContent = year;
        yearSelect.appendChild(option);
      }

      // Update hidden date input and validate
      function updateHiddenDate() {
        const day = daySelect.value;
        const month = monthSelect.value;
        const year = yearSelect.value;

        if (day && month && year) {
          // Format as YYYY-MM-DD for backend processing
          dobHidden.value = `${year}-${month}-${day}`;
          validateDOB();
        } else {
          dobHidden.value = "";
          errorElement.textContent = "Please select your date of birth";
        }
      }

      // Validate date of birth
      function validateDOB() {
        const day = parseInt(daySelect.value);
        const month = parseInt(monthSelect.value);
        const year = parseInt(yearSelect.value);

        if (!day || !month || !year) {
          errorElement.textContent = "Please select your date of birth";
          return false;
        }

        // Create date object (month is 0-indexed in JS Date)
        const selectedDate = new Date(year, month - 1, day);
        const today = new Date();
        const minDate = new Date();
        minDate.setFullYear(today.getFullYear() - 120);

        // Check if the date is valid (e.g., not Feb 30th)
        if (selectedDate.getDate() !== day ||
          selectedDate.getMonth() !== month - 1 ||
          selectedDate.getFullYear() !== year) {
          errorElement.textContent = "Please enter a valid date";
          return false;
        }

        // Check if user is at least 18
        const eighteenYearsAgo = new Date();
        eighteenYearsAgo.setFullYear(today.getFullYear() - 18);
        if (selectedDate > eighteenYearsAgo) {
          errorElement.textContent = "You must be at least 18 years old";
          return false;
        }

        // Check if date is not too old (120 years)
        if (selectedDate < minDate) {
          errorElement.textContent = "Please enter a realistic birth date";
          return false;
        }

        // All validations passed
        errorElement.textContent = "";
        return true;
      }

      // Add event listeners
      daySelect.addEventListener("change", updateHiddenDate);
      monthSelect.addEventListener("change", updateHiddenDate);
      yearSelect.addEventListener("change", updateHiddenDate);

      // Form submission validation
      document.getElementById("signupForm").addEventListener("submit", function (e) {
        if (!validateDOB()) {
          e.preventDefault(); // Prevent form submission if validation fails
        }
      });
    });

    // Password & Caps Lock Validation (unchanged)
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
</body>

</html>