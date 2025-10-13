<?php require "./pageScript.php"; ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Change Password - PIXLSHARE</title>
  <link rel="shortcut icon" href="<?= filePath("/") ?>assets/logos/ pixlshareLogo_white_128.png"
    type="image/x-icon">
  <link rel="stylesheet" href="./css/index.min.css">
  <link rel="manifest" href="<?= filePath("/") ?>manifest.json" />
</head>

<body>
  <?php
  if (isset($_GET['error'])) {
    $error = htmlspecialchars($_GET['error']);
    ?>
    <div class="errorHeader">
      <div>
        <ion-icon name="help-outline"></ion-icon>
      </div>
      <h2>
        <?= $error; ?>
      </h2>
      <div>
        <a href="./">
          <ion-icon name="close-circle-outline"></ion-icon>
        </a>
      </div>
    </div>
    <?php
  }
  ?>
  <?php require "../../../backend/_nav_noacc.php"; ?>
  <div class="formContainer">
    <form action="../../../backend/scripts/account/_passwordChange.php" method="post">
      <h2>Change Password</h2>
      <input type="password" name="password" id="password" placeholder="Password" required />
      <input type="password" name="repassword" id="repassword" placeholder="Re-Password" required />
      <input type="hidden" name="type" value="2">
      <input type="hidden" name="pageKey" value="<?= $key; ?>">
      <input type="hidden" name="uuid" value="<?= $UUID; ?>">
      <input type="submit" value="Submit" />
    </form>
  </div>
  <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>

</html>