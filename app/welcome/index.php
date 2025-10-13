<?php
require "./pageScript.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>PixlShare - Share Your Visual Pixles</title>
  <link rel="shortcut icon" href="<?= filePath("/assets/logos/"); ?>pixlshareLogo_color_128.png" type="image/x-icon">
  <link rel="stylesheet" href="./css/index.min.css" />
  <script>
    if ("serviceWorker" in navigator) {
      window.addEventListener("load", () => {
        navigator.serviceWorker
          .register("./sw.js")
          .then((registration) => {
            console.log(
              "Service Worker registered with scope:",
              registration.scope
            );
          })
          .catch((error) => {
            console.error("Service Worker registration failed:", error);
          });
      });
    }
  </script>
</head>

<body>
  <header>
    <div class="navbarContainer">
      <nav class="navbar">
        <a href="#" class="logo" aria-label="PXL Logo">
          <img src="../assets/logos/pxl_logo_1350_white.png" alt="PXL" aria-label="PXL" />
        </a>
        <div class="nav-links">
          <a href="../home/">Home</a>
          <a href="../explore/">Explore</a>
        </div>
        <div class="auth-buttons">
          <button onclick="window.location.href='../account/signin/'" class="btn btn-outline">
            Log In
          </button>
          <button onclick="window.location.href='../account/signup/'" class="btn btn-primary">
            Sign Up
          </button>
        </div>
      </nav>
      <div class="auth-buttons-long">
        <button onclick="window.location.href='../account/signin/'" class="btn btn-outline">
          Log In
        </button>
        <button onclick="window.location.href='../account/signup/'" class="btn btn-primary">
          Sign Up
        </button>
      </div>
    </div>
  </header>

  <section class="hero">
    <div class="container">
      <h1>Share Your Visual Pixls</h1>
      <p>
        PixlShare is a vibrant community where programmers, gamers, and tech
        enthusiasts can connect, share ideas, and have fun together!
      </p>
      <p>
        Share your favorite music videos, movies, and memes with fellow
        members who appreciate the same content you enjoy in your free time.
      </p>
      <div class="hero-buttons">
        <button onclick="window.location.href='../account/signup/'" class="btn btn-primary">Get Started Free</button>
        <button onclick="window.location.href='../explore/'" class="btn btn-outline">Explore Gallery</button>
      </div>
    </div>
  </section>

  <section class="features">
    <div class="container">
      <div class="section-title">
        <h2>Powerful Features</h2>
        <p>
          Everything you need to showcase your visual content and build your
          creative portfolio
        </p>
      </div>
      <div class="features-grid">
        <div class="feature-card">
          <div class="feature-icon">
            <ion-icon name="cloud-upload"></ion-icon>
          </div>
          <h3>Easy Upload</h3>
          <p>
            Upload your images with a single click. Our intuitive interface
            makes sharing your work effortless.
          </p>
        </div>
        <div class="feature-card">
          <div class="feature-icon">
            <ion-icon name="people"></ion-icon>
          </div>
          <h3>Community</h3>
          <p>
            Connect with like-minded creators, get feedback, and discover
            inspiring work from others.
          </p>
        </div>
      </div>
    </div>
  </section>

  <!-- Feed Preview -->
  <section class="feed-preview">
    <div class="container">
      <div class="section-title">
        <h2>See What's Trending</h2>
        <p>Discover amazing work from our community of talented creators</p>
      </div>
      <div class="preview-container">
        <div class="preview-header">
          <ion-icon name="flame"></ion-icon>
          <div class="preview-title">
            Trending Now <span>(Still work in progress...)</span>
          </div>
        </div>
        <div class="preview-posts">
          <!-- Fake post #1 -->
          <div class="postCard">
            <div class="topCardContainer">
              <div class="profileLink">
                <a href="#">
                  <img src="../<?= $pfp_image_link; ?>" alt="" />
                  <p>@<?= $profileUsername; ?></p>
                </a>
              </div>
              <div class="buttonTray">
                <button type="button" name="share button" aria-label="share button">
                  <ion-icon name="share-outline"></ion-icon>
                </button>
                <button type="button" name="like button" aria-label="like button" class="likeButton">
                  <ion-icon name="heart-outline"></ion-icon>
                  <span class="likeCount">21</span>
                </button>
              </div>
            </div>
            <div class="middleCardContainer">
              <div class="postImageContainer">
                <a href="#">
                  <img src="<?= $postImage1; ?>" alt="Post image" class="lazy-img" />
                </a>
              </div>
            </div>
          </div>
          <!-- Fake post #2 -->
          <div class="postCard">
            <div class="topCardContainer">
              <div class="profileLink">
                <a href="#">
                  <img src="../<?= $pfp_image_link; ?>" alt="" />
                  <p>@<?= $profileUsername; ?></p>
                </a>
              </div>
              <div class="buttonTray">
                <button type="button" name="share button" aria-label="share button">
                  <ion-icon name="share-outline"></ion-icon>
                </button>
                <button type="button" name="like button" aria-label="like button" class="likeButton">
                  <ion-icon name="heart-outline"></ion-icon>
                  <span class="likeCount">11</span>
                </button>
              </div>
            </div>
            <div class="middleCardContainer">
              <div class="postImageContainer">
                <a href="#">
                  <img src="<?= $postImage2; ?>" alt="Post image" class="lazy-img" />
                </a>
              </div>
            </div>
          </div>
          <!-- Fake post #3 -->
          <div class="postCard">
            <div class="topCardContainer">
              <div class="profileLink">
                <a href="#">
                  <img src="../<?= $pfp_image_link; ?>" alt="" />
                  <p>@<?= $profileUsername; ?></p>
                </a>
              </div>
              <div class="buttonTray">
                <button type="button" name="share button" aria-label="share button">
                  <ion-icon name="share-outline"></ion-icon>
                </button>
                <button type="button" name="like button" aria-label="like button" class="likeButton">
                  <ion-icon name="heart-outline"></ion-icon>
                  <span class="likeCount">15</span>
                </button>
              </div>
            </div>
            <div class="middleCardContainer">
              <div class="postImageContainer">
                <a href="#">
                  <img src="<?= $postImage3; ?>" alt="Post image" class="lazy-img" />
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- CTA Section -->
  <section class="cta">
    <div class="container">
      <h2>Ready to Share Your Vision?</h2>
      <p>
        Share your favorite music videos, movies, and memes with fellow
        members who appreciate the same content you enjoy in your free time.
      </p>
      <button onclick="window.location.href='./account/signup/'" class="btn btn-primary">
        Create Your Account
      </button>
    </div>
  </section>

  <!-- Footer -->
  <footer>
    <div class="container">
      <div class="footer-content">
        <div class="footer-column">
          <a href="#" class="logo" aria-label="PXL Logo">
            <img src="../assets/logos/pxl_logo_1350_white.png" alt="" />
          </a>
          <p>
            The ultimate platform for photographers and visual creators to
            showcase their work and connect with a global community.
          </p>
          <div class="social-links">
            <a href="https://x.com/PixlShare" aria-label="PixlShare x link"><ion-icon
                name="logo-twitter"></ion-icon></a>
          </div>
        </div>
        <div class="footer-column">
          <h3>Explore</h3>
          <ul class="footer-links">
            <li><a href="../explore/">Random Photos</a></li>
          </ul>
        </div>
        <div class="footer-column">
          <h3>Resources</h3>
          <ul class="footer-links">
            <li>
              <a href="https://trello.com/b/RkMeumWE/pixlshare">Trello</a>
            </li>
            <li>
              <a href="../legal/termsandconditions/">Community Guidelines</a>
            </li>
            <li><a href="../profile/support/">Help Center</a></li>
            <li>
              <a href="../profile/support/apidocs/">API Documentation</a>
            </li>
          </ul>
        </div>
        <div class="footer-column">
          <h3>Company</h3>
          <ul class="footer-links">
            <li><a href="mailto:pixlshareofficial@gmail.com">Contact</a></li>
            <li><a href="../legal/privacypolicy/">Privacy Policy</a></li>
            <li>
              <a href="../legal/termsandconditions/">Terms of Service</a>
            </li>
          </ul>
        </div>
      </div>
      <div class="copyright">
        <p>&copy; 2023 PixlShare. All rights reserved.</p>
      </div>
    </div>
  </footer>

  <script>
    // Simple animation for feature cards on scroll
    document.addEventListener("DOMContentLoaded", function () {
      const featureCards = document.querySelectorAll(".feature-card");

      const observer = new IntersectionObserver(
        (entries) => {
          entries.forEach((entry) => {
            if (entry.isIntersecting) {
              entry.target.style.opacity = 1;
              entry.target.style.transform = "translateY(0)";
            }
          });
        },
        { threshold: 0.1 }
      );

      featureCards.forEach((card) => {
        card.style.opacity = 0;
        card.style.transform = "translateY(20px)";
        card.style.transition = "opacity 0.5s ease, transform 0.5s ease";
        observer.observe(card);
      });

      // Button hover effects
      const buttons = document.querySelectorAll(".btn");
      buttons.forEach((button) => {
        button.addEventListener("mouseenter", function () {
          this.style.transform = "translateY(-2px)";
        });

        button.addEventListener("mouseleave", function () {
          this.style.transform = "translateY(0)";
        });
      });
    });
  </script>
  <script type="module" src="https://cdn.jsdelivr.net/npm/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
  <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js" crossorigin></script>
</body>

</html>