<footer>
  <div class="footerContent">
    <div class="footerBranding">
      <h2 class="logoFont">PixlShare</h2>
      <p>&copy; <?= date("Y"); ?> PixlShare. All rights reserved.</p>
    </div>
    <div class="line"></div>
    <div class="footerLinks">
      <a href="<?= filePath("/"); ?>sitemap.xml">Sitemap</a>
      <a href="<?= filePath("/legal/privacypolicy/"); ?>">Privacy Policy</a>
      <a href="<?= filePath("/legal/termsandconditions/"); ?>">Terms of Service</a>
      <a href="<?= filePath("/legal/cookies/"); ?>">Cookies</a>
    </div>
    <div class="line"></div>
    <div class="footerSocial">
      <h3>Follow Us</h3>
      <div class="socialLinks">
        <a
          href="<?= filePath("/out/"); ?>?link=<?= urlencode("https://x.com/PixlShare"); ?>&return=<?= filePath(""); ?>">
          <img src="<?= filePath("/assets/logos/3rdpartylogos/") ?>logo-white.png" alt="">
        </a>
        <a
          href="<?= filePath("/out/"); ?>?link=<?= urlencode("https://trello.com/b/RkMeumWE/pixlshare"); ?>&return=<?= filePath(""); ?>">
          <img src="<?= filePath("/assets/logos/3rdpartylogos/") ?>trello-icon-gradient-blue.png" alt="">
        </a>
      </div>
    </div>
    <div class="line"></div>
    <div class="footerContact">
      <h3>Contact Us</h3>
      <p>Email: <a href="mailto:pixlshareofficial@gmail.com">pixlshareofficial@gmail.com</a></p>
      <p>DMCA: <a href="mailto:pixlshareofficial@gmail.com">pixlshareofficial@gmail.com</a></p>
    </div>
  </div>
</footer>