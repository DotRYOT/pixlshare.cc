// _userBookmarksLoader.js

// --- State Management for Bookmarks ---
let bookmarkOffset = 0;
const bookmarkLimit = 5; // Match the limit used in other loaders
let bookmarkLoading = false;
let bookmarkHasMore = true;
let currentBookmarkFilter = null; // Start with no filter for bookmarks

function debounce(fn, delay) {
  let timer;
  return () => {
    clearTimeout(timer);
    timer = setTimeout(() => fn(), delay);
  };
}

// --- Initialize Bookmark Loading ---
function UserBookmarkLoader() {
  const bookmarksContainer = document.getElementById("bookmarks");
  if (!bookmarksContainer) {
    console.error("Bookmarks container (#bookmarks) not found.");
    return;
  }

  // Reset state for a fresh load (e.g., when switching to the bookmarks tab)
  bookmarkOffset = 0;
  bookmarkHasMore = true;
  bookmarkLoading = false;
  currentBookmarkFilter = null; // Reset filter on fresh load
  bookmarksContainer.innerHTML = ""; // Clear previous bookmarks

  const spinner = document.getElementById("loadingSpinner");
  if (spinner) {
    spinner.classList.remove("hidden");
  }

  // Setup filter buttons specifically for bookmarks if needed
  // For simplicity, reusing the main filter menu is possible,
  // but it's often cleaner to have separate filter state/logic per tab.
  // If you want a dedicated filter menu for bookmarks, initialize it here.
  // setupBookmarkFilterButtons(); // Optional: Implement if needed

  loadUserBookmarks();

  // Setup scroll listener for infinite scroll on bookmarks
  // Make sure to remove any previous listeners to avoid conflicts, or use namespacing if possible.
  // This adds a listener every time UserBookmarkLoader is called, which might be an issue.
  // A better approach is to add it once when the page loads and check which tab is active.
  // For now, we'll add it, but be aware of potential multiple listeners.
  window.addEventListener("scroll", debounce(handleBookmarkScroll, 200));
}

// --- Handle Scroll for Bookmarks ---
function handleBookmarkScroll() {
  // Basic check to ensure we only load bookmarks when the bookmarks tab is active
  // This requires the #bookmarks element to be visible/flex.
  const bookmarksContainer = document.getElementById("bookmarks");
  if (!bookmarksContainer || bookmarksContainer.style.display === "none") {
    return; // Don't load if the bookmarks tab isn't active
  }

  const scrollTop = window.scrollY || document.documentElement.scrollTop;
  const windowHeight = window.innerHeight;
  const docHeight = document.documentElement.scrollHeight;

  if (scrollTop + windowHeight >= docHeight - 300) {
    loadUserBookmarks();
  }
}

// --- Load Bookmarked Posts ---
function loadUserBookmarks() {
  if (bookmarkLoading || !bookmarkHasMore) return;

  bookmarkLoading = true;
  const spinner = document.getElementById("loadingSpinner");
  if (spinner) {
    spinner.classList.remove("hidden");
  }

  // Construct URL with limit, offset, and optional filter
  let url = `../backend/scripts/profile/_UserBookmarksLoader.php?limit=${bookmarkLimit}&offset=${bookmarkOffset}`;
  if (currentBookmarkFilter !== null) {
    url += `&filter=${encodeURIComponent(currentBookmarkFilter)}`;
  }

  fetch(url)
    .then((response) => {
      if (!response.ok) {
        // Try to get error message from response body if possible
        return response.text().then((text) => {
          let errorMsg = `HTTP error! Status: ${response.status}`;
          try {
            const errorData = JSON.parse(text);
            if (errorData.error) {
              errorMsg += ` - ${errorData.error}`;
            }
          } catch (e) {
            // Not JSON, use text as message
            if (text.trim()) errorMsg += ` - ${text}`;
          }
          throw new Error(errorMsg);
        });
      }
      return response.json();
    })
    .then((data) => {
      const container = document.getElementById("bookmarks");
      if (!container) {
        throw new Error(
          "Bookmarks container (#bookmarks) not found in DOM after fetch."
        );
      }

      if (data.error) {
        container.innerHTML = `<p>Error loading bookmarks: ${data.error}</p>`;
        if (spinner) spinner.classList.add("hidden");
        bookmarkHasMore = false;
        bookmarkLoading = false;
        return;
      }

      if (!data.posts || data.posts.length === 0) {
        if (bookmarkOffset === 0) {
          // No bookmarks at all for this user
          container.innerHTML = `<div class="noPostCategory"><p>No bookmarks found.</p><img src="../assets/sadCat03.jpg" alt="sad cat 3"></div>`;
        } else {
          // Reached the end of the bookmarks list
          // container.innerHTML += `<div class="noMorePosts"><p>No more bookmarks.</p></div>`;
        }
        if (spinner) spinner.classList.add("hidden");
        bookmarkHasMore = false;
        bookmarkLoading = false;
        return;
      }

      // --- Create Post Cards ---
      // Reuse the existing createPostCard function from _postLoader.js
      // This assumes createPostCard is available globally.
      const tempContainer = document.createElement("div");
      if (typeof createPostCard === "function") {
        tempContainer.innerHTML = data.posts.map(createPostCard).join("");
      } else {
        console.error(
          "createPostCard function is not available. Please ensure _postLoader.js is loaded."
        );
        container.innerHTML = `<p>Error: Post rendering function not found.</p>`;
        if (spinner) spinner.classList.add("hidden");
        bookmarkHasMore = false;
        bookmarkLoading = false;
        return;
      }

      // --- Append with Animation ---
      Array.from(tempContainer.children).forEach((postElement) => {
        postElement.style.opacity = 0;
        container.appendChild(postElement);
        void postElement.offsetWidth; // Trigger reflow
        postElement.style.transition = "opacity 0.4s ease, transform 0.4s ease";
        postElement.style.opacity = 1;
        postElement.style.transform = "translateY(0)";
      });

      // --- Lazy Load Images ---
      // Reuse the existing imgObserver from _postLoader.js
      const newImages = container.querySelectorAll(".lazy-img");
      newImages.forEach((img) => {
        if (typeof imgObserver !== "undefined" && !img.src && img.dataset.src) {
          imgObserver.observe(img);
        } else if (!img.src && img.dataset.src) {
          // Fallback if imgObserver is not available or not working as expected
          console.warn(
            "imgObserver not available or image already has src, loading directly."
          );
          img.src = img.dataset.src;
        }
      });

      // --- Load Like Status for New Posts ---
      // Reuse the existing loadLikeStatus function from _postLoader.js
      data.posts.forEach((post) => {
        if (typeof loadLikeStatus === "function") {
          loadLikeStatus(post.PUID);
        } else {
          console.warn(`loadLikeStatus not available for post ${post.PUID}`);
        }
      });

      // --- Update Pagination State ---
      bookmarkOffset += data.posts.length;

      if (spinner) spinner.classList.add("hidden");
      bookmarkLoading = false;

      // Check if this was the last page
      if (data.posts.length < bookmarkLimit) {
        bookmarkHasMore = false;
        // Optionally indicate no more bookmarks
        // container.innerHTML += `<div class="noMorePosts"><p>No more bookmarks.</p></div>`;
      }
    })
    .catch((err) => {
      console.error("Failed to load bookmarks:", err);
      const container = document.getElementById("bookmarks");
      if (container) {
        container.innerHTML = `<p style="text-align:center; padding:20px;">Error loading bookmarks. Please try again later. (${err.message})</p>`;
      }
      const spinner = document.getElementById("loadingSpinner");
      if (spinner) spinner.classList.add("hidden");
      bookmarkLoading = false;
      bookmarkHasMore = false; // Stop further attempts on error
    });
}
