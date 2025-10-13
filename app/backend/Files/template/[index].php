<?php include "./pageScript.php"; ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title> - PIXLSHARE</title>
  <link rel="shortcut icon" href="../assets/logos/pixlshareLogo_white_128.png" type="image/x-icon">
  <link rel="stylesheet" href="./css/index.min.css">
</head>

<body>
  <?php require "../backend/_nav.php"; ?>
  <p>Welcome - <?= $_SESSION['user']['username']; ?></p>

  <script type="module" src="https://cdn.jsdelivr.net/npm/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
  <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js" crossorigin></script>
</body>

</html>