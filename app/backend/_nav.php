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
      <div id="NoteBox" class="NoteBox">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960">
          <path
            d="M120-120v-720h720v720H120Zm66.67-66.67h586.66v-130.66H636q-27.33 39.33-68.83 60.66-41.5 21.34-87.17 21.34t-87.17-21.34Q351.33-278 324-317.33H186.67v130.66ZM480-302q40 0 72.33-23 32.34-23 51.67-59h169.33v-389.33H186.67V-384H356q19.33 36 51.67 59Q440-302 480-302ZM186.67-186.67H773.33 186.67Z" />
        </svg>
      </div>
      <div id="NoteAlert" class="NoteAlert" style="display: none;">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960">
          <path
            d="M278-474h404v-66.67H278V-474Zm0-142.67h404v-66.66H278v66.66ZM120-120v-720h720v720H120Zm66.67-66.67h586.66v-130.66H636q-27.33 39.33-68.83 60.66-41.5 21.34-87.17 21.34t-87.17-21.34Q351.33-278 324-317.33H186.67v130.66ZM480.16-302q39.84 0 72.17-23 32.34-23 51.67-59h169.33v-389.33H186.67V-384H356q19.33 36 51.83 59t72.33 23ZM186.67-186.67H773.33 186.67Z" />
        </svg>
      </div>
    </button>
    <?php if ($_SESSION['user']['userLevel'] == 1) { ?>
      <button type="button" name="adminPage" onclick="window.location.href='<?= filePath("/admin/"); ?>'">
        <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px" fill="#FFFFFF">
          <path
            d="M480-81q-140-35-230-162.5T160-523v-238l320-120 320 120v238q0 152-90 279.5T480-81Zm0-62q115-38 187.5-143.5T740-523v-196l-260-98-260 98v196q0 131 72.5 236.5T480-143Zm0-337Z" />
        </svg>
      </button>
    <?php } ?>
  </div>
</nav>
<nav id="bottomNav">
  <div class="navButton first">
    <button type="button" name="home" onclick="window.location.href='<?= filePath("/home/"); ?>'">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960">
        <path
          d="M220-180h150v-250h220v250h150v-390L480-765 220-570v390Zm-60 60v-480l320-240 320 240v480H530v-250H430v250H160Zm320-353Z" />
      </svg>
    </button>
  </div>
  <div class="navButton">
    <button type="button" name="explore" onclick="window.location.href='<?= filePath("/explore/"); ?>'">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960">
        <path
          d="M120-120v-245h330v245H120Zm390 0v-415h330v415H510ZM390-305Zm180-170Zm-450 50v-415h330v415H120Zm270-60Zm120-110v-245h330v245H510Zm60-60ZM180-180h210v-125H180v125Zm390 0h210v-295H570v295ZM180-485h210v-295H180v295Zm390-170h210v-125H570v125Z" />
      </svg>
    </button>
  </div>
  <div class="postButton">
    <button type="button" onclick="togglePostPopup()">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960">
        <path
          d="M321-531v-60h319v60H321Zm0 127v-60h319v60H321Zm0 127v-60h319v60H321Zm371-327v-88h-88v-60h88v-88h60v88h88v60h-88v88h-60ZM120-120v-720h454v60H180v600h600v-394h60v454H120Z" />
      </svg>
    </button>
  </div>
  <div class="navButton">
    <button type="button" onclick="toggleSearchPopup()">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960">
        <path
          d="M796-121 533-384q-30 26-69.96 40.5Q423.08-329 378-329q-108.16 0-183.08-75Q120-479 120-585t75-181q75-75 181.5-75t181 75Q632-691 632-584.85 632-542 618-502q-14 40-42 75l264 262-44 44ZM377-389q81.25 0 138.13-57.5Q572-504 572-585t-56.87-138.5Q458.25-781 377-781q-82.08 0-139.54 57.5Q180-666 180-585t57.46 138.5Q294.92-389 377-389Z" />
      </svg>
    </button>
  </div>
  <?php if ($_SESSION['user']['userLevel'] == 1) { ?>
    <div class="navButton">
      <button type="button" name="store" id="store" onclick="window.location.href='<?= filePath("/skripstore/"); ?>'">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960">
          <path
            d="M160-80v-640h170v-10q0-63 43.5-106.5T480-880q63 0 106.5 43.5T630-730v10h170v640H160Zm60-60h520v-520H630v120h-60v-120H390v120h-60v-120H220v520Zm170-580h180v-10q0-38-26-64t-64-26q-38 0-64 26t-26 64v10ZM220-140v-520 520Z" />
        </svg>
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
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960">
      <path d="M450-450H200v-60h250v-250h60v250h250v60H510v250h-60v-250Z" />
    </svg>
  </button>
</div>

<div class="searchContainer" id="searchPopup" style="display: none;">
  <div class="searchHeader">
    <button type="button" name="closeSearchPage" class="closeSearchWindow" onclick="toggleSearchPopup()">
      <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px" fill="#ac2f2fff">
        <path d="m249-207-42-42 231-231-231-231 42-42 231 231 231-231 42 42-231 231 231 231-42 42-231-231-231 231Z" />
      </svg>
    </button>
    <div class="searchPopup">
      <form id="searchForm">
        <input type="text" name="query" id="query" placeholder="Search..." required>
        <button type="submit" id="searchQueryButton">
          <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px" fill="#FFFFFF">
            <path
              d="M796-121 533-384q-30 26-69.96 40.5Q423.08-329 378-329q-108.16 0-183.08-75Q120-479 120-585t75-181q75-75 181.5-75t181 75Q632-691 632-584.85 632-542 618-502q-14 40-42 75l264 262-44 44ZM377-389q81.25 0 138.13-57.5Q572-504 572-585t-56.87-138.5Q458.25-781 377-781q-82.08 0-139.54 57.5Q180-666 180-585t57.46 138.5Q294.92-389 377-389Z" />
          </svg>
        </button>
      </form>
    </div>
  </div>
  <div class="searchResults" id="searchResults" style="display: none;">
  </div>
</div>

<div class="postContainer" id="PostPopup" style="display: none;">
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
            <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px" fill="#FFFFFF">
              <path
                d="m249-207-42-42 231-231-231-231 42-42 231 231 231-231 42 42-231 231 231 231-42 42-231-231-231 231Z" />
            </svg>
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
          <textarea name="postBody" id="postBody" placeholder="Text..." maxlength="500"></textarea>
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
              <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px"
                fill="#FFFFFF">
                <path
                  d="m249-207-42-42 231-231-231-231 42-42 231 231 231-231 42 42-231 231 231 231-42 42-231-231-231 231Z" />
              </svg>
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
                <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px"
                  fill="#FFFFFF">
                  <path
                    d="M480-480ZM120-120v-720h425v60H180v600h600v-365h60v425H120Zm120-162h480L576-474 449-307l-94-124-115 149Zm453-323v-87h-88v-60h88v-88h60v88h87v60h-87v87h-60Z" />
                </svg>
              </button>
              <button type="button" id="addYouTubeVideo" onclick="toggleyoutubeEmbed()">
                <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px"
                  fill="#FFFFFF">
                  <path
                    d="M616-242q-27 1-51.5 1.5t-43.5.5h-41q-71 0-133-2-53-2-104.5-5.5T168-257q-26-7-45-26t-26-45q-6-23-9.5-56T82-447q-2-36-2-73t2-73q2-30 5.5-63t9.5-56q7-26 26-45t45-26q23-6 74.5-9.5T347-798q62-2 133-2t133 2q53 2 104.5 5.5T792-783q26 7 45 26t26 45q6 23 9.5 56t5.5 63q2 36 2 73v17q-19-8-39-12.5t-41-4.5q-83 0-141.5 58.5T600-320q0 21 4 40.5t12 37.5ZM400-400l208-120-208-120v240Zm360 200v-80h-80v-80h80v-80h80v80h80v80h-80v80h-80Z" />
                </svg>
              </button>
              <button type="button" name="textLinkShower" class="textLinkShower" style="display: none;">
                <p class="fakeURL" id="fakeURL">q5elqE21EHk</p>
              </button>
            </div>
            <button type="submit" id="postQueryButton" onsubmit="showLoaderAndHideForm()">
              <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px"
                fill="#FFFFFF">
                <path
                  d="M321-531v-60h319v60H321Zm0 127v-60h319v60H321Zm0 127v-60h319v60H321Zm371-327v-88h-88v-60h88v-88h60v88h88v60h-88v88h-60ZM120-120v-720h454v60H180v600h600v-394h60v454H120Z" />
              </svg>
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
  // Load notifications
  async function loadNotificationsIntoTray() {
    const win = document.getElementById('notificationWindow');
    if (!win) return;

    const noNoteEl = win.querySelector('.noNote');

    try {
      const response = await fetch('<?= filePath("/"); ?>backend/scripts/account/_loadNotifications.php');
      if (!response.ok) throw new Error('Failed to load notifications');

      const data = await response.json();

      document.querySelectorAll('.note.dynamic').forEach(el => el.remove());

      if (!data.notifications || data.totalCount === 0) {
        noNoteEl.style.display = 'flex';
        return;
      }

      const NoteAlert = document.getElementById('NoteAlert');
      const NoteBox = document.getElementById('NoteBox');
      if (data.totalCount > 0) {
        NoteAlert.style.display = 'flex';
        NoteBox.style.display = 'none';
      } else {
        NoteAlert.style.display = 'none';
        NoteBox.style.display = 'flex';
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

  document.getElementById('postForm').addEventListener('submit', function (event) {
    const youtubeLink = document.getElementById('youtubeLink').value.trim();
    const imageUpload = document.getElementById('image-upload').files.length;
    const textarea = document.getElementById('postBody').value.trim();

    if (!youtubeLink && !imageUpload && !textarea) {
      event.preventDefault();
      alert('Please provide content.');
    }
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
</script>