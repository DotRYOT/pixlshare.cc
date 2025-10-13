<?php include "./pageScript.php"; ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Explore - PIXLSHARE</title>
  <link rel="shortcut icon" href="<?= filePath("/assets/logos/"); ?>pixlshareLogo_color_128.png" type="image/x-icon">
  <link rel="stylesheet" href="./css/index.min.css">
  <link rel="manifest" href="<?= filePath("/") ?>manifest.json" />
  <?php if ($_SERVER['HTTP_HOST'] == 'pixlshare.cc') { ?>
  <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
  <script>
    if ('serviceWorker' in navigator) {
      window.addEventListener('load', () => {
        navigator.serviceWorker.register('../sw.js')
          .then(registration => {
            console.log('Service Worker registered with scope:', registration.scope);
          })
          .catch(error => {
            console.error('Service Worker registration failed:', error);
          });
      });
    }
  </script>
  <?php } ?>
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
  require "../backend/_nav.php";
  ?>

  <div class="pageFilterToolbar">
    <div class="filterToolBar">
      <div class="pageType">
        <button type="button" class="logoFont">Home</button>
        <p class="logoFont">/</p>
        <button type="button" class="logoFont selectedButton">Explore</button>
      </div>
      <div class="FilterButtons">
        <button type="button">
          <ion-icon name="settings-outline"></ion-icon>
        </button>
        <!-- <button type="button" id="filterButton">
          <ion-icon name="filter-outline"></ion-icon>
        </button> -->
      </div>
    </div>
    <script>
      document.getElementById('filterButton').addEventListener('click', function () {
        const filterMenu = document.getElementById('filterMenu');
        filterMenu.classList.toggle('show');
      });
    </script>
  </div>

  <div class="postCardContainer"></div>
  <div id="loadingSpinner" class="spinner hidden">Loading...</div>
  <script src="./_postLoader.js"></script>

  <?php require "../backend/_footer.php"; ?>
  <script type="module" src="https://cdn.jsdelivr.net/npm/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
  <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js" crossorigin></script>
</body>


</html>