// ====================================================================================================
// CORE FUNCTIONS FOR DATE AND WEEK DETERMINATION 
// ====================================================================================================

/**
 * Helper function to calculate the ISO week number (1-52 or 53).
 * Source: https://weeknumber.net/how-to/javascript
 * @param {Date} d The date object to check.
 * @returns {number} The week number.
 */

function getWeekNumber(d) {
    // Copy date object to avoid modifying the original
    d = new Date(Date.UTC(d.getFullYear(), d.getMonth(), d.getDate()));
    // Set to nearest Thursday: current date + 4 - current day number.
    // Monday is 1, Sunday is 7.
    d.setUTCDate(d.getUTCDate() + 4 - (d.getUTCDay() || 7));
    // Get first day of year
    const yearStart = new Date(Date.UTC(d.getUTCFullYear(), 0, 1));
    // Calculate full weeks to go
    const weekNo = Math.ceil((((d - yearStart) / 86400000) + 1) / 7);
    return weekNo;
}

/**
 * Determines if the current week is Odd or Even based on its ISO week number.
 * @returns {string} "Odd" or "Even"
 */
function getCurrentWeekType() {
    const now = new Date();
    // Week number is calculated (e.g., 47)
    const weekNumber = getWeekNumber(now);

    // Week 1, 3, 5... are Odd. Week 2, 4, 6... are Even.
    if (weekNumber % 2 !== 0) {
        return "Odd";
    } else {
        return "Even";
    }
}

// --------------------------
// Room Status and Schedule Loading Functions
// --------------------------

function toMinutes(timeString) {
    const [hours, minutes, seconds] = timeString.split(':');
    return parseInt(hours) * 60 + parseInt(minutes);
}

/**
 * Loads the schedule for the given room and day, using the currently selected week type 
 * (from localStorage, which is typically set by the user or defaults to Odd).
 * This function is used when opening the modal.
/**
 * Loads the schedule for the given room and day, using the currently selected week type 
 * (from localStorage, which is typically set by the user or defaults to Odd).
 * This function is used when opening the modal.
 * @param {string} day - e.g., "Monday"
 */
function loadSchedules(day) {
    if (!window.currentRoomID) return;

    const weekType = localStorage.getItem("selectedWeek") || getCurrentWeekType();

    // Calculate today's date string in YYYY-MM-DD format for comparison
    const today = new Date();
    const yyyy = today.getFullYear();
    const mm = String(today.getMonth() + 1).padStart(2, '0');
    const dd = String(today.getDate()).padStart(2, '0');
    const todayDateString = `${yyyy}-${mm}-${dd}`;
    // console.log("Current Date for Filtering:", todayDateString); // Add this for debugging

    fetch("../pages/landingpage.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `action=getSchedules&roomID=${window.currentRoomID}&dayOfWeek=${day}&weekType=${weekType}`
    })
    .then(res => res.json())
    .then(schedules => {
        const tbody = document.querySelector(".classSchedTable tbody");
        tbody.innerHTML = "";

        // 🌟 REFINED FILTER LOGIC:
        const filteredSchedules = schedules.filter(s => {
            const reserveDate = s.ReserveDate ? s.ReserveDate.trim() : '';

            // This condition checks for a valid, non-placeholder date string
            const isOneTimeReservation = reserveDate.length > 5 && // ensure it's longer than a placeholder like '-'
                                         !reserveDate.startsWith('0000') &&
                                         reserveDate !== '-'; // Check for your specific '-' placeholder

            if (isOneTimeReservation) {
                // Show reserved schedules ONLY if the date matches today
                return reserveDate === todayDateString;
            }

            // Otherwise, treat it as a regular (non-date-specific) schedule and always show it
            return true;
        });


        if (filteredSchedules.length === 0) {
            tbody.innerHTML = `<tr><td colspan="5" class="text-center">No schedule found</td></tr>`;
        } else {
            filteredSchedules.forEach(s => {
                tbody.innerHTML += `
                    <tr>
                        <td>${s.Instructor}</td>
                        <td>${s.Subject}</td>
                        <td>${s.TimeFrom} - ${s.TimeTo}</td>
                        <td>${s.Section}</td>
                        <td>${s.ReserveDate || 'N/A'}</td>
                    </tr>`;
            });
        }
    })
    .catch(err => console.error("Failed to fetch schedules:", err));
}

/**
 * Loads the REAL-TIME room status (Occupied/Available) based on the actual current day and week type.
 * Note: This function uses the DYNAMIC week type, not the user-selected localStorage one,
 * to ensure the room status is always accurate for the current moment.
 */
function toMinutes(timeString) {
    const parts = timeString.split(':');
    const hours = parseInt(parts[0]);
    const minutes = parseInt(parts[1]);
    return hours * 60 + minutes;
}

function loadRoomStatuses() {
    // Determine the week type and current day, as before
    const weekType = localStorage.getItem("selectedWeek") || "Odd";  
    const dayOfWeek = new Date().toLocaleString("en-US", { weekday: "long" });

    // 🌟 NEW: Calculate today's date string in YYYY-MM-DD format for comparison
    const today = new Date();
    const yyyy = today.getFullYear();
    const mm = String(today.getMonth() + 1).padStart(2, '0');
    const dd = String(today.getDate()).padStart(2, '0');
    const todayDateString = `${yyyy}-${mm}-${dd}`;
    // --------------------------------------------------------------------------

    document.querySelectorAll(".clickable-room").forEach(roomCard => {
        const roomID = roomCard.dataset.room;

        fetch("../pages/landingpage.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: new URLSearchParams({
                action: "getSchedules",
                roomID: roomID,
                dayOfWeek: dayOfWeek,   // 👈 Based on TODAY
                weekType: weekType
            })
        })
        .then((res) => res.json())
        .then((schedules) => {

            // 🌟 NEW: Filter the incoming schedules to remove future reservations
            const currentDaySchedules = schedules.filter((s) => {
                const reserveDate = s.ReserveDate ? s.ReserveDate.trim() : '';
                
                // Identify if it's a one-time reservation based on your criteria
                const isOneTimeReservation = reserveDate.length > 5 &&
                                             !reserveDate.startsWith('0000') &&
                                             reserveDate !== '-';
                
                if (isOneTimeReservation) {
                    // Include reserved schedules ONLY if the date matches today
                    return reserveDate === todayDateString;
                }

                // Include regular schedules (where ReserveDate is not a specific future date)
                return true;
            });
            // --------------------------------------------------------------------------

            let status = "Available";

            // Loop through the filtered list: currentDaySchedules
            if (currentDaySchedules.length > 0) {
                const now = new Date();
                const currentMinutes = now.getHours() * 60 + now.getMinutes();

                currentDaySchedules.forEach((sch) => { // NOTE: Changed from 'schedules' to 'currentDaySchedules'
                    const start = toMinutes(sch.TimeFrom);
                    const end = toMinutes(sch.TimeTo);

                    // Occupied if within schedule (today)
                    if (currentMinutes >= start && currentMinutes <= end) {
                        status = "Occupied";
                    }
                });
            }

            const statusDiv = roomCard.querySelector(".room-status");
            statusDiv.textContent = status;
            statusDiv.className = "room-status " + status.toLowerCase();
        })
        .catch((err) => console.error(err));
    });
}



// ====================================================================================================
// DOM CONTENT LOADED - EVENT LISTENERS & INITIALIZATION
// ====================================================================================================

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


    // Initialize the week button text based on stored value (or dynamic default)
    const weekBtn = document.querySelector(".oddWeek-btn");
    const initialWeek = localStorage.getItem("selectedWeek") || getCurrentWeekType();
    if (weekBtn) {
        weekBtn.textContent = initialWeek + " Week";
    }

    // Initial load of room statuses (using dynamic current week)
    loadRoomStatuses();
});

// =========================
// Modal Configuration
// =========================
const classroomModal = document.getElementById("classroomModal");
const closeModalBtn = document.getElementById("closeclassroomModal");
const closeFooterBtn = document.getElementById("closeAddUserFooter");

function closeClassroomModal() {
  classroomModal.classList.remove("show"); // hides modal
}

closeModalBtn.addEventListener("click", closeClassroomModal);
closeFooterBtn.addEventListener("click", closeClassroomModal);

window.addEventListener("click", (e) => {
  if (e.target === classroomModal) {
    closeClassroomModal();
  }
});

// =========================
// Room Card Click Handler (Trigger)
// =========================
document.querySelectorAll(".room-card.clickable-room").forEach((card) => {
  card.addEventListener("click", () => {
    const roomID = card.getAttribute("data-room");
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
    const today = days[new Date().getDay()]; // Determines the current day
    window.currentRoomID = roomID;

    const roomInput = document.getElementById("roomID");
    if (roomInput) roomInput.value = roomID;

    // Open modal
    classroomModal.classList.add("show");

    // Update modal title with room number
    document.querySelector(
      "#classroomModal .custom-modal-title"
    ).innerText = `Classroom Schedule - Room ${roomNumber}`;

    // Load today's schedules (using today's day and selected/default week type)
    loadSchedules(today);
  });
});


// ====================================================================================================
// Week Toggle Logic (Allows user to override the actual week type for schedule viewing)
// ====================================================================================================

const weekBtn = document.querySelector(".oddWeek-btn");
if (weekBtn) {
    weekBtn.addEventListener("click", () => {
        const currentWeekText = weekBtn.textContent.trim();
        const currentWeek = currentWeekText.includes("Odd") ? "Odd" : "Even";
        const nextWeek = currentWeek === "Odd" ? "Even" : "Odd";

        // Check for SweetAlert before proceeding
        if (typeof Swal === 'undefined') {
             console.error("SweetAlert is not defined. Cannot show confirmation dialog.");
             return;
        }

        Swal.fire({
            title: "Change Week?",
            text: `Are you sure you want to view schedules for the ${nextWeek} week?`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: `Yes, change to ${nextWeek}`,
            cancelButtonText: "Cancel"
        }).then((result) => {
            if (result.isConfirmed) {
                // Update week in button and localStorage
                weekBtn.textContent = nextWeek + " Week";
                localStorage.setItem("selectedWeek", nextWeek);

                // Reload room statuses (this will use the DYNAMIC current week, but the
                // user-selected week is preserved in localStorage for the modal's
                // loadSchedules function when a room is clicked)
                // Note: If you want the room status to update to the *selected* week, 
                // you would need to adjust loadRoomStatuses to respect localStorage, 
                // but this makes the 'real-time' status inaccurate. I kept it dynamic 
                // for real-time accuracy.

                Swal.fire({
                    title: "Week Set!",
                    text: `Schedules will now display based on the ${nextWeek} week.`,
                    icon: "success",
                    confirmButtonText: "OK"
                });
            }
        });
    });
}


// ======================================================================================================================
// Miscellaneous Existing Functions
// ======================================================================================================================

window.addEventListener("scroll", function () {
  const header = document.querySelector("header");

  if (window.scrollY > 10) {
    header.classList.add("scrolled");
  } else {
    header.classList.remove("scrolled");
  }
});

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

(function initMenu() {
  if (window.innerWidth < 960) {
    const ul = document.getElementById("main-menu");
    if (ul) ul.style.display = "none";
  }
})();

function updateTimeDay() {
            const now = new Date();

            // Get day
            const days = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
            const day = days[now.getDay()];

            // Get 24-hour time
            const hours = String(now.getHours()).padStart(2, '0'); // military time
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');

            // Set the text content
            document.getElementById('time').textContent =
                `${day}, ${hours}:${minutes}:${seconds}`;
        }

        // Update every second
        setInterval(updateTimeDay, 1000);

        // Initial call
        updateTimeDay();