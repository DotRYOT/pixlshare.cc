<div class="commentLoadedArea"></div>

<script>
  function createPostCard(post) {
    const isUserLiked = Boolean(post.liked);
    const likeCount = post.likeCount || 0;
    const likedStyle = isUserLiked ? 'inline' : 'none';
    const unlikedStyle = isUserLiked ? 'none' : 'inline';

    return `
      <div class="commentCard" id="comment-${post.COM_PUID}">
        <div class="commentTopContainer">
          <div class="profileLink">
            <a href="<?= filePath('/profile/u/'); ?>${post.UUID}/">
              <img src="<?= filePath("/"); ?>${post.pfp_image_link}" alt="Profile" />
              <p>@${post.username}</p>
            </a>
          </div>
      
          <div class="commentToolBar">
            <button onclick="toggleCommentLike('${post.COM_PUID}', this)" class="commentLikeButton">
              <ion-icon name="heart-outline" style="display: ${unlikedStyle};"></ion-icon>
              <ion-icon name="heart" class="liked" style="display: ${likedStyle}; color: red;"></ion-icon>
              <span class="likeCount">${likeCount}</span>
            </button>
          </div>
        </div>
      
        <div class="commentBottomContainer">
          <div class="commentTextContainer">
            <a href="<?= filePath("/profile/u/");?>${post.UUID}/post/${post.COM_PUID}">
              ${post.media_id ? `<img src="<?= filePath("/"); ?>${post.media_id}" />` : ''}
              <p>${post.content}</p>
            </a>
            <small>${new Date(post.Time * 1000).toLocaleString()}</small>
          </div>
        </div>
      </div>
    `;
  }

  function shareComment(url) {
    if (navigator.clipboard) {
      navigator.clipboard.writeText(url);
      alert("Comment link copied!");
    } else {
      alert("Sharing not supported on this browser.");
    }
  }

  function toggleCommentLike(puid, button) {
    const iconOutline = button.querySelector('ion-icon[name="heart-outline"]');
    const iconFull = button.querySelector('ion-icon[name="heart"]');
    const countSpan = button.querySelector('.likeCount');

    if (!iconOutline || !iconFull || !countSpan) return;

    const isCurrentlyLiked = iconFull.style.display === 'inline';
    const newAction = isCurrentlyLiked ? 'unlike' : 'like';

    fetch(`<?= filePath("/backend/api/_likePost.php"); ?>?PUID=${puid}&action=${newAction}`)
      .then(response => response.json())
      .then(data => {
        if (data.error) {
          alert(data.error);
          return;
        }

        // Update UI based on response
        iconOutline.style.display = data.status === 'liked' ? 'none' : 'inline';
        iconFull.style.display = data.status === 'liked' ? 'inline' : 'none';
        iconFull.style.color = data.status === 'liked' ? 'red' : 'inherit'; // Optional
        countSpan.textContent = Math.max(data.count, 0);
      })
      .catch(err => {
        console.error("Like error:", err.message);
        alert("Failed to update like status");
      });
  }

  function loadLikeStatus(puid) {
    fetch(`<?= filePath("/backend/api/_checkLikeStatus.php"); ?>?PUID=${puid}`)
      .then(response => {
        if (!response.ok) throw new Error('Network response was not ok');
        return response.json();
      })
      .then(data => {
        if (data.error) {
          console.error('Error:', data.error);
          return;
        }

        // Update UI based on response
        const likeIconFull = document.getElementById(`like-icon-full-${puid}`);
        const likeIconOutline = document.getElementById(`like-icon-outline-${puid}`);
        const likeCount = document.getElementById(`like-count-${puid}`);

        if (likeIconFull && likeIconOutline && likeCount) {
          likeIconFull.style.display = data.liked ? 'inline' : 'none';
          likeIconOutline.style.display = data.liked ? 'none' : 'inline';
          likeCount.textContent = data.count;
        }
      })
      .catch(error => {
        console.error('Fetch error:', error);
      });
  }

  function loadPosts() {
    fetch(`<?= filePath("/backend/scripts/comments/_commentFeedLoader.php"); ?>?OG_PUID=<?= $PUID; ?>`)
      .then(response => {
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
      })
      .then(data => {
        const container = document.querySelector('.commentLoadedArea');
        if (data.error) {
          console.error("Server error:", data.error);
          container.innerHTML = `<div class="noPosts">${data.error}</div>`;
          return;
        }

        if (data.comments && data.comments.length > 0) {
          container.innerHTML = data.comments.map(comment => createPostCard(comment)).join('');
        } else {
          container.innerHTML = `<div class="noPosts">No comments yet.</div>`;
        }
      })
      .catch(error => {
        console.error("Fetch error:", error.message);
        document.querySelector('.commentLoadedArea').innerHTML = `
        <div class="noPosts">Error loading posts. Please try again later.</div>
      `;
      });
  }

  window.onload = loadPosts;
</script>