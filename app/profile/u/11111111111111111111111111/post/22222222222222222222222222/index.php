<?php
require "./pageScript.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
   
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $firstFiveWords; ?> - PIXLSHARE</title>
  <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
  <link rel="shortcut icon" href="<?= filePath("/assets/logos/") ?> pixlshareLogo_white_128.png"
    type="image/x-icon">
  <link rel="stylesheet" href="<?= filePath("/profile/u/css/") ?>index.min.css">
  <link rel="stylesheet" href="./css/index.min.css">
  <link rel="manifest" href="<?= filePath("/") ?>manifest.json" />

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
      <h2>This post has been suspended. Click the link below to access the support forms!</h2>
      <a
        href="<?= filePath("") . "/profile/support/suspendedpost/?puid=" . $PUID . "&uuid=" . $SessionRedirectUUID; ?>">Support</a>
    </div>
  </div>
  <div class="moreMenu" id="moreMenu" style="display: none;">
    <div class="moreMenuContainer">
      <?php if ($PostOwner === true || $PageAdmin === true) { ?>
        <section>
          <button name="Delete" type="button">
            <ion-icon name="trash-outline"></ion-icon>
          </button>
          <button type="button" name="edit" onclick="PostEdit()">
            <ion-icon name="create-outline"></ion-icon>
          </button>
        </section>
        <div class="line"></div>
      <?php } ?>
      <section>
        <button name="reportPost" type="button">
          <ion-icon name="alert-outline"></ion-icon>
        </button>
        <button type="button" name="share" onclick="shareCopyButton()">
          <ion-icon name="share-outline"></ion-icon>
          <input type="text" value="https://<?= $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>"
            id="copyInput" readonly>
        </button>
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
              <div class="progress-bar"></div>
              <button id="volume"><ion-icon name="volume-high"></ion-icon></button>
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
          <button type="button" name="like">
            <ion-icon name="heart-outline"></ion-icon>
            <ion-icon name="heart" class="liked" style="display: none;"></ion-icon>
            <span style="margin-left: .25rem;">0</span>
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
      <?php if ($PostOwner === true || $PageAdmin === true) { ?>

        function PostEdit() {
          document.getElementById("moreMenu").style.display = 'none';
          document.getElementById("mainPostBody").style.display = 'none';
          document.getElementById("EditPostContainer").style.display = 'flex';
        }

        document.querySelector('button[name="Delete"]').addEventListener('click', function () {
          if (confirm('Are you sure you want to delete this post?')) {
            fetch(`../../../../../backend/api/_deletePost.php?PUID=<?= $PUID; ?>`, {
              method: 'POST'
            })
              .then(response => response.text())
              .then(data => {
                // console.log('Delete response:', data);
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
      <?php } ?>
      document.querySelector('button[name="reportPost"]').addEventListener('click', function () {
        if (confirm('Are you sure you want to report this post?')) {
          fetch(`../../../../../backend/scripts/admin/_reportPost.php?PUID=<?= $PUID; ?>`, {
            method: 'POST'
          })
            .then(response => response.text())
            .then(data => {
              // console.log('Report response:', data);
              if (data.includes('success')) {
                alert('Post reported successfully');
                // window.location.href = '<?= filePath("/profile/u/") . $UUID; ?>';
              } else {
                alert('Error reporting post: ' + data);
              }
            })
            .catch(error => console.error('Error reporting post:', error));
        }
      });
      // Share button logic
      function shareCopyButton() {
        var copyText = document.getElementById("copyInput");
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        document.execCommand("copy");

        alert("URL copied: " + copyText.value);
      }

      // Like Script

      document.addEventListener('DOMContentLoaded', function () {
        checkLikeStatus();
        // setInterval(checkLikeStatus, 10000);
      });

      function checkLikeStatus() {
        fetch(`../../../../../backend/api/_likeCheck.php?PUID=<?= $PUID; ?>`)
          .then(response => response.json())
          .then(data => {
            updateLikeUI(data.status === 'liked', data.count);
          })
          .catch(error => console.error('Error checking like status:', error));
      }

      function updateLikeUI(isLiked, count) {
        const heartOutline = document.querySelector('ion-icon[name="heart-outline"]');
        const heart = document.querySelector('ion-icon[name="heart"]');
        const likeCount = document.querySelector('button[name="like"] span');

        heartOutline.style.display = isLiked ? 'none' : 'inline';
        heart.style.display = isLiked ? 'inline' : 'none';
        likeCount.textContent = count;
      }

      document.querySelector('button[name="like"]').addEventListener('click', function () {
        const isLiked = document.querySelector('ion-icon[name="heart"]').style.display === 'inline';
        const newLikeStatus = !isLiked;
        const currentCount = parseInt(document.querySelector('button[name="like"] span').textContent);

        fetch(`../../../../../backend/api/_likePost.php?PUID=<?= $PUID; ?>&action=${newLikeStatus ? 'like' : 'unlike'}`)
          .then(response => response.text())
          .then(data => {
            if (data.includes("Rate limit exceeded")) {
              alert(data);
            } else {
              // console.log('Like response:', data);
              updateLikeUI(newLikeStatus, newLikeStatus ? currentCount + 1 : currentCount - 1);
            }
          })
          .catch(error => console.error('Error liking/unliking post:', error));
      });

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

      document.querySelector('button[name="bookmarkButton"]').addEventListener('click', function () {
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
        volumeControl.value = volumeToSet; // Set the slider value
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

      document.getElementById('fullscreenButton').addEventListener('click', function () {
        const mainVideo = document.getElementById('mainVideo');

        if (!document.fullscreenElement) {
          mainVideo.requestFullscreen().catch(err => {
            console.error(`Error attempting to enable fullscreen mode: ${err.message} (${err.name})`);
          });
        } else {
          document.exitFullscreen();
        }
      });
    </script>
  </div>
  <div id="imageModal" class="modal" style="display:none;">
    <ion-icon class="close" onclick="closeModal()" name="close-outline"></ion-icon>
    <img class="modal-content" id="modalImage">
  </div>
  <section class="commentSection">
    <div class="commentPostArea">
      <form id="commentForm">
        <input type="hidden" name="PUID" value="<?= $PUID; ?>">
        <textarea name="comment" id="comment" placeholder="Comment..." required></textarea>
        <input type="submit" value="Post Comment">
      </form>
    </div>
    <div class="commentLoadedArea"></div>
    <script>
      document.getElementById("commentForm").addEventListener("submit", function (event) {
        event.preventDefault();
        const formData = new FormData(this);
        fetch("../../../../../backend/api/_postComment.php", {
          method: "POST",
          body: formData
        })
          .then(response => response.text())
          .then(data => {
            if (data.includes("Rate limit exceeded")) {
              alert(data);
            } else {
              // console.log(data);
              loadComments();
              document.getElementById("comment").value = '';
            }
          })
          .catch(error => {
            console.error('Error:', error);
          });
      });

      function createPostCard(post, isAdmin) {
        return `
      <div class="commentCard">
        <div class="commentTopContainer">
          <div class="profileLink">
            <a href="<?= filePath("/profile/u/"); ?>${post.UUID}/">
              <img src="<?= filePath("/"); ?>${post.pfp_image_link}" alt="" />
              <p>@${post.username}</p>
            </a>
          </div>
          ${isAdmin ? `
          <div class="commentButtonTray">
            <button onclick="deleteComment('${post.PUID}')">
              <ion-icon name="trash-outline"></ion-icon>
            </button>
          </div>` : ''}
        </div>
        <div class="commentBottomContainer">
          <div class="commentTextContainer">
            <p>${post.content}</p>
            <small>${new Date(post.Time * 1000).toLocaleString()}</small>
          </div>
        </div>
      </div>
      `;
      }

      function loadComments() {
        const ogPuid = "<?= $PUID; ?>";

        fetch(`<?= filePath("/backend/scripts/comments/_commentFeedLoader.php"); ?>?PUID=${ogPuid}`)
          .then(async (response) => {
            const contentType = response.headers.get("content-type");
            if (contentType && contentType.includes("application/json")) {
              return response.json();
            } else {
              const text = await response.text();
              console.error("Unexpected non-JSON response:", text);
              document.querySelector('.commentLoadedArea').innerHTML = `<div class="noPosts">Error loading comments</div>`;
              return;
            }
          })
          .then(data => {
            if (data.error) {
              console.error(data.error);
              return;
            }

            const container = document.querySelector('.commentLoadedArea');
            if (data.comments.length === 0) {
              container.innerHTML = '<div class="noPosts">No comments yet.</div>';
              return;
            }

            container.innerHTML = data.comments.map(comment => createPostCard(comment)).join('');
            // After rendering, refresh like status for each comment
            document.querySelectorAll('.commentLikeButton').forEach(button => {
              const puid = button.closest('.commentCard')?.id?.replace('comment-', '');
              if (puid) checkLikeStatus(puid, button);
            });
          })
          .catch(error => {
            console.error("Fetch error:", error);
            document.querySelector('.commentLoadedArea').innerHTML = '<div class="noPosts">Error loading comments. Please try again later.</div>';
          });
      }

      window.onload = function () {
        loadComments();
      };
      // setInterval(loadComments, 10000);
    </script>
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
  <script src="../../../../../backend/videoPlayer.js"></script>
  <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>

</html>