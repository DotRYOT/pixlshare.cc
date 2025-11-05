<?php
require "../../../../../backend/Files/repoPages/userPostPage/pageScript.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $firstFiveWords; ?> - PIXLSHARE</title>
  <?php if ($_SERVER['HTTP_HOST'] == 'pixlshare.cc') { ?>
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
  <?php } ?>
  <link rel="shortcut icon" href="<?= filePath("/assets/logos/"); ?>pixlshareLogo_color_128.png" type="image/x-icon">
  <link rel="stylesheet" href="<?= filePath("/profile/u/css/") ?>index.min.css">
  <?php if ($_SERVER['HTTP_HOST'] == 'pixlshare.cc') { ?>
    <link rel="manifest" href="<?= filePath("/") ?>manifest.json" />
    <script>
      if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
          navigator.serviceWorker.register('../../../../../sw.js')
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
  <!-- Open Graph Meta Tags -->
  <meta property="og:title" content="<?= htmlspecialchars($poster_username); ?> - PIXLSHARE">
  <meta property="og:description" content="<?= htmlspecialchars($firstFiveWords); ?>">
  <meta property="og:image" content="<?= htmlspecialchars($previewImage); ?>">
  <meta property="og:url" content="<?= htmlspecialchars($postURL); ?>">
  <meta property="og:type" content="article">
  <meta property="og:locale" content="en_US">

  <!-- Twitter Card Meta Tags -->
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="<?= htmlspecialchars($poster_username); ?> - PIXLSHARE">
  <meta name="twitter:description" content="<?= htmlspecialchars($firstFiveWords); ?>">
  <meta name="twitter:image" content="<?= htmlspecialchars($previewImage); ?>">
  <meta name="twitter:image:alt" content="<?= filePath("/assets/logos/"); ?>pxl_logo_1350_Color.png">
  <meta name="twitter:site" content="@pixlshare">
  <link href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css " rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/toastify-js "></script>
</head>

<body>
  <?php
  if ($_SESSION['user']['UUID'] === null) {
    require "../../../../../backend/_nav_noacc.php";
  } else {
    require "../../../../../backend/_nav.php";
  }

  if ($postState === "500") {
    $postStateVis = 'style="display: flex;"';
  } else {
    $postStateVis = 'style="display: none;"';
  }
  ?>
  <div id="postSuspended" <?= $postStateVis; ?>>
    <div class="postSuspendedContainer">
      <?php if ($PageAdmin === true) { ?>
        <div class="closeSuspended">
          <button type="button" name="closeSuspended" onclick="closeSuspended()">
            <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px" fill="#FFFFFF">
              <path
                d="m249-207-42-42 231-231-231-231 42-42 231 231 231-231 42 42-231 231 231 231-42 42-231-231-231 231Z" />
            </svg>
          </button>
        </div>
        <script>
          function closeSuspended() {
            const postSuspended = document.getElementById("postSuspended");
            postSuspended.style.display = "none";
          }
        </script>
      <?php } ?>
      <div class="postSuspendedContent">
        <h2>This post has been suspended.</h2>
        <p>If you believe this is a mistake, please contact support.</p>
        <?php if ($PageAdmin === true || $PostOwner === true) { ?>
          <p>If you are the poster, you can appeal the suspension by clicking the link below.</p>
          <a
            href="<?= filePath("") . "/profile/support/suspendedpost/?puid=" . $PUID . "&uuid=" . $SessionRedirectUUID; ?>">APPEAL
            HERE</a>
        <?php } ?>
      </div>
    </div>
  </div>
  <div class="moreMenu" id="moreMenu" style="display: none;">
    <div class="moreMenuContainer">
      <?php if ($PageAdmin === true) { ?>
        <script>
          function setupFormHandlers() {
            document.querySelectorAll(".report-action-form").forEach(form => {
              if (form.dataset.listenerAttached) return;
              form.dataset.listenerAttached = 'true';

              form.addEventListener("submit", function (e) {
                e.preventDefault();
                const formData = new FormData(this);
                formData.set("submit", "Finish");
                const actionUrl = '<?= filePath("/backend/scripts/admin/"); ?>_postAdminAction.php';
                fetch(actionUrl, {
                  method: "POST",
                  body: formData
                })
                  .then(response => {
                    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                    return response.json();
                  })
                  .then(json => {
                    if (json.success) {
                      Toastify({
                        text: json.message || 'Status updated successfully.',
                        duration: 3000,
                        gravity: "top",
                        position: "right",
                        style: {
                          background: "#4CAF50"
                        }
                      }).showToast();
                      // window.location.reload();
                    } else {
                      Toastify({
                        text: json.error || 'Something went wrong.',
                        duration: 4000,
                        gravity: "top",
                        position: "right",
                        style: {
                          background: "#f44336"
                        }
                      }).showToast();
                    }
                  })
                  .catch(error => {
                    console.error("Request failed:", error);
                    Toastify({
                      text: 'An error occurred while processing your request.',
                      duration: 5000,
                      gravity: "top",
                      position: "right",
                      style: {
                        background: "#f44336"
                      }
                    }).showToast();
                  });
              });
            });
          }

          document.addEventListener('DOMContentLoaded', function () {
            console.log("Initializing admin form handlers for post page.");
            setupFormHandlers();
          });

          // --- Functions to build the option strings ---
          // These functions create the HTML string for the <option> elements
          function buildUserStateOptions(selectedValue) {
            // Define the options. Compare the value (as integer) with selectedValue (also integer)
            const options = [
              { value: 0, label: "Valid" },
              { value: 500, label: "Suspend" },
              { value: 600, label: "Warn 1" },
              { value: 610, label: "Warn 2" },
              { value: 620, label: "Warn 3" },
              { value: 700, label: "Ban" }
            ];

            // Map the options to HTML strings, checking if they should be selected
            return options.map(opt =>
              `<option value="${opt.value}" ${parseInt(selectedValue) === opt.value ? 'selected' : ''}>${opt.label}</option>`
            ).join('');
          }

          function buildPostStateOptions(selectedValue) {
            // Define the options for post state
            const options = [
              { value: 0, label: "Valid" },
              { value: 500, label: "Suspend" }
              // Add more post states if needed
            ];

            // Map the options to HTML strings, checking if they should be selected
            return options.map(opt =>
              `<option value="${opt.value}" ${parseInt(selectedValue) === opt.value ? 'selected' : ''}>${opt.label}</option>`
            ).join('');
          }

          // --- Function to fetch status and populate the form ---
          function populateAdminFormWithStatus() {
            const puid = "<?= $PUID; ?>"; // Get PUID from PHP
            const fetchUrl = '../../../../../backend/scripts/admin/_getPostStatus.php?PUID=' + encodeURIComponent(puid);

            fetch(fetchUrl)
              .then(response => {
                if (!response.ok) {
                  throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
              })
              .then(data => {
                if (data.success) {
                  const userStateSelect = document.getElementById('userPunishment_<?= $PUID; ?>');
                  const postStateSelect = document.getElementById('postPunishment_<?= $PUID; ?>');

                  if (userStateSelect) {
                    // Build the options HTML string with the correct one selected
                    const userOptionsHtml = buildUserStateOptions(data.currentUserState);
                    // Set the innerHTML of the select
                    userStateSelect.innerHTML = userOptionsHtml;
                    console.log(`Populated user state select with current state: ${data.currentUserState}`);
                  } else {
                    console.warn('User state select element (userPunishment_<?= $PUID; ?>) not found.');
                  }

                  if (postStateSelect) {
                    const postOptionsHtml = buildPostStateOptions(data.currentPostState);
                    postStateSelect.innerHTML = postOptionsHtml;
                    console.log(`Populated post state select with current state: ${data.currentPostState}`);
                  } else {
                    console.warn('Post state select element (postPunishment_<?= $PUID; ?>) not found.');
                  }

                } else {
                  console.error('Error fetching status:', data.error);
                  const userSelect = document.getElementById('userPunishment_<?= $PUID; ?>');
                  const postSelect = document.getElementById('postPunishment_<?= $PUID; ?>');
                  if (userSelect) userSelect.innerHTML = `<option value="">Error loading status</option>`;
                  if (postSelect) postSelect.innerHTML = `<option value="">Error loading status</option>`;
                }
              })
              .catch(error => {
                console.error('Fetch error for post status:', error);
                // Handle error, maybe show a message in the form area
                const userSelect = document.getElementById('userPunishment_<?= $PUID; ?>');
                const postSelect = document.getElementById('postPunishment_<?= $PUID; ?>');
                if (userSelect) userSelect.innerHTML = `<option value="">Fetch Error</option>`;
                if (postSelect) postSelect.innerHTML = `<option value="">Fetch Error</option>`;
              });
          }

          document.addEventListener('DOMContentLoaded', function () {
            console.log("DOM loaded, attempting to populate admin form status.");
            setupFormHandlers();
            populateAdminFormWithStatus();
          });
        </script>
        <div class="admin-report-card"
          style="margin-bottom: 15px; padding: 10px; border: 1px solid #444; border-radius: 5px;">
          <h4 style="margin-top: 0;">Admin Actions</h4>
          <form class="report-action-form" data-puid="<?= $PUID ?>">
            <input type="hidden" name="PUID" value="<?= $PUID ?>" />
            <input type="hidden" name="DEF_UUID" value="<?= $UUID ?>" />
            <div class="form-group">
              <label for="userPunishment_<?= $PUID ?>">User Punishment</label>
              <!-- Options will be populated by JS -->
              <select name="options" id="userPunishment_<?= $PUID ?>"></select>
            </div>
            <div class="form-group">
              <label for="postPunishment_<?= $PUID ?>">Post Punishment</label>
              <!-- Options will be populated by JS -->
              <select name="postoptions" id="postPunishment_<?= $PUID ?>"></select>
            </div>
            <div class="form-actions">
              <button type="submit" name="submit" value="Finish" style="flex: 1; padding: 8px;">Apply Status</button>
            </div>
          </form>
        </div>
      <?php } ?>
      <?php if ($PostOwner === true || $PageAdmin === true) { ?>
        <section class="middleMoreMenu">
          <button name="Delete" type="button">
            <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px" fill="#FFFFFF">
              <path
                d="M201-120v-630h-41v-60h188v-30h264v30h188v60h-41v630H201Zm60-60h438v-570H261v570Zm106-86h60v-399h-60v399Zm166 0h60v-399h-60v399ZM261-750v570-570Z" />
            </svg>
          </button>
          <button type="button" name="edit" onclick="ToggleMoreMenu(), PostEdit()">
            <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px" fill="#FFFFFF">
              <path
                d="M180-180h44l472-471-44-44-472 471v44Zm-60 60v-128l617-616 128 128-617 616H120Zm659-617-41-41 41 41Zm-105 64-22-22 44 44-22-22Z" />
            </svg>
          </button>
        </section>
        <div class="line"></div>
      <?php } ?>
      <section class="bottomMoreMenu">
        <div class="bottomTopSection">
          <form id="reportPostForm" style="display: none;">
            <select name="reason" id="reportReason" required>
              <option value="1">Spam</option>
              <option value="2">Abuse</option>
              <option value="3">Fraud</option>
              <option value="4">Hate</option>
              <option value="5">Nudity</option>
              <option value="6">Violence</option>
              <option value="7">Harassment</option>
              <option value="8">Impersonation</option>
              <option value="9">Scam</option>
              <option value="10">Bullying</option>
            </select>
            <textarea name="extraInfo" placeholder="Extra info..." maxlength="500"></textarea>
            <button type="submit">Report Post</button>
          </form>
        </div>
        <div class="bottomBottomSection">
          <button name="reportPost" type="button" onclick="ToggleReportForm()">
            <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px" fill="#FFFFFF">
              <path
                d="M480-281q14 0 24.5-10.5T515-316q0-14-10.5-24.5T480-351q-14 0-24.5 10.5T445-316q0 14 10.5 24.5T480-281Zm-30-144h60v-263h-60v263ZM330-120 120-330v-300l210-210h300l210 210v300L630-120H330Zm25-60h250l175-175v-250L605-780H355L180-605v250l175 175Zm125-300Z" />
            </svg>
          </button>
          <button type="button" name="share" onclick="shareCopyButton()">
            <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px" fill="#FFFFFF">
              <path
                d="M686-80q-47.5 0-80.75-33.25T572-194q0-8 5-34L278-403q-16.28 17.34-37.64 27.17Q219-366 194-366q-47.5 0-80.75-33.25T80-480q0-47.5 33.25-80.75T194-594q24 0 45 9.3 21 9.29 37 25.7l301-173q-2-8-3.5-16.5T572-766q0-47.5 33.25-80.75T686-880q47.5 0 80.75 33.25T800-766q0 47.5-33.25 80.75T686-652q-23.27 0-43.64-9Q622-670 606-685L302-516q3 8 4.5 17.5t1.5 18q0 8.5-1 16t-3 15.5l303 173q16-15 36.09-23.5 20.1-8.5 43.07-8.5Q734-308 767-274.75T800-194q0 47.5-33.25 80.75T686-80Zm.04-60q22.96 0 38.46-15.54 15.5-15.53 15.5-38.5 0-22.96-15.54-38.46-15.53-15.5-38.5-15.5-22.96 0-38.46 15.54-15.5 15.53-15.5 38.5 0 22.96 15.54 38.46 15.53 15.5 38.5 15.5Zm-492-286q22.96 0 38.46-15.54 15.5-15.53 15.5-38.5 0-22.96-15.54-38.46-15.53-15.5-38.5-15.5-22.96 0-38.46 15.54-15.5 15.53-15.5 38.5 0 22.96 15.54 38.46 15.53 15.5 38.5 15.5Zm492-286q22.96 0 38.46-15.54 15.5-15.53 15.5-38.5 0-22.96-15.54-38.46-15.53-15.5-38.5-15.5-22.96 0-38.46 15.54-15.5 15.53-15.5 38.5 0 22.96 15.54 38.46 15.53 15.5 38.5 15.5ZM686-194ZM194-480Zm492-286Z" />
            </svg>
            <input type="text" value="https://<?= $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>" id="copyInput"
              readonly>
          </button>
        </div>
      </section>
    </div>
    <div class="closeMoreMenu">
      <button type="button" name="CloseMoreMenu" onclick="ToggleMoreMenu()">
        <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px" fill="#FFFFFF">
          <path d="m249-207-42-42 231-231-231-231 42-42 231 231 231-231 42 42-231 231 231 231-42 42-231-231-231 231Z" />
        </svg>
      </button>
    </div>
  </div>
  <div class="postCard">
    <div class="imageContainer">
      <?php
      if (!empty($Post_YT_Link)) {
        echo '
        <iframe
          width="1280"
          height="720"
          src="https://www.youtube.com/embed/' . $Post_YT_Link . '"
          frameborder="0"
          allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
          referrerpolicy="strict-origin-when-cross-origin"
          allowfullscreen>
        </iframe>';
      } else {
        if (substr($media_id, -3) == 'mp4') {
          ?>
          <div class="video-wrapper">
            <video preload="auto">
              <source src="../../../../..<?= $media_id; ?>" type="video/mp4">
              Your browser does not support the video tag.
            </video>
            <div class="controls">
              <button id="play-pause">
                <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px" fill="#FFFFFF">
                  <path d="M320-203v-560l440 280-440 280Z" />
                </svg>
              </button>

              <!-- Volume control group -->
              <div class="volume-control">
                <button id="volume">
                  <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px"
                    fill="#FFFFFF">
                    <path
                      d="M560-131v-62q97-28 158.5-107.5T780-481q0-101-61-181T560-769v-62q124 28 202 125.5T840-481q0 127-78 224.5T560-131ZM120-360v-240h160l200-200v640L280-360H120Zm420 48v-337q55 17 87.5 64T660-480q0 57-33 104t-87 64ZM420-648 307-540H180v120h127l113 109v-337Zm-94 168Z" />
                  </svg>
                </button>
                <div class="volume-slider">
                  <input type="range" min="0" max="1" step="0.05" value="1">
                </div>
              </div>

              <div class="progress-bar">
                <div class="buffer-bar"></div>
              </div>
              <button id="fullscreen">
                <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px" fill="#FFFFFF">
                  <path
                    d="M120-120v-193h60v133h133v60H120Zm527 0v-60h133v-133h60v193H647ZM120-647v-193h193v60H180v133h-60Zm660 0v-133H647v-60h193v193h-60Z" />
                </svg>
              </button>
            </div>
          </div>
          <?php
        } else {
          echo '<img onclick="showImage(\'' . $media_id . '\');" src="../../../../..' . $media_id . '" alt="">';
        }
      }
      ?>
    </div>
    <!-- <div class="line"></div> -->
    <section class="mainPostBody" id="mainPostBody">
      <div class="contentContainer">
        <div class="profileLink">
          <a href="<?= filePath("/profile/u/") . $UUID; ?>">
            <img src="<?= filePath("/") . $poster_image_link; ?>" alt="">
            <p><?= $poster_username; ?></p>
          </a>
        </div>
        <div class="textContainer">
          <p><?= nl2br(htmlspecialchars($content)); ?></p>
        </div>
      </div>
      <!-- <div class="line"></div> -->
      <div class="toolBar">
        <div class="leftToolBar">
          <button id="mainPostLikeButton" type="button" name="like" data-puid="<?= $PUID; ?>">
            <svg xmlns="http://www.w3.org/2000/svg" class="heart-outline-icon" height="48px" viewBox="0 -960 960 960"
              width="48px" fill="#FFFFFF">
              <path
                d="m480-121-41-37q-105.77-97.12-174.88-167.56Q195-396 154-451.5T96.5-552Q80-597 80-643q0-90.15 60.5-150.58Q201-854 290-854q57 0 105.5 27t84.5 78q42-54 89-79.5T670-854q89 0 149.5 60.42Q880-733.15 880-643q0 46-16.5 91T806-451.5Q765-396 695.88-325.56 626.77-255.12 521-158l-41 37Zm0-79q101.24-93 166.62-159.5Q712-426 750.5-476t54-89.14q15.5-39.13 15.5-77.72 0-66.14-42-108.64T670.22-794q-51.52 0-95.37 31.5T504-674h-49q-26-56-69.85-88-43.85-32-95.37-32Q224-794 182-751.5t-42 108.82q0 38.68 15.5 78.18 15.5 39.5 54 90T314-358q66 66 166 158Zm0-297Z" />
            </svg>
            <svg xmlns="http://www.w3.org/2000/svg" class="heart-icon" style="display: none;" height="48px"
              viewBox="0 -960 960 960" width="48px" fill="#ff0000ff">
              <path
                d="m480-121-41-37q-106-97-175-167.5t-110-126Q113-507 96.5-552T80-643q0-90 60.5-150.5T290-854q57 0 105.5 27t84.5 78q42-54 89-79.5T670-854q89 0 149.5 60.5T880-643q0 46-16.5 91T806-451.5q-41 55.5-110 126T521-158l-41 37Z" />
            </svg>
            <span class="like-count">0</span>
          </button>
          <!-- <button type="button" name="re-post">
            <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px" fill="#FFFFFF">
              <path
                d="M280-80 120-240l160-160 42 44-86 86h464v-160h60v220H236l86 86-42 44Zm-80-450v-220h524l-86-86 42-44 160 160-160 160-42-44 86-86H260v160h-60Z" />
            </svg>
            <span style="margin-left: .25rem;">0</span>
          </button> -->
        </div>
        <div class="rightToolBar">
          <button type="button" name="bookmarkButton">
            <svg xmlns="http://www.w3.org/2000/svg" class="bookmark-outline-icon" height="48px" viewBox="0 -960 960 960"
              width="48px" fill="#FFFFFF">
              <path d="M200-120v-725h560v725L480-240 200-120Zm60-91 220-93 220 93v-574H260v574Zm0-574h440-440Z" />
            </svg>
            <svg xmlns="http://www.w3.org/2000/svg" class="bookmark-icon" height="48px" viewBox="0 -960 960 960"
              width="48px" fill="#FFFFFF" style="display: none;">
              <path d="M200-120v-725h560v725L480-240 200-120Z" />
            </svg>
          </button>
          <button type="button" name="moreMenu" onclick="ToggleMoreMenu()">
            <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px" fill="#FFFFFF">
              <path
                d="M479.86-160Q460-160 446-174.14t-14-34Q432-228 446.14-242t34-14Q500-256 514-241.86t14 34Q528-188 513.86-174t-34 14Zm0-272Q460-432 446-446.14t-14-34Q432-500 446.14-514t34-14Q500-528 514-513.86t14 34Q528-460 513.86-446t-34 14Zm0-272Q460-704 446-718.14t-14-34Q432-772 446.14-786t34-14Q500-800 514-785.86t14 34Q528-732 513.86-718t-34 14Z" />
            </svg>
          </button>
        </div>
      </div>
    </section>
    <?php if ($PostOwner === true || $PageAdmin === true) { ?>
      <section class="EditPostContainer" id="EditPostContainer" style="display: none;">
        <form action="<?= filePath("/backend/api/"); ?>_editPostOptions.php" method="post">
          <script>
            fetch('<?= filePath("/backend/json/"); ?>CategoryOptions.json')
              .then(response => response.json())
              .then(data => {
                const selectElement = document.getElementById('PostTextFilterOptions');
                data.forEach(option => {
                  const opt = document.createElement('option');
                  opt.value = option.value;
                  opt.textContent = option.text;
                  selectElement.appendChild(opt);
                });
              })
              .catch(error => console.error('Error fetching the JSON:', error));
          </script>
          <select name="PostTextFilterOptions" id="PostTextFilterOptions"></select>
          <textarea name="postText" id="postText"><?= $content; ?></textarea>
          <input type="hidden" name="PUID" value="<?= $PUID; ?>">
          <input type="hidden" name="PosterUUID" value="<?= $PosterUUID; ?>">
          <button type="submit" name="editPost">
            <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px" fill="#FFFFFF">
              <path
                d="M560-80v-123l263-262 122 122L683-80H560Zm300-263-37-37 37 37ZM620-140h38l121-122-18-19-19-18-122 121v38ZM160-80v-800h400l240 240v116h-60v-76H520v-220H220v680h280v60H160Zm350-400Zm251 199-19-18 37 37-18-19Z" />
            </svg>
            <span>Edit</span>
          </button>
        </form>
      </section>
    <?php } ?>
    <script>
      function ToggleMoreMenu() {
        const moreMenu = document.getElementById("moreMenu");
        const body = document.body;
        if (moreMenu.style.display === 'none' || moreMenu.style.display === '') {
          moreMenu.style.display = 'flex';
          body.classList.add('scrollOff');
        } else {
          moreMenu.style.display = 'none';
          body.classList.remove('scrollOff');
        }
      }
      function ToggleReportForm() {
        const ReportForm = document.getElementById("reportPostForm");
        if (ReportForm.style.display === 'none' || ReportForm.style.display === '') {
          ReportForm.style.display = 'flex';
        } else {
          ReportForm.style.display = 'none';
        }
      }
      <?php if ($PostOwner === true || $PageAdmin === true) { ?>

        function PostEdit() {
          document.getElementById("moreMenu").style.display = 'none';
          document.getElementById("mainPostBody").style.display = 'none';
          document.getElementById("EditPostContainer").style.display = 'flex';
        }

        const deleteButton = document.querySelector('button[name="Delete"]');
        if (deleteButton) {
          deleteButton.addEventListener('click', function () {
            if (confirm('Are you sure you want to delete this post?')) {
              fetch(`../../../../../backend/api/_deletePost.php?PUID=<?= $PUID; ?>`, {
                method: 'POST'
              })
                .then(response => response.text())
                .then(data => {
                  if (data.includes('success')) {
                    alert('Post deleted successfully');
                    window.location.href = '<?= filePath("/profile/u/") . $UUID; ?>';
                  } else {
                    alert('Error deleting post: ' + data);
                  }
                })
                .catch(error => console.error('Error deleting post:', error));
            }
          });
        }
      <?php } ?>
      const reportForm = document.getElementById('reportPostForm');
      if (reportForm) {
        reportForm.addEventListener('submit', function (e) {
          e.preventDefault(); // Prevent default form submission

          const reason = document.querySelector('#reportReason').value;
          const extraInfo = document.querySelector('textarea[name="extraInfo"]').value;

          fetch('../../../../../backend/scripts/admin/_reportPost.php?PUID=<?= $PUID; ?>', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
              reason: reason,
              extraInfo: extraInfo
            })
          })
            .then(response => response.text())
            .then(data => {
              if (data.includes('successfully')) {
                alert('Post reported successfully');
                ToggleMoreMenu();
              } else {
                alert('Error reporting post: ' + data);
                ToggleMoreMenu();
              }
            })
            .catch(error => {
              console.error('Error reporting post:', error);
              alert('Failed to send report. Please try again.');
            });
        });
      }
      // Share button logic
      function shareCopyButton() {
        var copyText = document.getElementById("copyInput");
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        document.execCommand("copy");

        alert("URL copied: " + copyText.value);
      }

      // Like Script

      document.addEventListener("DOMContentLoaded", function () {
        const likeButton = document.getElementById("mainPostLikeButton");
        if (likeButton) {
          const puid = likeButton.getAttribute("data-puid");
          if (puid) {
            checkLikeStatus(puid, likeButton);
            const statusInterval = setInterval(() => {
              checkLikeStatus(puid, likeButton);
            }, 10000);

          } else {
            console.warn("PUID not found on the main like button.");
          }
        } else {
          console.warn("Main like button not found on page.");
        }
      });

      function checkLikeStatus(puid, button) {
        if (!puid) {
          console.error("PUID is undefined or empty in checkLikeStatus");
          return;
        }
        fetch(`<?= filePath("/backend/api/_checkLikeStatus.php"); ?>?PUID=${puid}`)
          .then(response => {
            if (!response.ok) {
              throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
          })
          .then(data => {
            const isLiked = data.liked;
            const count = data.count;
            updateLikeUI(isLiked, count);

          })
          .catch(error => {
            console.error("Error fetching like status for PUID", puid, ":", error);
          });
      }

      document.addEventListener("DOMContentLoaded", () => {
        document.querySelectorAll('.commentCard .commentLikeButton').forEach(button => {
          const puid = button.closest('.commentCard')?.id?.replace('comment-', '');
          if (puid) checkLikeStatus(puid, button);
        });
      });

      function updateLikeUI(isLiked, count) {
        const button = document.querySelector(`button[name="like"][data-puid="${PUID}"]`);
        if (!button) {
          console.error("Main like button for PUID", PUID, "not found.");
          return;
        }

        const heartOutlineIcon = button.querySelector('.heart-outline-icon');
        const heartIcon = button.querySelector('.heart-icon');
        const likeCountSpan = button.querySelector('.like-count');

        if (heartOutlineIcon && heartIcon && likeCountSpan) {
          if (isLiked) {
            heartOutlineIcon.style.display = 'none';
            heartIcon.style.display = 'block';
            heartIcon.style.fill = '#ff0000ff';
          } else {
            heartOutlineIcon.style.display = 'block';
            heartIcon.style.display = 'none';
          }
          likeCountSpan.textContent = count;
        } else {
          console.warn("Like UI elements (SVG icons or count span) not found for button with PUID:", PUID);
        }
      }

      const PUID = "<?= $PUID ?>"; // Get PUID from PHP
      const likeButton = document.querySelector('button[name="like"][data-puid="' + PUID + '"]'); // Select the specific button

      if (likeButton) {
        likeButton.addEventListener('click', function () {
          const currentHeartIcon = likeButton.querySelector('.heart-icon');
          const currentHeartOutlineIcon = likeButton.querySelector('.heart-outline-icon');
          const currentIsLiked = currentHeartIcon && window.getComputedStyle(currentHeartIcon).display !== 'none';
          const newLikeStatus = !currentIsLiked;
          const currentCountSpan = likeButton.querySelector('.like-count');
          let currentCount = parseInt(currentCountSpan.textContent) || 0;

          // Send the request to the backend
          fetch(`<?= filePath("/backend/api/"); ?>_likePost.php?PUID=${PUID}&action=${newLikeStatus ? 'like' : 'unlike'}`)
            .then(response => response.text())
            .then(data => {
              if (data.includes("Rate limit exceeded")) {
                alert(data);
              } else {
                checkLikeStatus(PUID, likeButton);
              }
            })
            .catch(error => {
              console.error('Error liking/unliking post:', error);
              Toastify({
                text: 'An error occurred while updating the like status. Please try again.',
                duration: 3000,
                gravity: "top",
                position: "right",
                style: {
                  background: "#f44336" // Red color for error
                }
              }).showToast();
            });
        });
      } else {
        console.warn("Main like button not found for attaching click handler.");
      }

      // Bookmark scripts

      function updateBookmarkUI(isBookmarked) {
        const bookmarkButton = document.querySelector('button[name="bookmarkButton"]');
        if (!bookmarkButton) {
          console.error("Bookmark button not found.");
          return;
        }

        const bookmarkOutlineIcon = bookmarkButton.querySelector('.bookmark-outline-icon');
        const bookmarkIcon = bookmarkButton.querySelector('.bookmark-icon');

        if (bookmarkOutlineIcon && bookmarkIcon) {
          if (isBookmarked) {
            bookmarkOutlineIcon.style.display = 'none';
            bookmarkIcon.style.display = 'block';
            bookmarkIcon.style.fill = '#007eaf';
          } else {
            bookmarkOutlineIcon.style.display = 'block';
            bookmarkIcon.style.display = 'none';
          }
        } else {
          console.warn("Bookmark UI elements (SVG icons) not found inside the bookmark button.");
        }
      }

      document.addEventListener('DOMContentLoaded', function () {
        checkBookmarkStatus(); // Check status on initial page load
      });

      function checkBookmarkStatus() {
        fetch(`../../../../../backend/api/_bookmarkCheck.php?PUID=<?= $PUID; ?>`)
          .then(response => {
            if (!response.ok) {
              throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
          })
          .then(data => {
            const isBookmarked = data.status === 'bookmarked';
            updateBookmarkUI(isBookmarked); // Update UI based on fetched status
          })
          .catch(error => console.error('Error checking bookmark status:', error));
      }

      const bookmarkButton = document.querySelector('button[name="bookmarkButton"]');
      if (bookmarkButton) {
        bookmarkButton.addEventListener('click', function () {
          const currentBookmarkIcon = bookmarkButton.querySelector('.bookmark-icon');
          const currentIsBookmarked = currentBookmarkIcon && window.getComputedStyle(currentBookmarkIcon).display !== 'none';
          const newBookmarkStatus = !currentIsBookmarked;
          fetch(`../../../../../backend/api/_bookmarkPost.php?action=${newBookmarkStatus ? 'bookmark' : 'unbookmark'}&PUID=<?= $PUID; ?>`)
            .then(response => response.json())
            .then(data => {
              if (data.error && data.error.includes("Rate limit exceeded")) {
                alert(data.error);
              } else if (data.message) {
                checkBookmarkStatus();
              } else {
                console.error("Unexpected response format from bookmark action:", data);
                checkBookmarkStatus();
              }
            })
            .catch(error => {
              console.error('Error bookmarking/unbookmarking post:', error);
              Toastify({
                text: 'An error occurred while updating the bookmark status. Please try again.',
                duration: 3000,
                gravity: "top",
                position: "right",
                style: {
                  background: "#f44336"
                }
              }).showToast();
            });
        });
      } else {
        console.warn("Bookmark button not found for attaching click handler.");
      }
    </script>
  </div>
  <div id="imageModal" class="modal" style="display:none;">
    <svg class="close" onclick="closeModal()" xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px" fill="#FFFFFF">
      <path d="m249-207-42-42 231-231-231-231 42-42 231 231 231-231 42 42-231 231 231 231-42 42-231-231-231 231Z" />
    </svg>
    <img class="modal-content" id="modalImage">
  </div>
  <section class="commentSection">
    <?php
    require_once "../../../../../backend/scripts/comments/_commentFormTemplate.php";
    require_once "../../../../../backend/scripts/comments/_commentPostedTemplate.php";
    ?>
  </section>
  <script>
    function showImage(imageSrc) {
      const modal = document.getElementById("imageModal");
      const modalImg = document.getElementById("modalImage");
      modal.style.display = "block";
      modalImg.src = "../../../../.." + imageSrc;
    }

    function closeModal() {
      document.getElementById("imageModal").style.display = "none";
    }
  </script>
  <?php require "../../../../../backend/_footer.php"; ?>
  <script>
    window.onload = function () {
      loadPosts();
    };
  </script>
  <script src="../../../../../backend/videoPlayer.js"></script>
  <script>
    document.addEventListener("DOMContentLoaded", function () {
      const hash = window.location.hash;

      if (hash) {
        console.log("Hash detected:", hash);

        if (hash.startsWith("#comment-")) {
          const targetId = hash.substring(1); // Remove '#'
          console.log("Looking for ID:", targetId);

          const element = document.getElementById(targetId);

          if (element) {
            console.log("Found comment immediately:", element);
            scrollToComment(element);
            return;
          } else {
            console.log("Comment not found yet. Starting observer...");

            const observer = new MutationObserver(() => {
              const el = document.getElementById(targetId);
              if (el) {
                console.log("Found comment via observer:", el);
                observer.disconnect();
                scrollToComment(el);
              }
            });

            const container = document.getElementById('comments-container') || document.body;
            observer.observe(container, { childList: true, subtree: true });
          }
        } else {
          console.warn("Hash does not start with #comment-");
        }
      } else {
        console.log("No hash in URL.");
      }
    });

    function scrollToComment(element) {
      console.log("Scrolling to comment...");
      element.scrollIntoView({ behavior: "smooth", block: "center" });
      element.classList.add("highlight");
    }
  </script>
</body>

</html>