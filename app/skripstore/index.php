<?php require "./pageScript.php"; ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Skrip Store - PIXLSHARE</title>
  <link rel="shortcut icon" href="<?= filePath("/assets/logos/"); ?>pixlshareLogo_color_128.png" type="image/x-icon">
  <link rel="stylesheet" href="./css/index.min.css">
  <link href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css " rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/toastify-js "></script>
</head>

<body>
  <?php
  require "../backend/_nav.php";
  ?>
  <div class="store-container">
    <div class="store-header">
      <h1>Skrip Store</h1>
      <p>Boost your experience with Skrip currency</p>
    </div>

    <div class="category-filters">
      <button class="category-btn" data-category="all">All Items</button>
      <button class="category-btn" data-category="boosts">Boosts</button>
      <button class="category-btn" data-category="likes">Super Likes</button>
      <button class="category-btn" data-category="features">Features</button>
      <button class="category-btn" data-category="customization">Customization</button>
    </div>

    <div class="products-grid">
      <!-- Example Product Cards -->

      <div class="product-card" data-category="customization">
        <div class="product-image">
          <img src="../assets/moneyBG.png" alt="Premium Icon">
        </div>
        <div class="product-info">
          <div class="product-name">Profile Gif Access</div>
          <div class="product-description">Get access to use gifs on your profile.</div>
          <div class="product-price">
            <span class="price-tag">$10.00</span>
            <a class="buy-button" href="">Buy</a>
            <!-- <a target="_blank" class="buy-button" href="">Buy</a> -->
          </div>
        </div>
      </div>

      <div class="product-card" data-category="boosts">
        <div class="product-image">
          <img src="../assets/moneyBG.png" alt="Boost Icon">
        </div>
        <div class="product-info">
          <div class="product-name">Post Boost Voucher</div>
          <div class="product-description">Get more visibility for your posts
            with this boost feature.</div>
          <div class="product-price">
            <span class="price-tag">$5.00</span>
            <a target="_blank" class="buy-button-nonactive">Buy</a>
          </div>
        </div>
      </div>

      <div class="product-card" data-category="likes">
        <div class="product-image">
          <img src="../assets/moneyBG.png" alt="Heart Icon">
        </div>
        <div class="product-info">
          <div class="product-name">Super Like Voucher</div>
          <div class="product-description">Show extra appreciation with a
            Super Like.</div>
          <div class="product-price">
            <span class="price-tag">$1.00</span>
            <a target="_blank" class="buy-button-nonactive">Buy</a>
          </div>
        </div>
      </div>

      <div class="product-card" data-category="features">
        <div class="product-image">
          <img src="../assets/moneyBG.png" alt="Premium Icon">
        </div>
        <div class="product-info">
          <div class="product-name">Profile Highlight Voucher</div>
          <div class="product-description">Make your profile stand out with
            special highlighting.</div>
          <div class="product-price">
            <span class="price-tag">$25.00</span>
            <a target="_blank" class="buy-button-nonactive">Buy</a>
          </div>
        </div>
      </div>
    </div>

    <!-- <div class="cart-section">
      <h2>Your Cart</h2>
      <div id="cart-items">
      </div>
      <div class="cart-total" id="cart-total">Total: 0 Skrip</div>
    </div> -->
  </div>

  <script>
    // Existing category filter logic (unchanged)
    document.querySelectorAll('.category-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        const category = btn.getAttribute('data-category');
        document.querySelectorAll('.product-card').forEach(card => {
          if (category === 'all' || card.getAttribute('data-category') === category) {
            card.style.display = 'flex';
          } else {
            card.style.display = 'none';
          }
        });
      });
    });

    // Cart logic with Toastify
    const cartItems = [];
    let totalSkrip = 0;

    document.querySelectorAll('.buy-button').forEach(button => {
      button.addEventListener('click', () => {
        const productCard = button.closest('.product-card');
        const name = productCard.querySelector('.product-name').textContent;
        const price = parseInt(productCard.querySelector('.price-tag').textContent);

        cartItems.push({ name, price });
        totalSkrip += price;
        updateCart();

        Toastify({
          text: `✅ Added "${name}" (${price} Skrip) to cart`,
          duration: 3000,
          close: true,
          gravity: "bottom",
          position: "right",
          backgroundColor: "#4caf50",
          stopOnFocus: true,
        }).showToast();
      });
    });

    function updateCart() {
      const cartItemsContainer = document.getElementById('cart-items');
      cartItemsContainer.innerHTML = '';

      cartItems.forEach(item => {
        const div = document.createElement('div');
        div.className = 'cart-item';
        div.innerHTML = `
                    <span>${item.name}</span>
                    <span>${item.price} Skrip</span>
                `;
        cartItemsContainer.appendChild(div);
      });

      document.getElementById('cart-total').textContent = `Total: ${totalSkrip} Skrip`;
    }
  </script> <!-- Ionicons -->
  <script type="module" src="https://cdn.jsdelivr.net/npm/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
  <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js" crossorigin></script>
</body>

</html>