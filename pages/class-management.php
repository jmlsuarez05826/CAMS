<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <link rel="stylesheet" href="../assets/css/class-management.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>



    <?php
    require_once '../includes/admin-sidebar.php';
    ?>

</head>

<body>

    <div class="topbar">
        <h2>Welcome Admin!</h2>

        <div class="topbar-right">
            <div class="search-container">
                <i class="bi bi-search search-icon"></i>
                <input type="text" placeholder="Search" class="search-field">
                <div class="notification-wrapper">
                    <i class="bi bi-bell-fill notification-icon"></i>
                </div>
            </div>
            <div id="time"></div>
        </div>

    </div>


    <div class="building-title">
        <h3>Building 1</h3>
    </div>

    <div class="building-block">

        <!-- The Container goes here-->

        <!-- White container for floors -->
        <div class="floor-container">
            <div class="floor">Floor 1</div>
            <div class="floor">Floor 2</div>
            <div class="floor">Floor 3</div>
            <button class="add-floor">+ Add Floor</button>
            <div class="floor-indicator"></div>
        </div>

        <!-- Apply loops, these are just placeholders-->

        <!-- Room Containers -->
        <div class="room-container">
            <!-- Add Room button as its own white card -->
            <!-- Add Room Card -->
            <div class="room-card add-room-btn">
                <i class="bi bi-door-open"></i>
                <span>Add Room</span>
            </div>



            <!-- Individual room cards -->
            <div class="room-card">
                <div class="room-label">Room no</div>
                <div class="room-number">101</div>
                <hr> <!-- horizontal line -->
                <div class="room-status">Available</div>
            </div>

            <div class="room-card">
                <div class="room-label">Room no</div>
                <div class="room-number">102</div>
                <hr> <!-- horizontal line -->
                <div class="room-status">Available</div>
            </div>

            <div class="room-card">
                <div class="room-label">Room no</div>
                <div class="room-number">103</div>
                <hr> <!-- horizontal line -->
                <div class="room-status">Available</div>
            </div>

        </div>
    </div>

    <div class="building-title">
        <h3>Building 2</h3>
    </div>

    <div class="building-block">

        <!-- The Container goes here-->

        <!-- White container for floors -->
        <div class="floor-container">
            <div class="floor">Floor 1</div>
            <div class="floor">Floor 2</div>
            <div class="floor">Floor 3</div>
            <button class="add-floor">+ Add Floor</button>
            <div class="floor-indicator"></div>
        </div>

        <!-- Apply loops, these are just placeholders-->

        <!-- Room Containers -->
        <div class="room-container">
            <!-- Add Room button as its own white card -->
            <div class="room-card add-room-btn">
                <i class="bi bi-door-open"></i>
                <span>Add Room</span>
            </div>

            <!-- Individual room cards -->
            <div class="room-card">
                <div class="room-label">Room no</div>
                <div class="room-number">101</div>
                <hr> <!-- horizontal line -->
                <div class="room-status">Available</div>
            </div>

            <div class="room-card">
                <div class="room-label">Room no</div>
                <div class="room-number">102</div>
                <hr> <!-- horizontal line -->
                <div class="room-status">Available</div>
            </div>

            <div class="room-card">
                <div class="room-label">Room no</div>
                <div class="room-number">103</div>
                <hr> <!-- horizontal line -->
                <div class="room-status">Available</div>
            </div>

        </div>


        <div class="building-title">
            <h3>Building 2</h3>
        </div>

        <div class="building-block">

            <!-- The Container goes here-->

            <!-- White container for floors -->
            <div class="floor-container">
                <div class="floor">Floor 1</div>
                <div class="floor">Floor 2</div>
                <div class="floor">Floor 3</div>
                <button class="add-floor">+ Add Floor</button>
                <div class="floor-indicator"></div>
            </div>

            <!-- Apply loops, these are just placeholders-->

            <!-- Room Containers -->
            <div class="room-container">
                <!-- Add Room button as its own white card -->
                <div class="room-card add-room-btn">
                    <i class="bi bi-door-open"></i>
                    <span>Add Room</span>
                </div>

                <!-- Individual room cards -->
                <div class="room-card">
                    <div class="room-label">Room no</div>
                    <div class="room-number">101</div>
                    <hr> <!-- horizontal line -->
                    <div class="room-status">Available</div>


                </div>

                <div class="room-card">
                    <div class="room-label">Room no</div>
                    <div class="room-number">102</div>
                    <hr> <!-- horizontal line -->
                    <div class="room-status">Available</div>
                </div>

                <div class="room-card">
                    <div class="room-label">Room no</div>
                    <div class="room-number">103</div>
                    <hr> <!-- horizontal line -->
                    <div class="room-status">Available</div>
                </div>

            </div>

        </div>


        <script>
            //SWAL for the add room button
            document.querySelectorAll(".add-room-btn").forEach(button => {
                button.addEventListener("click", () => {
                    Swal.fire({
                        title: "Add Room Schedule",
                        width: "800px",
                        html: `
            <div style="text-align:left; font-size:14px;">

             <div style="font-size:18px; font-weight:bold; margin-bottom:5px;">
        Building 1
    </div>
    <hr style="margin:0 0 5px 0;">

    <br>
                <div style="max-height:250px; overflow-y:auto; border:1px solid #ccc; border-radius:5px; padding:5px;">
                    <table id="scheduleTable" style="width:100%; border-collapse:collapse; font-size:16px;">
                        <thead>
                            <tr style="background:#eee; font-weight:bold;">
                                <th>Subject</th>
                                <th>Instructor</th>
                                <th>Time</th>
                                <th>Section</th>
                                <th style="width:60px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td contenteditable="true">TI101</td>
                                <td contenteditable="true">Mr. A</td>
                                <td contenteditable="true">1-2pm</td>
                                <td contenteditable="true">Sec1</td>
                                <td>
                                    <button class="addRowBtn">+</button>
                                    <button class="deleteRowBtn">X</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            `,
                        confirmButtonText: "Submit",
                        showCancelButton: true,
                        cancelButtonText: "Cancel",

                        didOpen: () => {
                            const popup = Swal.getPopup();

                            // Enable / disable building name (optional)
                            const enableBuilding = popup.querySelector("#enableBuilding");
                            const buildingInput = popup.querySelector("#buildingName");
                            if (enableBuilding) {
                                enableBuilding.addEventListener("change", e => {
                                    buildingInput.disabled = !e.target.checked;
                                });
                            }

                            // Event delegation for add/delete buttons
                            const table = popup.querySelector("#scheduleTable tbody");
                            if (table) {
                                table.addEventListener("click", e => {
                                    const target = e.target;

                                    if (target.classList.contains("addRowBtn")) {
                                        const newRow = document.createElement("tr");
                                        newRow.innerHTML = `
                    <td contenteditable="true"></td>
                    <td contenteditable="true"></td>
                    <td contenteditable="true"></td>
                    <td contenteditable="true"></td>
                    <td>
                        <button class="addRowBtn">+</button>
                        <button class="deleteRowBtn">X</button>
                    </td>
                `;
                                        table.appendChild(newRow);
                                    }

                                    if (target.classList.contains("deleteRowBtn")) {
                                        if (table.rows.length > 1) target.closest("tr").remove();
                                    }
                                });
                            }
                        }

                    });
                });
            });

            // Select all existing room cards except the add-room card
            document.querySelectorAll(".room-card:not(.add-room-btn)").forEach(card => {
                card.addEventListener("click", () => {
                    const roomNumber = card.querySelector(".room-number")?.textContent.trim() || "";
                    const roomStatus = card.querySelector(".room-status")?.textContent.trim() || "";


                    Swal.fire({
                        title: `Edit Room ${roomNumber}`,
                        width: "600px",
                        html: `
<div style="text-align:left; font-size:14px;">

<div style="text-align:left; font-size:14px;">

             <div style="font-size:18px; font-weight:bold; margin-bottom:5px;">
        Building 1
    </div>
    <hr style="margin:0 0 5px 0;">

    <br>
    <table id="editRoomTable" style="width:100%; border-collapse:collapse; font-size:13px;">
        <thead>
            <tr style="background:#eee; font-weight:bold;">
                <th>Room Number</th>
                <th>Status</th>
                <th>Instructor</th>
                <th>Subject</th>
                <th>Section</th>
                <th>Schedule</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td contenteditable="true">${roomNumber}</td>
                <td contenteditable="true">Available</td>
                <td contenteditable="true"></td>
                <td contenteditable="true"></td>
                <td contenteditable="true"></td>
                <td contenteditable="true"></td>
            </tr>
        </tbody>
    </table>
</div>
`,
                        confirmButtonText: "Save",
                        showCancelButton: true,
                        cancelButtonText: "Cancel",
                        didOpen: () => {
                            const popup = Swal.getPopup();

                            // Add row functionality
                            popup.querySelector("#editRoomTable").addEventListener("click", function(e) {
                                const table = popup.querySelector("#editRoomTable tbody");

                                if (e.target.classList.contains("addRowBtn")) {
                                    const row = document.createElement("tr");
                                    row.innerHTML = `
                            <td contenteditable="true"></td>
                            <td contenteditable="true"></td>
                            <td>
                                <button class="addRowBtn">+</button>
                                <button class="deleteRowBtn">X</button>
                            </td>
                        `;
                                    table.appendChild(row);
                                }

                                if (e.target.classList.contains("deleteRowBtn")) {
                                    if (table.rows.length > 1) e.target.closest("tr").remove();
                                }
                            });
                        },
                        preConfirm: () => {
                            const popup = Swal.getPopup();
                            const rows = [...popup.querySelectorAll("#editRoomTable tbody tr")].map(row => ({
                                roomNumber: row.cells[0].textContent.trim(),
                                status: row.cells[1].textContent.trim(),
                                instructor: row.cells[2].textContent.trim(),
                                subject: row.cells[3].textContent.trim(),
                                section: row.cells[4].textContent.trim(),
                                schedule: row.cells[5].textContent.trim()
                            }));

                            if (rows.some(r => !r.roomNumber || !r.status)) {
                                Swal.showValidationMessage("Please fill in Room Number and Status");
                                return false;
                            }

                            return rows;
                        }

                    });
                });
            });



            // Script for the time
            function updateTime() {
                const now = new Date();
                const hours = String(now.getHours()).padStart(2, '0');
                const minutes = String(now.getMinutes()).padStart(2, '0');
                const seconds = String(now.getSeconds()).padStart(2, '0');
                document.getElementById('time').textContent = `${hours}:${minutes}:${seconds}`;
            }

            // Update every second
            setInterval(updateTime, 1000);

            // Initial call
            updateTime();


            //script for an interative add floor button
            document.querySelectorAll('.building-block').forEach(building => { //Use loops to display buildings

                const floors = building.querySelectorAll('.floor');
                const indicator = building.querySelector('.floor-indicator');

                floors.forEach((floor, index) => {
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
        </script>

</body>




</html>