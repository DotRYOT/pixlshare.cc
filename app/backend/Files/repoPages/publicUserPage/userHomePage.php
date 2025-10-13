<?php require "../../../backend/Files/repoPages/publicUserPage/pageScript.php"; ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profile - PIXLSHARE</title>
  <?php if ($_SERVER['HTTP_HOST'] == 'pixlshare.cc') { ?>
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
  <?php } ?>
  <link rel="shortcut icon" href="<?= filePath("/assets/logos/"); ?>pixlshareLogo_color_128.png" type="image/x-icon">
  <link rel="stylesheet" href="<?= filePath("/") ?>profile/css/publicProfile.min.css">
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
</head>

<body>
  <?php
  if ($_SESSION['user']['UUID'] === null) {
    require "../../../backend/_nav_noacc.php";
  } else {
    require "../../../backend/_nav.php";
  }
  ?>
  <section class="profileContainer">
    <div class="profileUserCard">
      <div class="userProfileBackground">
        <img src="<?= $profile_BGImageLink; ?>" alt="">
      </div>
      <div class="userProfilePicture">
        <img src="<?= $profile_PFPImageLink; ?>" alt="">
        <?php
        if ($userLevel === '1') {
          ?>
          <div class="admin" title="Site Admin">
            <ion-icon name="shield-outline"></ion-icon>
          </div>
          <?php
        }
        if ($isOver18 == true) {
          ?>
          <div class="check" title="Age Verified Profile">
            <ion-icon name="checkmark-outline"></ion-icon>
          </div>
        <?php } ?>
      </div>
      <div class="userInfo">
        <?php if (isset($_SESSION['user']['UUID'])) { ?>
          <button id="followButton" onclick="followUser(this)">
            <ion-icon id="followingPersonIcon" name="person-add-outline"></ion-icon>
            <h3 id="followText"></h3>
          </button>
        <?php } ?>
      </div>
    </div>
    <div class="userProfileInfo">
      <div class="userInfoName">
        <div class="userName" onclick="copyLink()">@<?= $profile_UserName; ?></div>
      </div>
      <div class="userInfoBio">
        <div class="userBio"><?= $profile_bio; ?></div>
      </div>
    </div>
  </section>
  <div class="toolBar">
    <div class="toolBarContainerViewPage">
      <button type="button" name="followerCount">
        <p><span id="followerCount"></span> Followers</p>
      </button>
    </div>
  </div>
  <section class="postCardContainer userPosts" id="userPosts"></section>
  <script>
    const PageUUID = '<?= $HostUUID; ?>';
  </script>
  <script src="<?= filePath("/profile/scripts/") ?>_accountPagePostLoader.js"></script>
  <div id="loadingSpinner" class="spinner hidden">Loading...</div>
  <?php if (isset($_SESSION['user']['UUID'])) { ?>
    <script>
      function followUser(button) {
        const isFollowing = button.classList.contains("following");
        button.disabled = true;
        const url = isFollowing ?
          '../../../backend/api/_unfollowUser.php?UUID=<?= $HostUUID; ?>' :
          '../../../backend/api/_followUser.php?UUID=<?= $HostUUID; ?>';

        fetch(url, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            userId: '<?= $HostUUID; ?>'
          })
        })
          .then(response => {
            console.log('Response status:', response.status);
            return response.text();
          })
          .then(text => {
            console.log('Response text:', text);
            try {
              const data = JSON.parse(text);
              if (data.success) {
                if (isFollowing) {
                  document.getElementById('followText').innerText = 'Follow';
                  button.classList.remove("following");
                  document.getElementById('followingPersonIcon').setAttribute('name', 'person-add-outline');
                } else {
                  document.getElementById('followText').innerText = 'Following';
                  button.classList.add("following");
                  document.getElementById('followingPersonIcon').setAttribute('name', 'person-outline');
                }
              }
            } catch (error) {
              console.error('Error parsing JSON:', error);
              // alert('An error occurred: ' + text);
            }
          })
          .catch(error => {
            console.error('Error:', error);
          })
          .finally(() => {
            button.disabled = false;
          });
      }

      function checkIfFollowing() {
        fetch('../../../backend/api/_checkFollowing.php?UUID=<?= $HostUUID; ?>')
          .then(response => {
            if (!response.ok) {
              throw new Error('Network response was not ok: ' + response.statusText);
            }
            return response.json();
          })
          .then(data => {
            if (data.isFollowing) {
              document.getElementById('followText').innerText = 'Following';
              document.getElementById('followButton').classList.add("following");
              document.getElementById('followingPersonIcon').setAttribute('name', 'person-outline'); // Change icon to person-remove-outline
            } else {
              document.getElementById('followText').innerText = 'Follow';
              document.getElementById('followingPersonIcon').setAttribute('name', 'person-outline'); // Change icon back to person-outline
            }
          })
          .catch(error => {
            console.error('Error checking following status:', error);
            // alert('Failed to check following status. Please try again later.');
          });
      }

      document.addEventListener('DOMContentLoaded', checkIfFollowing);

      function LoadFollowerCount() {
        fetch("../../../backend/api/_loadFollowNumber.php?UUID=<?= $HostUUID; ?>").then(response => {
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
  <?php } ?>

  <?php
  require "../../../backend/_footer.php";
  ?>
  <script type="module" src="https://cdn.jsdelivr.net/npm/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
  <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js" crossorigin></script>
</body>

</html>