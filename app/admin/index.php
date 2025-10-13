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
        <ion-icon name="refresh-outline"></ion-icon>
        Recent Posts
      </button>
      <button class="tab" id="tab2" aria-controls="page2" aria-selected="false" onclick="recentLogins()">
        <ion-icon name="analytics-outline"></ion-icon>
        Recent Logins
      </button>
      <button class="tab" id="tab3" aria-controls="page3" aria-selected="false" onclick="fetchAndRenderReports()">
        <ion-icon name="shield-half-outline"></ion-icon>
        Reports
      </button>
      <button class="tab" id="tab4" aria-controls="page4" aria-selected="false">
        <ion-icon name="construct-outline"></ion-icon>
        User Vars
      </button>
      <button class="tab" id="tab5" aria-controls="page5" aria-selected="false">
        <ion-icon name="add-outline"></ion-icon>
        Post Settings
      </button>
      <button class="tab" id="tab6" aria-controls="page6" aria-selected="false">
        <ion-icon name="settings-outline"></ion-icon>
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
  <!-- Ionicons -->
  <script type="module" src="https://cdn.jsdelivr.net/npm/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
  <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js" crossorigin></script>

  <script>
    function formatDate(dob) {
      const options = { year: 'numeric', month: 'long', day: 'numeric' };
      return new Date(dob).toLocaleDateString(undefined, options);
    }
  </script>
</body>

</html>