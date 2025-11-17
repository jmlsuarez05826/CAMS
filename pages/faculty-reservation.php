<?php
require_once '../pages/camsdatabase.php';
require_once '../pages/cams-sp.php';


$crud = new Crud();

$buildings = $crud->getBuildings();
$floors = $crud->getFloors();
$rooms = $crud->getRooms();

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Reservation</title>

    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link rel="stylesheet" href="../assets/css/faculty-reservation.css">


</head>

<body>
    <header>
        <div class="topbar">
            <h2 class="system-title">Classroom Management <br>System</h2>

            <div class="search-field">
                <i class="bi bi-search search-icon"></i>
                <input type="text" placeholder="Search">
            </div>

            <div class="topbar-right">
                <div class="notification-icon">
                    <i class="bi bi-bell-fill"></i>
                </div>

                <div class="profile-info">
                    <i class="bi bi-person-circle profile-icon"></i>
                    <div class="profile-text">
                        <p class="profile-name">Mark Cristopher</p>
                        <p class="profile-number">093480324</p>
                    </div>

                    <!-- Dropdown arrow -->
                    <i class="bi bi-caret-down-fill dropdown-icon"></i>

                    <!-- Dropdown menu -->
                    <div class="profile-dropdown">
                        <p class="logout">Logout</p>
                    </div>
                </div>


            </div>
        </div>

        <div class="separator"></div>
    </header>

    <main>
        <section class="main-top">
            <h2 class="main-title">Reservations</h2>

            <div class="main-options">
                <button class="tab-btn active" data-target="classrooms">Classrooms</button>
                <button class="tab-btn" data-target="equipments">Equipments</button>
                <button class="tab-btn" data-target="reservations">Reservations</button>
            </div>
        </section>

        <section class="main-content">

            <div class="table-contents">
                <div id="classrooms" class="tab-content active">
                    <div class="tab-scroll">
                        <!-- Classroom table here -->

                        <?php foreach ($buildings as $index => $building): ?>
                            <div class="building-title">
                                <h3><?= htmlspecialchars($building['BuildingName']) ?></h3>
                                <?php if ($index === 0): ?>
                                    <!-- This button only appears on the first building because of the condition -->

                                <?php endif; ?>
                            </div>

                            <div class="building-block">

                                <!-- Floor Container -->
                                <div class="floor-container">
                                    <?php foreach ($floors as $floor): ?>
                                        <?php if ($floor['BuildingID'] == $building['BuildingID']): ?>
                                            <div class="floor" data-floor="<?= htmlspecialchars($floor['FloorID']) ?>">
                                                Floor <?= htmlspecialchars($floor['FloorNumber']) ?>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                    <div class="floor-indicator"></div>
                                </div>

                                <!-- Room Containers for each floor -->
                                <?php foreach ($floors as $floor): ?>
                                    <?php if ($floor['BuildingID'] == $building['BuildingID']): ?>
                                        <div class="room-container" data-floor="<?= htmlspecialchars($floor['FloorID']) ?>" style="display:none;">
                                            <?php foreach ($rooms as $room): ?>
                                                <?php if ($room['FloorID'] == $floor['FloorID']): ?>
                                                    <div class="room-card">
                                                        <div class="room-label">Room no</div>
                                                        <div class="room-number"><?= htmlspecialchars($room['RoomNumber']) ?></div>
                                                        <hr>
                                                        <div class="room-status">Available</div>
                                                    </div>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- The Equipment data content starts here -->
                <div id="equipments" class="tab-content">
                    <div class="table-wrapper">
                        <div class="table-scroll">
                            <table class="equipment-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Equipment Name</th>
                                        <th>Quantity</th>
                                        <th>Status</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="equipment-row"
                                        data-room="Projector"
                                        data-capacity="5"
                                        data-status="Available">
                                        <td>1</td>
                                        <td>Projector</td>
                                        <td>10</td>
                                        <td><span class="badge bg-success">Available</span></td>
                                    </tr>

                                    <tr class="equipment-row">
                                        <td>2</td>
                                        <td>Viewboard</td>
                                        <td>7</td>
                                        <td><span class="badge bg-success">Available</span></td>

                                    </tr>
                                    <tr class="equipment-row">
                                        <td>2</td>
                                        <td>Viewboard</td>
                                        <td>7</td>
                                        <td><span class="badge bg-success">Available</span></td>

                                    </tr>
                                    <tr class="equipment-row">
                                        <td>2</td>
                                        <td>Viewboard</td>
                                        <td>7</td>
                                        <td><span class="badge bg-success">Available</span></td>

                                    </tr>
                                    <tr class="equipment-row">
                                        <td>2</td>
                                        <td>Viewboard</td>
                                        <td>7</td>
                                        <td><span class="badge bg-success">Available</span></td>
                                    </tr>
                                    <tr class="equipment-row">
                                        <td>2</td>
                                        <td>Viewboard</td>
                                        <td>7</td>
                                        <td><span class="badge bg-success">Available</span></td>

                                    </tr>
                                    <tr class="equipment-row">
                                        <td>2</td>
                                        <td>Viewboard</td>
                                        <td>7</td>
                                        <td><span class="badge bg-success">Available</span></td>

                                    </tr>
                                    <tr class="equipment-row">
                                        <td>2</td>
                                        <td>Viewboard</td>
                                        <td>7</td>
                                        <td><span class="badge bg-success">Available</span></td>

                                    </tr>
                                    <tr class="equipment-row">
                                        <td>2</td>
                                        <td>Viewboard</td>
                                        <td>7</td>
                                        <td><span class="badge bg-success">Available</span></td>

                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>

            <div id="reservations" class="tab-content">
                <!-- Reservation table here -->
                <div class="table-wrapper">
                    <div class="table-scroll">
                        <table class="reservation-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Reservation </th>
                                    <th>Quantity</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>Projector</td>
                                    <td>5</td>
                                    <td><span class="badge bg-success">Available</span></td>
                                    <td>Reserve</td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>Viewboard</td>
                                    <td>7</td>
                                    <td><span class="badge bg-success">Available</span></td>
                                    <td>Reserve</td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>Viewboard</td>
                                    <td>7</td>
                                    <td><span class="badge bg-success">Available</span></td>
                                    <td>Reserve</td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>Viewboard</td>
                                    <td>7</td>
                                    <td><span class="badge bg-success">Available</span></td>
                                    <td>Reserve</td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>Viewboard</td>
                                    <td>7</td>
                                    <td><span class="badge bg-success">Available</span></td>
                                    <td>Reserve</td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>Viewboard</td>
                                    <td>7</td>
                                    <td><span class="badge bg-success">Available</span></td>
                                    <td>Reserve</td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>Viewboard</td>
                                    <td>7</td>
                                    <td><span class="badge bg-success">Available</span></td>
                                    <td>Reserve</td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>Viewboard</td>
                                    <td>7</td>
                                    <td><span class="badge bg-success">Available</span></td>
                                    <td>Reserve</td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>Viewboard</td>
                                    <td>7</td>
                                    <td><span class="badge bg-success">Available</span></td>
                                    <td>Reserve</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
            </div>
        </section>

    </main>

    <script>
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
                                            classCode: document.getElementById('reserve-class')?.value ?? "",
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

        //script for the red floor indicator
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
    </script>


</body>


</html>