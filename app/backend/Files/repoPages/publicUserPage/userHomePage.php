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
            <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px" fill="#FFFFFF">
              <path
                d="M480-81q-140-35-230-162.5T160-523v-238l320-120 320 120v238q0 152-90 279.5T480-81Zm0-62q115-38 187.5-143.5T740-523v-196l-260-98-260 98v196q0 131 72.5 236.5T480-143Zm0-337Z" />
            </svg>
          </div>
          <?php
        }
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
        <?php if (isset($_SESSION['user']['UUID'])) { ?>
          <button id="followButton" onclick="followUser(this)">
            <svg xmlns="http://www.w3.org/2000/svg" id="followingPersonIcon" style="display: none;" height="48px" viewBox="0 -960 960 960"
              width="48px" fill="#FFFFFF">
              <path
                d="M480-481q-66 0-108-42t-42-108q0-66 42-108t108-42q66 0 108 42t42 108q0 66-42 108t-108 42ZM160-160v-94q0-38 19-65t49-41q67-30 128.5-45T480-420q62 0 123 15.5t127.92 44.69q31.3 14.13 50.19 40.97Q800-292 800-254v94H160Zm60-60h520v-34q0-16-9.5-30.5T707-306q-64-31-117-42.5T480-360q-57 0-111 11.5T252-306q-14 7-23 21.5t-9 30.5v34Zm260-321q39 0 64.5-25.5T570-631q0-39-25.5-64.5T480-721q-39 0-64.5 25.5T390-631q0 39 25.5 64.5T480-541Zm0-90Zm0 411Z" />
            </svg>
            <svg xmlns="http://www.w3.org/2000/svg" id="followingPersonIconPlus"height="48px"
              viewBox="0 -960 960 960" width="48px" fill="#FFFFFF">
              <path
                d="M730-400v-130H600v-60h130v-130h60v130h130v60H790v130h-60Zm-370-81q-66 0-108-42t-42-108q0-66 42-108t108-42q66 0 108 42t42 108q0 66-42 108t-108 42ZM40-160v-94q0-35 17.5-63.5T108-360q75-33 133.34-46.5t118.5-13.5Q420-420 478-406.5T611-360q33 15 51 43t18 63v94H40Zm60-60h520v-34q0-16-9-30.5T587-306q-71-33-120-43.5T360-360q-58 0-107.5 10.5T132-306q-15 7-23.5 21.5T100-254v34Zm260-321q39 0 64.5-25.5T450-631q0-39-25.5-64.5T360-721q-39 0-64.5 25.5T270-631q0 39 25.5 64.5T360-541Zm0-90Zm0 411Z" />
            </svg>
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
              const followingPersonIcon = document.getElementById('followingPersonIcon');
              const followingPersonIconPlus = document.getElementById('followingPersonIconPlus');
              if (data.success) {
                if (isFollowing) {
                  document.getElementById('followText').innerText = 'Follow';
                  button.classList.remove("following");
                  followingPersonIcon.style.display = 'none';
                  followingPersonIconPlus.style.display = 'block';
                } else {
                  document.getElementById('followText').innerText = 'Following';
                  button.classList.add("following");
                  followingPersonIcon.style.display = 'block';
                  followingPersonIconPlus.style.display = 'none';
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