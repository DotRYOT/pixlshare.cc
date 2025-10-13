<?php require "./pageScript.php"; ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Account Stats - PIXLSHARE</title>
  <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
  <link rel="shortcut icon" href="<?= filePath("/assets/logos/"); ?>pixlshareLogo_color_128.png" type="image/x-icon">
  <link rel="stylesheet" href="./css/index.min.css">
  <link rel="manifest" href="<?= filePath("/"); ?>manifest.json" />
</head>

<body>
  <?php require "../../../backend/_nav.php"; ?>
  <div id="followerCount"></div>

  <script>
    function LoadFollowers() {
      fetch("../../../backend/api/_accountStats.php?uuid=<?= $GET_UUID; ?>")
        .then(response => {
          return response.text() // Get response as text
            .then(text => {
              if (!response.ok) {
                throw new Error('Network response was not ok: ' + response.statusText);
              }
              if (text.trim() === "") {
                throw new Error('Received empty response from the server.');
              }
              try {
                return JSON.parse(text); // Parse JSON response
              } catch (e) {
                throw new Error('Failed to parse JSON: ' + e.message);
              }
            });
        })
        .then(data => {
          // Assuming data is an array of objects with 'uuid', 'name', and 'profilePicture' properties
          const followerCards = data.map(user => `
          <a href="../../../profile/u/${user.uuid}">
            <div class="userCard">
              <img src="../../../${user.profilePicture}" alt="${user.name}'s profile picture" />
              <h3>${user.name}</h3>
            </div>
          </a>
          `).join(''); // Create user cards
          document.getElementById('followerCount').innerHTML = followerCards; // Display user cards
        })
        .catch(error => console.error('Error loading followers:', error));
    }
    LoadFollowers();
  </script>
  <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>

</html>