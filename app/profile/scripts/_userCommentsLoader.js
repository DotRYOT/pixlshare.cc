// _userCommentsLoader.js

let commentOffset = 0;
const commentLimit = 5; // Or whatever limit you prefer
let commentLoading = false;
let commentHasMore = true;
// 🔁 Debounce utility function (Reused from post loader)
function debounce(fn, delay) {
  let timer;
  return () => {
    clearTimeout(timer);
    timer = setTimeout(() => fn(), delay);
  };
}

// Function to initialize comment loading
function userCommentsLoader() {
  const commentsContainer = document.getElementById("userComments");
  if (!commentsContainer) {
    console.error("Comments container (#userComments) not found.");
    return;
  }

  // Reset state for fresh load if needed (e.g., on tab switch)
  // This part depends on how you manage state. You might want to reset only when switching tabs.
  // For now, we'll assume it's called fresh when the tab is opened.
  commentOffset = 0;
  commentHasMore = true;
  commentLoading = false;
  commentsContainer.innerHTML = ""; // Clear previous comments if any

  const spinner = document.getElementById("loadingSpinner");
  if (spinner) {
    spinner.classList.remove("hidden");
  }

  loadUserComments();

  // Setup scroll listener for infinite scroll on comments
  window.addEventListener("scroll", debounce(handleCommentScroll, 200));
}

function handleCommentScroll() {
  const scrollTop = window.scrollY || document.documentElement.scrollTop;
  const windowHeight = window.innerHeight;
  const docHeight = document.documentElement.scrollHeight;

  // Trigger load when near the bottom
  if (scrollTop + windowHeight >= docHeight - 300) {
    loadUserComments();
  }
}

// Lazy Load Images (Reused from post loader)
// Ensure the observer is available globally or redefined if needed
// Assuming imgObserver is defined globally as in your original script
// If not, uncomment and use the definition from _postLoader.js here.

function loadUserComments() {
  if (commentLoading || !commentHasMore) return;

  commentLoading = true;
  const spinner = document.getElementById("loadingSpinner");
  if (spinner) {
    spinner.classList.remove("hidden");
  }

  // Construct URL to fetch comments
  // Assuming you have or will create a backend script like _UserCommentLoader.php
  const url = `../backend/scripts/profile/_userCommentsLoader.php?limit=${commentLimit}&offset=${commentOffset}`;

  fetch(url)
    .then((response) => {
      if (!response.ok)
        throw new Error(`HTTP error! Status: ${response.status}`);
      return response.json();
    })
    .then((data) => {
      const container = document.getElementById("userComments"); // Target comments container
      if (!container) {
        throw new Error(
          "Comments container (#userComments) not found in DOM after fetch."
        );
      }

      if (data.error) {
        container.innerHTML = `<p>Error loading comments: ${data.error}</p>`;
        if (spinner) spinner.classList.add("hidden");
        commentHasMore = false;
        commentLoading = false;
        return;
      }

      if (!data.comments || data.comments.length === 0) {
        if (commentOffset === 0) {
          // No comments at all for this user
          container.innerHTML = `<div class="noPostCategory"><p>No comments found.</p><img src="../assets/sadCat03.jpg" alt="sad cat 3"></div>`;
        } else {
          // Reached the end of the comment list
          // Optionally add a "no more comments" message if desired, but don't replace content
          // container.innerHTML += `<div class="noMorePosts"><p>No more comments.</p></div>`;
          // Or just stop loading
        }
        if (spinner) spinner.classList.add("hidden");
        commentHasMore = false;
        commentLoading = false;
        return;
      }

      // Create comment elements
      const tempContainer = document.createElement("div");
      tempContainer.innerHTML = data.comments.map(createCommentCard).join("");

      // Append with fade-in animation (similar to posts)
      Array.from(tempContainer.children).forEach((commentElement) => {
        commentElement.style.opacity = 0;
        container.appendChild(commentElement);
        void commentElement.offsetWidth; // Trigger reflow
        commentElement.style.transition =
          "opacity 0.4s ease, transform 0.4s ease";
        commentElement.style.opacity = 1;
        commentElement.style.transform = "translateY(0)";
      });

      // Observe new images for lazy loading
      const newImages = container.querySelectorAll(".lazy-img");
      newImages.forEach((img) => {
        // Check if imgObserver is defined globally, otherwise define it or handle differently
        if (typeof imgObserver !== "undefined" && !img.src && img.dataset.src) {
          imgObserver.observe(img);
        } else if (!img.src && img.dataset.src) {
          // Fallback if imgObserver is not available
          img.src = img.dataset.src;
        }
      });

      // Update offset for next page
      commentOffset += data.comments.length;

      if (spinner) spinner.classList.add("hidden");
      commentLoading = false;

      // Check if this was the last page
      if (data.comments.length < commentLimit) {
        commentHasMore = false;
        // Optionally indicate no more comments
        // container.innerHTML += `<div class="noMorePosts"><p>No more comments.</p></div>`;
      }
    })
    .catch((err) => {
      console.error("Failed to load comments:", err.message);
      const container = document.getElementById("userComments");
      if (container) {
        container.innerHTML = `<p style="text-align:center; padding:20px;">Error loading comments. Please try again later.</p>`;
      }
      const spinner = document.getElementById("loadingSpinner");
      if (spinner) spinner.classList.add("hidden");
      commentLoading = false;
      commentHasMore = false;
    });
}

function createCommentCard(comment) {
  // Ensure required fields are present
  if (
    !comment.PUID ||
    !comment.OG_PUID ||
    !comment.OG_Posters_UUID ||
    !comment.content
  ) {
    console.error("Invalid comment data received:", comment);
    return `<div class="postCard"><p>Error: Incomplete comment data.</p></div>`; // Or handle gracefully
  }

  // Determine the correct UUID and PUID for navigation (to the original post)
  const targetUUID = comment.OG_Posters_UUID;
  const targetPUID = comment.OG_PUID;
  const commentAnchor = `#comment-${comment.PUID}`; // Anchor to scroll to this specific comment on the post page

  // Optional: Fetch like status for the original post if needed, or implement comment-specific likes later
  // For now, we focus on displaying the comment content and linking to the original post

  return `
    <div
      id="comment-${comment.PUID}"
      class="postCard"
      style="transform: translateY(20px); border-left: 3px solid #ff4500;"
    >
      <!-- Visual distinction for comments -->
      <div class="topCardContainer">
        <div class="profileLink">
          <!-- Link to the original post author's profile -->
          <a href="../profile/u/${targetUUID}/">
            <img
              src="../${comment.pfp_image_link}"
              alt="Profile picture of ${comment.username}"
            />
            <p>
              @${comment.username}
              <span style="font-size: 0.8em; color: #666;">(commented)</span>
            </p>
          </a>
        </div>
        <div class="buttonTray">
          <!-- Share button for the original post -->
          <button onclick="sharePost('${targetUUID}', '${targetPUID}')">
            <ion-icon name="share-outline"></ion-icon>
          </button>
          <!-- Like button for the original post (you might want comment-specific likes later) -->
          <!-- Placeholder - you'd need to adapt togglePostLike/loadLikeStatus for comments or create new functions -->
          <!--
        <button onclick="toggleCommentLike('${comment.PUID}', this)" class="likeButton">
          <ion-icon name="heart-outline" id="comment-like-icon-outline-${comment.PUID}" style="display: inline;"></ion-icon>
          <ion-icon name="heart" class="liked" id="comment-like-icon-full-${comment.PUID}" style="display: none; color: red;"></ion-icon>
          <span id="comment-like-count-${comment.PUID}" class="likeCount">0</span>
        </button>
        -->
        </div>
      </div>
      <div class="bottomCardContainer">
        <div class="textContainer">
          <!-- Link to the original post, scrolling to this specific comment -->
          <a
            href="../profile/u/${targetUUID}/post/${targetPUID}/${commentAnchor}"
          >
            <p><strong>Comment:</strong> ${comment.content}</p>
            <!-- Optionally indicate it's a reply to the original post -->
            <p style="font-size: 0.85em; color: #555; margin-top: 5px;">
              Replied to post:
              <em>${comment.original_post_snippet || "..."}</em>
            </p>
          </a>
        </div>
      </div>
      ${comment.image_id
        ? `
    <div class="middleCardContainer">
      <div class="postImageContainer">
        <!-- Image links to the original post/comment -->
        <a href="../profile/u/${targetUUID}/post/${targetPUID}/${commentAnchor}">
          <img data-src="../${comment.image_id}" alt="Comment image" class="lazy-img">
        </a>
      </div>
    </div>`
        : ""}
      <!-- Display snippet or title of the original post -->
      ${comment.original_post_snippet && !comment.image_id // Avoid duplication if image already shown
        ? `
    <div class="originalPostSnippet" style="border-top: 1px solid #eee; margin-top: 10px; width: 100%; padding-top: 10px;">
        <p style="font-size: 0.85em; color: #555;"><strong>On:</strong> <em>${comment.original_post_snippet}</em></p>
    </div>`
        : ""}
    </div>
  `;
}
