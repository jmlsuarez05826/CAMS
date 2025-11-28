<?php
        session_start();
        if (!isset($_SESSION['UserID']) || empty($_SESSION['UserID'])) {
    header("Location: ../pages/login.php");
    exit();
}

if (!isset($_SESSION['Role']) || $_SESSION['Role'] !== 'Faculty') {
    // Not an admin, redirect or show error
    header("Location: ../pages/login.php");
    exit();
}

        require_once '../pages/camsdatabase.php';
        require_once '../pages/cams-sp.php';

        $crud = new Crud();

        // Return JSON for units if equipmentID is provided
        // Return JSON for units if equipmentID is provided
        if (isset($_GET['equipmentID'])) {
            $equipmentID = (int)$_GET['equipmentID'];
            $units = $crud->getEquipmentUnits($equipmentID);

            // Add "hasPending" flag for each unit based on current user's reservations
            $userID = $_SESSION['UserID'] ?? null;
            $userReservations = $userID ? $crud->getUserReservations($userID) : [];

            foreach ($units as &$unit) {
                $unit['hasPending'] = false;
                foreach ($userReservations as $res) {
                    if ($res['UnitID'] == $unit['UnitID'] && strtolower($res['Status']) === 'pending') {
                        $unit['hasPending'] = true;
                        break;
                    }
                }
            }

            header('Content-Type: application/json');
            echo json_encode($units);
            exit; // stop execution here
        }



    if (isset($_POST['action']) && $_POST['action'] === 'getSchedules') {
        $roomID = $_POST['roomID'];
        $dayOfWeek = $_POST['dayOfWeek'] ?? null;
        $weekType = $_POST['weekType'] ?? null;

        try {
            $schedules = $crud->getSchedulesByRoom($roomID, $dayOfWeek, $weekType);
            echo json_encode($schedules);
        } catch (PDOException $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
        exit;
    }


    if (isset($_POST['reserveUnit'])) {

        $unitID = $_POST['unitID'];
        $userID = $_SESSION['UserID'] ?? null;
        $date = $_POST['date'];
        $timeFrom = $_POST['timeFrom'];
        $timeTo = $_POST['timeTo'];

        if (!$userID) {
            echo "error: no user ID";
            exit;
        }

        try {
            if ($crud->reserveEquipment($unitID, $userID, $date, $timeFrom, $timeTo)) {
                echo "success";
            } else {
                echo "error";
            }
        } catch (PDOException $e) {
            echo "error: " . $e->getMessage();
        }

        exit;
    }

if (isset($_POST['reserveClassroom'])) {
    $roomID = $_POST['p_RoomID'];
    $userID = $_POST['p_UserID'];
    $subject = $_POST['p_Subject'];
    $section = $_POST['p_Section'];
    $date = $_POST['p_ReservationDate'];
    
    // 🌟 1. Retrieve the new parameters from the POST data
    $dayOfWeek = $_POST['p_DayOfWeek'];
    $weekType = $_POST['p_WeekType'];
    
    $timeFrom = $_POST['p_TimeFrom'];
    $timeTo = $_POST['p_TimeTo'];

    // 🌟 2. Pass ALL EIGHT parameters to the reserveClassroom function
    $success = $crud->reserveClassroom(
        $roomID, 
        $userID, 
        $subject, 
        $section,
        $date, 
        $dayOfWeek, // Must be included
        $weekType,  // Must be included
        $timeFrom, 
        $timeTo
    );

    if ($success) {
        echo "success";
    } else {
        echo "Failed to reserve classroom";
    }
    exit;
}
      

    


       $userID = $_SESSION['UserID'] ?? null;
$userReservations = $crud->getUserReservations($userID); // equipment
$userClassroomReservations = $crud->getUserClassroomReservations($userID); // classroom

// Combine them if you want one table
$allReservations = array_merge(
    array_map(fn($r) => ['type'=>'equipment'] + $r, $userReservations),
    array_map(fn($r) => ['type'=>'classroom'] + $r, $userClassroomReservations)
);


        if ($userID) {
            $userReservations = $crud->getUserReservations($userID);
        }

        if (isset($_POST['action']) && $_POST['action'] === 'cancelReservation') {
            $reservationID = $_POST['reservationID'] ?? null;

            if ($reservationID) {
                try {
                    if ($crud->cancelReservation($reservationID)) {
                        echo json_encode(['status' => 'success']);
                    } else {
                        echo json_encode(['status' => 'error', 'message' => 'Failed to cancel']);
                    }
                } catch (PDOException $e) {
                    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'No reservation ID provided']);
            }
            exit;
        }



        $buildings = $crud->getBuildings();
        $floors = $crud->getFloors();
        $rooms = $crud->getRooms();
        $equipments = $crud->getEquipments();


        $firstname = $_SESSION['FirstName'];
        $lastname = $_SESSION['LastName'];
        $number = $_SESSION['PhoneNumber'];
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
                                <p class="profile-name">
                                    <?php echo $_SESSION['FirstName'] . " " . $_SESSION['LastName']; ?>
                                </p>
                                <p class="profile-number"> <?php echo $_SESSION['PhoneNumber'] ?></p>
                                <p class="profile-time" id="timeDay"></p> <!-- Real-time day & time here -->
                            </div>

                            <!-- Dropdown arrow -->
                            <i class="bi bi-caret-down-fill dropdown-icon"></i>

                            <!-- Dropdown menu -->
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

                        <button class="oddWeek-btn">Odd Week</button>

                    </div>

                </section>

                <section class="main-content">

                    <div class="table-contents">
                        <div id="classrooms" class="tab-content active">
                            
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
                                                           <div class="room-card clickable-room" data-room="<?= $room['RoomID'] ?>">
                                                                <div class="room-label">Room no</div>
                                                                <div class="room-number"><?= htmlspecialchars($room['RoomNumber']) ?></div>
                                                                <hr>
                                                                <div class="room-status">Loading... </div>
                                                            </div>

                                                        <?php endif; ?>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endforeach; ?>
                           
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
                                            <?php if (!empty($equipments)): ?>
                                                <?php foreach ($equipments as $equipment): ?>
                                                    <tr class="clickable-row"
                                                        data-id="<?= $equipment['EquipmentID'] ?>"
                                                        data-image="<?= htmlspecialchars($equipment['EquipmentIMG'] ? '../uploads/equipments/' . $equipment['EquipmentIMG'] : '../uploads/equipments/default.png') ?>">
                                                        <td><?= htmlspecialchars($equipment['EquipmentID']) ?></td>
                                                        <td><?= htmlspecialchars($equipment['EquipmentName']) ?></td>
                                                        <td><?= htmlspecialchars($equipment['Quantity']) ?></td>
                                                        <td>
                                                            <span class="badge 
                                                    <?php
                                                    $status = strtolower(trim($equipment['Status'] ?? 'available'));
                                                    echo $status === 'available' ? 'available' : ($status === 'reserved' ? 'reserved' : 'maintenance');
                                                    ?>">
                                                                <?= htmlspecialchars($equipment['Status'] ?? 'Available') ?>
                                                            </span>

                                                        </td>
                                                    </tr>


                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="5" class="text-center">No equipment found</td>
                                                </tr>
                                            <?php endif; ?>
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
                                            <th>Status</th>
                                            <th>Action</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($allReservations)): ?>
    <?php foreach ($allReservations as $res): ?>
        <tr>
            <td><?= htmlspecialchars($res['ReservationID']) ?></td>
            <td>
                <?= htmlspecialchars($res['type'] === 'equipment' ? $res['EquipmentName'] : $res['RoomNumber']) ?>
            </td>
            <td>
                <span class="badge 
                    <?= strtolower(trim($res['Status'])) === 'pending' ? 'pending' : (strtolower(trim($res['Status'])) === 'approved' ? 'approved' : 'cancelled') ?>">
                    <?= htmlspecialchars($res['Status']) ?>
                </span>
            </td>
            <td>
                <?php if (strtolower($res['Status']) === 'pending'): ?>
                    <button class="cancelBtn" data-id="<?= $res['ReservationID'] ?>"><i class="bi bi-x-lg cancelIcon"></i></button>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
<?php else: ?>
    <tr>
        <td colspan="5" class="text-center">No reservations found</td>
    </tr>
<?php endif; ?>


                                    </tbody>

                                </table>
                            </div>
                        </div>

                    </div>
                    </div>
                </section>

                </div>
                </section>

                <!-- class sched modal -->
                <div class="custom-modal" id="classroomModal">
                    <div class="custom-modal-dialog">
                        <div class="custom-modal-content">
                            <div class="custom-modal-header">
                                <h5 class="custom-modal-title">Classroom Schedule</h5>
                                <button type="button" class="custom-close" id="closeclassroomModal">&times;</button>
                            </div>

                            <div class="custom-modal-body">
                                <form method="post" id="classSchedForm">

                                    <p>Building Name Room No</p>
                                    <select id="dayFilter" class="day-dropdown">
                                        <option>Monday</option>
                                        <option>Tuesday</option>
                                        <option>Wednesday</option>
                                        <option>Thursday</option>
                                        <option>Friday</option>
                                        <option>Saturday</option>
                                        <option>Sunday</option>
                                    </select>

                                    <div class="Sched-table-wrapper">
                                        <table class="classSchedTable">
                                            <thead>
                                                <tr>
                                                    <th>Instructor</th>
                                                    <th>Subject</th>
                                                    <th>Time</th>
                                                    <th>Section</th>
                                                    <th>Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>

                                    <hr class="table-separator">

                                    <div class="custom-modal-footer">
                                        <button type="button" class="btn-close-modal" id="closeAddUserFooter">Close</button>
                                        <button type="submit" name="add" id="addBtn">Reserve</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reservation Modal -->
                <div class="custom-modal" id="reserveModal">
                    <div class="custom-modal-dialog">
                        <div class="custom-modal-content">
                            <div class="custom-modal-header">
                                <h5 class="custom-modal-title">Reserve Classroom</h5>
                                <button type="button" class="custom-close" id="closeReserveModal">&times;</button>
                            </div>

                            <div class="custom-modal-body">
                                <form id="reserveForm">
                                    <input type="hidden" id="roomID" name="roomID">
                                    <div class="form-group">
                                        <label for="subject">Subject</label>
                                        <input type="text" id="subject" name="subject" required>
                                    </div>
                                    <div class="form-group">
            <label for="section">Section</label>
            <input type="text" id="section" name="section" required> 
        </div>
                                    <div class="form-group">
                                        <label for="fromTime">From</label>
                                        <input type="time" id="fromTime" name="fromTime" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="toTime">To</label>
                                        <input type="time" id="toTime" name="toTime" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="date">Date</label>
                                        <input type="date" id="date" name="date" required>
                                    </div>

                                    <hr class="table-separator">

                                    <div class="custom-modal-footer">
                                        <button type="button" class="btn-cancel" id="closeReserveFooter">Cancel</button>
                                        <button type="submit" class="btn-confirm" id="confirmReserve">Reserve</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- chat logic -->
                <div id="chat-toggle">
                    💬
                    <div id="chat-notif"></div>
                </div>


                <!-- Chat container -->
                <div id="chat-container">
                    <div id="chat-header">
                        <span id="back-btn" style="display:none;">
                            < </span>
                                <span id="chat-title">Contact</span>
                                <span id="close-btn">✖</span>
                    </div>

                    <!-- Faculty list view -->
                    <div id="faculty-list"></div>

                    <!-- Messages view -->
                    <div id="chat-messages" style="display:none;"></div>

                    <!-- Input -->
                    <div id="chat-input" style="display:none;">
                        <input type="text" id="chat-text" placeholder="Type a message...">
                        <button onclick="sendChat()">Send</button>
                    </div>
                </div>

            </main>

            <script>
        // Pass PHP session variable to JS
        const facultyReservationUserID = <?= json_encode($_SESSION['UserID'] ?? null) ?>;
    </script>

        <script src="../js/faculty-reservation.js"></script>
        </body>


        </html>