
            // --- Place this code at the top level of your script ---

// Function to calculate the ISO week number (1 to 52/53)
function getWeekNumber(d) {
    // Copy date so don't modify original
    d = new Date(Date.UTC(d.getFullYear(), d.getMonth(), d.getDate()));
    // Set to nearest Thursday: current date + 4 - current day number (adjusting for Sunday=0, Monday=1, etc.)
    d.setUTCDate(d.getUTCDate() + 4 - (d.getUTCDay() || 7));
    // Get first day of year
    var yearStart = new Date(Date.UTC(d.getUTCFullYear(), 0, 1));
    // Calculate full weeks to the nearest Thursday
    var weekNo = Math.ceil((((d - yearStart) / 86400000) + 1) / 7);
    return weekNo;
}

// Function to determine if the week number is Odd or Even
function getCurrentWeekType(date) {
    const weekNumber = getWeekNumber(date);
    return (weekNumber % 2 !== 0) ? "Odd" : "Even";
}
            
            window.onload = function() {
                    const chatContainer = document.getElementById('chat-container');
                    const toggleBtn = document.getElementById('chat-toggle');
                    const closeBtn = document.getElementById('close-btn');
                    const backBtn = document.getElementById('back-btn');
                    const chatTitle = document.getElementById('chat-title');
                    const facultyListDiv = document.getElementById('faculty-list');
                    const chatMessages = document.getElementById('chat-messages');
                    const chatInput = document.getElementById('chat-input');
                    const chatText = document.getElementById('chat-text');
                    const notifDot = document.getElementById('chat-notif');
                    const userId = facultyReservationUserID;

                    let currentFacultyId = null;

                    // ----------------- OPEN/CLOSE CHAT -----------------
                    toggleBtn.onclick = function() {
                        chatContainer.style.display = 'flex';
                        toggleBtn.style.display = 'none';
                        showFacultyList();
                    };

                    closeBtn.onclick = function() {
                        chatContainer.style.display = 'none';
                        toggleBtn.style.display = 'flex';
                    };

                    backBtn.onclick = function() {
                        currentFacultyId = null;
                        chatMessages.style.display = 'none';
                        chatInput.style.display = 'none';
                        facultyListDiv.style.display = 'block';
                        chatTitle.textContent = 'Select Faculty';
                        backBtn.style.display = 'none';
                    };

                    // ----------------- SHOW FACULTY LIST -----------------
                    function showFacultyList() {
                        facultyListDiv.style.display = 'block';
                        chatMessages.style.display = 'none';
                        chatInput.style.display = 'none';
                        backBtn.style.display = 'none';
                        chatTitle.textContent = 'Select Faculty';

                        fetch('faculty_chat.php?action=get_faculty')
                            .then(res => res.json())
                            .then(data => {
                                facultyListDiv.innerHTML = '';
                                data.forEach(fac => {
                                    const div = document.createElement('div');
                                    div.classList.add('faculty-item');
                                    div.textContent = fac.FirstName + ' ' + fac.LastName + ' (' + fac.PhoneNumber + ')';

                                    // Open chat when clicked
                                    div.onclick = function() {
                                        openChat(fac.UserID, fac.FirstName + ' ' + fac.LastName);
                                    };

                                    facultyListDiv.appendChild(div);
                                });
                            })
                            .catch(err => console.error('Error fetching faculty:', err));
                    }

                    // ----------------- OPEN CHAT -----------------
                    function openChat(facultyId, facultyName) {
                        currentFacultyId = facultyId;
                        facultyListDiv.style.display = 'none';
                        chatMessages.style.display = 'flex';
                        chatInput.style.display = 'flex';
                        backBtn.style.display = 'inline';
                        chatTitle.textContent = facultyName;
                        loadChat(true); // force scroll
                    }

                    // ----------------- LOAD CHAT MESSAGES -----------------
                    function loadChat(forceScroll = false) {
                        if (!currentFacultyId) return;
                        fetch(`faculty_chat.php?action=fetch_messages&faculty_id=${currentFacultyId}`)
                            .then(res => res.json())
                            .then(data => {
                                chatMessages.innerHTML = '';
                                data.forEach(msg => {
                                    const div = document.createElement('div');
                                    div.classList.add('message');
                                    div.classList.add(msg.sender_id == userId ? 'faculty' : 'admin');
                                    div.innerHTML = `<span>${msg.message}</span><small>${msg.timestamp}</small>`;
                                    chatMessages.appendChild(div);
                                });

                                if (forceScroll) chatMessages.scrollTop = chatMessages.scrollHeight;
                            })
                            .catch(err => console.error('Error loading chat:', err));
                    }

                    // ----------------- SEND CHAT -----------------
                    window.sendChat = function() {
                        const msg = chatText.value.trim();
                        if (!msg || !currentFacultyId) return;

                        fetch('faculty_chat.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded'
                                },
                                body: 'action=send_message&receiver_id=' + currentFacultyId + '&message=' + encodeURIComponent(msg)
                            })
                            .then(res => res.json())
                            .then(resp => {
                                if (resp.success) {
                                    chatText.value = '';
                                    loadChat(true); // scroll to bottom
                                } else {
                                    console.error(resp.error || 'Failed to send message');
                                    alert(resp.error || 'Failed to send message');
                                }
                            })
                            .catch(err => console.error('Error sending chat:', err));
                    };

                    // ----------------- ENTER KEY TO SEND -----------------
                    chatText.addEventListener('keydown', function(e) {
                        if (e.key === 'Enter' && !e.shiftKey) {
                            e.preventDefault();
                            sendChat();
                        }
                    });

                    // ----------------- AUTO-REFRESH -----------------
                    setInterval(() => {
                        if (currentFacultyId) loadChat();
                        updateNotifDot();
                    }, 1000);

                    // ----------------- UNREAD NOTIFICATION DOT -----------------
                    function updateNotifDot() {
                        fetch('faculty_chat.php?action=get_faculty')
                            .then(res => res.json())
                            .then(data => {
                                let hasUnread = false;
                                const fetches = data.map(fac =>
                                    fetch(`faculty_chat.php?action=fetch_messages&faculty_id=${fac.UserID}`)
                                    .then(res => res.json())
                                    .then(msgs => {
                                        if (msgs.some(m => m.status === 'unread' && m.sender_id != userId)) {
                                            hasUnread = true;
                                        }
                                    })
                                );

                                Promise.all(fetches).then(() => {
                                    notifDot.style.display = hasUnread ? 'block' : 'none';
                                });
                            });
                    }
                    updateNotifDot();
                };

                // Script for real-time day & 12-hour format time
                function updateTimeDay() {
                    const now = new Date();

                    // Get day
                    const days = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
                    const day = days[now.getDay()];

                    // Get hours and minutes
                    let hours = now.getHours();
                    const minutes = String(now.getMinutes()).padStart(2, '0');
                    const seconds = String(now.getSeconds()).padStart(2, '0');
                    const ampm = hours >= 12 ? 'PM' : 'AM';

                    // Convert 24-hour to 12-hour format
                    hours = hours % 12;
                    hours = hours ? hours : 12; // the hour '0' should be '12'
                    hours = String(hours).padStart(2, '0');

                    // Set the text content
                    document.getElementById('timeDay').textContent = `${day}, ${hours}:${minutes}:${seconds} ${ampm}`;
                }

                // Update every second
                setInterval(updateTimeDay, 1000);

                // Initial call
                updateTimeDay();





                document.getElementById('logout-btn').addEventListener('click', function(e) {
                    e.preventDefault(); // prevent immediate navigation
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You will be logged out.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = '../pages/logout.php';
                        }
                    });
                });

                document.addEventListener('DOMContentLoaded', () => {

                    document.querySelectorAll('.clickable-row').forEach(row => {
                        row.addEventListener('click', () => {
                            const id = row.dataset.id;
                            const equipmentName = row.cells[1].innerText;
                            const totalQty = row.cells[2].innerText;
                            const imgSrc = row.dataset.image || 'https://cdn-icons-png.flaticon.com/512/1048/1048953.png';

                            fetch(`../pages/faculty-reservation.php?equipmentID=${id}`)
                                .then(res => res.json())
                                .then(units => {
                                    const unitListHTML = units.map(u => {
                                        let btnHTML = '';
                                        if (u.hasPending) {
                                            btnHTML = `<button class="reserveBtn pending" disabled>Pending Request</button>`;
                                        } else if (u.Status.toLowerCase() === 'available') {
                                            btnHTML = `<button class="reserveBtn available" data-unit-id="${u.UnitID}">Reserve</button>`;
                                        }
                                        // else leave btnHTML empty for unavailable

                                        return `
            <div class="unit-card ${u.Status.toLowerCase().replace(' ', '-')}" data-unit-id="${u.UnitID}">
                <span class="dot"></span>
                <span class="unit-label">${equipmentName} #${u.UnitNumber}</span>
                <span class="unit-status">${u.Status}</span>
                ${btnHTML}
            </div>
        `;
                                    }).join('');



                                    Swal.fire({
                                        width: "650px", // string with px
                                        heightAuto: false,
                                        showConfirmButton: true, // same as admin
                                        showCloseButton: true,
                                        customClass: {
                                            popup: "equip-modal"
                                        }, // apply same modal CSS
                                        html: `
                                    <div class="equip-modal">
                                        <div class="equip-header">
                                            <h2 class="equip-title">Equipment Information</h2>
                                            <hr class="equip-divider">
                                        </div>
                                        <div class="equip-container" style="display:flex; gap:20px;">
                                            <div class="equip-image-box">
                                                <img src="${imgSrc}" alt="${equipmentName}" style="width:140px; height:140px; object-fit:cover; border-radius:5px;">
                                            </div>
                                            <div class="equip-info">
                                                <p><strong>Equipment Name:</strong> ${equipmentName}</p>
                                                <p><strong>Total Units:</strong> ${totalQty}</p>
                                                <p>Available: ${units.filter(u => u.Status.toLowerCase() === "available").length}</p>
                                                <p>Reserved: ${units.filter(u => u.Status.toLowerCase() !== "available").length}</p>
                                            </div>
                                        </div>
                                        <hr class="equip-divider">
                                        <h3 class="unit-status-title">Unit Status</h3>
                                        <div class="unit-list" style="display:flex; flex-wrap:wrap; gap:10px;">
                                            ${unitListHTML}
                                        </div>
                                    </div>
                                `
                                    });
                                })
                                .catch(err => Swal.fire('Error', 'Failed to fetch units: ' + err.message, 'error'));
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
                    // document.querySelectorAll(".room-card").forEach(card => {
                    //     card.addEventListener("click", () => {
                    //         openClassroomModal(); // show modal when clicking any room
                    //     });
                    // });

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

                const reserveBtn = document.getElementById("addBtn");
                const reserveModal = document.getElementById("reserveModal");
                const closeReserveBtn = document.getElementById("closeReserveModal");
                const closeReserveFooter = document.getElementById("closeReserveFooter");

                // Show second modal on reserve click
                reserveBtn.addEventListener("click", function(e) {
                    e.preventDefault(); // prevent form submission
                    reserveModal.classList.add("show");
                });

                // Close second modal
                closeReserveBtn.addEventListener("click", () => reserveModal.classList.remove("show"));
                closeReserveFooter.addEventListener("click", () => reserveModal.classList.remove("show"));

                // Optional: click outside modal closes it
                window.addEventListener("click", (e) => {
                    if (e.target === reserveModal) {
                        reserveModal.classList.remove("show");
                    }
                });


document.addEventListener("DOMContentLoaded", () => {
        const reserveForm = document.getElementById("reserveForm");

        reserveForm.addEventListener("submit", function(e) {
            e.preventDefault(); // stop default form submission

            // Collect data
            const roomID = document.getElementById("roomID").value;
            const subject = document.getElementById("subject").value;
            const timeFrom = document.getElementById("fromTime").value;
            const timeTo = document.getElementById("toTime").value;
            const date = document.getElementById("date").value; // e.g., "2025-11-28"
const section = document.getElementById("section").value;
            // 🌟 1. CALCULATE DAY OF WEEK AND WEEK TYPE 🌟
            const reservationDate = new Date(date);
            
            // Calculate DayOfWeek (e.g., "Friday")
            const dayOfWeek = reservationDate.toLocaleDateString('en-US', { weekday: 'long' });

            // Function to determine if the date falls on an Odd or Even week
            function getWeekType(d) {
                const day = new Date(Date.UTC(d.getFullYear(), d.getMonth(), d.getDate()));
                day.setUTCDate(day.getUTCDate() + 4 - (day.getUTCDay() || 7));
                const yearStart = new Date(Date.UTC(day.getUTCFullYear(), 0, 1));
                const weekNo = Math.ceil((((day - yearStart) / 86400000) + 1) / 7);
                return weekNo % 2 !== 0 ? 'Even' : 'Odd';
            }

            const weekType = getWeekType(reservationDate);
            // 🌟 END CALCULATION 🌟


            const formData = new FormData();
            formData.append("p_RoomID", roomID);
            formData.append("p_UserID", facultyReservationUserID);
            formData.append("p_Subject", subject);
            formData.append("p_Section", section);
            formData.append("p_ReservationDate", date);
            formData.append("p_TimeFrom", timeFrom);
            formData.append("p_TimeTo", timeTo);
            
            // 🌟 2. APPEND THE NOW-DEFINED VARIABLES 🌟
            formData.append("p_DayOfWeek", dayOfWeek);
            formData.append("p_WeekType", weekType);
            
            formData.append("reserveClassroom", 1);

            fetch("faculty-reservation.php", {
                method: "POST",
                body: formData
            })
            .then(res => res.text())
            .then(resp => {
                // ... (rest of your success/error handling)
                if (resp.trim() === "success") {
                    Swal.fire("Success", "Classroom reserved!", "success");
                    document.getElementById("reserveModal").style.display = "none";
                    reserveForm.reset();
                    // You might want to reload your schedule display here
                } else {
                    Swal.fire("Error", resp, "error");
                }
            })
            .catch(err => {
                console.error(err);
                Swal.fire("Error", "Something went wrong", "error");
            });
        });

        // Close modal buttons... (rest of the script)
        // Close modal buttons
        document.getElementById("closeReserveModal").addEventListener("click", () => {
            document.getElementById("reserveModal").style.display = "none";
        });
        // document.getElementById("closeReserveFooter").addEventListener("click", () => {
        //     document.getElementById("reserveModal").style.display = "none";
        // });
    });

        





// ======================
// EQUIPMENT SIDEBAR
// ======================

                document.addEventListener('DOMContentLoaded', () => {
                    const rows = document.querySelectorAll('.equipment-row');

                    rows.forEach(row => {
                        row.addEventListener('click', (e) => {
                            if (e.target.tagName === 'BUTTON') return;

                            const cells = row.querySelectorAll('td');
                            const item = cells[1].innerText;
                            const quantity = parseInt(cells[2].innerText);
                            const status = cells[3].innerText;


                            //Apply backend logic here
                            let unitListHTML = '';
                            for (let i = 0; i < quantity; i++) {
                                const num = i + 1;
                                const isReserved = num <= 4; // placeholder logic
                                unitListHTML += `
                <div class="unit-card ${isReserved ? 'reserved' : 'available'}" data-unit="${num}">
                <span class="dot"></span>
                <span class="unit-label">${item} #${num}</span>
                <span class="unit-status">
                    ${isReserved ? 'Reserved until 3PM' : 'Available'}
                </span>
                </div>
                `;
                            }

                            Swal.fire({
                                width: "650px",
                                heightAuto: false,
                                showConfirmButton: false,
                                showCloseButton: true,
                                closeButtonHtml: '&times;',
                                customClass: {
                                    popup: "equip-modal"
                                },
                                html: `
                <div class="equip-header">
                    <h2 class="equip-title">Equipment Information</h2>
                    <hr class="equip-divider">
                </div>

                <div class="equip-container">

                    <div class="equip-image-box">
                        <img src="https://cdn-icons-png.flaticon.com/512/1048/1048953.png" class="equip-image">
                    </div>

                    <div class="equip-info">

                        <h2 class="equip-name">${item}</h2>
                        <div class="equip-summary">
                            <p><strong>Total Units:</strong> ${quantity}</p>
                            <p><strong>Available:</strong> 3</p>
                            <p><strong>Reserved:</strong> 4</p>
                        </div>

                    </div>
                </div>

                <hr class="equip-divider">
                <h3 class="unit-status-title">Unit Status</h3>
                <div class="unit-list">
                    ${unitListHTML}
                </div>
                `,
                                focusConfirm: false,
                                preConfirm: () => ({
                                    name: document.getElementById("edit-name")?.value ?? "",
                                    qty: document.getElementById("edit-qty")?.value ?? "",
                                    status: document.getElementById("edit-status")?.value ?? ""
                                }),

                                didOpen: () => {
                                    // Select all available units and add click listeners
                                    const availableUnits = Swal.getHtmlContainer().querySelectorAll('.unit-card.available');

                                    availableUnits.forEach(unit => {
                                        unit.addEventListener('click', () => {
                                            const unitNumber = unit.getAttribute('data-unit');
                                            const equipmentName = `${item} #${unitNumber}`;

                                            Swal.fire({
                                                title: `Reserve ${equipmentName}`,
                                                html: `
                <div class="reserve-modal">
                    <div class="section-title">Class Information</div>
                    <hr class="equip-divider">

                    <div class="row">
                        <label>Class Code</label>
                        <input id="reserve-class" class="swal2-input" placeholder="IT202">
                    </div>

                    <div class="row">
                        <label>Subject Name</label>
                        <input id="reserve-subject" class="swal2-input" placeholder="Database Sys">
                    </div>

                    <br>
                    <div class="section-title">Reservation Schedule</div>
                    <hr class="equip-divider">
                    <div class="row">
                        <label>Date</label>
                        <input id="reserve-date" type="date" class="swal2-input">
                    </div>
                    <div class="row">
                        <label>Time</label>
                        <div class="time-range">
                            <input id="reserve-from" type="time" class="swal2-input small-input" placeholder="From">
                            <span class="dash"> - </span>
                            <input id="reserve-to" type="time" class="swal2-input small-input" placeholder="To">
                        </div>
                    </div>


                    <div class="row">
                        <label>Equipment</label>
                        <input id="reserve-equipment" class="swal2-input" value="${equipmentName}" readonly>
                    </div>

                    <hr class="equip-divider">
                    <div class="row">
                        <label>Purpose (optional)</label>
                        <textarea id="reserve-purpose" class="swal2-textarea" placeholder="Presentation for Lab 3"></textarea>
                    </div>
                </div>
                `,

                                                showCancelButton: true,
                                                confirmButtonText: 'Reserve',
                                                cancelButtonText: 'Cancel',
                                                focusConfirm: false,
                                                preConfirm: () => ({
                                                    subject: document.getElementById('reserve-subject')?.value ?? "",
                                                    date: document.getElementById('reserve-date')?.value ?? "",
                                                    time: document.getElementById('reserve-time')?.value ?? "",
                                                    equipment: document.getElementById('reserve-equipment')?.value ?? "",
                                                    purpose: document.getElementById('reserve-purpose')?.value ?? ""
                                                })
                                            }).then(result => {
                                                if (result.isConfirmed) {
                                                    console.log("Reserved:", result.value);

                                                    // Update UI to show reservation
                                                    unit.querySelector('.unit-status').innerText = `Reserved until TBD`;
                                                    unit.classList.remove('available');
                                                    unit.classList.add('reserved');
                                                }
                                            });
                                        });
                                    });
                                }

                            }).then(result => {
                                if (result.isConfirmed) {
                                    console.log("Updated data:", result.value);
                                }
                            });

                        });
                    });
                });

                //Script for the equipment row modal
                // Attach click listener to all rows
                document.querySelectorAll(".equipment-row").forEach(row => {
                    row.addEventListener("click", () => {
                        // Get data attributes from the row
                        const room = row.dataset.room;
                        const capacity = row.dataset.capacity;
                        const status = row.dataset.status;

                        // Trigger SweetAlert
                        Swal.fire({
                            title: `${room}`,
                            html: `
                <div style="text-align:left; font-size:16px;">
                    <p><b>Capacity:</b> ${capacity}</p>
                    <p><b>Status:</b> ${status}</p>
                    <hr>
                    <button id="editBtn" class="swal2-confirm swal2-styled" style="margin-top:10px;">Edit</button>
                </div>
                `,
                            showConfirmButton: false,
                            width: "400px",
                            didOpen: () => {
                                document.getElementById("editBtn").addEventListener("click", () => {
                                    Swal.fire({
                                        title: `Edit ${room}`,
                                        input: "text",
                                        inputLabel: "Change Status",
                                        inputValue: status,
                                        showCancelButton: true,
                                        confirmButtonText: "Save",
                                    });
                                });
                            }
                        });
                    });
                });


                // Select all tab buttons
                const tabButtons = document.querySelectorAll('.tab-btn');

                tabButtons.forEach(btn => {
                    btn.addEventListener('click', () => {
                        // Remove 'active' from all buttons
                        tabButtons.forEach(b => b.classList.remove('active'));

                        // Add 'active' to the clicked button
                        btn.classList.add('active');

                        // Optional: switch content based on data-target
                        const target = btn.getAttribute('data-target');
                        document.querySelectorAll('.tab-content').forEach(content => {
                            content.classList.remove('active');
                        });
                        document.getElementById(target).classList.add('active');
                    });
                });

        

    // Updated equipment reservation modal with date/time + confirmation
    document.addEventListener("click", function(e) {
        if (e.target.classList.contains("reserveBtn")) {
            const unitID = e.target.getAttribute("data-unit-id");
            const equipmentName = e.target.closest(".unit-card").querySelector(".unit-label").innerText;

            // FIRST MODAL: Ask for date & time range
            Swal.fire({
                title: "Reserve Equipment",
                html: `
                    <div class="form-group">
                        <label>Date:</label>
                        <input type="date" id="resDate" class="swal2-input">

                        <label>Start Time:</label>
                        <input type="time" id="startTime" class="swal2-input">

                        <label>End Time:</label>
                        <input type="time" id="endTime" class="swal2-input">
                    </div>
                `,
                confirmButtonText: "Next",
                showCancelButton: true,
                preConfirm: () => {
                    const date = document.getElementById("resDate").value;
                    const timeFrom = document.getElementById("startTime").value;
                    const timeTo = document.getElementById("endTime").value;

                    if (!date || !timeFrom || !timeTo) {
                        Swal.showValidationMessage("Please fill up all fields.");
                        return false;
                    }

                    return { date, timeFrom, timeTo };
                }
            }).then(firstStep => {
                if (!firstStep.isConfirmed) return;

                const { date, timeFrom, timeTo } = firstStep.value;

                // SECOND MODAL: Confirmation with equipment name
                Swal.fire({
                    title: "Confirm Reservation?",
                    html: `
                        <div style="text-align:left; font-size:16px;">
                            <p><strong>Equipment:</strong> ${equipmentName}</p>
                            <p><strong>Date:</strong> ${date}</p>
                            <p><strong>Time:</strong> ${timeFrom} - ${timeTo}</p>
                        </div>
                    `,
                    icon: "question",
                    showCancelButton: true,
                    confirmButtonText: "Yes, reserve"
                }).then(result => {
                    if (!result.isConfirmed) return;

                    // FINAL STEP: Submit reservation to backend
                    fetch("faculty-reservation.php", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/x-www-form-urlencoded"
                        },
                    body: `reserveUnit=1&unitID=${unitID}&date=${date}&timeFrom=${timeFrom}&timeTo=${timeTo}`

                    })
                    .then(res => res.text())
                    .then(response => {
                        if (response.trim() === "success") {
                            Swal.fire("Reserved!", "Your reservation request is now pending.", "success");

                            // Update the UI
                            e.target.innerText = "Reserved";
                            e.target.disabled = true;
                            e.target.closest('.unit-card').classList.remove('available');
                            e.target.closest('.unit-card').classList.add('reserved');
                        } else {
                            Swal.fire("Error", "Failed to reserve.", "error");
                        }
                    });
                });
            });
        }
    });

// =====================   
// //CLASSROOM SIDEBAR
// =====================

     document.querySelectorAll('.building-block').forEach(building => {
                    const floors = building.querySelectorAll('.floor');
                    const indicator = building.querySelector('.floor-indicator');

                    // Function to update indicator
                    const updateIndicator = (floor) => {
                        const position = floor.offsetLeft;
                        const width = floor.offsetWidth;

                        indicator.style.left = position + "px";
                        indicator.style.width = width + "px";
                    }

                    // Default active floor
                    if (floors.length > 0) {
                        const defaultFloor = floors[0];
                        defaultFloor.classList.add('active');
                        updateIndicator(defaultFloor);
                    }

                    // Handle clicks
                    floors.forEach((floor) => {
                        floor.addEventListener('click', () => {
                            floors.forEach(f => f.classList.remove('active'));
                            floor.classList.add('active');
                            updateIndicator(floor);
                        });
                    });

                    // Update indicator on window resize
                    window.addEventListener('resize', () => {
                        const activeFloor = building.querySelector('.floor.active');
                        if (activeFloor) updateIndicator(activeFloor);
                    });
                });


                //script for the rooms
                document.querySelectorAll('.building-block').forEach(buildingBlock => {
                    const floors = buildingBlock.querySelectorAll('.floor');
                    const roomContainers = buildingBlock.querySelectorAll('.room-container');

                    floors.forEach(floor => {
                        floor.addEventListener('click', () => {
                            const floorID = floor.getAttribute('data-floor');

                            // Hide all room containers in this building
                            roomContainers.forEach(container => container.style.display = 'none');

                            // Show the selected floor's room container
                            const target = buildingBlock.querySelector(`.room-container[data-floor="${floorID}"]`);
                            if (target) target.style.display = 'flex'; // or 'block' depending on your layout
                        });
                    });

                    // Optionally show the first floor by default
                    if (floors.length > 0) {
                        const firstFloorID = floors[0].getAttribute('data-floor');
                        const firstContainer = buildingBlock.querySelector(`.room-container[data-floor="${firstFloorID}"]`);
                        if (firstContainer) firstContainer.style.display = 'flex';
                        floors[0].classList.add('active');
                    }
                });


                document.querySelectorAll(".room-card.clickable-room").forEach(card => {
        card.addEventListener("click", () => {
            const roomID = card.getAttribute("data-room"); // make sure to set this in PHP
            const roomNumber = card.querySelector(".room-number").innerText;
            window.currentRoomID = roomID;

              const roomInput = document.getElementById("roomID");
        if (roomInput) roomInput.value = roomID;
        
            // DEBUG: check if roomID is correctly set
            console.log("Clicked roomID:", roomID);
            console.log("window.currentRoomID:", window.currentRoomID);

            // Set currentDay to today's day automatically
            const days = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
            const today = days[new Date().getDay()];
            window.currentDay = today;

            // Also update the dayFilter dropdown to match today
            const daySelect = document.getElementById("dayFilter");
            if (daySelect) daySelect.value = today;

            // Open modal
            const classroomModal = document.getElementById("classroomModal");
            classroomModal.classList.add("show");

            // Update modal title with room number
            document.querySelector("#classroomModal .custom-modal-title").innerText = `Classroom Schedule - Room ${roomNumber}`;

            // Load today's schedules
            loadSchedules(today);
        });
    });

                document.addEventListener("click", function(e) {
                    const btn = e.target.closest(".cancelBtn"); // button or child icon
                    if (!btn) return; // not a cancel button click

                    const reservationID = btn.dataset.id; // always use btn
                    const row = btn.closest("tr");

                    Swal.fire({
                        title: "Cancel this reservation?",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonText: "Yes, cancel",
                    }).then(result => {
                        if (result.isConfirmed) {
                            fetch("../pages/faculty-reservation.php", {
                                    method: "POST",
                                    headers: {
                                        "Content-Type": "application/x-www-form-urlencoded"
                                    },
                                    body: `action=cancelReservation&reservationID=${reservationID}`
                                })
                                .then(res => res.json())
                                .then(data => {
                                    if (data.status === "success") {
                                        Swal.fire("Cancelled!", "Your reservation has been cancelled.", "success");
                                        row.remove(); // remove the row
                                    } else {
                                        Swal.fire("Error", data.message || "Something went wrong.", "error");
                                    }
                                })
                                .catch(err => {
                                    console.error(err);
                                    Swal.fire("Error", "Network or server error", "error");
                                });
                        }
                    });
                });

                document.getElementById("dayFilter").addEventListener("change", function() {
        window.currentDay = this.value;
        loadSchedules(this.value);
    });


                function loadSchedules(day) {
        if (!window.currentRoomID) return;

        const weekType = weekBtn.textContent.includes("Odd") ? "Odd" : "Even";

        fetch("../pages/faculty-reservation.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `action=getSchedules&roomID=${window.currentRoomID}&dayOfWeek=${day}&weekType=${weekType}`
        })
        .then(res => res.json())
        .then(schedules => {
            const tbody = document.querySelector(".classSchedTable tbody");
            tbody.innerHTML = "";

            if (schedules.length === 0) {
                tbody.innerHTML = `<tr><td colspan="5" class="text-center">No schedule found</td></tr>`;
            } else {
                schedules.forEach(s => {
                    tbody.innerHTML += `
                        <tr>
                            <td>${s.Instructor}</td>
                            <td>${s.Subject}</td>
                            <td>${s.TimeFrom} - ${s.TimeTo}</td>
                            <td>${s.Section}</td>
                            <td>${s.ReserveDate}</td>
                        </tr>`;
                });
            }
        })
        .catch(err => console.error("Failed to fetch schedules:", err));
    }

                   
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

        fetch("../pages/faculty-reservation.php", {
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
