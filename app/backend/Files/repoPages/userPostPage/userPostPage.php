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
            <ion-icon name="close-outline"></ion-icon>
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
            <ion-icon name="trash-outline"></ion-icon>
          </button>
          <button type="button" name="edit" onclick="ToggleMoreMenu(), PostEdit()">
            <ion-icon name="create-outline"></ion-icon>
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
            <ion-icon name="alert-outline"></ion-icon>
          </button>
          <button type="button" name="share" onclick="shareCopyButton()">
            <ion-icon name="share-outline"></ion-icon>
            <input type="text" value="https://<?= $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>" id="copyInput"
              readonly>
          </button>
        </div>
      </section>
    </div>
    <div class="closeMoreMenu">
      <button type="button" name="CloseMoreMenu" onclick="ToggleMoreMenu()">
        <ion-icon name="close-outline"></ion-icon>
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
              <button id="play-pause"><ion-icon name="play"></ion-icon></button>

              <!-- Volume control group -->
              <div class="volume-control">
                <button id="volume"><ion-icon name="volume-high"></ion-icon></button>
                <div class="volume-slider">
                  <input type="range" min="0" max="1" step="0.05" value="1">
                </div>
              </div>

              <div class="progress-bar">
                <div class="buffer-bar"></div>
              </div>
              <button id="fullscreen"><ion-icon name="expand"></ion-icon></button>
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
            <ion-icon class="heart-outline-icon" name="heart-outline"></ion-icon>
            <ion-icon class="heart-icon" name="heart" style="display: none;"></ion-icon>
            <span class="like-count">0</span>
          </button>
          <!-- <button type="button" name="re-post">
            <ion-icon name="repeat-outline"></ion-icon>
            <span style="margin-left: .25rem;">0</span>
          </button> -->
        </div>
        <div class="rightToolBar">
          <button type="button" name="bookmarkButton">
            <ion-icon name="bookmark-outline"></ion-icon>
            <ion-icon name="bookmark" class="bookmarked" style="display: none;"></ion-icon>
          </button>
          <button type="button" name="moreMenu" onclick="ToggleMoreMenu()">
            <ion-icon name="ellipsis-vertical-outline"></ion-icon>
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
            <ion-icon name="create-outline"></ion-icon>
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

      // document.addEventListener('DOMContentLoaded', function () {
      //   checkLikeStatus();
      //   setInterval(checkLikeStatus, 1000);
      // });

      document.addEventListener("DOMContentLoaded", function () {
        const likeButton = document.getElementById("mainPostLikeButton");
        if (likeButton) {
          const puid = likeButton.getAttribute("data-puid");
          checkLikeStatus(puid, likeButton);
          setInterval(() => {
            checkLikeStatus(puid, likeButton);
          }, 10000);
        } else {
          console.warn("Like button not found on page.");
        }
      });

      function checkLikeStatus(puid, button) {
        if (!button) {
          console.error("Button is undefined in checkLikeStatus");
          return;
        }

        fetch(`<?= filePath("/backend/api/_checkLikeStatus.php"); ?>?PUID=${puid}`)
          .then(response => {
            if (!response.ok) throw new Error("Network response was not ok");
            return response.json();
          })
          .then(data => {
            if (data.error) {
              console.error("Like Error:", data.error);
              return;
            }

            const heart = button.querySelector(".heart-icon");
            const heartOutline = button.querySelector(".heart-outline-icon");
            const likeCount = button.querySelector(".like-count");

            if (heart && heartOutline && likeCount) {
              heart.style.display = data.liked ? "inline" : "none";
              heartOutline.style.display = data.liked ? "none" : "inline";
              likeCount.textContent = data.count;

              // ✅ Add this line to set the heart color to red when liked
              heart.style.color = data.liked ? "red" : ""; // Empty string resets color
            }
          })
          .catch(error => {
            console.error("Fetch error:", error);
          });
      }

      document.addEventListener("DOMContentLoaded", () => {
        document.querySelectorAll('.commentCard .commentLikeButton').forEach(button => {
          const puid = button.closest('.commentCard')?.id?.replace('comment-', '');
          if (puid) checkLikeStatus(puid, button);
        });
      });

      function updateLikeUI(isLiked, count) {
        const heart = document.querySelector('ion-icon[name="heart"]');
        const heartOutline = document.querySelector('ion-icon[name="heart-outline"]');
        const likeCount = document.querySelector('button[name="like"] span');

        if (heart && heartOutline && likeCount) {
          // Toggle icon visibility
          heart.style.display = isLiked ? 'inline' : 'none';
          heartOutline.style.display = isLiked ? 'none' : 'inline';

          // Update like count
          likeCount.textContent = count;

          // Set colors
          if (isLiked) {
            heart.style.color = 'red';         // Red when liked
            heartOutline.style.color = '';     // Reset outline color
          } else {
            heart.style.color = '';            // Reset filled heart color
            heartOutline.style.color = 'white';// White when unliked
          }
        }
      }

      const likeButton = document.querySelector('button[name="like"]');
      if (likeButton) {
        likeButton.addEventListener('click', function () {
          const isLiked = document.querySelector('ion-icon[name="heart"]').style.display === 'inline';
          const newLikeStatus = !isLiked;
          const currentCount = parseInt(document.querySelector('button[name="like"] span').textContent);

          fetch(`<?= filePath("/backend/api/"); ?>_likePost.php?PUID=<?= $PUID; ?>&action=${newLikeStatus ? 'like' : 'unlike'}`)
            .then(response => response.text())
            .then(data => {
              if (data.includes("Rate limit exceeded")) {
                alert(data);
              } else {
                // Parse updated count from response if available
                const updatedCount = newLikeStatus ? currentCount + 1 : Math.max(currentCount - 1, 0);
                updateLikeUI(newLikeStatus, updatedCount);
              }
            })
            .catch(error => console.error('Error liking/unliking post:', error));
        });
      }

      // Bookmark scripts

      document.addEventListener('DOMContentLoaded', function () {
        checkBookmarkStatus();
      });

      function checkBookmarkStatus() {
        fetch(`../../../../../backend/api/_bookmarkCheck.php?PUID=<?= $PUID; ?>`)
          .then(response => response.json())
          .then(data => {
            // console.log('Bookmark check response:', data);
            updateBookmarkUI(data.status === 'bookmarked');
          })
          .catch(error => console.error('Error checking bookmark status:', error));
      }

      function updateBookmarkUI(isBookmarked) {
        const bookmarkOutline = document.querySelector('ion-icon[name="bookmark-outline"]');
        const bookmark = document.querySelector('ion-icon[name="bookmark"]');

        if (bookmarkOutline) {
          bookmarkOutline.style.display = isBookmarked ? 'none' : 'inline';
        }
        if (bookmark) {
          bookmark.style.display = isBookmarked ? 'inline' : 'none';
        }
      }

      const bookmarkButton = document.querySelector('button[name="bookmarkButton"]');
      if (bookmarkButton) {
        bookmarkButton.addEventListener('click', function () {
          const isBookmarked = document.querySelector('ion-icon[name="bookmark"]').style.display === 'inline';
          const newBookmarkStatus = !isBookmarked;
          fetch(`../../../../../backend/api/_bookmarkPost.php?action=${newBookmarkStatus ? 'bookmark' : 'unbookmark'}&PUID=<?= $PUID; ?>`)
            .then(response => response.text())
            .then(data => {
              if (data.includes("Rate limit exceeded")) {
                alert(data);
              } else {
                // console.log('Bookmark response:', data);
                updateBookmarkUI(newBookmarkStatus); // Update UI based on new status
              }
            })
            .catch(error => console.error('Error bookmarking/unbookmarking post:', error));
        });
      }

      const defaultVolume = 0.5;

      function setVolumeForAllVideos(volume) {
        const videos = document.querySelectorAll('video');
        videos.forEach(video => {
          video.volume = volume;
        });
      }

      const savedVolume = localStorage.getItem('videoVolume');
      const volumeToSet = savedVolume !== null ? parseFloat(savedVolume) : defaultVolume;

      document.addEventListener('DOMContentLoaded', function () {
        setVolumeForAllVideos(volumeToSet); // Set volume and play videos

        const volumeControl = document.getElementById('volumeControl');
        if (volumeControl) {
          volumeControl.value = volumeToSet; // Set the slider value
        }
      });

      document.addEventListener('input', function (event) {
        if (event.target.matches('#volumeControl')) {
          const newVolume = parseFloat(event.target.value);
          setVolumeForAllVideos(newVolume);
          localStorage.setItem('videoVolume', newVolume);
        }
      });

      function togglePlayPause() {
        const mainVideo = document.querySelector('video#mainVideo'); // Adjust the selector as needed
        const playPauseButton = document.getElementById('playPause');

        if (mainVideo) {
          const playIcon = playPauseButton.querySelector('ion-icon[name="play-outline"]');
          const pauseIcon = playPauseButton.querySelector('ion-icon[name="pause-outline"]');

          if (mainVideo.paused) {
            mainVideo.play();
            playIcon.style.display = 'none';
            pauseIcon.style.display = 'flex';
          } else {
            mainVideo.pause();
            playIcon.style.display = 'flex';
            pauseIcon.style.display = 'none';
          }
        } else {
          console.error('Main video element not found.');
        }
      }

      const fullscreenButton = document.getElementById('fullscreenButton');
      if (fullscreenButton) {
        fullscreenButton.addEventListener('click', function () {
          const mainVideo = document.getElementById('mainVideo');

          if (!document.fullscreenElement) {
            mainVideo.requestFullscreen().catch(err => {
              console.error(`Error attempting to enable fullscreen mode: ${err.message} (${err.name})`);
            });
          } else {
            document.exitFullscreen();
          }
        });
      }
    </script>
  </div>
  <div id="imageModal" class="modal" style="display:none;">
    <ion-icon class="close" onclick="closeModal()" name="close-outline"></ion-icon>
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
  <script type="module" src="https://cdn.jsdelivr.net/npm/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
  <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js" crossorigin></script>
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