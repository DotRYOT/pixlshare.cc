<?php require "./pageScript.php"; ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profile - PIXLSHARE</title>
  <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
  <link rel="shortcut icon" href="<?= filePath("/assets/logos/"); ?>pixlshareLogo_color_128.png" type="image/x-icon">
  <link rel="stylesheet" href="./css/index.min.css">
  <?php if ($_SERVER['HTTP_HOST'] == 'pixlshare.cc') { ?>
    <link rel="manifest" href="<?= filePath("/") ?>manifest.json" />
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
  <script>
    function toggleTab(tabId) {
      const userPosts = document.getElementById('userPosts');
      const likesTab = document.getElementById('LikesTab');
      const bookmarks = document.getElementById('bookmarks');
      const userComments = document.getElementById('userComments');

      if (tabId === 'LikesTab') {
        likesTab.style.display = 'flex';
        userPosts.style.display = 'none';
        bookmarks.style.display = 'none';
        userComments.style.display = 'none';
        UserLikesLoader();
      } else if (tabId === 'userPosts') {
        likesTab.style.display = 'none';
        userPosts.style.display = 'flex';
        bookmarks.style.display = 'none';
        userComments.style.display = 'none';
        loadPosts();
      } else if (tabId === 'bookmarks') {
        likesTab.style.display = 'none';
        userPosts.style.display = 'none';
        bookmarks.style.display = 'flex';
        userComments.style.display = 'none';
        UserBookmarkLoader();
      } else if (tabId === 'userComments') {
        likesTab.style.display = 'none';
        userPosts.style.display = 'none';
        bookmarks.style.display = 'none';
        userComments.style.display = 'flex';
        userCommentsLoader();
      }
    }

    document.addEventListener('DOMContentLoaded', function () {
      toggleTab('userPosts');
      // UserPostLoader();
    });
  </script>
</head>

<body>
  <?php require "../backend/_nav.php"; ?>
  <section class="profileContainer">
    <div class="profileUserCard">
      <div class="userProfileBackground">
        <img src="<?= $BGImageLink; ?>" alt="">
      </div>
      <div class="userProfilePicture">
        <img src="<?= $PFPImageLink; ?>" alt="">
        <?php
        if ($_SESSION['user']['userLevel'] === 1) {
          ?>
          <div class="admin" title="Site Admin">
            <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px" fill="#FFFFFF">
              <path
                d="M480-81q-140-35-230-162.5T160-523v-238l320-120 320 120v238q0 152-90 279.5T480-81Zm0-62q115-38 187.5-143.5T740-523v-196l-260-98-260 98v196q0 131 72.5 236.5T480-143Zm0-337Z" />
            </svg>
          </div>
          <?php
        }
        $ageCheck = json_decode($_SESSION['user']['ageCheck'], true);
        $isOver18 = $ageCheck['is_over_18'];
        if ($isOver18 == true) {
          ?>
          <div class="check" title="Age Verified Profile">
            <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px" fill="#FFFFFF">
              <path d="M378-246 154-470l43-43 181 181 384-384 43 43-427 427Z" />
            </svg>
          </div>
        <?php } ?>
      </div>
      <div class="userInfo">
        <button type="button" onclick="location.href='./settings/stats/?tag=<?= $UUID; ?>';">
          <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px" fill="#FFFFFF">
            <path
              d="M38-160v-94q0-35 18-63.5t50-42.5q73-32 131.5-46T358-420q62 0 120 14t131 46q32 14 50.5 42.5T678-254v94H38Zm700 0v-94q0-63-32-103.5T622-423q69 8 130 23.5t99 35.5q33 19 52 47t19 63v94H738ZM358-481q-66 0-108-42t-42-108q0-66 42-108t108-42q66 0 108 42t42 108q0 66-42 108t-108 42Zm360-150q0 66-42 108t-108 42q-11 0-24.5-1.5T519-488q24-25 36.5-61.5T568-631q0-45-12.5-79.5T519-774q11-3 24.5-5t24.5-2q66 0 108 42t42 108ZM98-220h520v-34q0-16-9.5-31T585-306q-72-32-121-43t-106-11q-57 0-106.5 11T130-306q-14 6-23 21t-9 31v34Zm260-321q39 0 64.5-25.5T448-631q0-39-25.5-64.5T358-721q-39 0-64.5 25.5T268-631q0 39 25.5 64.5T358-541Zm0 321Zm0-411Z" />
          </svg>
          <p>Followers</p>
          <span id="followerCount">0</span>
        </button>
      </div>
    </div>
    <div class="userProfileInfo">
      <div class="userInfoName">
        <div class="userName" onclick="copyLink()">@<?= $UserName; ?></div>
      </div>
      <div class="userInfoBio">
        <div class="userBio">
          <?= nl2br($profile_bio); ?>
        </div>
      </div>
    </div>
  </section>
  <script>
    function copyLink() {
      const linkToCopy = "<?= filePath("/profile/?p=") . $UserName; ?>";
      navigator.clipboard.writeText(linkToCopy).then(() => {
        alert('Link copied to clipboard!');
      }).catch(err => {
        console.error('Failed to copy: ', err);
      });
    }

    function LoadFollowerCount() {
      fetch("../backend/api/_loadFollowNumber.php?UUID=<?= $UUID; ?>").then(response => {
        console.log('Response status:', response.status);
        return response.text();
      })
        .then(data => {
          document.getElementById('followerCount').innerHTML = data;
        })
        .catch(error => console.error('Error loading likes'));
    }
    setInterval(LoadFollowerCount, 10000);
    LoadFollowerCount();
  </script>
  <div class="toolBar">
    <div class="toolBarContainer">
      <button type="button" name="userPosts" onclick="toggleTab('userPosts')">
        <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px" fill="#FFFFFF">
          <path
            d="M120-120v-245h330v245H120Zm390 0v-415h330v415H510ZM390-305Zm180-170Zm-450 50v-415h330v415H120Zm270-60Zm120-110v-245h330v245H510Zm60-60ZM180-180h210v-125H180v125Zm390 0h210v-295H570v295ZM180-485h210v-295H180v295Zm390-170h210v-125H570v125Z" />
        </svg>
      </button>
      <button type="button" name="userComments" onclick="toggleTab('userComments')">
        <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px" fill="#FFFFFF">
          <path
            d="M240-400h480v-60H240v60Zm0-130h480v-60H240v60Zm0-130h480v-60H240v60ZM80-240v-640h800v800L720-240H80Zm60-60h606l74 80v-600H140v520Zm0 0v-520 520Z" />
        </svg>
      </button>
      <button type="button" name="Likes" onclick="toggleTab('LikesTab')">
        <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px" fill="#FFFFFF">
          <path
            d="m480-121-41-37q-105.77-97.12-174.88-167.56Q195-396 154-451.5T96.5-552Q80-597 80-643q0-90.15 60.5-150.58Q201-854 290-854q57 0 105.5 27t84.5 78q42-54 89-79.5T670-854q89 0 149.5 60.42Q880-733.15 880-643q0 46-16.5 91T806-451.5Q765-396 695.88-325.56 626.77-255.12 521-158l-41 37Zm0-79q101.24-93 166.62-159.5Q712-426 750.5-476t54-89.14q15.5-39.13 15.5-77.72 0-66.14-42-108.64T670.22-794q-51.52 0-95.37 31.5T504-674h-49q-26-56-69.85-88-43.85-32-95.37-32Q224-794 182-751.5t-42 108.82q0 38.68 15.5 78.18 15.5 39.5 54 90T314-358q66 66 166 158Zm0-297Z" />
        </svg>
      </button>
      <button type="button" name="Likes" onclick="toggleTab('bookmarks')">
        <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px" fill="#FFFFFF">
          <path d="M200-120v-725h560v725L480-240 200-120Zm60-91 220-93 220 93v-574H260v574Zm0-574h440-440Z" />
        </svg>
      </button>
      <button type="button" onclick="window.location.href='./settings/'">
        <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px" fill="#FFFFFF">
          <path
            d="m388-80-20-126q-19-7-40-19t-37-25l-118 54-93-164 108-79q-2-9-2.5-20.5T185-480q0-9 .5-20.5T188-521L80-600l93-164 118 54q16-13 37-25t40-18l20-127h184l20 126q19 7 40.5 18.5T669-710l118-54 93 164-108 77q2 10 2.5 21.5t.5 21.5q0 10-.5 21t-2.5 21l108 78-93 164-118-54q-16 13-36.5 25.5T592-206L572-80H388Zm48-60h88l14-112q33-8 62.5-25t53.5-41l106 46 40-72-94-69q4-17 6.5-33.5T715-480q0-17-2-33.5t-7-33.5l94-69-40-72-106 46q-23-26-52-43.5T538-708l-14-112h-88l-14 112q-34 7-63.5 24T306-642l-106-46-40 72 94 69q-4 17-6.5 33.5T245-480q0 17 2.5 33.5T254-413l-94 69 40 72 106-46q24 24 53.5 41t62.5 25l14 112Zm44-210q54 0 92-38t38-92q0-54-38-92t-92-38q-54 0-92 38t-38 92q0 54 38 92t92 38Zm0-130Z" />
        </svg>
      </button>
    </div>
  </div>
  <section class="postCardContainer userPosts" id="userPosts" style="display: none;"></section>
  <section class="postCardContainer userComments" id="userComments" style="display: none;"></section>
  <section class="postCardContainer LikesTab" id="LikesTab" style="display: none;"></section>
  <section class="postCardContainer bookmarks" id="bookmarks" style="display: none;"></section>
  <script src="./scripts/_accountPostLoader.js"></script>
  <script src="./scripts/_userCommentsLoader.js"></script>
  <script src="./scripts/_userLikesLoader.js"></script>
  <script src="./scripts/_userBookmarksLoader.js"></script>
  <div id="loadingSpinner" class="spinner hidden">Loading...</div>
  <?php require "../backend/_footer.php"; ?>
  <script type="module" src="https://cdn.jsdelivr.net/npm/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
  <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js" crossorigin></script>
</body>

</html>