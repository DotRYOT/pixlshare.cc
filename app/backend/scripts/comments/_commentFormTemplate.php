<!-- Scoped Comment Post Form -->
<div class="commentPostPopup">
  <div class="commentLoaderZoomie" style="display: none;">
    <l-line-wobble size="150" stroke="5" bg-opacity="0.1" speed="2" color="#ff4500"></l-line-wobble>
  </div>

  <form class="commentPostForm" method="post" enctype="multipart/form-data"
    action="<?= filePath('/backend/api/') ?>post/_submitComment.php">

    <div class="commentMiddleContainer">
      <textarea name="postBody" class="commentPostBody" placeholder="Text..." maxlength="500"></textarea>
      <p class="commentCharCount">800 characters remaining</p>
      <p class="commentLineBreakCount">15 line breaks remaining</p>
      <img class="commentImagePreview" src="#" alt="Image Preview" style="display: none;">
      <canvas class="commentVideoPreview" style="display: none;"></canvas>
    </div>

    <div class="commentYouTubeEmbedPopup" style="display: none;">
      <div class="commentEmbedPopupContainer">
        <button class="commentEmbedPopupClose" type="button"
          onclick="commentForm_toggleYouTubeEmbed(this.closest('.commentPostPopup'))">
          <ion-icon name="close-outline"></ion-icon>
        </button>
        <section class="commentYouTubeInputSection">
          <input type="url" class="commentYouTubeLink" placeholder="Link">
          <button type="button" onclick="commentForm_addYouTubeLink(this.closest('.commentPostPopup'))">Add
            Link</button>
        </section>
      </div>
    </div>

    <input type="hidden" name="commentLinkData" class="commentLinkData">
    <input type="hidden" name="OG_PUID" value="<?= $PUID; ?>">
    <input type="hidden" name="OG_UUID" value="<?= $Poster_UUID; ?>">

    <div class="commentBottomContainer">
      <div class="commentTopLayer">
        <?php if ($_SERVER['HTTP_HOST'] == 'pixlshare.cc') { ?>
        <div style="display: block; flex-flow: row;">
          <div class="cf-turnstile" data-sitekey="0x4AAAAAAA27XzOGlxRe70Bl" data-size="flexible"></div>
        </div>
        <?php } ?>
      </div>
      <div class="commentBottomLayer">
        <div class="commentBottomLeft">
          <button type="button" class="commentImageUploadButton">
            <ion-icon name="image-outline"></ion-icon>
          </button>
          <input type="file" class="commentImageUpload" accept="image/*" name="images" style="display: none;">

          <button type="button" onclick="commentForm_toggleYouTubeEmbed(this.closest('.commentPostPopup'))">
            <ion-icon name="logo-youtube"></ion-icon>
          </button>

          <button type="button" class="commentTextLinkShower" style="display: none;">
            <p class="commentFakeURL">q5elqE21EHk</p>
          </button>
        </div>

        <button type="submit" onclick="commentForm_showLoaderAndHideForm(this)">
          <ion-icon size="large" name="newspaper-outline"></ion-icon>
          <h2>Post</h2>
        </button>
      </div>
    </div>
  </form>
</div>

<script>
  function commentForm_init(container) {
    const textarea = container.querySelector('.commentPostBody');
    const charCount = container.querySelector('.commentCharCount');
    const lineBreakCount = container.querySelector('.commentLineBreakCount');

    // Character/Line Count
    if (textarea && charCount && lineBreakCount) {
      textarea.addEventListener('input', function () {
        const charsRemaining = 800 - this.value.length;
        charCount.textContent = `${charsRemaining} characters remaining`;

        const lineBreaks = (this.value.match(/\n/g) || []).length;
        const linesRemaining = 15 - lineBreaks;
        lineBreakCount.textContent = `${linesRemaining} line breaks remaining`;
      });
    }

    // Image Upload Preview
    const imageUpload = container.querySelector('.commentImageUpload');
    const imagePreview = container.querySelector('.commentImagePreview');

    if (imageUpload && imagePreview) {
      imageUpload.addEventListener('change', function () {
        const file = this.files[0];
        if (file && file.type.startsWith('image/')) {
          const reader = new FileReader();
          reader.onload = function (e) {
            imagePreview.src = e.target.result;
            imagePreview.style.display = 'block';
          };
          reader.readAsDataURL(file);
        } else {
          alert('Please select a valid image file.');
          imagePreview.style.display = 'none';
        }
      });

      container.querySelector('.commentImageUploadButton')?.addEventListener('click', function () {
        imageUpload.click();
      });
    }

    // YouTube Embed Functions
    window.commentForm_toggleYouTubeEmbed = function (container) {
      const popup = container.querySelector('.commentYouTubeEmbedPopup');
      if (popup) {
        popup.style.display = popup.style.display === 'none' ? 'flex' : 'none';
      }
    };

    window.commentForm_addYouTubeLink = function (container) {
      const input = container.querySelector('.commentYouTubeLink');
      const fakeURL = container.querySelector('.commentFakeURL');
      const linkData = container.querySelector('.commentLinkData');

      if (input && fakeURL && linkData) {
        const link = input.value.trim();
        const regex = /^(?:https?:\/\/)?(?:www\.)?(?:youtube\.com|youtu\.be)\/(?:watch\?v=)?([^&\s]+)/;
        const match = link.match(regex);

        if (match && match[1]) {
          const videoId = match[1];
          fakeURL.textContent = `youtube.com/watch?v=${videoId}`;
          linkData.value = videoId;
          container.querySelector('.commentTextLinkShower').style.display = 'block';
          commentForm_toggleYouTubeEmbed(container);
        } else {
          alert('Invalid YouTube link.');
        }
      }
    };

    window.commentForm_showLoaderAndHideForm = function (button) {
      const container = button.closest('.commentPostPopup');
      if (container) {
        const loader = container.querySelector('.commentLoaderZoomie');
        const form = container.querySelector('.commentPostForm');
        if (loader && form) {
          loader.style.display = 'flex';
          form.style.display = 'none';
        }
      }
    }
  }

  // Initialize all comment forms
  document.querySelectorAll('.commentPostPopup').forEach(commentForm_init);
</script>