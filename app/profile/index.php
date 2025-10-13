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
            <ion-icon name="shield-outline"></ion-icon>
          </div>
          <?php
        }
        $ageCheck = json_decode($_SESSION['user']['ageCheck'], true);
        $isOver18 = $ageCheck['is_over_18'];
        if ($isOver18 == true) {
          ?>
          <div class="check" title="Age Verified Profile">
            <ion-icon name="checkmark-outline"></ion-icon>
          </div>
        <?php } ?>
      </div>
      <div class="userInfo">
        <button type="button" onclick="location.href='./settings/stats/?tag=<?= $UUID; ?>';">
          <ion-icon name="people-outline"></ion-icon>
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
        <ion-icon name="grid-outline"></ion-icon>
      </button>
      <button type="button" name="userComments" onclick="toggleTab('userComments')">
        <ion-icon name="chatbubble-ellipses-outline"></ion-icon>
      </button>
      <button type="button" name="Likes" onclick="toggleTab('LikesTab')">
        <ion-icon name="heart-outline"></ion-icon>
      </button>
      <button type="button" name="Likes" onclick="toggleTab('bookmarks')">
        <ion-icon name="bookmarks-outline"></ion-icon>
      </button>
      <button type="button" onclick="window.location.href='./settings/'">
        <ion-icon name="settings-outline"></ion-icon>
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