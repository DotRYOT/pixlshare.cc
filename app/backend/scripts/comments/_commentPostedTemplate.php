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
              <svg xmlns="http://www.w3.org/2000/svg" id="heart-outline" style="display: ${unlikedStyle};" height="40px" viewBox="0 -960 960 960" width="40px" fill="#FFFFFF">
                <path d="m480-120.67-46.67-42q-104.18-95.08-172.25-164.04Q193-395.67 152.67-450.17q-40.34-54.5-56.5-99.16Q80-594 80-640q0-91.44 61.33-152.72 61.34-61.28 152-61.28 55.34 0 103.34 25.33 48 25.34 83.33 72.67 39.33-49.33 86.33-73.67 47-24.33 100.34-24.33 90.66 0 152 61.28Q880-731.44 880-640q0 46-16.17 90.67-16.16 44.66-56.5 99.16-40.33 54.5-108.41 123.46-68.07 68.96-172.25 164.04l-46.67 42Zm0-88.66q99.49-90.67 163.75-155.5Q708-429.67 745.67-478.17q37.66-48.5 52.66-86.42t15-75.31q0-64.1-41.33-105.77-41.33-41.66-105.18-41.66-50.02 0-92.59 29.83-42.56 29.83-65.56 81.5h-58q-22.34-51-64.9-81.17-42.57-30.16-92.59-30.16-63.85 0-105.18 41.66-41.33 41.67-41.33 105.88 0 37.46 15 75.62 15 38.17 52.66 87Q252-428.33 316.67-363.83q64.66 64.5 163.33 154.5Zm0-289Z"/>
              </svg>
              <svg xmlns="http://www.w3.org/2000/svg" id="heart" class="liked" style="display: ${likedStyle}; fill: red;" height="40px" viewBox="0 -960 960 960" width="40px" fill="#FFFFFF">
                <path d="m480-120.67-46.67-42q-104.33-95-172.33-164-68-69-108.33-123.5-40.34-54.5-56.5-99.16Q80-594 80-640q0-91.33 61.33-152.67 61.34-61.33 152-61.33 55.34 0 103.34 25.33 48 25.34 83.33 72.67 39.33-49.33 86.33-73.67 47-24.33 100.34-24.33 90.66 0 152 61.33Q880-731.33 880-640q0 46-16.17 90.67-16.16 44.66-56.5 99.16Q767-395.67 699-326.67t-172.33 164l-46.67 42Z"/>
              </svg>
              <span class="likeCount">${likeCount}</span>
            </button>
          </div>
        </div>
      
        <div class="commentBottomContainer">
          <div class="commentTextContainer">
            <a href="<?= filePath("/profile/u/"); ?>${post.UUID}/post/${post.COM_PUID}">
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
    const iconOutline = document.getElementById('heart-outline');
    const iconFull = document.getElementById('heart');
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
        iconFull.style.color = data.status === 'liked' ? 'red' : 'inherit';
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