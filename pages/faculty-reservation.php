    <?php

    session_start();

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
        try {
            $schedules = $crud->getSchedulesByRoom($roomID);
            echo json_encode($schedules);
        } catch (PDOException $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
        exit;
    }

    if (isset($_GET['roomID'])) {
        $roomID = (int)$_GET['roomID'];
        $schedules = $crud->getSchedulesByRoom($roomID);
        header('Content-Type: application/json');
        echo json_encode($schedules);
        exit;
    }

    if (isset($_POST['reserveUnit'])) {
        $unitID = $_POST['unitID'];
        $userID = $_SESSION['UserID'] ?? null;

        if (!$userID) {
            echo "error: no user ID";
            exit;
        }

        try {
            if ($crud->reserveEquipment($unitID, $userID)) {
                echo "success";
            } else {
                echo "error";
            }
        } catch (PDOException $e) {
            echo "error: " . $e->getMessage();
        }

        exit;
    }

    $userID = $_SESSION['UserID'] ?? null;
    $userReservations = [];

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
                                                        <div class="room-card" data-room-id="<?= $room['RoomID'] ?>">
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
                                    <?php if (!empty($userReservations)): ?>
                                        <?php foreach ($userReservations as $res): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($res['ReservationID']) ?></td>
                                                <td><?= htmlspecialchars($res['EquipmentName']) ?></td>
                                                <td>
                                                    <span class="badge 
                                                        <?= strtolower(trim($res['Status'])) === 'available' ? 'available' : (strtolower(trim($res['Status'])) === 'pending' ? 'pending' : 'cancelled') ?>">
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
                                    <option value="">Select Day</option>
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
                                <div class="form-group">
                                    <label for="subject">Subject</label>
                                    <input type="text" id="subject" name="subject" required>
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
                const userId = <?= $_SESSION['UserID']; ?>;
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
        </script>
        <script>
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


            //odd week btn script
            const weekBtn = document.querySelector(".oddWeek-btn");
            const savedWeek = localStorage.getItem("selectedWeek");
            if (savedWeek) {
                weekBtn.textContent = `${savedWeek} Week`;
            } else {
                weekBtn.textContent = "Odd Week"; // default
            }

            // Now your existing event listener
            weekBtn.addEventListener("click", function() {
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
                        weekBtn.textContent = `${nextWeek} Week`;

                        // Save to localStorage
                        localStorage.setItem("selectedWeek", nextWeek);

                        Swal.fire({
                            title: "Week Changed!",
                            text: `The week has been updated to ${nextWeek}.`,
                            icon: "success",
                            confirmButtonText: "OK"
                        });

                        if (window.currentRoomID && window.currentDay) {
                            loadSchedules(window.currentDay);
                        }
                    }
                });
            });


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
        </script>

        <script>
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
            document.querySelectorAll(".room-card").forEach(card => {
                card.addEventListener("click", () => {
                    openClassroomModal(); // show modal when clicking any room
                });
            });

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


            const reserveForm = document.getElementById("reserveForm");

            reserveForm.addEventListener("submit", function(e) {
                e.preventDefault(); // prevent actual form submission for demo

                // Close the modal
                reserveModal.classList.remove("show");

                // Show SweetAlert
                Swal.fire({
                    icon: 'success',
                    title: 'Request Sent!',
                    text: 'Your classroom reservation request has been sent.',
                    timer: 2000, // auto close after 2 seconds
                    showConfirmButton: false
                });

                // Optional: reset form fields
                reserveForm.reset();
            });



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

            document.querySelectorAll(".room-card").forEach(card => {
                card.addEventListener("click", () => {
                    const roomID = card.getAttribute("data-room-id"); // make sure to set this in PHP
                    const roomNumber = card.querySelector(".room-number").innerText;

                    // Open modal
                    const classroomModal = document.getElementById("classroomModal");
                    classroomModal.classList.add("show");

                    // Update modal title with room number
                    document.querySelector("#classroomModal .custom-modal-title").innerText = `Classroom Schedule - Room ${roomNumber}`;

                    // Fetch schedules from backend
                    fetch(`../pages/faculty-reservation.php?roomID=${roomID}`)
                        .then(res => res.json())
                        .then(schedules => {
                            const tbody = document.querySelector(".classSchedTable tbody");
                            tbody.innerHTML = ""; // clear existing rows

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
                            </tr>
                        `;
                                });
                            }
                        })
                        .catch(err => console.error("Failed to fetch schedules:", err));
                });
            });

            document.addEventListener("click", function(e) {
                if (e.target.classList.contains("reserveBtn")) {
                    let unitID = e.target.getAttribute("data-unit-id");


                    Swal.fire({
                        title: "Reserve this unit?",
                        icon: "question",
                        showCancelButton: true,
                        confirmButtonText: "Yes, reserve"
                    }).then(result => {
                        if (result.isConfirmed) {

                            fetch("faculty-reservation.php", {
                                    method: "POST",
                                    headers: {
                                        "Content-Type": "application/x-www-form-urlencoded"
                                    },
                                    body: "reserveUnit=1&unitID=" + unitID
                                })
                                .then(res => res.text())
                                .then(response => {
                                    if (response.trim() === "success") {
                                        Swal.fire("Reserved!", "Request is now pending.", "success");

                                        // Update the button/UI
                                        e.target.innerText = "Reserved";
                                        e.target.disabled = true;
                                        e.target.closest('.unit-card').classList.remove('available');
                                        e.target.closest('.unit-card').classList.add('reserved');
                                    } else {
                                        Swal.fire("Error", "Failed to reserve.", "error");
                                    }
                                });

                        }
                    });
                }
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
        </script>


    </body>


    </html>