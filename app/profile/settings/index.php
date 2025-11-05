<?php require "./pageScript.php"; ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Settings - PIXLSHARE</title>
  <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
  <link rel="shortcut icon" href="<?= filePath("/assets/logos/"); ?>pixlshareLogo_color_128.png" type="image/x-icon">
  <link rel="stylesheet" href="./css/index.min.css">
  <link rel="manifest" href="<?= filePath("/"); ?>manifest.json" />
</head>

<body>
  <?php require "../../backend/_nav.php"; ?>
  <section class="InPageNav">
    <button type="button" onclick="toggleTab('settingsContainer')">
      <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px" fill="#FFFFFF">
        <path
          d="M222-255q63-44 125-67.5T480-346q71 0 133.5 23.5T739-255q44-54 62.5-109T820-480q0-145-97.5-242.5T480-820q-145 0-242.5 97.5T140-480q0 61 19 116t63 109Zm257.81-195q-57.81 0-97.31-39.69-39.5-39.68-39.5-97.5 0-57.81 39.69-97.31 39.68-39.5 97.5-39.5 57.81 0 97.31 39.69 39.5 39.68 39.5 97.5 0 57.81-39.69 97.31-39.68 39.5-97.5 39.5Zm.66 370Q398-80 325-111.5t-127.5-86q-54.5-54.5-86-127.27Q80-397.53 80-480.27 80-563 111.5-635.5q31.5-72.5 86-127t127.27-86q72.76-31.5 155.5-31.5 82.73 0 155.23 31.5 72.5 31.5 127 86t86 127.03q31.5 72.53 31.5 155T848.5-325q-31.5 73-86 127.5t-127.03 86Q562.94-80 480.47-80Zm-.47-60q55 0 107.5-16T691-212q-51-36-104-55t-107-19q-54 0-107 19t-104 55q51 40 103.5 56T480-140Zm0-370q34 0 55.5-21.5T557-587q0-34-21.5-55.5T480-664q-34 0-55.5 21.5T403-587q0 34 21.5 55.5T480-510Zm0-77Zm0 374Z" />
      </svg>
      <h3>Profile</h3>
    </button>
    <button type="button" onclick="toggleTab('accountSettings')">
      <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px" fill="#FFFFFF">
        <path d="M80-160v-640h341l60 60h399v580H80Zm60-60h680v-460H456l-60-60H140v520Zm0 0v-520 520Z" />
      </svg>
      <h3>Account</h3>
    </button>
  </section>

  <section class="AccountPageContent">
    <div class="settingsContainer" id="settingsContainer">
      <form id="settingsForm" action="../../backend/scripts/account/_profileUpdateScript.php" method="post"
        enctype="multipart/form-data">
        <div class="imageContainer">
          <div class="imagePreview">
            <img id="profilePicturePreview" src="<?= $PFPImageLink; ?>" alt="Profile Picture Preview">
            <label for="profilePicture">Profile Picture</label>
            <button type="button" name="profilePicUpload"
              onclick="document.getElementById('profilePictureInput').click();">
              <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px"
                fill="#FFFFFF">
                <path
                  d="M236-277h489L578-473 446-302l-93-127-117 152ZM120-120v-720h720v720H120Zm60-60h600v-600H180v600Zm0 0v-600 600Z" />
              </svg>
              <input type="file" name="profileImage" id="profilePictureInput" accept="image/*" style="display: none;">
            </button>
          </div>
          <div class="imagePreview">
            <img id="backgroundImagePreview" src="<?= $BGImageLink; ?>" alt="Background Image Preview">
            <label for="backgroundImage">Background Image</label>
            <button type="button" name="profilePicUpload"
              onclick="document.getElementById('backgroundImageInput').click();">
              <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px"
                fill="#FFFFFF">
                <path
                  d="M236-277h489L578-473 446-302l-93-127-117 152ZM120-120v-720h720v720H120Zm60-60h600v-600H180v600Zm0 0v-600 600Z" />
              </svg>
              <input type="file" name="backgroundImage" id="backgroundImageInput" accept="image/*"
                style="display: none;">
            </button>
          </div>
        </div>
        <input type="submit" value="Upload Images" class="upload-button">
      </form>
      <form id="bioForm" action="../../backend/scripts/account/_profileUpdateScript.php" method="post">
        <label for="bioText">Edit Bio</label>
        <textarea name="bioText" id="bioText" placeholder="Profile Bio"><?= $profile_bio; ?></textarea>
        <input type="submit" value="Update Bio">
      </form>
    </div>
    <div class="accountSettings" id="accountSettings" style="display: none;">
      <div class="accountContainer">
        <script>
          fetch("../../backend/scripts/profile/_UserState.php?UUID=<?= $VUID; ?>").then(response => {
            return response.json();
          }).then(data => {
            const userStateOptions = [
              { value: "0", text: "Good", color: "green" },
              { value: "500", text: "Suspend", color: "red" },
              { value: "600", text: "Warn 1", color: "orange" },
              { value: "610", text: "Warn 2", color: "orange" },
              { value: "620", text: "Warn 3", color: "orange" },
              { value: "700", text: "Ban", color: "red" }
            ];
            const userState = userStateOptions.find(option => option.value === data.userState.toString());
            if (userState) {
              document.getElementById('userState').innerHTML = userState.text;
              document.getElementById('userState').style.color = userState.color;
            } else {
              // Fallback for unknown user state
              document.getElementById('userState').innerHTML = "Unknown";
              document.getElementById('userState').style.color = "gray";
            }
          });
        </script>
        <button>
          Account Standing : <span id="userState"> Good</span>
        </button>
        <button type="button" onclick="toggleAgeForm()">
          <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px" fill="#FFFFFF">
            <path
              d="M120-80v-740h125v-60h65v60h340v-60h65v60h125v740H120Zm60-60h600v-430H180v430Zm0-490h600v-130H180v130Zm0 0v-130 130Z" />
          </svg>
          <p>Verify Age</p>
        </button>
        <button type="button" onclick="togglePasswordChangeForm()" class="normal">
          <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px" fill="#FFFFFF">
            <path
              d="m388-80-20-126q-19-7-40-19t-37-25l-118 54-93-164 108-79q-2-9-2.5-20.5T185-480q0-9 .5-20.5T188-521L80-600l93-164 118 54q16-13 37-25t40-18l20-127h184l20 126q19 7 40.5 18.5T669-710l118-54 93 164-108 77q2 10 2.5 21.5t.5 21.5q0 10-.5 21t-2.5 21l108 78-93 164-118-54q-16 13-36.5 25.5T592-206L572-80H388Zm48-60h88l14-112q33-8 62.5-25t53.5-41l106 46 40-72-94-69q4-17 6.5-33.5T715-480q0-17-2-33.5t-7-33.5l94-69-40-72-106 46q-23-26-52-43.5T538-708l-14-112h-88l-14 112q-34 7-63.5 24T306-642l-106-46-40 72 94 69q-4 17-6.5 33.5T245-480q0 17 2.5 33.5T254-413l-94 69 40 72 106-46q24 24 53.5 41t62.5 25l14 112Zm44-210q54 0 92-38t38-92q0-54-38-92t-92-38q-54 0-92 38t-38 92q0 54 38 92t92 38Zm0-130Z" />
          </svg>
          <p>Change Password</p>
        </button>
        <div class="pageGap5"></div>
        <button type="button" onclick="window.location.href='../../backend/scripts/account/_authReset.php'"
          class="normal" title="Reset authentication tokens for your account!">
          <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px" fill="#FFFFFF">
            <path
              d="M480-160q-133 0-226.5-93.5T160-480q0-133 93.5-226.5T480-800q85 0 149 34.5T740-671v-129h60v254H546v-60h168q-38-60-97-97t-137-37q-109 0-184.5 75.5T220-480q0 109 75.5 184.5T480-220q83 0 152-47.5T728-393h62q-29 105-115 169t-195 64Z" />
          </svg>
          <p>Auth Reset</p>
        </button>
        <button type="button" onclick="window.location.href='../../account/signout/'" class="normal">
          <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px" fill="#FFFFFF">
            <path
              d="m388-80-20-126q-19-7-40-19t-37-25l-118 54-93-164 108-79q-2-9-2.5-20.5T185-480q0-9 .5-20.5T188-521L80-600l93-164 118 54q16-13 37-25t40-18l20-127h184l20 126q19 7 40.5 18.5T669-710l118-54 93 164-108 77q2 10 2.5 21.5t.5 21.5q0 10-.5 21t-2.5 21l108 78-93 164-118-54q-16 13-36.5 25.5T592-206L572-80H388Zm48-60h88l14-112q33-8 62.5-25t53.5-41l106 46 40-72-94-69q4-17 6.5-33.5T715-480q0-17-2-33.5t-7-33.5l94-69-40-72-106 46q-23-26-52-43.5T538-708l-14-112h-88l-14 112q-34 7-63.5 24T306-642l-106-46-40 72 94 69q-4 17-6.5 33.5T245-480q0 17 2.5 33.5T254-413l-94 69 40 72 106-46q24 24 53.5 41t62.5 25l14 112Zm44-210q54 0 92-38t38-92q0-54-38-92t-92-38q-54 0-92 38t-38 92q0 54 38 92t92 38Zm0-130Z" />
          </svg>
          <p>Signout</p>
        </button>
        <button type="button" name="requestAccountDelete" class="AccountWarningStyle">
          <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px" fill="#FFFFFF">
            <path
              d="m40-120 440-760 440 760H40Zm104-60h672L480-760 144-180Zm340.18-57q12.82 0 21.32-8.68 8.5-8.67 8.5-21.5 0-12.82-8.68-21.32-8.67-8.5-21.5-8.5-12.82 0-21.32 8.68-8.5 8.67-8.5 21.5 0 12.82 8.68 21.32 8.67 8.5 21.5 8.5ZM454-348h60v-224h-60v224Zm26-122Z" />
          </svg>
          <p>Request Account Deletion</p>
        </button>
      </div>
    </div>
  </section>

  <div class="accountExport" id="accountExport" style="display: none;">
    <div class="accountExportContainer">
      <h1>Attention!</h1>
      <p>Your posts/data will be exported and e-mailed a link to you from the PixlShare official email. Don't forget to
        download
        it before it expires in 30 days.</p>
      <h3>Your account will also be disabled to start the process.</h3>
      <h4>Thank you for using PixlShare (:</h4>
      <h3>Are you sure you want to delete your account?</h3>
      <div class="accountExportFinish">
        <button name="finishDelete" class="finishWarning">Confirm!</button>
        <button name="finishCancel">Cancel!</button>
      </div>
    </div>
  </div>

  <section id="passwordChangeForm" style="display: none;">
    <button type="button" class="close" onclick="togglePasswordChangeForm()">
      <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px" fill="#FFFFFF">
        <path d="m249-207-42-42 231-231-231-231 42-42 231 231 231-231 42 42-231 231 231 231-42 42-231-231-231 231Z" />
      </svg>
    </button>
    <form action="../../backend/scripts/account/_passwordChange.php" method="post">
      <input type="password" name="CurrentPassword" id="CurrentPassword" placeholder="Current Password">
      <input type="password" name="NewPassword" id="NewPassword" placeholder="New Password">
      <input type="password" name="RePassword" id="RePassword" placeholder="Re-Password">
      <input type="hidden" name="type" value="1">
      <input type="submit" value="Change Password">
    </form>
  </section>

  <section id="ageForm" class="ageForm" style="display: none;">
    <button type="button" class="close" onclick="toggleAgeForm()">
      <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px" fill="#FFFFFF">
        <path d="m249-207-42-42 231-231-231-231 42-42 231 231 231-231 42 42-231 231 231 231-42 42-231-231-231 231Z" />
      </svg>
    </button>
    <form action="../../backend/scripts/account/_ageVerify.php" method="post">
      <input type="date" name="age" id="age">
      <input type="submit" value="Select Age">
    </form>
  </section>

  <script>
    function togglePasswordChangeForm() {
      const passwordChangeForm = document.getElementById('passwordChangeForm');
      if (passwordChangeForm.style.display === 'none' || passwordChangeForm.style.display === '') {
        passwordChangeForm.style.display = 'flex';
      } else {
        passwordChangeForm.style.display = 'none';
      }
    }

    function toggleAgeForm() {
      const ageToggle = document.getElementById('ageForm');
      if (ageToggle.style.display === 'none' || ageToggle.style.display === '') {
        ageToggle.style.display = 'flex';
      } else {
        ageToggle.style.display = 'none';
      }
    }
    document.querySelector('button[name="requestAccountDelete"]').addEventListener('click', function () {
      const accountExport = document.getElementById('accountExport');
      accountExport.style.display = 'flex';
      if (document.querySelector('button[name="finishCancel"]')) {
        document.querySelector('button[name="finishCancel"]').addEventListener('click', function () {
          accountExport.style.display = 'none';
        });
      }
      if (document.querySelector('button[name="finishDelete"]')) {
        document.querySelector('button[name="finishDelete"]').addEventListener('click', function () {
          fetch(`../../backend/scripts/account/_requestAccountDelete.php`, {
            method: 'POST'
          })
            .then(response => response.text())
            .then(data => {
              console.log('Delete response:', data);
              if (data.includes('success')) {
                alert('Request sent successfully');
                window.location.href = '<?= filePath("/profile/"); ?>';
              } else {
                alert('Error sending request: ' + data);
              }
            })
            .catch(error => console.error('Error deleting post:', error));
        });
      }
    });

    const profilePictureInput = document.getElementById('profilePictureInput');
    const profilePicturePreview = document.getElementById('profilePicturePreview');
    const backgroundImageInput = document.getElementById('backgroundImageInput');
    const backgroundImagePreview = document.getElementById('backgroundImagePreview');

    profilePictureInput.addEventListener('change', function () {
      const file = this.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
          profilePicturePreview.src = e.target.result;
        }
        reader.readAsDataURL(file);
      }
    });

    backgroundImageInput.addEventListener('change', function () {
      const file = this.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
          backgroundImagePreview.src = e.target.result;
        }
        reader.readAsDataURL(file);
      }
    });

    function toggleTab(tabId) {
      const settingsContainer = document.getElementById('settingsContainer');
      const accountSettings = document.getElementById('accountSettings');

      if (tabId === 'settingsContainer') {
        settingsContainer.style.display = 'flex';
        accountSettings.style.display = 'none';
      } else {
        settingsContainer.style.display = 'none';
        accountSettings.style.display = 'flex';
      }
    }
  </script>
</body>

</html>