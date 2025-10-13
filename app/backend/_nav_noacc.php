<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="module" src="https://cdn.jsdelivr.net/npm/ldrs/dist/auto/lineWobble.js"></script>
<!-- %@@@@@@@@@@@@@@@@%@@@@%@%@@@@@@@@@@@@@@@@@@@@%@@@@@@ -->
<!-- @@@@@@@@@@@%@%%@@@@@@@@@@@@@@@@@@@%@%@@@@@@@@@@@@@@@ -->
<!-- @@@@@@@@#::::::::::::-+#%%@%%::::::::::::::::::+%@@% -->
<!-- @@@@@@@%-               .*%%+                 .*%%@@ -->
<!-- @@@@@@@%.                 %%:                 :%@@@@ -->
<!-- @@@@@@@*                  ##                  =%@@@@ -->
<!-- @@@@@#:      *%%%=      -#%%%%%*.     .*%%%%%%%@@@@@ -->
<!-- @@@@%=       ===-       #%@@@@%=      =%@@@@@%@@@@@@ -->
<!-- @@@@%-                 +%@@%@%#:      *%@@%@@@@@@%@@ -->
<!-- @@@%*.               :#%@@@@@%*      :#@@@@@@@@@%@@@ -->
<!-- @@@%*-              :+%%%@%@@%#=      -%%@@@@@@@@@@@ -->
<!-- @@@@%=      .%#:      *%@%@@%@%=      =%@@@@@@@@@@@@ -->
<!-- @%%@%-      =@%=      =%@@@@@@#:      *%@@%@@@@@@@@@ -->
<!-- @@@@*.      #@%*      .#%@@@@%*      :#%@@@@@@@@@@%@ -->
<!-- @@@@#*******%%%%#******%%@@@@%#******#%@@@@@@@@@@@@@ -->
<!-- @@@@%@@@@@@@%@@@@%@@@@@@%@@@@@@@@@@@@@@@@@@@@%%@@@@@ -->
<!-- @@@@@%@@@@@@@@@%@@@@@@%@@@%@@@@%@@@@@@@@@@%@@@@@@@@@ -->
<nav id="topNav">
  <div class="topNavLeft">
    <p class="logoFont">PXL</p>
  </div>
  <div class="topNavRight">
  </div>
</nav>
<nav id="bottomNav">
  <div class="navButton">
    <button type="button" name="explore" onclick="window.location.href='<?= filePath("/account/signup/"); ?>'">
      <ion-icon size="large" name="log-in-outline"></ion-icon>
    </button>
  </div>
  <!-- <div class="postButton">
    <button type="button" name="home" onclick="window.location.href='<?= filePath("/explore/"); ?>'">
      <ion-icon size="large" name="grid-outline"></ion-icon>
    </button>
  </div> -->
  <div class="navButton">
    <button type="button" name="search" onclick="window.location.href='<?= filePath("/account/signin/"); ?>'">
      <ion-icon size="large" name="create-outline"></ion-icon>
    </button>
  </div>
</nav>