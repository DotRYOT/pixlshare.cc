let offset = 0;
const limit = 5;
let loading = false;
let hasMore = true;
let currentFilter = 0;

// 🔁 Debounce utility function
function debounce(fn, delay) {
  let timer;
  return () => {
    clearTimeout(timer);
    timer = setTimeout(() => fn(), delay);
  };
}

document.addEventListener("DOMContentLoaded", () => {
  const activeFilterLink = document.querySelector("#filterMenu a.active");
  if (activeFilterLink) {
    currentFilter = parseInt(activeFilterLink.dataset.filter) || 0;
  }

  // Create spinner element if not already present
  const spinner = document.getElementById("loadingSpinner");
  spinner.classList.remove("hidden");

  loadPosts();
  setupFilterButtons();

  window.addEventListener("scroll", debounce(handleScroll, 200));
});

function setupFilterButtons() {
  const filterButtons = document.querySelectorAll("#filterMenu a");

  filterButtons.forEach((button) => {
    button.addEventListener("click", (e) => {
      e.preventDefault();

      filterButtons.forEach((btn) => btn.classList.remove("active"));
      button.classList.add("active");

      const newFilter = parseInt(button.dataset.filter);

      if (newFilter === currentFilter) return;

      // Reset state
      currentFilter = newFilter;
      offset = 0;
      hasMore = true;

      const container = document.querySelector(".userPosts");
      container.innerHTML = "";

      const spinner = document.getElementById("loadingSpinner");
      spinner.classList.remove("hidden");

      loadPosts();
    });
  });

  filterButtons.forEach((button) => {
    if (parseInt(button.dataset.filter) === currentFilter) {
      button.classList.add("active");
    }
  });
}

function handleScroll() {
  const scrollTop = window.scrollY || document.documentElement.scrollTop;
  const windowHeight = window.innerHeight;
  const docHeight = document.documentElement.scrollHeight;

  if (scrollTop + windowHeight >= docHeight - 300) {
    loadPosts();
  }
}

// Lazy Load Images
const imgObserver = new IntersectionObserver(
  (entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        const img = entry.target;
        if (img.dataset.src && !img.src) {
          img.src = img.dataset.src;
        }
        imgObserver.unobserve(img);
      }
    });
  },
  {
    rootMargin: "0px 0px 200px 0px",
    threshold: 0.01,
  }
);

function loadPosts() {
  if (loading || !hasMore) return;

  // Always assume there may be more posts on fresh load
  if (offset === 0) hasMore = true;

  loading = true;

  const spinner = document.getElementById("loadingSpinner");
  spinner.classList.remove("hidden");

  const url = `../backend/scripts/profile/_UserPostLoader.php?filter=${currentFilter}&limit=${limit}&offset=${offset}`;

  fetch(url)
    .then((response) => {
      if (!response.ok)
        throw new Error(`HTTP error! Status: ${response.status}`);
      return response.json();
    })
    .then((data) => {
      const container = document.querySelector(".userPosts");

      if (data.error) {
        container.innerHTML = `<p>${data.error}</p>`;
        spinner.classList.add("hidden");
        hasMore = false;
        loading = false;
        return;
      }

      if (!data.posts || data.posts.length === 0) {
        if (offset === 0) {
          container.innerHTML = `<div class="noPostCategory"><p>No posts found in this category.</p><img src="../assets/sadCat03.jpg" alt="sad cat 3"></div>`;
        } else {
          container.innerHTML += `<div class="noMorePosts"><p>No more posts found.</p><img src="../assets/sadCat02.jpg" alt="sad cat 3"></div>`;
        }
        spinner.classList.add("hidden");
        hasMore = false;
        loading = false;
        return;
      }

      const tempContainer = document.createElement("div");
      tempContainer.innerHTML = data.posts.map(createPostCard).join("");

      Array.from(tempContainer.children).forEach((postElement) => {
        postElement.style.opacity = 0;
        container.appendChild(postElement);

        void postElement.offsetWidth;

        postElement.style.transition = "opacity 0.4s ease, transform 0.4s ease";
        postElement.style.opacity = 1;
        postElement.style.transform = "translateY(0)";
      });

      const newImages = container.querySelectorAll(".lazy-img");
      newImages.forEach((img) => {
        if (!img.src && img.dataset.src) {
          imgObserver.observe(img);
        }
      });

      data.posts.forEach((post) => {
        loadLikeStatus(post.PUID);
      });

      offset += data.posts.length;
      spinner.classList.add("hidden");
      loading = false;

      if (data.posts.length < limit) {
        hasMore = false;
      }
    })
    .catch((err) => {
      console.error("Failed to load posts:", err.message);
      const container = document.querySelector(".userPosts");
      container.innerHTML = `<p style="text-align:center; padding:20px;">Error loading posts. Please try again later.</p>`;
      spinner.classList.add("hidden");
      loading = false;
      hasMore = false;
    });
}

function createPostCard(post) {
  const isUserLiked = Boolean(post.liked);
  const likeCount = post.likeCount || 0;
  const likedStyle = isUserLiked ? "inline" : "none";
  const unlikedStyle = isUserLiked ? "none" : "inline";

  // Determine the correct UUID and PUID for navigation
  let targetUUID = post.UUID; // Default to the current post's UUID
  let targetPUID = post.PUID; // Default to the current post's PUID
  let commentAnchor = ""; // Anchor for scrolling to the comment

  if (post.CatFilterOption == "999" && post.OG_Posters_UUID && post.OG_PUID) {
    // If it's a comment, use the OG_Posters_UUID and OG_PUID for navigation
    targetUUID = post.OG_Posters_UUID;
    targetPUID = post.OG_PUID;
    commentAnchor = `#comment-${post.PUID}`; // Add the comment anchor
  }

  return `
  <div id="${post.PUID}" class="postCard" style="transform: translateY(20px);">
    <div class="topCardContainer">
      <div class="profileLink">
        <a href="../profile/u/${post.UUID}/">
          <img src="../${post.pfp_image_link}" alt="" />
          <p>@${post.username}</p>
        </a>
      </div>
      <div class="buttonTray">
        <button onclick="sharePost('${targetUUID}', '${targetPUID}')">
          <ion-icon name="share-outline"></ion-icon>
        </button>
        <button onclick="togglePostLike('${
          post.PUID
        }', this)" class="likeButton">
          <ion-icon name="heart-outline" id="like-icon-outline-${
            post.PUID
          }" style="display: ${unlikedStyle};"></ion-icon>
          <ion-icon name="heart" class="liked" id="like-icon-full-${
            post.PUID
          }" style="display: ${likedStyle}; color: red;"></ion-icon>
          <span id="like-count-${
            post.PUID
          }" class="likeCount">${likeCount}</span>
        </button>
      </div>
    </div>
    <div class="bottomCardContainer">
      <div class="textContainer">
        <a href="../profile/u/${targetUUID}/post/${targetPUID}/${commentAnchor}">
        ${
          post.postState == 0
            ? `<p>${post.content}</p>`
            : `<p class="warnText">Post has been suspended!</p>`
        }
        </a>
      </div>
    </div>
    ${
      post.image_id
        ? `
    <div class="middleCardContainer">
      <div class="postImageContainer">
        <a href="../profile/u/${targetUUID}/post/${targetPUID}/${commentAnchor}">
          ${
            post.postState == 0
              ? `<img data-src="../${post.image_id}" alt="Post image" class="lazy-img">`
              : `<img data-src="../assets/sadCat03.jpg" alt="Post image" class="lazy-img">`
          }
        </a>
      </div>
    </div>`
        : ""
    }
  </div>
  `;
}

function togglePostLike(puid, button) {
  const iconOutline = button.querySelector(`#like-icon-outline-${puid}`);
  const iconFull = button.querySelector(`#like-icon-full-${puid}`);
  const countSpan = button.querySelector(`#like-count-${puid}`);

  if (!iconOutline || !iconFull || !countSpan) return;

  const isCurrentlyLiked = iconFull.style.display === "inline";
  const newAction = isCurrentlyLiked ? "unlike" : "like";

  fetch(`../backend/api/_likePost.php?PUID=${puid}&action=${newAction}`)
    .then((response) => {
      if (!response.ok) throw new Error("Network error");
      return response.json();
    })
    .then((data) => {
      if (data.error) {
        alert(data.error);
        return;
      }

      iconOutline.style.display = data.status === "liked" ? "none" : "inline";
      iconFull.style.display = data.status === "liked" ? "inline" : "none";
      iconFull.style.color = data.status === "liked" ? "red" : "inherit";
      countSpan.textContent = Math.max(data.count, 0);
    })
    .catch((err) => {
      console.error("Like error:", err.message);
      alert("Failed to update like status");
    });
}

function sharePost(uuid, puid) {
  const url = `${window.location.origin}/profile/u/${uuid}/post/${puid}/`;
  if (navigator.clipboard) {
    navigator.clipboard.writeText(url);
    alert("Post link copied!");
  } else {
    alert("Sharing not supported on this browser.");
  }
}

function loadLikeStatus(puid) {
  fetch(`../backend/api/_checkLikeStatus.php?PUID=${puid}`)
    .then((response) => {
      if (!response.ok) throw new Error("Network response was not ok");
      return response.json();
    })
    .then((data) => {
      if (data.error) {
        console.error("Error:", data.error);
        return;
      }

      const likeIconFull = document.getElementById(`like-icon-full-${puid}`);
      const likeIconOutline = document.getElementById(
        `like-icon-outline-${puid}`
      );
      const likeCount = document.getElementById(`like-count-${puid}`);

      if (likeIconFull && likeIconOutline && likeCount) {
        likeIconFull.style.display = data.liked ? "inline" : "none";
        likeIconOutline.style.display = data.liked ? "none" : "inline";
        likeCount.textContent = Math.max(data.count, 0);
      }
    })
    .catch((error) => {
      console.error("Fetch error:", error);
    });
}
