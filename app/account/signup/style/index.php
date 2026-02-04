<?php
session_start();

require "../../../backend/connect/MainConnect.php";
require "../../../backend/_include.php";
require "../../../backend/_auth.php";

$UUID = mysqli_real_escape_string($conn_main, $_SESSION['user']['UUID']);

if ($_SESSION['user']['profile_setup_complete'] === true) {
  header("Location: ../../../home/");
  exit();
}

$_SESSION['user']['profile_setup_complete'] = true;

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Setup Your Profile - PIXLSHARE</title>
  <link rel="stylesheet" href="./css/index.min.css">
  <link rel="shortcut icon" href="../../../assets/logos/pixlshareLogo_white_128.png" type="image/x-icon">
  <link rel="manifest" href="../../../manifest.json">
</head>

<body>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script type="module" src="https://cdn.jsdelivr.net/npm/ldrs/dist/auto/lineWobble.js"></script>
  <!-- %@@@@@@@@@@@@@@@@%@@@@%@%@@@@@@@@@@@@@@@@@@@@%@@@@@@ -->
  <!-- @@@@@@@@@@@%@%%@@@@@@@@@@@@@@@@@@@%@%@@@@@@@@@@@@@@@ -->
  <!-- @@@@@@@@#::::::::::::-+#%%@%%::::::::::::::::::+%@@% -->
  <!-- @@@@@@@%-               .*%%+                 .*%%@@ -->
  <!-- @@@@@@@%.                 %%:                 :%@@@@ -->
  <!-- @@@@@@@*                  ##                  =%@@@@ -->
  <!-- @@@@@#:      *%%%=      -#%%%%%*.     .*%%%%%%%@@@@@ -->
  <!-- @@@@%=       ===-       #%@@@@%=      =%@@@@@%@@@@@@ -->
  <!-- @@@@%-                 +%@@%@%#:      *%@@%@@@@@@%@@ -->
  <!-- @@@%*.               :#%@@@@@%*      :#@@@@@@@@@%@@@ -->
  <!-- @@@%*-              :+%%%@%@@%#=      -%%@@@@@@@@@@@ -->
  <!-- @@@@%=      .%#:      *%@%@@%@%=      =%@@@@@@@@@@@@ -->
  <!-- @%%@%-      =@%=      =%@@@@@@#:      *%@@%@@@@@@@@@ -->
  <!-- @@@@*.      #@%*      .#%@@@@%*      :#%@@@@@@@@@@%@ -->
  <!-- @@@@#*******%%%%#******%%@@@@%#******#%@@@@@@@@@@@@@ -->
  <!-- @@@@%@@@@@@@%@@@@%@@@@@@%@@@@@@@@@@@@@@@@@@@@%%@@@@@ -->
  <!-- @@@@@%@@@@@@@@@%@@@@@@%@@@%@@@@%@@@@@@@@@@%@@@@@@@@@ -->
  <nav id="topNav">
    <div class="topNavLeft">
      <p class="logoFont">PXL</p>
    </div>
    <div class="topNavRight">
      <h2>Profile Style</h2>
    </div>
  </nav>
  <nav id="bottomNav">
    <div class="navButton">
      <button type="button" name="signup" onclick="window.location.href='../../signup/'">
        <ion-icon size="large" name="log-in-outline"></ion-icon>
      </button>
    </div>
    <div class="navButton">
      <button type="button" name="signin" onclick="window.location.href='../../signin/'">
        <ion-icon size="large" name="create-outline"></ion-icon>
      </button>
    </div>
  </nav>

  <form action="../../../backend/scripts/account/_profileUpdateScript.php" enctype="multipart/form-data" method="post"
    class="form-container">
    <!-- Page 1 - Profile Pictures -->
    <div class="form-page" id="page1">
      <h2>Profile Setup (1/3)</h2>

      <div class="upload-section">
        <h3>Profile Picture</h3>
        <label class="upload-label">
          Click to upload
          <input type="file" id="pfpUpload" name="profileImage" accept="image/*" hidden>
          <img id="pfpPreview" class="preview-image">
        </label>
        <button class="skip-btn" type="button" onclick="skipUpload('pfp')">Skip</button>
      </div>

      <div class="upload-section">
        <h3>Background Image</h3>
        <label class="upload-label">
          Click to upload
          <input type="file" id="bgUpload" name="backgroundImage" accept="image/*" hidden>
          <img id="bgPreview" class="preview-image">
        </label>
        <button class="skip-btn" type="button" onclick="skipUpload('bg')">Skip</button>
      </div>

      <div class="navigation-btns">
        <button type="button" onclick="nextPage()">Next</button>
      </div>
    </div>

    <!-- Page 2 - Bio -->
    <div class="form-page hidden" id="page2">
      <h2>Profile Setup (2/3)</h2>

      <div class="bio-section">
        <h3>Bio</h3>
        <textarea id="bio" placeholder="Tell us about yourself..." name="bioText"></textarea>
        <button class="skip-btn" type="button" onclick="skipBio()">Skip</button>
      </div>

      <div class="navigation-btns">
        <button type="button" onclick="prevPage()">Back</button>
        <button type="button" onclick="showBackupPage()">Next</button>
      </div>
    </div>

    <!-- Page 3 - Backup Phrase -->
    <div class="form-page hidden" id="page3">
      <h2>Backup Phrase</h2>
      <div id="backupPhraseDisplay">
        <div class="loading-message">Loading your backup phrase...</div>
      </div>
      <div class="alert">
        Important: Please save this backup phrase securely. It is essential for account recovery.
      </div>
      <button type="button" id="copyBtn" onclick="copyBackupPhrase()" disabled>Copy to Clipboard</button>
      <div class="navigation-btns">
        <button type="button" onclick="prevPageFromBackup()">Back</button>
        <button type="submit">Finish Setup</button>
      </div>
    </div>
  </form>

  <script>
    // Initialize pages
    document.addEventListener('DOMContentLoaded', function () {
      document.getElementById('page1').classList.remove('hidden');

      // Fetch backup phrase from server
      fetch('../../../backend/scripts/account/_getBackupPhrase.php')
        .then(response => response.text())
        .then(data => {
          if (data && data !== 'null' && !data.includes('Error')) {
            displayBackupPhrase(data);
          } else {
            document.getElementById('backupPhraseDisplay').innerHTML =
              '<div class="error-message">Failed to load backup phrase</div>';
          }
        })
        .catch(error => {
          console.error('Error fetching backup phrase:', error);
          document.getElementById('backupPhraseDisplay').innerHTML =
            '<div class="error-message">Failed to load backup phrase</div>';
        });
    });

    // Page Navigation
    function nextPage() {
      document.getElementById('page1').classList.add('hidden');
      document.getElementById('page2').classList.remove('hidden');
    }

    function prevPage() {
      document.getElementById('page2').classList.add('hidden');
      document.getElementById('page1').classList.remove('hidden');
    }

    function showBackupPage() {
      document.getElementById('page2').classList.add('hidden');
      document.getElementById('page3').classList.remove('hidden');
    }

    function prevPageFromBackup() {
      document.getElementById('page3').classList.add('hidden');
      document.getElementById('page2').classList.remove('hidden');
    }

    // Display backup phrase with styling
    function displayBackupPhrase(phrase) {
      const phraseContainer = document.getElementById('backupPhraseDisplay');
      const words = phrase.split(' ');

      // Clear loading message
      phraseContainer.innerHTML = '';

      // Create container for phrase words
      const phraseWrapper = document.createElement('div');
      phraseWrapper.className = 'phrase-wrapper';

      words.forEach((word, index) => {
        const wordElement = document.createElement('span');
        wordElement.className = 'phrase-word';
        wordElement.textContent = `${index + 1}. ${word}`;
        phraseWrapper.appendChild(wordElement);
      });

      phraseContainer.appendChild(phraseWrapper);

      // Enable copy button
      document.getElementById('copyBtn').disabled = false;
    }

    // Copy backup phrase to clipboard
    function copyBackupPhrase() {
      const phraseElements = document.querySelectorAll('#backupPhraseDisplay .phrase-word');
      if (phraseElements.length === 0) return;

      const phrase = Array.from(phraseElements)
        .map(el => el.textContent.substring(3)) // Remove number prefix
        .join(' ');

      navigator.clipboard.writeText(phrase)
        .then(() => {
          const btn = document.getElementById('copyBtn');
          const originalText = btn.textContent;
          btn.textContent = 'Copied!';

          setTimeout(() => {
            btn.textContent = originalText;
          }, 2000);
        })
        .catch(err => {
          console.error('Failed to copy: ', err);
          alert('Failed to copy backup phrase');
        });
    }

    // Image Preview Handling
    document.getElementById('pfpUpload').addEventListener('change', function (e) {
      handleImageUpload(e, 'pfpPreview');
    });

    document.getElementById('bgUpload').addEventListener('change', function (e) {
      handleImageUpload(e, 'bgPreview');
    });

    function handleImageUpload(event, previewId) {
      const file = event.target.files[0];
      const preview = document.getElementById(previewId);

      if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
          preview.src = e.target.result;
          preview.style.display = 'block';
        }
        reader.readAsDataURL(file);
      }
    }

    // Skip Functions
    function skipUpload(type) {
      if (type === 'pfp') {
        document.getElementById('pfpUpload').value = '';
        document.getElementById('pfpPreview').style.display = 'none';
      } else {
        document.getElementById('bgUpload').value = '';
        document.getElementById('bgPreview').style.display = 'none';
      }
    }

    function skipBio() {
      document.getElementById('bio').value = '';
    }
  </script>
  <!-- Ionicons -->
  <script type="module" src="https://cdn.jsdelivr.net/npm/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
  <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js" crossorigin></script>
</body>

</html>