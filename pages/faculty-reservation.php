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
                <link href='https://fonts.googleapis.com/css?family=Poppins' rel='stylesheet'>

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
                        <p class="logout" id="logout-btn">Logout</p>
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
                    <div class="table-scroll">
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
         document.getElementById('logout-btn').addEventListener('click', function (e) {
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
                    window.location.href = '../pages/landingpage.php'; // go to logout
                }
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