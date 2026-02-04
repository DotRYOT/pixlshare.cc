<?php require "./pageScript.php"; ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Forgot Password - PIXLSHARE</title>
  <link rel="shortcut icon" href="<?= filePath("/assets/logos/"); ?>pixlshareLogo_color_128.png" type="image/x-icon">
  <link rel="stylesheet" href="./css/index.min.css">
  <link rel="manifest" href="<?= filePath("/") ?>manifest.json" />
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
      <h2>Forgot Password</h2>
    </div>
  </nav>
  <nav id="bottomNav">
    <div class="navButton">
      <button type="button" name="signup" onclick="window.location.href='../signup'">
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
      <button type="button" name="signin" onclick="window.location.href='../signin'">
        <svg xmlns="http://www.w3.org/2000/svg" height="40px" viewBox="0 -960 960 960" width="40px" fill="#FFFFFF">
          <path
            d="M80-160v-100q0-33.67 17-62.33Q114-351 146.67-366q65-30 126.33-45.33 61.33-15.34 127-15.34 29.33 0 60.5 3.34Q491.67-420 523.33-412l-56 56q-17-2-33.5-3T400-360q-62.33 0-112.83 12.67-50.5 12.66-112.5 41.33-14.34 7-21.17 20-6.83 13-6.83 26v33.33h296L509.33-160H80Zm544 16L484-284l46.67-46.67L624-237.33l209.33-209.34L880-400 624-144ZM400-481.33q-66 0-109.67-43.67-43.66-43.67-43.66-109.67t43.66-109.66Q334-788 400-788t109.67 43.67q43.66 43.66 43.66 109.66T509.67-525Q466-481.33 400-481.33Zm42.67 254.66ZM400-548q37 0 61.83-24.83 24.84-24.84 24.84-61.84t-24.84-61.83Q437-721.33 400-721.33t-61.83 24.83q-24.84 24.83-24.84 61.83t24.84 61.84Q363-548 400-548Zm0-86.67Z" />
        </svg>
        <p>Sign in</p>
      </button>
    </div>
  </nav>

  <div class="formContainer">
    <form action="../../backend/scripts/account/_forgotPassword.php" method="post">
      <input type="email" name="email" id="email" required placeholder="Email">
      <input type="hidden" name="emailCheck" value="1" />
      <input type="submit" value="Submit" name="emailSubmit" />
    </form>
  </div>
</body>

</html>