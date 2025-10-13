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
      <ion-icon name="person-circle-outline"></ion-icon>
      <h3>Profile</h3>
    </button>
    <button type="button" onclick="toggleTab('accountSettings')">
      <ion-icon name="folder-open-outline"></ion-icon>
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
              <ion-icon name="image-outline"></ion-icon>
              <input type="file" name="profileImage" id="profilePictureInput" accept="image/*" style="display: none;">
            </button>
          </div>
          <div class="imagePreview">
            <img id="backgroundImagePreview" src="<?= $BGImageLink; ?>" alt="Background Image Preview">
            <label for="backgroundImage">Background Image</label>
            <button type="button" name="profilePicUpload"
              onclick="document.getElementById('backgroundImageInput').click();">
              <ion-icon name="image-outline"></ion-icon>
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
          <ion-icon name="calendar-outline"></ion-icon>
          <p>Verify Age</p>
        </button>
        <button type="button" onclick="togglePasswordChangeForm()" class="normal">
          <ion-icon name="settings-outline"></ion-icon>
          <p>Change Password</p>
        </button>
        <div class="pageGap5"></div>
        <button type="button" onclick="window.location.href='../../backend/scripts/account/_authReset.php'"
          class="normal" title="Reset authentication tokens for your account!">
          <ion-icon name="refresh-outline"></ion-icon>
          <p>Auth Reset</p>
        </button>
        <button type="button" onclick="window.location.href='../../account/signout/'" class="normal">
          <ion-icon name="settings-outline"></ion-icon>
          <p>Signout</p>
        </button>
        <button type="button" name="requestAccountDelete" class="AccountWarningStyle">
          <ion-icon name="warning-outline"></ion-icon>
          <p>Request Account Deletion</p>
        </button>
      </div>
    </div>
  </section>

  <section id="passwordChangeForm" style="display: none;">
    <button type="button" class="close" onclick="togglePasswordChangeForm()">
      <ion-icon name="close-outline"></ion-icon>
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
      <ion-icon name="close-outline"></ion-icon>
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
      if (confirm('Are you sure you want to delete this account?')) {
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
  <script type="module" src="https://cdn.jsdelivr.net/npm/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
  <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js" crossorigin></script>
</body>

</html>