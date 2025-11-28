   //Script for the room schedule modal

        document.querySelectorAll(".room-card.clickable-room").forEach(roomCard => {
            roomCard.addEventListener("click", () => {
                const roomID = roomCard.getAttribute("data-room");

                Swal.fire({
                    title: "Room Schedule",
                    width: "800px",
                    html: `
  <div style="text-align:left; font-size:14px;">
    <div style="font-size:18px; font-weight:bold; margin-bottom:5px;">
      Room: ${roomID}
      <hr style="margin:0 0 5px 0;">
    </div>

    <br>

    <!-- Day of the week dropdown -->
   <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
  
  <!-- LEFT SIDE (Day dropdown) -->
  <div>
    <label for="daySelect" style="font-weight:bold;">Select Day:</label>
    <select id="daySelect" style="margin-left:5px; padding:3px; border-radius:5px;">
      <option value="">--Choose Day--</option>
      <option value="Monday">Monday</option>
      <option value="Tuesday">Tuesday</option>
      <option value="Wednesday">Wednesday</option>
      <option value="Thursday">Thursday</option>
      <option value="Friday">Friday</option>
      <option value="Saturday">Saturday</option>
      <option value="Sunday">Sunday</option>
    </select>
  </div>

  <!-- RIGHT SIDE (Button) -->
  <button id="addScheduleBtn" style="padding:5px 10px; border-radius:10px">+ Add Schedule</button>
</div>


    <div style="max-height:250px; overflow-y:auto; border:1px solid #ccc; border-radius:5px; padding:5px;">
      <table id="scheduleTable" style="width:100%; border-collapse:collapse; align-items:center; font-size:16px;">
        <thead>
          <tr style="background:#eee; font-weight:bold;">
            <th>Subject</th>
            <th>Instructor</th>
            <th>From</th>
            <th>To</th>
            <th>Section</th>
            <th style="width:120px;">Actions</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>
`,
                    showCancelButton: true,
                    confirmButtonText: "Save",
                    cancelButtonText: "Cancel",
                    didOpen: () => {
                        const daySelect = Swal.getPopup().querySelector('#daySelect');
                        const table = Swal.getPopup().querySelector("#scheduleTable tbody");

                        function loadSchedules(selectedDay) {
                            table.innerHTML = ""; // Clear table
                            const weekType = weekBtn.textContent.includes("Odd") ? "Odd" : "Even";

                            fetch("", {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/x-www-form-urlencoded"
                                },
                                body: `action=getSchedules&roomID=${roomID}&dayOfWeek=${encodeURIComponent(selectedDay)}&weekType=${weekType}`
                            })

                                .then(res => res.json())
                                .then(schedules => {
                                    if (schedules.length === 0) {
                                        const emptyRow = document.createElement("tr");
                                        emptyRow.innerHTML = `<td colspan="6" style="text-align:center;">No schedules available</td>`;
                                        table.appendChild(emptyRow);
                                    } else {
                                        schedules.forEach(s => {
                                            const row = document.createElement("tr");
                                            row.innerHTML = `
                    <td>${s.Subject}</td>
                    <td>${s.Instructor}</td>
                    <td>${s.TimeFrom}</td>
                    <td>${s.TimeTo}</td>
                    <td>${s.Section}</td>
                    <td class="text-center cell-padding">
                        <button class="delete-btn" data-id="${s.UserID}">
                            <i class="bi bi-trash-fill delete-icon"></i>
                        </button>
                    </td>

                `;
                                            table.appendChild(row);
                                        });
                                    }
                                });
                        }

                        // Set current day as default
                        const days = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
                        const today = new Date();
                        daySelect.value = days[today.getDay()];

                        // Load schedules for the default day
                        loadSchedules(daySelect.value);

                        // Reload schedules whenever day changes
                        daySelect.addEventListener('change', function () {
                            loadSchedules(this.value);
                        });




                        // Inside didOpen
                        const addScheduleBtn = Swal.getPopup().querySelector("#addScheduleBtn");
                        addScheduleBtn.addEventListener("click", () => {
                            const table = Swal.getPopup().querySelector("#scheduleTable tbody");

                            // Remove "No schedules available" row if it exists
                            const noSchedulesRow = table.querySelector('tr td[colspan="6"]');
                            if (noSchedulesRow) table.innerHTML = "";

                            // Create a new editable row
                            const newRow = document.createElement("tr");
                            newRow.classList.add("new-schedule"); // mark it as new
                            newRow.innerHTML = `
    <td contenteditable="true" class="editable" data-placeholder="Enter Subject"></td>
    <td contenteditable="true" class="editable" data-placeholder="Enter Instructor"></td>
    <td><input type="time" style="width:80%;"></td>
    <td><input type="time" style="width:80%;"></td>
    <td contenteditable="true" class="editable" data-placeholder="Enter Section"></td>
    <td><button class="deleteRowBtn"><i class="bi bi-x"></i></button></td>
`;
                            table.appendChild(newRow);


                            // Add delete functionality
                            newRow.querySelector(".deleteRowBtn").addEventListener("click", () => {
                                newRow.remove();
                                if (table.rows.length === 0) {
                                    const emptyRow = document.createElement("tr");
                                    emptyRow.innerHTML = `<td colspan="6" style="text-align:center;">No schedules available</td>`;
                                    table.appendChild(emptyRow);
                                }
                            });
                        });

                    },

                    preConfirm: () => {
                        const popup = Swal.getPopup();
                        const rows = popup.querySelectorAll("#scheduleTable tbody tr.new-schedule"); // only new rows
                        let hasError = false;
                        const schedules = [];

                        rows.forEach(row => {
                            const subject = row.cells[0].innerText.trim();
                            const instructor = row.cells[1].innerText.trim();
                            const timeFromInput = row.cells[2].querySelector("input");
                            const timeToInput = row.cells[3].querySelector("input");
                            const timeFrom = timeFromInput ? timeFromInput.value : row.cells[2].innerText.trim();
                            const timeTo = timeToInput ? timeToInput.value : row.cells[3].innerText.trim();
                            const section = row.cells[4].innerText.trim();

                            if (!subject && !instructor && !timeFrom && !timeTo && !section) return;

                            if (!subject || !instructor || !timeFrom || !timeTo || !section) {
                                hasError = true;
                                return;
                            }

                            schedules.push({
                                subject,
                                instructor,
                                timeFrom,
                                timeTo,
                                section
                            });
                        });

                        if (hasError) {
                            Swal.showValidationMessage("All fields must be filled out before saving!");
                            return false;
                        }

                        return schedules;
                    }


                }).then(result => {
                    if (result.isConfirmed) {
                        const schedules = result.value;

                        schedules.forEach(s => {
                            const formData = new URLSearchParams();
                            formData.append("action", "addSchedule");
                            formData.append("roomID", roomID);
                            formData.append("subject", s.subject);
                            formData.append("instructor", s.instructor);
                            formData.append("timeFrom", s.timeFrom);
                            formData.append("timeTo", s.timeTo);
                            formData.append("section", s.section);
                            formData.append("weekType", weekBtn.textContent.includes("Odd") ? "Odd" : "Even");
                            formData.append("dayOfWeek", daySelect.value);

                            fetch("", {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/x-www-form-urlencoded"
                                },
                                body: formData.toString()
                            })
                                .then(res => res.text())
                                .then(res => {
                                    if (res.trim() !== "success") console.error("Failed to add schedule:", res);
                                })
                                .catch(err => console.error("Error:", err));
                        });

                        Swal.fire({
                            icon: "success",
                            title: "Schedules saved successfully!",
                            confirmButtonText: "OK"
                        }).then(() => window.location.reload());
                    }
                });
            });
        });



        //Script for the add room button
        document.querySelectorAll(".add-room-btn").forEach(button => {
            button.addEventListener("click", () => {
                const floorID = button.getAttribute("data-floor");

                Swal.fire({
                    title: "Add Room",
                    html: `
        <input type="number" id="roomNumber" class="swal2-input" placeholder="Enter Room Number" required>
      `,
                    confirmButtonText: "Add",
                    showCancelButton: true,
                    cancelButtonText: "Cancel",
                    preConfirm: () => {
                        const roomNumber = Swal.getPopup().querySelector("#roomNumber").value.trim();
                        if (!roomNumber) {
                            Swal.showValidationMessage("Please enter a room number");
                            return false;
                        }
                        return {
                            floorID,
                            roomNumber
                        };
                    }
                }).then(result => {
                    if (result.isConfirmed) {
                        const data = result.value;

                        // AJAX request to same file
                        fetch("", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/x-www-form-urlencoded"
                            },
                            body: `action=addRoom&floorID=${encodeURIComponent(data.floorID)}&roomNumber=${encodeURIComponent(data.roomNumber)}`
                        })
                            .then(response => response.text())
                            .then(res => {
                                if (res.trim() === "success") {
                                    Swal.fire({
                                        icon: "success",
                                        title: "Room added successfully!",
                                        confirmButtonText: "OK"
                                    }).then(() => window.location.reload());
                                } else {
                                    Swal.fire({
                                        icon: "error",
                                        title: "Failed to add room",
                                        text: res
                                    });
                                }
                            })
                            .catch(err => {
                                Swal.fire({
                                    icon: "error",
                                    title: "Error",
                                    text: err
                                });
                            });
                    }
                });
            });
        });

        // Script for an interactive floor indicator
        document.querySelectorAll('.building-block').forEach(building => {
            const floors = building.querySelectorAll('.floor');
            const indicator = building.querySelector('.floor-indicator');

            //Default active floor 
            if (floors.length > 0) {
                const defaultFloor = floors[0];
                defaultFloor.classList.add('active');

                const position = defaultFloor.offsetLeft;
                const width = defaultFloor.offsetWidth;

                indicator.style.left = position + "px";
                indicator.style.width = width + "px";
            }

            // Handle user clicks
            floors.forEach((floor) => {
                floor.addEventListener('click', () => {
                    floors.forEach(f => f.classList.remove('active'));
                    floor.classList.add('active');

                    const position = floor.offsetLeft;
                    const width = floor.offsetWidth;

                    indicator.style.left = position + "px";
                    indicator.style.width = width + "px";
                });
            });
        });


        //SWAL for the add building button
        document.querySelectorAll('.addBuilding-btn').forEach(button => {
            button.addEventListener('click', () => {
                Swal.fire({
                    title: 'Add Building',
                    html: `
                <input type="text" id="buildingName" class="swal2-input" placeholder="Building Name">
                <input type="file" id="buildingImage" class="swal2-input" accept="image/*" style="flex:1;">
            `,
                    showCancelButton: true,
                    confirmButtonText: 'Save',
                    cancelButtonText: 'Close',
                    focusConfirm: false,
                    preConfirm: () => {
                        const name = Swal.getPopup().querySelector('#buildingName').value;
                        if (!name) {
                            Swal.showValidationMessage('Please enter a building name');
                        }
                        return name;
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        const buildingName = result.value;
                        const fileInput = document.getElementById("buildingImage");
                        const file = fileInput.files[0];

                        const formData = new FormData();
                        formData.append("action", "addBuilding");
                        formData.append("buildingName", buildingName);
                        if (file) formData.append("buildingImage", file);

                        fetch("", {
                            method: "POST",
                            body: formData
                        })
                            .then(response => response.json()) // parse JSON
                            .then(res => {
                                if (res.status === "success") {
                                    Swal.fire({
                                        icon: "success",
                                        title: "Building added successfully!",
                                        confirmButtonText: "OK"
                                    }).then(() => window.location.reload());
                                } else {
                                    Swal.fire({
                                        icon: "error",
                                        title: "Failed to add building",
                                        text: res.message
                                    });
                                }
                            })
                            .catch(err => {
                                Swal.fire({
                                    icon: "error",
                                    title: "Error",
                                    text: err
                                });
                            });

                    }
                });
            });
        });




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
            document.getElementById('time').textContent = `${day}, ${hours}:${minutes}:${seconds} ${ampm}`;
        }

        // Update every second
        setInterval(updateTimeDay, 1000);

        // Initial call
        updateTimeDay();

        // SWAL for the Add Floor button
        document.querySelectorAll(".add-floor").forEach(button => {
            button.addEventListener("click", () => {
                const buildingID = button.getAttribute("data-building");

                Swal.fire({
                    title: "Add Floor",
                    html: `<p>Click Add to create a new floor automatically.</p>`,
                    confirmButtonText: "Add Floor",
                    showCancelButton: true,
                    cancelButtonText: "Cancel",
                    preConfirm: () => {
                        return {
                            buildingID
                        }; // only send buildingID
                    }
                }).then(result => {
                    if (result.isConfirmed) {
                        const data = result.value;

                        fetch("", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/x-www-form-urlencoded"
                            },
                            body: `action=addFloor&buildingID=${encodeURIComponent(data.buildingID)}`
                        })
                            .then(response => response.text())
                            .then(res => {
                                if (res.trim() === "success") {
                                    Swal.fire({
                                        icon: "success",
                                        title: "Floor added successfully!",
                                        confirmButtonText: "OK"
                                    }).then(() => window.location.reload());
                                } else {
                                    Swal.fire({
                                        icon: "error",
                                        title: "Failed to add floor",
                                        text: res
                                    });
                                }
                            })
                            .catch(err => {
                                Swal.fire({
                                    icon: "error",
                                    title: "Error",
                                    text: err
                                });
                            });
                    }
                });
            });
        });


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

        document.querySelectorAll('.editBuilding-btn').forEach(button => {
            button.addEventListener('click', () => {
                const buildingID = button.getAttribute('data-id');
                const buildingName = button.getAttribute('data-name');
                const currentImage = button.getAttribute('data-image');

                Swal.fire({
                    title: 'Edit Building',
                    html: `
                <input type="text" id="buildingName" class="swal2-input" placeholder="Building Name" value="${buildingName}">
                <input type="file" id="buildingImage" class="swal2-input" accept="image/*" style="flex:1;">
            `,
                    showCancelButton: true,
                    confirmButtonText: 'Save',
                    cancelButtonText: 'Close',
                    focusConfirm: false,
                    preConfirm: () => {
                        const name = Swal.getPopup().querySelector('#buildingName').value;
                        if (!name) {
                            Swal.showValidationMessage('Please enter a building name');
                        }
                        return name;
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        const name = result.value;
                        const fileInput = document.getElementById("buildingImage");
                        const file = fileInput.files[0];

                        const formData = new FormData();
                        formData.append("action", "editBuilding");
                        formData.append("buildingID", buildingID);
                        formData.append("buildingName", name);
                        if (file) formData.append("buildingImage", file);

                        fetch("", {
                            method: "POST",
                            body: formData
                        })
                            .then(response => response.json())
                            .then(res => {
                                if (res.status === "success") {
                                    Swal.fire({
                                        icon: "success",
                                        title: "Building updated successfully!",
                                        confirmButtonText: "OK"
                                    }).then(() => window.location.reload());
                                } else {
                                    Swal.fire({
                                        icon: "error",
                                        title: "Failed to update building",
                                        text: res.message
                                    });
                                }
                            })
                            .catch(err => {
                                Swal.fire({
                                    icon: "error",
                                    title: "Error",
                                    text: err
                                });
                            });
                    }
                });
            });
        });

// --------------------------
// //Room Status Logic//
// --------------------------
        function toMinutes(timeString) {
    const [hours, minutes, seconds] = timeString.split(':');
    return parseInt(hours) * 60 + parseInt(minutes);
}

function loadRoomStatuses() {
    const weekType = localStorage.getItem("selectedWeek") || "Odd";

    document.querySelectorAll(".clickable-room").forEach(roomCard => {
        const roomID = roomCard.dataset.room;

        fetch("../pages/class-management.php", {
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
