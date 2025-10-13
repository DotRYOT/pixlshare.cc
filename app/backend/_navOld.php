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
<nav>
  <div class="logoContainer logoFont" onclick="toggleNavSize()">
    P<span>X</span><span>L</span>
  </div>
  <div class="NavBody">
    <div class="MainNavBody">
      <a href="<?= filePath("/home/"); ?>">
        <div>
          <ion-icon size="large" name="home-outline"></ion-icon>
          <p>Home</p>
        </div>
      </a>
      <a href="<?= filePath("/explore/"); ?>">
        <div>
          <ion-icon size="large" name="grid-outline"></ion-icon>
          <p>Explore</p>
        </div>
      </a>
      <a id="searchLink" onclick="toggleSearchPopup()">
        <div>
          <ion-icon size="large" name="search-outline"></ion-icon>
          <p>Search</p>
        </div>
      </a>
      <a onclick="togglePostPopup()">
        <div>
          <ion-icon size="large" name="add-outline"></ion-icon>
          <p>Post</p>
        </div>
      </a>
    </div>
    <div class="LowerNavBody">
      <?php if ($_SESSION['user']['userLevel'] == 1) { ?>
        <a href="<?= filePath("/admin/"); ?>">
          <div>
            <ion-icon size="large" name="shield-checkmark-outline"></ion-icon>
            <p>Admin</p>
          </div>
        </a>
      <?php } ?>
      <a href="<?= filePath("/profile/"); ?>">
        <div>
          <img src="<?= $PFPImageLink; ?>" alt="">
          <p>Profile</p>
        </div>
      </a>
    </div>
  </div>
</nav>

<div class="searchContainer" id="searchPopup" style="display: none;">
  <div class="searchHeader">
    <div class="searchPopup">
      <form id="searchForm">
        <input type="text" name="query" id="query" placeholder="Search..." required>
        <button type="submit" id="searchQueryButton">
          <ion-icon size="large" name="search-outline"></ion-icon>
        </button>
      </form>
    </div>
    <button type="button" name="closeSearchPage" onclick="toggleSearchPopup()">
      <ion-icon size="large" name="close-outline"></ion-icon>
    </button>
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
        action="<?= filePath('/backend/api/') ?>_submitPost.php">
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
            <button type="button" name="youtubeEmbedTab" onclick="toggleyoutubeEmbed()">
              <ion-icon name="close-outline"></ion-icon>
            </button>
            <section>
              <!-- <input type="text" name="vanityText" id="vanityText" placeholder="Text"> -->
              <input type="url" name="youtubeLink" id="youtubeLink" placeholder="Link">
              <button type="button" name="addLink" onclick="addYouTubeLink()">
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
          <div>
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
          <div style="display: block; flex-flow: row;">
            <div class="cf-turnstile" data-sitekey="0x4AAAAAAA27XzOGlxRe70Bl" data-size="flexible">
            </div>
          </div>
          <button type="submit" id="postQueryButton" onclick="showLoaderAndHideForm()">
            <ion-icon size="large" name="newspaper-outline"></ion-icon>
            <h2>Post</h2>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
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
  let isNavSmall = localStorage.getItem('navState') === 'true';

  NavSizeChange();

  function NavSizeChange() {
    const nav = document.querySelector('nav');
    const logoSpans = document.querySelectorAll('.logoContainer span');
    const navParagraphs = document.querySelectorAll('nav p');
    const body = document.body;

    if (isNavSmall) {
      nav.style.width = '5rem';
      logoSpans.forEach(span => span.style.display = 'none');
      navParagraphs.forEach(p => p.style.display = 'none');
      body.style.marginLeft = '6rem';
    } else {
      nav.style.width = '15rem';
      logoSpans.forEach(span => span.style.display = 'inline');
      navParagraphs.forEach(p => p.style.display = 'block');
      body.style.marginLeft = '16rem';
    }
  }

  function toggleNavSize() {
    isNavSmall = !isNavSmall;
    localStorage.setItem('navState', isNavSmall);
    NavSizeChange();
  }

  function toggleSearchPopup() {
    const Searchpopup = document.getElementById('searchPopup');
    Searchpopup.style.display = Searchpopup.style.display === 'none' ? 'flex' : 'none';
  }

  function togglePostPopup() {
    const Postpopup = document.getElementById('PostPopup');
    const body = document.body;
    const isHidden = Postpopup.style.display === 'none';

    Postpopup.style.display = isHidden ? 'flex' : 'none';
    body.style.overflow = isHidden ? 'hidden' : 'auto'; // Prevent or allow scrolling
  }

  document.getElementById('searchForm').addEventListener('submit', async function (event) {
    event.preventDefault();

    const query = document.getElementById('query').value;

    try {
      const response = await fetch(`<?= filePath("/backend/api/"); ?>_searchQuery.php?query=${encodeURIComponent(query)}`);

      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }

      const result = await response.text();

      const searchResults = document.getElementById('searchResults');
      searchResults.innerHTML = result;
      searchResults.style.display = 'block';
    } catch (error) {
      const searchResults = document.getElementById('searchResults');
      searchResults.innerHTML = `Error: ${error.message}`;
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