<?php include "./pageScript.php"; ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Home - PIXLSHARE</title>
  <link rel="shortcut icon" href="<?= filePath("/assets/logos/"); ?>pixlshareLogo_color_128.png" type="image/x-icon">
  <link rel="stylesheet" href="./css/index.min.css">
  <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
  <?php if ($_SERVER['HTTP_HOST'] == 'pixlshare.cc') { ?>
    <link rel="manifest" href="<?= filePath("/"); ?>manifest.json" />
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
  require "../backend/_nav.php";
  // Load filter options from JSON file
  $categoryOptions = json_decode(file_get_contents('../backend/json/CategoryOptions.json'), true);
  ?>

  <div class="pageFilterToolbar">
    <div class="filterToolBar">
      <div class="pageType">
        <button type="button" class="logoFont selectedButton">Home</button>
        <p class="logoFont">/</p>
        <button type="button" class="logoFont">Explore</button>
      </div>
      <div class="FilterButtons">
        <button type="button">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960">
            <path
              d="m388-80-20-126q-19-7-40-19t-37-25l-118 54-93-164 108-79q-2-9-2.5-20.5T185-480q0-9 .5-20.5T188-521L80-600l93-164 118 54q16-13 37-25t40-18l20-127h184l20 126q19 7 40.5 18.5T669-710l118-54 93 164-108 77q2 10 2.5 21.5t.5 21.5q0 10-.5 21t-2.5 21l108 78-93 164-118-54q-16 13-36.5 25.5T592-206L572-80H388Zm48-60h88l14-112q33-8 62.5-25t53.5-41l106 46 40-72-94-69q4-17 6.5-33.5T715-480q0-17-2-33.5t-7-33.5l94-69-40-72-106 46q-23-26-52-43.5T538-708l-14-112h-88l-14 112q-34 7-63.5 24T306-642l-106-46-40 72 94 69q-4 17-6.5 33.5T245-480q0 17 2.5 33.5T254-413l-94 69 40 72 106-46q24 24 53.5 41t62.5 25l14 112Zm44-210q54 0 92-38t38-92q0-54-38-92t-92-38q-54 0-92 38t-38 92q0 54 38 92t92 38Zm0-130Z" />
          </svg>
        </button>
        <button type="button" id="filterButton">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960">
            <path d="M400-160v-280L118-800h724L560-440v280H400Zm80-276 240-304H240l240 304Zm0 0Z" />
          </svg>
        </button>
      </div>
    </div>
    <div id="filterMenu" class="filterMenu" style="display: none;">
      <div class="filterMenuContainer">
        <?php foreach ($categoryOptions as $option): ?>
          <div class="MenuButton">
            <a href="#" data-filter="<?= $option['value']; ?>">
              <?= $option['text']; ?>
            </a>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
    <script>
      document.getElementById('filterButton').addEventListener('click', function () {
        const filterMenu = document.getElementById('filterMenu');
        filterMenu.style.display = filterMenu.style.display === 'none' ? 'flex' : 'none';
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