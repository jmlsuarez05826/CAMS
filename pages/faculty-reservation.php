<?php
session_start();

require_once '../pages/camsdatabase.php';
require_once '../pages/cams-sp.php';

if (!isset($_SESSION['UserID']) || empty($_SESSION['UserID'])) {
    header("Location: ../pages/login.php");
    exit();
}

if (!isset($_SESSION['Role']) || $_SESSION['Role'] !== 'Faculty') {
    // Not an admin, redirect or show error
    header("Location: ../pages/login.php");
    exit();
}

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
                                        <div class="room-container" data-floor="<?= htmlspecialchars($floor['FloorID']) ?>"
                                            style="display:none;">
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

        <div id="chat-box" style="height:300px; overflow-y:scroll; border:1px solid #ccc; padding:10px;"></div>

        <input type="text" id="message" placeholder="Type a message...">
        <button onclick="sendMessage()">Send</button>

        <script>
            function loadMessages() {
                fetch('fetch_messages.php')
                    .then(res => res.json())
                    .then(data => {
                        let html = '';
                        data.forEach(msg => {
                            html += `<p><strong>${msg.sender_id == <?php echo $_SESSION['UserID']; ?> ? 'You' : 'Admin'}:</strong> ${msg.message}</p>`;
                        });
                        let chatBox = document.getElementById('chat-box');
                        chatBox.innerHTML = html;
                        chatBox.scrollTop = chatBox.scrollHeight;
                    });
            }

            function sendMessage() {
                let msg = document.getElementById('message').value;
                if (msg.trim() === '') return;

                fetch('send_message.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'message=' + encodeURIComponent(msg)
                }).then(() => {
                    document.getElementById('message').value = '';
                    loadMessages();
                });
            }

            setInterval(loadMessages, 1000);
            loadMessages();
        </script>


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
                    window.location.href = '../pages/logout.php';
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

    <!-- Chat Toggle -->
    <div id="chat-toggle">💬</div>

    <!-- Chat container -->
    <div id="chat-container">
        <div id="chat-header">
            <span id="back-btn" style="display:none;">⬅</span>
            <span id="chat-title">Select Faculty</span>
            <span id="close-btn">✖</span>
        </div>

        <!-- Faculty list -->
        <div id="faculty-list"></div>

        <!-- Chat messages -->
        <div id="chat-messages" style="display:none;"></div>

        <!-- Chat input -->
        <div id="chat-input" style="display:none;">
            <input type="text" id="chat-text" placeholder="Type a message...">
            <button onclick="sendChat()">Send</button>
        </div>
    </div>

    <!-- Include the CSS we wrote -->
    <style>
        /* Paste the CSS I gave you here */
    </style>

    <!-- JS -->
    <script>
        window.onload = function () {
            const chatContainer = document.getElementById('chat-container');
            const toggleBtn = document.getElementById('chat-toggle');
            const closeBtn = document.getElementById('close-btn');
            const backBtn = document.getElementById('back-btn');
            const chatTitle = document.getElementById('chat-title');
            const facultyListDiv = document.getElementById('faculty-list');
            const chatMessages = document.getElementById('chat-messages');
            const chatInput = document.getElementById('chat-input');
            const userId = <?= $_SESSION['UserID']; ?>;
            let currentFacultyId = null;

            // Open chat
            toggleBtn.onclick = function () {
                chatContainer.style.display = 'flex';
                toggleBtn.style.display = 'none';
                showFacultyList();
            };

            // Close chat
            closeBtn.onclick = function () {
                chatContainer.style.display = 'none';
                toggleBtn.style.display = 'flex';
            };

            // Back button
            backBtn.onclick = function () {
                currentFacultyId = null;
                chatMessages.style.display = 'none';
                chatInput.style.display = 'none';
                facultyListDiv.style.display = 'block';
                chatTitle.textContent = 'Select Faculty';
                backBtn.style.display = 'none';
            };

            // Show faculty list
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
                            div.onclick = function () {
                                openChat(fac.UserID, fac.FirstName + ' ' + fac.LastName);
                            };
                            facultyListDiv.appendChild(div);
                        });
                    });
            }

            // Open chat with selected faculty
            function openChat(facultyId, facultyName) {
                currentFacultyId = facultyId;
                facultyListDiv.style.display = 'none';
                chatMessages.style.display = 'flex';
                chatInput.style.display = 'flex';
                backBtn.style.display = 'inline';
                chatTitle.textContent = facultyName;
                loadChat();
            }

            // Load chat messages
            function loadChat(forceScroll = false) {
                if (!currentFacultyId) return;

                fetch('faculty_chat.php?action=fetch_messages&faculty_id=' + currentFacultyId)
                    .then(res => res.json())
                    .then(data => {
                        chatMessages.innerHTML = '';
                        data.forEach(msg => {
                            const div = document.createElement('div');
                            div.classList.add('message');
                            div.classList.add(msg.sender_id == userId ? 'admin' : 'faculty');
                            div.innerHTML = `<span>${msg.message}</span><small>${msg.timestamp}</small>`;
                            chatMessages.appendChild(div);
                        });

                        // Check if user is near bottom
                        const scrollPosition = chatMessages.scrollTop + chatMessages.clientHeight;
                        const scrollThreshold = chatMessages.scrollHeight - 10; // 10px tolerance

                        if (!chatMessages.dataset.hasScrolled || forceScroll || scrollPosition >= scrollThreshold) {
                            chatMessages.scrollTop = chatMessages.scrollHeight;
                            chatMessages.dataset.hasScrolled = true;
                        }
                    });
            }

            // Send chat message
            window.sendChat = function () {
                const msgInput = document.getElementById('chat-text');
                const msg = msgInput.value.trim();
                if (msg === '' || !currentFacultyId) return;

                fetch('faculty_chat.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'action=send_message&receiver_id=' + currentFacultyId + '&message=' + encodeURIComponent(msg)
                }).then(() => {
                    msgInput.value = '';
                    loadChat(true); // Force scroll after sending
                });
            };


            // Auto-refresh chat
            setInterval(loadChat, 1000);
        };

        const chatInput = document.getElementById('chat-text'); // your message input
        chatInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' && !e.shiftKey) { // Enter without Shift
                e.preventDefault(); // prevent newline
                sendChat(); // call your send function
            }
        });

    </script>



</body>


</html>