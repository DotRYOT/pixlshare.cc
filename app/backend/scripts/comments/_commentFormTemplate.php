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
          <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px" fill="#FFFFFF">
            <path
              d="m249-207-42-42 231-231-231-231 42-42 231 231 231-231 42 42-231 231 231 231-42 42-231-231-231 231Z" />
          </svg>
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
            <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px" fill="#FFFFFF">
              <path
                d="M480-480ZM120-120v-720h425v60H180v600h600v-365h60v425H120Zm120-162h480L576-474 449-307l-94-124-115 149Zm453-323v-87h-88v-60h88v-88h60v88h87v60h-87v87h-60Z" />
            </svg>
          </button>
          <input type="file" class="commentImageUpload" accept="image/*" name="images" style="display: none;">

          <button type="button" onclick="commentForm_toggleYouTubeEmbed(this.closest('.commentPostPopup'))">
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#FFFFFF">
              <path
                d="M616-242q-27 1-51.5 1.5t-43.5.5h-41q-71 0-133-2-53-2-104.5-5.5T168-257q-26-7-45-26t-26-45q-6-23-9.5-56T82-447q-2-36-2-73t2-73q2-30 5.5-63t9.5-56q7-26 26-45t45-26q23-6 74.5-9.5T347-798q62-2 133-2t133 2q53 2 104.5 5.5T792-783q26 7 45 26t26 45q6 23 9.5 56t5.5 63q2 36 2 73v17q-19-8-39-12.5t-41-4.5q-83 0-141.5 58.5T600-320q0 21 4 40.5t12 37.5ZM400-400l208-120-208-120v240Zm360 200v-80h-80v-80h80v-80h80v80h80v80h-80v80h-80Z" />
            </svg>
          </button>

          <button type="button" class="commentTextLinkShower" style="display: none;">
            <p class="commentFakeURL">q5elqE21EHk</p>
          </button>
        </div>

        <button type="submit" onclick="commentForm_showLoaderAndHideForm(this)">
          <svg xmlns="http://www.w3.org/2000/svg" height="40px" viewBox="0 -960 960 960" width="40px" fill="#FFFFFF">
            <path
              d="M320.67-527.33V-594H640v66.67H320.67Zm0 124.66v-66.66H640v66.66H320.67Zm0 124.67v-66.67H640V-278H320.67ZM688-602.67V-688h-85.33v-66.67H688V-840h66.67v85.33H840V-688h-85.33v85.33H688ZM120-120v-720h449.33v66.67H186.67v586.66h586.66v-382.66H840V-120H120Z" />
          </svg>
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