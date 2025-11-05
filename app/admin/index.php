<?php include "./pageScript.php"; ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin - PIXLSHARE</title>
  <link rel="shortcut icon" href="<?= filePath("/assets/logos/"); ?>pixlshareLogo_color_128.png" type="image/x-icon">
  <link rel="stylesheet" href="./css/index.min.css">
  <script async src="./index.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css " rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/toastify-js "></script>
  <script>
    function createPostCard(post) {
      return `
        <div class="postCard">
          <div class="topCardContainer">
            <div class="profileLink" id="check">
              <a href="<?= filePath("/profile/u/"); ?>${post.UUID}/">
                <img src="<?= filePath("/"); ?>${post.pfp_image_link}" alt="" />
                <p>@${post.username}</p>
              </a>
            </div>
            <div class="buttonTray">
              <button></button>
              <button></button>
            </div>
          </div>
          <div class="bottomCardContainer">
            <div class="textContainer">
              <a href="<?= filePath("/"); ?>profile/u/${post.UUID}/post/${post.PUID}/">
                <p>${post.content}</p>
              </a>
            </div>
          </div>
          ${post.image_id ? `
          <div class="middleCardContainer">
            <div class="postImageContainer">
              <a href="<?= filePath("/"); ?>profile/u/${post.UUID}/post/${post.PUID}/">
                <img src="<?= filePath("/"); ?>${post.image_id}" />
              </a>
            </div>
          </div>` : ""}
        </div>`;
    }

    function PostLoader() {
      fetch("../backend/scripts/admin/_recentPostsLoader.php")
        .then((response) => {
          if (!response.ok)
            throw new Error(`HTTP error! status: ${response.status}`);
          return response.json();
        })
        .then((data) => {
          if (data.error) {
            console.error(data.error);
            return;
          }
          const container = document.getElementById('page1');
          if (data.posts.length === 0) {
            container.innerHTML = '<div class="noPosts">No posts found from users you follow</div>';
            return;
          }
          container.innerHTML = data.posts.map((post) => createPostCard(post)).join("");
        })
        .catch((error) => {
          console.error("Error loading posts:", error);
          const container = document.getElementById('page1');
          container.innerHTML = '<div class="noPosts">Error loading posts. Please try again later.</div>';
        });
    }

    function createLoginLogs(data) {
      return `
        <div class="card">
          <div class="topContainer">
            <h3>${data.UUName}</h3>
            <p><span>IP Address: </span><code>${data.ip_address}</code></p>
          </div>
          <div class="bottomContainer">
            <p><span>User Agent: </span><code>${data.user_agent}</code></p>
            <p><span>Attempt Time: </span><code>${data.attempt_time}</code></p>
          </div>
        </div>`;
    }

    function recentLogins() {
      fetch("../backend/scripts/admin/_recentLogins.php")
        .then((response) => {
          if (!response.ok)
            throw new Error(`HTTP error! status: ${response.status}`);
          return response.json();
        })
        .then((data) => {
          if (data.error) {
            console.error(data.error);
            return;
          }
          const container = document.getElementById('page2');
          if (data.data.length === 0) {
            container.innerHTML = '<div class="noPosts">No login logs found.</div>';
            return;
          }
          container.innerHTML = data.data.map((post) => createLoginLogs(post)).join("");
        })
        .catch((error) => {
          console.error("Error loading posts:", error);
          const container = document.getElementById('page2');
          container.innerHTML = '<div class="noPosts">Error loading login logs.</div>';
        });
    }

    // Render Report Card
    function renderAdminReportCard(data) {
      const userStateOptions = [
        { value: "0", label: "Valid", selected: data.userState == 0 },
        { value: "500", label: "Suspend", selected: data.userState == 500 },
        { value: "600", label: "Warn 1", selected: data.userState == 600 },
        { value: "610", label: "Warn 2", selected: data.userState == 610 },
        { value: "620", label: "Warn 3", selected: data.userState == 620 },
        { value: "700", label: "Ban", selected: data.userState == 700 }
      ];
      const postStateOptions = [
        { value: "0", label: "Valid", selected: data.postState == 0 },
        { value: "500", label: "Suspend", selected: data.postState == 500 }
      ];
      const reasonOptions = [
        { value: "1", label: "Spam", selected: data.reason == 1 },
        { value: "2", label: "Abuse", selected: data.reason == 2 },
        { value: "3", label: "Fraud", selected: data.reason == 3 },
        { value: "4", label: "Hate", selected: data.reason == 4 },
        { value: "5", label: "Nudity", selected: data.reason == 5 },
        { value: "6", label: "Violence", selected: data.reason == 6 },
        { value: "7", label: "Harassment", selected: data.reason == 7 },
        { value: "8", label: "Impersonation", selected: data.reason == 8 },
        { value: "9", label: "Scam", selected: data.reason == 9 },
        { value: "10", label: "Bullying", selected: data.reason == 10 },
      ]

      // Find the selected reason label
      const selectedReason = reasonOptions.find(opt => opt.selected)?.label || 'Unknown';

      const userStateSelect = userStateOptions.map(opt => `<option value="${opt.value}" ${opt.selected ? 'selected' : ''}>${opt.label}</option>`).join('');
      const postStateSelect = postStateOptions.map(opt => `<option value="${opt.value}" ${opt.selected ? 'selected' : ''}>${opt.label}</option>`).join('');

      return `
        <div class="admin-report-card">
          <div class="left-report-card-content">
            <h3>Post ID: <a href="<?= filePath("/profile/u/"); ?>${data.defendantUUID}/post/${data.PUID}/">${data.PUID}</a></h3>
            <p><strong>Reporter:</strong> <a href="/profile/u/${data.reporterUUID}">${data.reporterName}</a></p>
            <p><strong>Reason:</strong> ${selectedReason}</p>
            <p class="reporterMessage"><strong>Reporter message:</strong> ${data.extra_info}</p>
            <p><strong>Defendant:</strong> <a href="/profile/u/${data.defendantUUID}">${data.defendantName}</a></p>
          </div>
          <form class="report-action-form" data-puid="${data.PUID}">
            <input type="hidden" name="PUID" value="${data.PUID}" />
            <input type="hidden" name="DEF_UUID" value="${data.defendantUUID}" />
            <div class="form-group">
              <label>User Punishment</label>
              <select name="options">${userStateSelect}</select>
            </div>
            <div class="form-group">
              <label>Post Punishment</label>
              <select name="postoptions">${postStateSelect}</select>
            </div>
            <div class="form-actions">
              <button type="submit" name="submit" value="Finish">Apply Status</button>
              <button type="button" onclick='dismissReport("${data.PUID}")'>Close Ticket</button>
            </div>
          </form>
        </div>`;
    }

    function fetchAndRenderReports() {
      const container = document.getElementById("page3");
      if (!container) return;
      container.innerHTML = `<div class="loading">Loading reports...</div>`;
      fetch("../backend/scripts/admin/_loadAdminRequest.php")
        .then(response => {
          if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
          return response.json();
        })
        .then(json => {
          if (json.total === 0) {
            container.innerHTML = `<div class="no-data">No reports found.</div>`;
            return;
          }
          container.innerHTML = json.reports.map(report => renderAdminReportCard(report)).join("");
          setupFormHandlers(); // Attach event listeners to forms
        })
        .catch(error => {
          console.error("Error loading reports:", error);
          container.innerHTML = `<div class="error">Failed to load reports.</div>`;
        });
    }

    function setupFormHandlers() {
      document.querySelectorAll(".report-action-form").forEach(form => {
        form.addEventListener("submit", function (e) {
          e.preventDefault();
          const formData = new FormData(this);
          formData.set("submit", "Finish");
          const actionUrl = "../backend/scripts/admin/_postAdminAction.php";
          fetch(actionUrl, {
            method: "POST",
            body: formData
          })
            .then(response => {
              if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
              return response.json();
            })
            .then(json => {
              if (json.success) {
                Toastify({
                  text: json.message || 'Status updated successfully.',
                  duration: 3000,
                  gravity: "top",
                  position: "right",
                  style: {
                    background: "#4CAF50"
                  }
                }).showToast();
                fetchAndRenderReports(); // Refresh list
              } else {
                Toastify({
                  text: json.error || 'Something went wrong.',
                  duration: 4000,
                  gravity: "top",
                  position: "right",
                  style: {
                    background: "#f44336"
                  }
                }).showToast();
              }
            })
            .catch(error => {
              console.error("Request failed:", error);
              Toastify({
                text: 'An error occurred while processing your request.',
                duration: 5000,
                gravity: "top",
                position: "right",
                style: {
                  background: "#f44336"
                }
              }).showToast();
            });
        });
      });
    }

    function dismissReport(puid) {
      const actionUrl = "../backend/scripts/admin/_postAdminAction.php";
      const form = document.querySelector(`.report-action-form[data-puid='${puid}']`);
      if (!form) {
        Toastify({
          text: 'Report form not found!',
          duration: 4000,
          gravity: "top",
          position: "right",
          style: {
            background: "#f44336"
          }
        }).showToast();
        return;
      }
      const defUuid = form.querySelector("[name='DEF_UUID']").value;
      const puidValue = form.querySelector("[name='PUID']").value;
      if (!defUuid || !puidValue) {
        Toastify({
          text: 'Missing required fields!',
          duration: 4000,
          gravity: "top",
          position: "right",
          style: {
            background: "#f44336"
          }
        }).showToast();
        return;
      }
      const formData = new FormData();
      formData.append("PUID", puidValue);
      formData.append("DEF_UUID", defUuid);
      formData.append("options", "0");
      formData.append("postoptions", "0");
      formData.append("submit", "CloseTicket");

      fetch(actionUrl, {
        method: "POST",
        body: formData
      })
        .then(response => response.json())
        .then(json => {
          if (json.success) {
            Toastify({
              text: json.message || 'Report closed successfully.',
              duration: 3000,
              gravity: "top",
              position: "right",
              style: {
                background: "#4CAF50"
              }
            }).showToast();
            fetchAndRenderReports();
          } else {
            Toastify({
              text: json.error || 'Something went wrong.',
              duration: 4000,
              gravity: "top",
              position: "right",
              style: {
                background: "#f44336"
              }
            }).showToast();
          }
        })
        .catch(error => {
          console.error("Request failed:", error);
          Toastify({
            text: 'Failed to close report.',
            duration: 5000,
            gravity: "top",
            position: "right",
            style: {
              background: "#f44336"
            }
          }).showToast();
        });
    }
  </script>
</head>

<body>
  <?php require "../backend/_nav.php"; ?>
  <div class="pageTabber">
    <div class="tabs scrollHidden">
      <button class="tab" id="tab1" aria-controls="page1" aria-selected="true" onclick="PostLoader()">
        <svg xmlns="http://www.w3.org/2000/svg" height="40px" viewBox="0 -960 960 960" width="40px" fill="#FFFFFF">
          <path
            d="M480-160q-133.33 0-226.67-93.33Q160-346.67 160-480q0-133.33 93.33-226.67Q346.67-800 480-800q79.67 0 143.33 32.5 63.67 32.5 110 90.17V-800H800v262.67H537.33V-604h168q-36-58.67-93.83-94T480-733.33q-106 0-179.67 73.66Q226.67-586 226.67-480q0 106 73.66 179.67Q374-226.67 480-226.67q81 0 147.67-46.33 66.66-46.33 93-122.33H790Q761.33-290 675.33-225q-86 65-195.33 65Z" />
        </svg>
        Recent Posts
      </button>
      <button class="tab" id="tab2" aria-controls="page2" aria-selected="false" onclick="recentLogins()">
        <svg xmlns="http://www.w3.org/2000/svg" height="40px" viewBox="0 -960 960 960" width="40px" fill="#FFFFFF">
          <path
            d="M120-120v-720h720v720H120Zm66.67-66.67h586.66v-586.66H186.67v586.66Zm96-91.33h66.66v-203.33h-66.66V-278Zm328 0h66.66v-413.33h-66.66V-278Zm-164 0h66.66v-118.67h-66.66V-278Zm0-203.33h66.66V-548h-66.66v66.67Zm-260 294.66v-586.66 586.66Z" />
        </svg>
        Recent Logins
      </button>
      <button class="tab" id="tab3" aria-controls="page3" aria-selected="false" onclick="fetchAndRenderReports()">
        <svg xmlns="http://www.w3.org/2000/svg" height="40px" viewBox="0 -960 960 960" width="40px" fill="#FFFFFF">
          <path
            d="M687.26-273.33q25.58 0 43.5-18.5 17.91-18.5 17.91-44.09 0-25.59-17.91-43.5-17.92-17.91-43.5-17.91-25.59 0-44.09 17.91-18.5 17.91-18.5 43.5t18.5 44.09q18.5 18.5 44.09 18.5ZM686.33-150q32.67 0 59.34-14.17 26.66-14.16 44.66-39.5-24.66-13.66-50.3-20.66-25.65-7-53.34-7-27.69 0-53.69 7-26 7-50 20.66 18 25.34 44.33 39.5 26.34 14.17 59 14.17ZM480-80q-138.33-33-229.17-157.5Q160-362 160-520v-240.67l320-120 320 120V-505q-15.67-7.33-33-13.17-17.33-5.83-33.67-8.16V-714L480-808l-253.33 94v194q0 66.33 20.5 124.67Q267.67-337 300.5-290.5t74.17 79.83q41.33 33.34 83 50.67 7.66 18.67 21.66 38.33 14 19.67 27 32.67-6.33 3.33-13.16 5.17Q486.33-82 480-80Zm208.33 0q-79.33 0-135.5-56.5-56.16-56.5-56.16-134.83 0-79.96 56.16-136.31Q608.99-464 688.67-464q79 0 135.5 56.36 56.5 56.35 56.5 136.31 0 78.33-56.5 134.83Q767.67-80 688.33-80ZM480-484Z" />
        </svg>
        Reports
      </button>
      <button class="tab" id="tab4" aria-controls="page4" aria-selected="false">
        <svg xmlns="http://www.w3.org/2000/svg" height="40px" viewBox="0 -960 960 960" width="40px" fill="#FFFFFF">
          <path
            d="M242.67-323.33h270.66v-108H242.67v108Zm366.66 0h108V-636h-108v312.67ZM242.67-528h270.66v-108H242.67v108Zm-96 301.33h666.66v-506.66H146.67v506.66ZM80-160v-640h800v640H80Zm66.67-66.67v-506.66 506.66Z" />
        </svg>
        User Vars
      </button>
      <button class="tab" id="tab5" aria-controls="page5" aria-selected="false">
        <svg xmlns="http://www.w3.org/2000/svg" height="40px" viewBox="0 -960 960 960" width="40px" fill="#FFFFFF">
          <path d="M446.67-446.67H200v-66.66h246.67V-760h66.66v246.67H760v66.66H513.33V-200h-66.66v-246.67Z" />
        </svg>
        Post Settings
      </button>
      <button class="tab" id="tab6" aria-controls="page6" aria-selected="false">
        <svg xmlns="http://www.w3.org/2000/svg" height="40px" viewBox="0 -960 960 960" width="40px" fill="#FFFFFF">
          <path
            d="m382-80-18.67-126.67q-17-6.33-34.83-16.66-17.83-10.34-32.17-21.67L178-192.33 79.33-365l106.34-78.67q-1.67-8.33-2-18.16-.34-9.84-.34-18.17 0-8.33.34-18.17.33-9.83 2-18.16L79.33-595 178-767.67 296.33-715q14.34-11.33 32.34-21.67 18-10.33 34.66-16L382-880h196l18.67 126.67q17 6.33 35.16 16.33 18.17 10 31.84 22L782-767.67 880.67-595l-106.34 77.33q1.67 9 2 18.84.34 9.83.34 18.83 0 9-.34 18.5Q776-452 774-443l106.33 78-98.66 172.67-118-52.67q-14.34 11.33-32 22-17.67 10.67-35 16.33L578-80H382Zm55.33-66.67h85l14-110q32.34-8 60.84-24.5T649-321l103.67 44.33 39.66-70.66L701-415q4.33-16 6.67-32.17Q710-463.33 710-480q0-16.67-2-32.83-2-16.17-7-32.17l91.33-67.67-39.66-70.66L649-638.67q-22.67-25-50.83-41.83-28.17-16.83-61.84-22.83l-13.66-110h-85l-14 110q-33 7.33-61.5 23.83T311-639l-103.67-44.33-39.66 70.66L259-545.33Q254.67-529 252.33-513 250-497 250-480q0 16.67 2.33 32.67 2.34 16 6.67 32.33l-91.33 67.67 39.66 70.66L311-321.33q23.33 23.66 51.83 40.16 28.5 16.5 60.84 24.5l13.66 110Zm43.34-200q55.33 0 94.33-39T614-480q0-55.33-39-94.33t-94.33-39q-55.67 0-94.5 39-38.84 39-38.84 94.33t38.84 94.33q38.83 39 94.5 39ZM480-480Z" />
        </svg>
        Site Settings
      </button>
    </div>
    <!-- Pages -->
    <div class="pages">
      <div id="page1" class="page" role="tabpanel" aria-labelledby="tab1" style="display: block;"></div>
      <div id="page2" class="page" role="tabpanel" aria-labelledby="tab2" style="display: none;"></div>
      <div id="page3" class="page" role="tabpanel" aria-labelledby="tab3" style="display: none;"></div>

      <div id="page4" class="page" role="tabpanel" aria-labelledby="tab4" style="display: none;">
        <div class="userVarsSearch">
          <form id="adminUserSearchForm">
            <input type="text" name="query" id="adminUserSearchInput" placeholder="Search users..." required>
            <button type="submit">Search</button>
          </form>
          <div id="returnSearch"></div>
        </div>
        <script>
          // === Separate, non-conflicting function name ===
          function setupUserVarsFormHandlers() {
            const resultsDiv = document.getElementById('returnSearch');

            // Use event delegation on the container
            resultsDiv?.addEventListener('submit', function (e) {
              const form = e.target;
              if (!form.classList.contains('userVarsForm')) return; // Only handle our forms

              e.preventDefault();

              const formData = new FormData(form);
              formData.append('submit', 'Finish'); // Ensure this field is sent

              const actionUrl = "../backend/scripts/admin/_postAdminAction.php";

              fetch(actionUrl, {
                method: "POST",
                body: formData
              })
                .then(response => {
                  if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                  return response.json();
                })
                .then(json => {
                  if (json.success) {
                    Toastify({
                      text: json.message || 'User status updated successfully.',
                      duration: 3000,
                      gravity: "top",
                      position: "right",
                      style: {
                        background: "#4CAF50"
                      }
                    }).showToast();

                    // Refresh the search results to show updated status
                    const currentQuery = document.getElementById('adminUserSearchInput').value;
                    if (currentQuery.trim()) {
                      // Re-run the search to get updated data
                      setTimeout(() => {
                        submitAdminSearch({ preventDefault: () => { } });
                      }, 1000);
                    }
                  } else {
                    Toastify({
                      text: json.error || 'Failed to update user status.',
                      duration: 4000,
                      gravity: "top",
                      position: "right",
                      style: {
                        background: "#f44336"
                      }
                    }).showToast();
                  }
                })
                .catch(error => {
                  console.error("Request failed:", error);
                  Toastify({
                    text: 'An error occurred while processing your request.',
                    duration: 5000,
                    gravity: "top",
                    position: "right",
                    style: {
                      background: "#f44336"
                    }
                  }).showToast();
                });
            });
          }

          // === Search Handling (Unchanged, but integrated) ===
          function submitAdminSearch(event) {
            event.preventDefault();

            const input = document.getElementById('adminUserSearchInput');
            const resultsDiv = document.getElementById('returnSearch');

            if (!input || !resultsDiv) return;

            const query = input.value.trim();
            if (!query) {
              resultsDiv.innerHTML = '<div class="noResults">Please enter a search term.</div>';
              return;
            }

            resultsDiv.innerHTML = `
    <div class="loading">
      <l-line-wobble size="35" stroke="4" bg-opacity="0.1" speed="2" color="#ff4500"></l-line-wobble>
    </div>
  `;

            fetch('../backend/scripts/admin/_adminQuery.php?query=' + encodeURIComponent(query))
              .then(response => {
                if (!response.ok) throw new Error("Network response was not ok");
                return response.json();
              })
              .then(data => {
                if (!data.success || !data.data || data.data.length === 0) {
                  resultsDiv.innerHTML = '<div class="noResults">No users found matching that query.</div>';
                  return;
                }

                // Function to build user state options with current state selected
                function buildUserStateOptions(currentState) {
                  // Convert currentState to string for comparison
                  const currentStateStr = String(currentState);

                  const userStateOptions = [
                    { value: "0", label: "Valid" },
                    { value: "500", label: "Suspend" },
                    { value: "600", label: "Warn 1" },
                    { value: "610", label: "Warn 2" },
                    { value: "620", label: "Warn 3" },
                    { value: "700", label: "Ban" }
                  ];

                  return userStateOptions
                    .map(opt => {
                      const isSelected = opt.value === currentStateStr;
                      return `<option value="${opt.value}" ${isSelected ? 'selected' : ''}>${opt.label}</option>`;
                    })
                    .join('');
                }

                let html = '<div class="userResults">';
                data.data.forEach(user => {
                  let dobText = "N/A", ageText = "N/A", isOver18Text = "N/A";
                  try {
                    const ageData = JSON.parse(user.userAge);
                    if (ageData && typeof ageData === "object") {
                      dobText = formatDate(ageData.dob);
                      ageText = ageData.age ?? "Unknown";
                      isOver18Text = ageData.is_over_18 ? "Yes" : "No";
                    }
                  } catch (e) {
                    console.error("Failed to parse userAge:", e);
                  }

                  html += `
          <div class="userCard">
            <div class="userCardMainInfo">
              <p><strong>Username:</strong> ${user.username}</p>
              <p><strong>Email:</strong> ${user.email}</p>
              <p><strong>UUID:</strong> ${user.UUID}</p>
            </div>
            <div class="adminProfileImages">
              <a target="_blank" href="<?= filePath("/"); ?>${user.pfp_image_link}">
                <img src="<?= filePath("/"); ?>${user.pfp_image_link}" />
              </a>
              <a target="_blank" href="<?= filePath("/"); ?>${user.bg_image_link}">
                <img src="<?= filePath("/"); ?>${user.bg_image_link}" />
              </a>
            </div>
            <div class="userDataContainer">
              <div class="userDataLeft">
                <p><strong>Bio:</strong> ${user.profile_bio || 'No bio provided'}</p>
                <p><strong>Last Login IP:</strong> ${user.user_ip}</p>
                <p><strong>Date of Birth:</strong> ${dobText}</p>
                <p><strong>Age:</strong> ${ageText}</p>
                <p><strong>Over 18:</strong> ${isOver18Text}</p>
                <a href="<?= filePath("/profile/u/"); ?>${user.UUID}/" target="_blank">View Profile</a>
              </div>
              <div class="userDataRight">
                <form class="userVarsForm" data-uuid="${user.UUID}">
                  <input type="hidden" name="PUID" value="${user.UUID}" />
                  <input type="hidden" name="DEF_UUID" value="${user.UUID}" />
                  <input type="hidden" name="postoptions" value="0" />
                  <div class="form-group">
                    <label>User State</label>
                    <select name="options">
                      ${buildUserStateOptions(user.userState || 0)}
                    </select>
                  </div>
                  <div class="form-actions">
                    <button type="submit" name="submit" value="Finish">Apply Status</button>
                  </div>
                </form>
              </div>
            </div>
          </div>`;
                });
                html += '</div>';
                resultsDiv.innerHTML = html;

                // ✅ Now attach handler *after* forms are added
                setupUserVarsFormHandlers();
              })
              .catch(error => {
                console.error('Fetch error:', error);
                resultsDiv.innerHTML = '<div class="error">An error occurred while fetching results.</div>';
              });
          }

          // === DOM Ready: Attach search listener only once ===
          document.addEventListener('DOMContentLoaded', function () {
            const tabButton = document.querySelector('[aria-controls="page4"]');
            const adminForm = document.getElementById('adminUserSearchForm');

            if (!tabButton || !adminForm) return;

            tabButton.addEventListener('click', function () {
              // Only attach once
              if (!adminForm.dataset.listenerAttached) {
                adminForm.addEventListener('submit', submitAdminSearch);
                adminForm.dataset.listenerAttached = 'true';
              }
            });
          });
        </script>
      </div>

      <div id="page5" class="page" role="tabpanel" aria-labelledby="tab4" style="display: none;">
        <script>

          let categoryData = [];
          let nextValue = 1000; // Starting value for new categories

          document.addEventListener('DOMContentLoaded', function () {
            loadCategories();
          });

          function loadCategories() {
            fetch('../backend/json/CategoryOptions.json')
              .then(response => response.json())
              .then(data => {
                categoryData = data;
                // Find the highest value to set nextValue correctly
                const values = data.map(item => parseInt(item.value)).filter(val => !isNaN(val) && val >= 11);
                nextValue = values.length > 0 ? Math.max(...values) + 1 : 11;
                renderForm();
              })
              .catch(error => {
                console.error('Error loading categories:', error);
                categoryData = [];
                renderForm();
              });
          }

          function renderForm() {
            const form = document.querySelector('#page5 form');
            form.innerHTML = '<h3>Edit Categories</h3>';

            // Prevent form submission
            form.addEventListener('submit', function (e) {
              e.preventDefault();
              return false;
            });

            // Render existing categories
            categoryData.forEach((item, index) => {
              const container = document.createElement('div');
              container.className = 'category-item';
              container.innerHTML = `
          <label for="category-${index}">${item.text} (ID: ${item.value}):</label>
          <input type="text" id="category-${index}" 
                 name="category-${index}" 
                 value="${item.text}" 
                 data-value="${item.value}">
          <button type="button" class="remove-btn" data-index="${index}">Remove</button>
        `;
              form.appendChild(container);
            });

            // Add new category input
            const newCategoryContainer = document.createElement('div');
            newCategoryContainer.className = 'new-category';
            newCategoryContainer.innerHTML = `
        <h4>Add New Category</h4>
        <input type="text" id="new-category-name" placeholder="Enter new category name">
        <button type="button" id="add-category-btn">Add Category</button>
      `;
            form.appendChild(newCategoryContainer);

            // Add save button
            const saveButton = document.createElement('button');
            saveButton.type = 'button';
            saveButton.textContent = 'Save Changes';
            saveButton.id = 'save-categories-btn';
            form.appendChild(saveButton);

            // Add reload button
            const reloadButton = document.createElement('button');
            reloadButton.type = 'button';
            reloadButton.textContent = 'Reload Data';
            reloadButton.id = 'reload-categories-btn';
            form.appendChild(reloadButton);

            // Attach event listeners
            document.getElementById('add-category-btn').addEventListener('click', addNewCategory);
            document.getElementById('save-categories-btn').addEventListener('click', saveChanges);
            document.getElementById('reload-categories-btn').addEventListener('click', loadCategories);

            // Attach remove button listeners
            document.querySelectorAll('.remove-btn').forEach(button => {
              button.addEventListener('click', function () {
                const index = parseInt(this.getAttribute('data-index'));
                removeCategory(index);
              });
            });
          }

          function addNewCategory() {
            const newNameInput = document.getElementById('new-category-name');
            const newName = newNameInput.value.trim();

            if (newName) {
              // Find the position to insert new categories (after value "10" but before 998/999)
              let insertIndex = categoryData.length; // Default to end

              // Find the position of "Question" (value "10")
              let questionIndex = -1;
              for (let i = 0; i < categoryData.length; i++) {
                if (categoryData[i].value === "10") {
                  questionIndex = i;
                  break;
                }
              }

              if (questionIndex !== -1) {
                // Insert after "Question"
                insertIndex = questionIndex + 1;

                // But make sure we don't insert after special categories (998, 999)
                // Find the first special category (998 or 999) after position
                let specialCategoryIndex = categoryData.length;
                for (let i = insertIndex; i < categoryData.length; i++) {
                  const currentValue = parseInt(categoryData[i].value);
                  if (currentValue >= 998) {
                    specialCategoryIndex = i;
                    break;
                  }
                }

                // Insert at the correct position
                insertIndex = Math.min(insertIndex, specialCategoryIndex);
              }

              // Insert the new category at the correct position
              const newCategory = {
                value: nextValue.toString(),
                text: newName
              };

              categoryData.splice(insertIndex, 0, newCategory);
              nextValue++;
              newNameInput.value = '';
              renderForm();
            }
          }

          function removeCategory(index) {
            if (index >= 0 && index < categoryData.length) {
              // Prevent removing system categories (0-10)
              const valueToRemove = parseInt(categoryData[index].value);
              if (valueToRemove >= 11) { // Only allow removing custom categories
                categoryData.splice(index, 1);
                // Update nextValue if needed
                const customValues = categoryData
                  .map(item => parseInt(item.value))
                  .filter(val => !isNaN(val) && val >= 11);
                nextValue = customValues.length > 0 ? Math.max(...customValues) + 1 : 11;
                renderForm();
              } else {
                alert('Cannot remove system categories (values below 11)');
              }
            }
          }

          function saveChanges() {
            // Prevent any default form behavior
            event.preventDefault();

            // Create a clean copy of the data
            const cleanData = JSON.parse(JSON.stringify(categoryData));

            // Prepare data to send
            const formData = new FormData();
            formData.append('categories', JSON.stringify(cleanData));

            // Send to server with correct path
            fetch('../backend/scripts/admin/_editJsonCategorys.php', {
              method: 'POST',
              body: formData
            })
              .then(response => response.json())
              .then(data => {
                if (data.success) {
                  alert('Categories saved successfully!');
                  // Reload to show the saved data
                  loadCategories();
                } else {
                  alert('Error saving categories: ' + data.message);
                }
              })
              .catch(error => {
                console.error('Error:', error);
                alert('Error saving categories. Check console for details.');
              });

            return false; // Prevent form submission
          }
        </script>
        <form action="" method="post"></form>
      </div>

      <div id="page6" class="page" role="tabpanel" aria-labelledby="tab6" style="display: none;">
        <button type="button"
          onclick="window.location.href='<?= filePath("/backend/scripts/admin/"); ?>sitemapGen.php'">Generate
          Sitemap</button>
      </div>

    </div>
  </div>

  <script>
    function formatDate(dob) {
      const options = { year: 'numeric', month: 'long', day: 'numeric' };
      return new Date(dob).toLocaleDateString(undefined, options);
    }
  </script>
</body>

</html>