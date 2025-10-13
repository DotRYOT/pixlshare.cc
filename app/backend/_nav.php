<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="module" src="https://cdn.jsdelivr.net/npm/ldrs/dist/auto/lineWobble.js"></script>
<script src="<?= filePath("/"); ?>/backend/gifPause.js"></script>
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
    <button type="button" name="notifications" onclick="toggleNotifications()">
      <ion-icon name="file-tray-full-outline"></ion-icon>
      <div id="NoteAlert" class="NoteAlert" style="display: none;">
        <ion-icon name="alert-outline"></ion-icon>
      </div>
    </button>
    <?php if ($_SESSION['user']['userLevel'] == 1) { ?>
      <button type="button" name="adminPage" onclick="window.location.href='<?= filePath("/admin/"); ?>'">
        <ion-icon name="shield-outline"></ion-icon>
      </button>
    <?php } ?>
  </div>
</nav>
<nav id="bottomNav">
  <div class="navButton first">
    <button type="button" name="home" onclick="window.location.href='<?= filePath("/home/"); ?>'">
      <ion-icon name="home-outline"></ion-icon>
    </button>
  </div>
  <div class="navButton">
    <button type="button" name="explore" onclick="window.location.href='<?= filePath("/explore/"); ?>'">
      <ion-icon name="grid-outline"></ion-icon>
    </button>
  </div>
  <div class="postButton">
    <button type="button" onclick="togglePostPopup()">
      <ion-icon name="add-circle-outline"></ion-icon>
    </button>
  </div>
  <div class="navButton">
    <button type="button" onclick="toggleSearchPopup()">
      <ion-icon name="search-outline"></ion-icon>
    </button>
  </div>
  <?php if ($_SESSION['user']['userLevel'] == 1) { ?>
    <div class="navButton">
      <button type="button" name="store" id="store" onclick="window.location.href='<?= filePath("/skripstore/"); ?>'">
        <ion-icon name="storefront-outline"></ion-icon>
      </button>
    </div>
  <?php } ?>
  <div class="navButton last">
    <button type="button" name="profile" onclick="window.location.href='<?= filePath("/profile/"); ?>'">
      <img src="<?= $PFPImageLink; ?>" alt="" />
    </button>
  </div>
</nav>

<div class="postButtonFloat">
  <button type="button" onclick="togglePostPopup()">
    <ion-icon name="add-circle-outline"></ion-icon>
  </button>
</div>

<div class="searchContainer" id="searchPopup" style="display: none;">
  <div class="searchHeader">
    <button type="button" name="closeSearchPage" class="closeSearchWindow" onclick="toggleSearchPopup()">
      <ion-icon size="large" name="close-outline"></ion-icon>
    </button>
    <div class="searchPopup">
      <form id="searchForm">
        <input type="text" name="query" id="query" placeholder="Search..." required>
        <button type="submit" id="searchQueryButton">
          <ion-icon size="large" name="search-outline"></ion-icon>
        </button>
      </form>
    </div>
  </div>
  <div class="searchResults" id="searchResults" style="display: none;">
  </div>
</div>

<div class="postContainer" id="PostPopup" style="display: none;">
  <!-- <div class="postContainer" id="PostPopup" style="display: flex;"> -->
  <div class="postHeader">
    <div class="postPopup">
      <div id="loaderZoomie" class="loaderZoomie" style="display: none;">
        <l-line-wobble size="150" stroke="5" bg-opacity="0.1" speed="2" color="#ff4500"></l-line-wobble>
      </div>
      <form id="postForm" method="post" enctype="multipart/form-data"
        action="<?= filePath('/backend/api/') ?>post/_submitPost.php">
        <div class="titleAndClose">
          <p>Categories</p>
          <select name="PostFilterOptions" id="postFilterOptions"></select>
          <button type="button" class="CloseButton" onclick="togglePostPopup()">
            <ion-icon size="large" name="close-outline"></ion-icon>
          </button>
        </div>

        <script>
          fetch('<?= filePath("/backend/json/"); ?>CategoryOptions.json')
            .then(response => response.json())
            .then(data => {
              const selectElement = document.getElementById('postFilterOptions');
              data.forEach(option => {
                const opt = document.createElement('option');
                opt.value = option.value;
                opt.textContent = option.text;
                selectElement.appendChild(opt);
              });
            })
            .catch(error => console.error('Error fetching the JSON:', error));
        </script>

        <div class="middlePostContainer">
          <textarea name="postBody" id="postBody" placeholder="Text..." required maxlength="500"></textarea>
          <p id="charCount">800 characters remaining</p>
          <p id="lineBreakCount">15 line breaks remaining</p>
          <img id="image-preview" src="#" alt="Image Preview" style="display: none;">
          <canvas id="video-preview" style="display: none;"></canvas>
        </div>

        <script>
          const textarea = document.getElementById('postBody');
          const charCountDisplay = document.getElementById('charCount');
          const lineBreakCountDisplay = document.getElementById('lineBreakCount');

          textarea.addEventListener('input', function () {
            const remainingChars = 800 - textarea.value.length;
            charCountDisplay.textContent = `${remainingChars} characters remaining`;

            const lineBreaks = (textarea.value.match(/\n/g) || []).length;
            const remainingLines = 15 - lineBreaks;
            lineBreakCountDisplay.textContent = `${remainingLines} line breaks remaining`;
          });
        </script>

        <div class="youtubeEmbedPopup" id="youtubeEmbedPopup" style="display: none;">
          <div class="EmbedPopupContainer">
            <button type="button" name="youtubeEmbedTabClose" id="youtubeEmbedTabClose" onclick="toggleyoutubeEmbed()">
              <ion-icon name="close-outline"></ion-icon>
            </button>
            <section id="youtubeinputSection">
              <input type="url" name="youtubeLink" id="youtubeLink" placeholder="Link">
              <button type="button" name="addLink" id="addLink" onclick="addYouTubeLink()">
                Add Link
              </button>
            </section>
          </div>
        </div>

        <input type="hidden" id="linkData" name="linkData">

        <script>
          function toggleyoutubeEmbed() {
            const youtubeEmbedPopup = document.getElementById('youtubeEmbedPopup');
            youtubeEmbedPopup.style.display = youtubeEmbedPopup.style.display === 'none' ? 'flex' : 'none';
          }

          function validateYouTubeLink(link) {
            const regex = /^(?:https?:\/\/)?(?:www\.)?(?:youtube\.com|youtu\.be)\/(?:watch\?v=)?([^&\s]+)/;
            const match = link.match(regex);
            if (match && match[1]) {
              document.getElementById('linkData').value = match[1];
              return true;
            }
            return false;
          }

          function addYouTubeLink() {
            const youtubeLinkInput = document.getElementById('youtubeLink');
            const link = youtubeLinkInput.value;

            if (validateYouTubeLink(link)) {
              const videoId = document.getElementById('linkData').value;
              document.getElementById('fakeURL').textContent = `youtube.com/watch?v=` + videoId;
              document.getElementById('linkData').value = videoId;
              document.querySelector('button[name="textLinkShower"]').style.display = 'block';
              toggleyoutubeEmbed();
            } else {
              alert('Invalid YouTube link. Please try again.');
            }
          }
        </script>

        <div class="line"></div>

        <div class="bottomPostContainer">
          <div class="topLayer">
            <?php if ($_SERVER['HTTP_HOST'] == 'pixlshare.cc') { ?>
              <div style="display: block; flex-flow: row;">
                <div class="cf-turnstile" data-sitekey="0x4AAAAAAA27XzOGlxRe70Bl" data-size="flexible">
                </div>
              </div>
            <?php } ?>
          </div>
          <div class="bottomLayer">
            <div class="bottomPostLeft">
              <button type="button" id="uploadImageButton" onclick="document.getElementById('image-upload').click();">
                <input type="file" id="image-upload" accept="image/*,video/*" name="images" style="display: none;">
                <ion-icon name="image-outline"></ion-icon>
              </button>
              <button type="button" id="addYouTubeVideo" onclick="toggleyoutubeEmbed()">
                <ion-icon name="logo-youtube"></ion-icon>
              </button>
              <button type="button" name="textLinkShower" class="textLinkShower" style="display: none;">
                <p class="fakeURL" id="fakeURL">q5elqE21EHk</p>
              </button>
            </div>
            <button type="submit" id="postQueryButton" onclick="showLoaderAndHideForm()">
              <ion-icon size="large" name="newspaper-outline"></ion-icon>
              <h2>Post</h2>
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<div id="notificationWindow" class="notificationWindow" style="display: none;">
  <div class="notificationHeader">
    <p>Notifications</p>
  </div>
  <div class="noNote">
    <p>No new notifications</p>
  </div>
  <div class="note">
  </div>
</div>

<script>
  // Load notifications from your existing PHP script
  async function loadNotificationsIntoTray() {
    const win = document.getElementById('notificationWindow');
    if (!win) return;

    const noNoteEl = win.querySelector('.noNote');

    try {
      // Fetch from your existing script
      const response = await fetch('<?= filePath("/"); ?>backend/scripts/account/_loadNotifications.php');
      if (!response.ok) throw new Error('Failed to load notifications');

      const data = await response.json();

      // Remove previous dynamic notes
      document.querySelectorAll('.note.dynamic').forEach(el => el.remove());

      if (!data.notifications || data.totalCount === 0) {
        noNoteEl.style.display = 'flex';
        return;
      }

      const NoteAlert = document.getElementById('NoteAlert');
      if (data.totalCount > 0) {
        NoteAlert.style.display = 'flex';
      } else {
        NoteAlert.style.display = 'none';
      }

      noNoteEl.style.display = 'none';

      const notes = Object.entries(data.notifications)
        .map(([id, notif]) => ({ id, ...notif }))
        .sort((a, b) => new Date(b.timestamp) - new Date(a.timestamp));

      notes.forEach(note => {

        const message = note.type === 'like' ? 'You have a new like on →' : (note.type === 'comment' ? 'Someone liked your comment on →' : 'You have a new notification on →');

        const noteEl = document.createElement('div');
        noteEl.className = 'note dynamic';
        noteEl.innerHTML = `
        <a href="<?= filePath("") ?>${note.actionUrl}?action=like" class="note-link">
          <p>${message}</p>
          <img src="<?= filePath("/"); ?>${note.postPreview}" alt="Post preview">
        </a>
      `;

        win.insertBefore(noteEl, noNoteEl);
      });

    } catch (error) {
      console.error('Error loading notifications:', error);
      noNoteEl.querySelector('p').textContent = 'Error loading notifications';
      noNoteEl.style.display = 'flex';
    }
  }
  document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('notificationWindow')) {
      loadNotificationsIntoTray();
      // Run every 10 seconds
      setInterval(loadNotificationsIntoTray, 10000);
    }
  });

  function showLoaderAndHideForm() {
    document.getElementById('postForm').style.display = 'none';
    document.getElementById('loaderZoomie').style.display = 'flex';
  }

  document.addEventListener('DOMContentLoaded', function () {
    const imageUpload = document.getElementById('image-upload');
    const imagePreview = document.getElementById('image-preview');
    const videoPreview = document.getElementById('video-preview');
    const canvasContext = videoPreview.getContext('2d');

    imageUpload.addEventListener('change', function () {
      const file = this.files[0];

      if (file) {
        const fileType = file.type;

        if (fileType.startsWith('image/')) {
          // Display the uploaded image
          const reader = new FileReader();
          reader.onload = function (e) {
            imagePreview.src = e.target.result;
            imagePreview.style.display = 'block';
            videoPreview.style.display = 'none';
          };
          reader.readAsDataURL(file);
        } else if (fileType.startsWith('video/')) {
          // Extract and display the first frame of the video
          const video = document.createElement('video');
          video.src = URL.createObjectURL(file);
          video.currentTime = 1; // Capture the first frame at 1 second

          video.addEventListener('loadeddata', function () {
            videoPreview.width = video.videoWidth;
            videoPreview.height = video.videoHeight;

            // Draw the first frame to the canvas
            canvasContext.drawImage(video, 0, 0, video.videoWidth, video.videoHeight);
            videoPreview.style.display = 'block';
            imagePreview.style.display = 'none';

            // Free up the video object URL after use
            URL.revokeObjectURL(video.src);
          });
        }
      }
    });
  });
</script>

<!-- Normal nav scripts -->

<script>
  function toggleNotifications() {
    var notificationWindow = document.getElementById('notificationWindow');
    var isHidden = notificationWindow.style.display === 'none' || notificationWindow.style.display === '';

    notificationWindow.style.display = isHidden ? 'flex' : 'none';
  }
  function toggleSearchPopup() {
    const searchPopup = document.getElementById('searchPopup');
    const isOpen = window.getComputedStyle(searchPopup).display === 'flex';

    searchPopup.style.display = isOpen ? 'none' : 'flex';

    document.body.style.overflow = isOpen ? '' : 'hidden';
  }

  function togglePostPopup() {
    const Postpopup = document.getElementById('PostPopup');
    const body = document.body;
    const isHidden = Postpopup.style.display === 'none';

    Postpopup.style.display = isHidden ? 'flex' : 'none';
    body.style.overflow = isHidden ? 'hidden' : 'auto';
  }

  document.getElementById('searchForm').addEventListener('submit', async function (event) {
    event.preventDefault();

    const query = document.getElementById('query').value;
    const searchResults = document.getElementById('searchResults');

    try {
      const response = await fetch(`<?= filePath("/backend/api/"); ?>_searchQuery.php?query=${encodeURIComponent(query)}`);
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      const result = await response.json();
      if (result.error) {
        searchResults.innerHTML = `<div class="error-message">Error: ${result.error}</div>`;
        searchResults.style.display = 'block';
        return;
      }
      let html = '<div class="searchResultsContainer">';
      if (result.posts && result.posts.length > 0) {
        html += '<div class="postResultsSection">';
        result.posts.forEach(post => {
          html += `
        <button class="resultPostReturn" onclick="window.location.href='<?= filePath("/profile/u/"); ?>${post.user_uuid}/post/${post.PUID}'">
          <div class="PostInfo">
            <img class="postImage" src="<?= filePath(""); ?>${post.image_id}" alt="user profile picture">
            <div class="PostInfoText">
              <div class="userPostInfo">
                <img src="<?= filePath("/"); ?>${post.pfp_image_link}" alt="user profile picture">
                <p class="postUsername">${post.username}</p>
              </div>
              <p class="postContent">${post.content}</p>
            </div>
          </div>
        </button>
        `;
        });
        html += '</div>';
      }
      // Display users if any
      if (result.users && result.users.length > 0) {
        html += '<div class="UserResultSection">';
        result.users.forEach(user => {
          html += `
          <button class="resultUserReturn" onclick="window.location.href='<?= filePath("/profile/u/"); ?>${user.uuid}/'">
            <div class="UserInfo">
              <img class="userAvatar" src="<?= filePath("/"); ?>${user.pfp_image_link}" alt="user profile picture">
              <div class="UserInfoText">
                <p class="username">${user.username}</p>
              </div>
            </div>
            <img class="userBackground" src="<?= filePath("/"); ?>${user.bg_image_link}" alt="user background image">
          </button>
          `;
        });
        html += '</div>';
      }

      if ((!result.posts || result.posts.length === 0) && (!result.users || result.users.length === 0)) {
        html += '<div class="no-results">No results found for your search.</div>';
      }

      html += '</div>';

      searchResults.innerHTML = html;
      searchResults.style.display = 'flex';

    } catch (error) {
      searchResults.innerHTML = `<div class="error-message">Error: ${error.message}</div>`;
      searchResults.style.display = 'block';
    }
  });

  const placeholderTexts = ["Search...", "Search.", "Search..", "Search..."];
  const inputField = document.getElementById('query');
  let index = 0;

  function animatePlaceholder() {
    inputField.placeholder = placeholderTexts[index];
    index = (index + 1) % placeholderTexts.length;
  }

  setInterval(animatePlaceholder, 300);

  document.getElementById('postForm').addEventListener('submit', function (event) {
    const youtubeLink = document.getElementById('youtubeLink').value;
    const imageUpload = document.getElementById('image-upload').files.length;

    if (!youtubeLink && !imageUpload) {
      event.preventDefault();
      alert('Please provide either an image or a YouTube link.');
    }
  });
</script>