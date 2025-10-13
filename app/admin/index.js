document.addEventListener("DOMContentLoaded", () => {
  const tabs = document.querySelectorAll(".tab");
  const pages = document.querySelectorAll(".page");

  // Function to handle tab clicks
  function handleTabClick(event) {
    const selectedTab = event.target;

    // Remove active state from all tabs
    tabs.forEach((tab) => {
      tab.setAttribute("aria-selected", "false");
      tab.style.backgroundColor = "";
      tab.style.borderColor = "";
    });

    // Hide all pages
    pages.forEach((page) => {
      page.style.display = "none";
    });

    // Activate the selected tab
    selectedTab.setAttribute("aria-selected", "true");

    // Show the corresponding page
    const pageId = selectedTab.getAttribute("aria-controls");
    const selectedPage = document.getElementById(pageId);
    if (selectedPage) {
      selectedPage.style.display = "block";
    }
  }

  // Attach click event listeners to all tabs
  tabs.forEach((tab) => {
    tab.addEventListener("click", handleTabClick);
  });

  // Initialize the first tab as active
  tabs[0].click();
});

