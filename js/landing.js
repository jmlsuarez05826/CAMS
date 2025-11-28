document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll(".building-card").forEach((card) => {
    const floorContainer = card.querySelector(".floor-container");
    const detailsContainer = card.querySelector(".building-block");
    const backBtn = card.querySelector(".back-btn");

    // Building click
    card.querySelector(".building").addEventListener("click", () => {
      // Hide all buildings
      document.querySelectorAll(".building-card").forEach((c) => {
        c.querySelector(".building").style.display = "none";
        c.querySelector(".building-block").style.display = "none";
        c.style.width = "";
      });

      // Expand this building
      card.style.width = "100%";
      card.style.display = "flex";
      card.style.flexDirection = "column";

      detailsContainer.style.display = "block";
      backBtn.style.display = "block";

      // ✅ Default to first floor by simulating a click
      const firstFloor = floorContainer.querySelector(".floor");
      if (firstFloor) {
        firstFloor.click(); // triggers the floor click handler
      }
    });

    // Floor click
    floorContainer.addEventListener("click", (e) => {
      if (!e.target.classList.contains("floor")) return;

      // Remove active from all floors
      floorContainer
        .querySelectorAll(".floor")
        .forEach((f) => f.classList.remove("active"));
      e.target.classList.add("active");

      const selectedFloor = e.target.dataset.floor;

      // Hide all room containers
      card
        .querySelectorAll(".room-container")
        .forEach((rc) => (rc.style.display = "none"));

      // Show only the matching one
      const targetRoomContainer = card.querySelector(
        `.room-container[data-floor="${selectedFloor}"]`
      );
      if (targetRoomContainer) targetRoomContainer.style.display = "flex";

      // Move floor indicator
      const indicator = floorContainer.querySelector(".floor-indicator");
      if (indicator) {
        indicator.style.width = e.target.offsetWidth + "px";
        indicator.style.left = e.target.offsetLeft + "px";
      }
    });

    // Back button click
    backBtn.addEventListener("click", (e) => {
      e.stopPropagation();
      document.querySelectorAll(".building-card").forEach((c) => {
        c.querySelector(".building").style.display = "block";
        c.querySelector(".building-block").style.display = "none";
        c.style.width = "";
      });
      backBtn.style.display = "none";
      detailsContainer.style.display = "none";
    });
  });
});

// =========================
// 1. Get modal element
// =========================
const classroomModal = document.getElementById("classroomModal");

// Close buttons inside the modal
const closeModalBtn = document.getElementById("closeclassroomModal");
const closeFooterBtn = document.getElementById("closeAddUserFooter");

// =========================
// 2. Function to open modal
// =========================
function openClassroomModal() {
  classroomModal.classList.add("show"); // makes modal visible
}

// =========================
// 3. Function to close modal
// =========================
function closeClassroomModal() {
  classroomModal.classList.remove("show"); // hides modal
}

// =========================
// 4. Attach click event to all .room-card items
//    THIS IS THE TRIGGER
// =========================

// =========================
// 5. Close modal using the "X" button
// =========================
closeModalBtn.addEventListener("click", closeClassroomModal);

// =========================
// 6. Close modal using footer Close button
// =========================
closeFooterBtn.addEventListener("click", closeClassroomModal);

// =========================
// 7. Close modal when clicking outside content
// =========================
window.addEventListener("click", (e) => {
  if (e.target === classroomModal) {
    closeClassroomModal();
  }
});

document.querySelectorAll(".room-card.clickable-room").forEach((card) => {
  card.addEventListener("click", () => {
    const roomID = card.getAttribute("data-room"); // make sure to set this in PHP
    const roomNumber = card.querySelector(".room-number").innerText;
    const days = [
      "Sunday",
      "Monday",
      "Tuesday",
      "Wednesday",
      "Thursday",
      "Friday",
      "Saturday",
    ];
    const today = days[new Date().getDay()];
    window.currentRoomID = roomID;

    const roomInput = document.getElementById("roomID");
    if (roomInput) roomInput.value = roomID;

    // DEBUG: check if roomID is correctly set
    console.log("Clicked roomID:", roomID);
    console.log("window.currentRoomID:", window.currentRoomID);

    // Set currentDay to today's day automatically

    // Open modal
    const classroomModal = document.getElementById("classroomModal");
    classroomModal.classList.add("show");

    // Update modal title with room number
    document.querySelector(
      "#classroomModal .custom-modal-title"
    ).innerText = `Classroom Schedule - Room ${roomNumber}`;

    // Load today's schedules
    loadSchedules(today);
  });
});

  function loadSchedules(day) {
    if (!window.currentRoomID) return;

    const weekType = "Odd"; // temporary fix

    fetch("../pages/landingpage.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `action=getSchedules&roomID=${window.currentRoomID}&dayOfWeek=${day}&weekType=${weekType}`
    })
    .then(res => res.json())
    .then(schedules => {
        const tbody = document.querySelector(".classSchedTable tbody");
        tbody.innerHTML = "";

        if (schedules.length === 0) {
            tbody.innerHTML = `<tr><td colspan="4" class="text-center">No schedule found</td></tr>`;
        } else {
            schedules.forEach(s => {
                tbody.innerHTML += `
                    <tr>
                        <td>${s.Instructor}</td>
                        <td>${s.Subject}</td>
                        <td>${s.TimeFrom} - ${s.TimeTo}</td>
                        <td>${s.Section}</td>
                    </tr>`;
            });
        }
    })
    .catch(err => console.error("Failed to fetch schedules:", err));
}

 


function loadRoomStatuses() {
  const weekType = "Odd"; // force week type to Odd

  document.querySelectorAll(".clickable-room").forEach((roomCard) => {
    const roomID = roomCard.dataset.room;

    fetch("faculty-reservation.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: new URLSearchParams({
        action: "getSchedules",
        roomID: roomID,
        dayOfWeek: new Date().toLocaleString("en-US", { weekday: "long" }),
        weekType: weekType,
      }),
    })
      .then((res) => res.json())
      .then((schedules) => {
        let status = "Available";
        const now = new Date();
        const currentTime =
          now.getHours().toString().padStart(2, "0") +
          ":" +
          now.getMinutes().toString().padStart(2, "0");

        // Build schedule list
        let scheduleHTML = "<ul>";
        schedules.forEach((sch) => {
          scheduleHTML += `<li>${sch.Subject || "Class"}: ${sch.TimeFrom} - ${
            sch.TimeTo
          }</li>`;
          if (currentTime >= sch.TimeFrom && currentTime <= sch.TimeTo) {
            status = "Occupied";
          }
        });
        scheduleHTML += "</ul>";

        // Update status
        const statusDiv = roomCard.querySelector(".room-status");
        statusDiv.textContent = status;
        statusDiv.className = "room-status " + status.toLowerCase();

        // Update schedules
        const schedDiv = roomCard.querySelector(".room-schedules");
        if (schedDiv) {
          schedDiv.innerHTML =
            schedules.length > 0 ? scheduleHTML : "No schedules today";
        }
      })
      .catch((err) => console.error(err));
  });
}

// ======================================================================================================================
window.addEventListener("scroll", function () {
  const header = document.querySelector("header");

  if (window.scrollY > 10) {
    header.classList.add("scrolled");
  } else {
    header.classList.remove("scrolled");
  }
});

// Mobile menu toggle (simple)
function toggleMobileMenu() {
  const ul = document.getElementById("main-menu");
  if (!ul) return;
  if (window.getComputedStyle(ul).display === "none") {
    ul.style.display = "flex";
    ul.style.flexDirection = "column";
    ul.style.position = "absolute";
    ul.style.right = "5%";
    ul.style.top = "66px";
    ul.style.background = "white";
    ul.style.padding = "10px 14px";
    ul.style.borderRadius = "10px";
    ul.style.boxShadow = "0 12px 36px rgba(15,15,15,0.08)";
  } else {
    // reset to default for wide screens
    if (window.innerWidth < 960) {
      ul.style.display = "none";
    } else {
      ul.style.display = "flex";
      ul.style.flexDirection = "row";
      ul.style.position = "";
      ul.style.right = "";
      ul.style.top = "";
      ul.style.background = "";
      ul.style.padding = "";
      ul.style.borderRadius = "";
      ul.style.boxShadow = "";
    }
  }
}

// Close mobile menu when resizing to desktop
window.addEventListener("resize", () => {
  const ul = document.getElementById("main-menu");
  if (!ul) return;
  if (window.innerWidth >= 960) {
    ul.style.display = "flex";
    ul.style.flexDirection = "row";
    ul.style.position = "";
    ul.style.right = "";
    ul.style.top = "";
    ul.style.background = "";
    ul.style.padding = "";
    ul.style.borderRadius = "";
    ul.style.boxShadow = "";
  } else {
    ul.style.display = "none";
  }
});

// FAQ accordion
document.querySelectorAll(".faq-item").forEach((item) => {
  item.addEventListener("click", () => {
    const isOpen = item.classList.contains("open");
    // close others (optional: single-open behavior)
    document.querySelectorAll(".faq-item").forEach((i) => {
      i.classList.remove("open");
      const ans = i.querySelector(".faq-a");
      if (ans) ans.style.display = "none";
    });

    if (!isOpen) {
      item.classList.add("open");
      const ans = item.querySelector(".faq-a");
      if (ans) ans.style.display = "block";
    }
  });
});

// initial responsive menu state
(function initMenu() {
  if (window.innerWidth < 960) {
    const ul = document.getElementById("main-menu");
    if (ul) ul.style.display = "none";
  }
})();

// Script for real-time day & 12-hour format time
function updateTimeDay() {
  const now = new Date();

  // Get day
  const days = [
    "Sunday",
    "Monday",
    "Tuesday",
    "Wednesday",
    "Thursday",
    "Friday",
    "Saturday",
  ];
  const day = days[now.getDay()];

  // Get hours and minutes
  let hours = now.getHours();
  const minutes = String(now.getMinutes()).padStart(2, "0");
  const seconds = String(now.getSeconds()).padStart(2, "0");
  const ampm = hours >= 12 ? "PM" : "AM";

  // Convert 24-hour to 12-hour format
  hours = hours % 12;
  hours = hours ? hours : 12; // the hour '0' should be '12'
  hours = String(hours).padStart(2, "0");

  // Set the text content
  document.getElementById(
    "timeDay"
  ).textContent = `${day}, ${hours}:${minutes}:${seconds} ${ampm}`;
}

// Update every second
setInterval(updateTimeDay, 1000);

// Initial call
updateTimeDay();

// ====================================================================================================

                  
// --------------------------
// //Room Status Logic
// --------------------------
          function toMinutes(timeString) {
    const [hours, minutes, seconds] = timeString.split(':');
    return parseInt(hours) * 60 + parseInt(minutes);
}

function loadRoomStatuses() {
    const weekType = localStorage.getItem("selectedWeek") || "Odd";

    document.querySelectorAll(".clickable-room").forEach(roomCard => {
        const roomID = roomCard.dataset.room;

        fetch("../pages/landingpage.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: new URLSearchParams({
                action: "getSchedules",
                roomID: roomID,
                dayOfWeek: new Date().toLocaleString("en-US", { weekday: "long" }),
                weekType: weekType
            })
        })
        .then(res => res.json())
        .then(schedules => {
            console.log("Schedules for room:", roomID, schedules);

            let status = "Available";

            if (schedules.length > 0) {
                const now = new Date();
                const currentMinutes = now.getHours() * 60 + now.getMinutes();

                schedules.forEach(sch => {
                    const start = toMinutes(sch.TimeFrom);
                    const end = toMinutes(sch.TimeTo);

                    if (currentMinutes >= start && currentMinutes <= end) {
                        status = "Occupied";
                    }
                });
            }

            const statusDiv = roomCard.querySelector(".room-status");
            statusDiv.textContent = status;
            statusDiv.className = "room-status " + status.toLowerCase();
        })
        .catch(err => console.error(err));
    });
}


        // Initial load
        loadRoomStatuses();

        // Reload when week changes with SweetAlert confirmation
        const weekBtn = document.querySelector(".oddWeek-btn");
        weekBtn.addEventListener("click", () => {
            const currentWeek = weekBtn.textContent.includes("Odd") ? "Odd" : "Even";
            const nextWeek = currentWeek === "Odd" ? "Even" : "Odd";

            Swal.fire({
                title: "Change Week?",
                text: `Are you sure you want to change the week to ${nextWeek}?`,
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: `Yes, change to ${nextWeek}`,
                cancelButtonText: "Cancel"
            }).then((result) => {
                if (result.isConfirmed) {
                    // Update week in button and localStorage
                    weekBtn.textContent = nextWeek + " Week";
                    localStorage.setItem("selectedWeek", nextWeek);

                    // Reload room statuses
                    loadRoomStatuses();

                    Swal.fire({
                        title: "Week Changed!",
                        text: `The week has been updated to ${nextWeek}.`,
                        icon: "success",
                        confirmButtonText: "OK"
                    });
                }
            });
        });
