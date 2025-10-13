// _userLikesLoader.js

let likesOffset = 0;
const likesLimit = 5; // Adjust as needed
let likesLoading = false;
let likesHasMore = true;

// 🔁 Debounce utility function (Reused)
function debounce(fn, delay) {
  let timer;
  return () => {
    clearTimeout(timer);
    timer = setTimeout(() => fn(), delay);
  };
}

// Function to initialize liked posts loading
function UserLikesLoader() {
  const likesContainer = document.getElementById("LikesTab");
  if (!likesContainer) {
    console.error("Likes container (#LikesTab) not found.");
    return;
  }

  // Reset state for fresh load
  likesOffset = 0;
  likesHasMore = true;
  likesLoading = false;
  likesContainer.innerHTML = ""; // Clear previous likes if any

  const spinner = document.getElementById("loadingSpinner");
  if (spinner) {
    spinner.classList.remove("hidden");
  }

  loadUserLikes();

  // Setup scroll listener for infinite scroll on likes
  // Use a namespaced event listener to avoid conflicts, or remove previous if needed
  window.addEventListener("scroll", debounce(handleLikesScroll, 200));
}

function handleLikesScroll() {
  const scrollTop = window.scrollY || document.documentElement.scrollTop;
  const windowHeight = window.innerHeight;
  const docHeight = document.documentElement.scrollHeight;

  // Trigger load when near the bottom
  if (scrollTop + windowHeight >= docHeight - 300) {
    loadUserLikes();
  }
}

// Reuse the existing imgObserver from _postLoader.js if it's loaded globally
// If not, you might need to redefine it here or ensure _postLoader.js loads first.
// Assuming imgObserver is available globally as in your original script.

function loadUserLikes() {
  if (likesLoading || !likesHasMore) return;

  likesLoading = true;
  const spinner = document.getElementById("loadingSpinner");
  if (spinner) {
    spinner.classList.remove("hidden");
  }

  // Construct URL to fetch liked posts
  const url = `../backend/scripts/profile/_UserLikesLoader.php?limit=${likesLimit}&offset=${likesOffset}`;

  fetch(url)
    .then((response) => {
      if (!response.ok)
        throw new Error(`HTTP error! Status: ${response.status}`);
      return response.json();
    })
    .then((data) => {
      const container = document.getElementById("LikesTab"); // Target likes container
      if (!container) {
        throw new Error(
          "Likes container (#LikesTab) not found in DOM after fetch."
        );
      }

      if (data.error) {
        container.innerHTML = `<p>Error loading liked posts: ${data.error}</p>`;
        if (spinner) spinner.classList.add("hidden");
        likesHasMore = false;
        likesLoading = false;
        return;
      }

      if (!data.posts || data.posts.length === 0) {
        if (likesOffset === 0) {
          // No liked posts at all for this user
          container.innerHTML = `<div class="noPostCategory"><p>No liked posts found.</p><img src="../assets/sadCat03.jpg" alt="sad cat 3"></div>`;
        } else {
          // Reached the end of the liked posts list
          // Optionally add a "no more likes" message
          // container.innerHTML += `<div class="noMorePosts"><p>No more liked posts.</p></div>`;
        }
        if (spinner) spinner.classList.add("hidden");
        likesHasMore = false;
        likesLoading = false;
        return;
      }

      // Create post elements (reusing the same card structure)
      const tempContainer = document.createElement("div");
      // Use the existing createPostCard function if it's globally available
      // Otherwise, copy it here or create a specific function for likes
      // Assuming createPostCard is available globally (from _postLoader.js)
      if (typeof createPostCard === "function") {
        tempContainer.innerHTML = data.posts.map(createPostCard).join("");
      } else {
        // Fallback: Basic card creation if createPostCard is not available
        tempContainer.innerHTML = data.posts
          .map(
            (post) =>
              `<div class="postCard"><p>Liked Post: ${
                post.content || "..."
              }</p></div>`
          )
          .join("");
        console.warn(
          "createPostCard function not found, using fallback rendering."
        );
      }

      // Append with fade-in animation (similar to posts)
      Array.from(tempContainer.children).forEach((postElement) => {
        postElement.style.opacity = 0;
        container.appendChild(postElement);
        void postElement.offsetWidth; // Trigger reflow
        postElement.style.transition = "opacity 0.4s ease, transform 0.4s ease";
        postElement.style.opacity = 1;
        postElement.style.transform = "translateY(0)";
      });

      // Observe new images for lazy loading
      const newImages = container.querySelectorAll(".lazy-img");
      newImages.forEach((img) => {
        // Check if imgObserver is defined globally
        if (typeof imgObserver !== "undefined" && !img.src && img.dataset.src) {
          imgObserver.observe(img);
        } else if (!img.src && img.dataset.src) {
          // Fallback if imgObserver is not available
          img.src = img.dataset.src;
        }
      });

      // Load like status for each post (this will update the heart icons correctly)
      data.posts.forEach((post) => {
        // Use the existing loadLikeStatus function if available
        if (typeof loadLikeStatus === "function") {
          loadLikeStatus(post.PUID);
        }
        // If not available, the initial 'liked' status from data.posts should suffice
        // or you'd need to implement a similar function here.
      });

      // Update offset for next page
      likesOffset += data.posts.length;

      if (spinner) spinner.classList.add("hidden");
      likesLoading = false;

      // Check if this was the last page
      if (data.posts.length < likesLimit) {
        likesHasMore = false;
        // Optionally indicate no more likes
        // container.innerHTML += `<div class="noMorePosts"><p>No more liked posts.</p></div>`;
      }
    })
    .catch((err) => {
      console.error("Failed to load liked posts:", err.message);
      const container = document.getElementById("LikesTab");
      if (container) {
        container.innerHTML = `<p style="text-align:center; padding:20px;">Error loading liked posts. Please try again later.</p>`;
      }
      const spinner = document.getElementById("loadingSpinner");
      if (spinner) spinner.classList.add("hidden");
      likesLoading = false;
      likesHasMore = false;
    });
}
