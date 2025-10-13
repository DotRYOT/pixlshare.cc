<?php require "./pageScript.php"; ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Suspended Post - PIXLSHARE</title>
  <link rel="shortcut icon" href="<?= filePath("/assets/logos/"); ?>pixlshareLogo_color_128.png" type="image/x-icon">
  <link rel="stylesheet" href="../css/index.min.css">
  <link rel="manifest" href="<?= filePath("/") ?>manifest.json" />
</head>

<body id="suspendedPostPage">
  <h1>Oops it looks like the post you are looking for is suspended!</h1>
  <?php if ($isPostOwner) { ?>
    <p>
      It looks like you are the post owner, refer to the support form for info!
      <a href="../?puid=<?= $PUID . "&uuid=" . $UUID; ?>">Support</a>
    </p>
  <?php } ?>
  <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>

</html>