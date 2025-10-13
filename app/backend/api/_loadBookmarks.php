<?php
session_start();

require "../_auth.php";
require "../_include.php";
require "../connect/MainConnect.php";

header('Content-Type: application/json');

if (!isset($_GET['UUID'])) {
  echo "Error: no UUID set!";
  exit;
}

$UUID = mysqli_real_escape_string($conn_main, $_GET['UUID']);

$sql = "SELECT bookmark FROM users WHERE UUID = ?";
$stmt = mysqli_prepare($conn_main, $sql);
mysqli_stmt_bind_param($stmt, "s", $UUID);
mysqli_stmt_execute($stmt);

$likedPosts = array();
mysqli_stmt_bind_result($stmt, $bookmarksJson);
if (mysqli_stmt_fetch($stmt)) {
  $likedPosts = json_decode($bookmarksJson, true); // Decode JSON into an array
}

mysqli_stmt_close($stmt);

$postData = array();
$LikedPosts_sql = "
  SELECT posts.PUID, posts.UUID, posts.content, posts.image_id, users.username, users.pfp_image_link 
  FROM posts 
  JOIN users ON posts.UUID = users.UUID 
  WHERE posts.PUID = ?
";
$LikedPosts_stmt = mysqli_prepare($conn_main, $LikedPosts_sql);

foreach ($likedPosts as $PUID) {
  mysqli_stmt_bind_param($LikedPosts_stmt, "s", $PUID);
  mysqli_stmt_execute($LikedPosts_stmt);
  mysqli_stmt_bind_result($LikedPosts_stmt, $POST_PUID, $POST_UUID, $POST_Content, $POST_image_id, $POST_username, $POST_pfp_image_link);
  while (mysqli_stmt_fetch($LikedPosts_stmt)) {
    if (empty($POST_pfp_image_link)) {
      $POST_pfp_image_link = filePath("/assets/logos/") . " pixlshareLogo_white_128.png";
    }
    $postData[] = [
      'PUID' => $POST_PUID,
      'UUID' => $POST_UUID,
      'content' => $POST_Content,
      'image_id' => $POST_image_id,
      'username' => $POST_username,
      'pfp_image_link' => $POST_pfp_image_link
    ];
  }
}
mysqli_stmt_close($LikedPosts_stmt);

foreach ($postData as $key => $post) {
  ?>
  <div class="postCard">
    <div class="topCardContainer">
      <div class="profileLink">
        <a href="<?= filePath("/profile/u/") . $post['UUID']; ?>/">
          <img src="<?= $post['pfp_image_link']; ?>" alt="" />
          <p>@<?= $post['username']; ?></p>
        </a>
      </div>
      <div class="buttonTray">
        <button>
          <ion-icon name="share-outline"></ion-icon>
        </button>
        <button>
          <ion-icon name="heart-outline"></ion-icon>
        </button>
      </div>
    </div>
    <div class="middleCardContainer">
      <div class="postImageContainer">
        <a href="../profile/u/<?= $post['UUID']; ?>/post/<?= $post['PUID']; ?>">
          <?php
          /* This block of code is responsible for displaying the appropriate HTML element based on the
          type of content in the `['image_id']` variable. Here's a breakdown of what each
          condition does: */
          if (substr($post['image_id'], -3) == 'mp4') {
            echo '<img src="' . filePath("/profile/u/") . $post['UUID'] . '/post/' . $post['PUID'] . '/frame_' . $post['PUID'] . '.jpg" alt="">';
          } else if (preg_match('/^[a-zA-Z0-9_-]{11}$/', $post['image_id'])) {
            echo '<img src="../assets/logos/3rdpartylogos/yt_logo_rgb_dark.png" alt="">';
          } else {
            echo '<img src="..' . $post['image_id'] . '" alt="">';
          }
          ?>
        </a>
      </div>
    </div>
    <div class="bottomCardContainer">
      <div class="textContainer">
        <p><?= $post['content']; ?></p>
      </div>
    </div>
  </div>
  <?php
}
