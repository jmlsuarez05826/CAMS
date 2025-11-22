<?php
session_start();
require_once '../pages/camsdatabase.php';
require_once '../pages/cams-sp.php';
require_once '../includes/admin-sidebar.php';

if (!isset($_SESSION['UserID']) || empty($_SESSION['UserID'])) {
    header("Location: ../pages/login.php");
    exit();
}

if (!isset($_SESSION['Role']) || $_SESSION['Role'] !== 'Admin') {
    header("Location: ../pages/login.php");
    exit();
}

$database = new Database();
$pdo = $database->getConnection();

$crud = new Crud();

// Dashboard numbers
$totalUsers = $crud->getUsersCount();
$totalRooms = $crud->getRoomsCount();
$roomStatus = $crud->getRoomStatus();
$equipmentStatus = $crud->getEquipmentStatus();

// Prepare chart data
$labels_r = [];
$data_r = [];
foreach ($roomStatus as $row) {
    $labels_r[] = $row['status'];
    $data_r[] = $row['total'];
}

$labels_e = [];
$data_e = [];
foreach ($equipmentStatus as $row) {
    $labels_e[] = $row['status'];
    $data_e[] = $row['total'];
}

$labels_json_r = json_encode($labels_r);
$data_json_r = json_encode($data_r);
$labels_json_e = json_encode($labels_e);
$data_json_e = json_encode($data_e);

// Daily users
$stmt = $pdo->prepare("CALL ViewDailyUsers()");
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
while ($stmt->nextRowset()) {;
} // clear remaining result sets

$date = [];
$count = [];
foreach ($results as $row) {
    $date[] = $row['visit_date'];
    $count[] = (int) $row['visit_count'];
}
$labels_json_d = json_encode($date);
$data_json_d = json_encode($count);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/admin-dash.css">
    <link rel="stylesheet" href="../assets/css/room-req.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
</head>

<body>

    <header>
        <div class="topbar">
            <h2 class="system-title">Welcome Admin!</h2>
            <div class="search-field">
                <i class="bi bi-search search-icon"></i>
                <input type="text" placeholder="Search">
            </div>
            <div class="topbar-right">
                <div class="notification-icon">
                    <i class="bi bi-bell-fill notification-icon"></i>
                </div>
                <div class="profile-info">
                    <i class="bi bi-person-circle profile-icon"></i>
                    <div class="profile-text">
                        <p class="profile-name">Mark Cristopher</p>
                        <p class="profile-number">093480324</p>
                        <div id="time"></div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Dashboard numbers -->
    <div class="chart-row">
        <div class="chart-container-1">
            <div class="information">
                <div class="circle"><i class="bi bi-person-fill chart-icon"></i></div>
                <div class="chart-info">
                    <h1>Total Users</h1>
                    <span class="chart-number" style=" font-size: 25px;"><?= $totalUsers ?></span>
                </div>
            </div>
        </div>
        <div class="chart-container">
            <div class="information">
                <div class="circle"><i class="bi bi-door-open chart-icon"></i></div>
                <div class="chart-info">
                    <h1>Total Rooms</h1>
                    <span class="chart-number"><?= $totalRooms ?></span>
                </div>
            </div>
        </div>
        <div class="chart-container">
            <div class="information">
                <div class="circle"><i class="bi bi-hourglass chart-icon"></i></div>
                <div class="chart-info">
                    <h1>Total Equipments</h1>
                    <span class="chart-number">45</span>
                </div>
            </div>
        </div>
        <div class="chart-container">
            <div class="information">
                <div class="circle"><i class="bi bi-card-list chart-icon"></i></div>
                <div class="chart-info">
                    <h1>Total Room Requests</h1>
                    <span class="chart-number">78</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphs -->
    <div class="single-row">
        <div class="single-container">
            <div class="chart-title">
                <h3>Daily Page Visits</h3>
            </div>
            <canvas id="roomUsageChart"></canvas>
        </div>
    </div>

    <div class="bar-row">
        <div class="bar-container" style=" max-width:50%; max-height: 30em;">
            <canvas id="RoomStatus"></canvas>
        </div>
        <div class="bar-container" style=" max-width:50%; max-height: 30em;">
            <canvas id="EquipmentStatus"></canvas>
        </div>
    </div>

    <!-- Admin ↔ Faculty Chat -->
    <label for="faculty-select">Select Faculty:</label>
    <select id="faculty-select" onchange="loadMessages()">
        <?php
        $stmt = $pdo->prepare("SELECT UserID, FirstName FROM users WHERE Role='Faculty'");
        $stmt->execute();
        $facultyList = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($facultyList as $row) {
            echo "<option value='{$row['UserID']}'>{$row['FirstName']}</option>";
        }
        ?>
    </select>


    <div id="chat-toggle">💬</div>

    <!-- Chat container -->
    <div id="chat-container">
        <div id="chat-header">
            <span id="back-btn" style="display:none;">⬅</span>
            <span id="chat-title">Select Faculty</span>
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


    <script>
        window.onload = function() {

            // Time update
            function updateTime() {
                const now = new Date();
                let hours = now.getHours();
                const minutes = String(now.getMinutes()).padStart(2, '0');
                const ampm = hours >= 12 ? 'PM' : 'AM';
                hours = hours % 12 || 12;
                document.getElementById('time').textContent = `${hours}:${minutes} ${ampm}`;
            }
            setInterval(updateTime, 1000);
            updateTime();

            // Chart.js
            const roomLabels = <?= $labels_json_r; ?>;
            const roomValues = <?= $data_json_r; ?>;
            const equipmentLabels = <?= $labels_json_e; ?>;
            const equipmentValues = <?= $data_json_e; ?>;
            const dailyLabels = <?= $labels_json_d; ?>;
            const dailyValues = <?= $data_json_d; ?>;

            new Chart(document.getElementById('RoomStatus'), {
                type: 'pie',
                data: {
                    labels: roomLabels,
                    datasets: [{
                        data: roomValues,
                        backgroundColor: ['#4CAF50', '#FF6384', '#36A2EB', '#FFCE56'],
                        borderColor: '#fff',
                        borderWidth: 4
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Room Status',
                            font: {
                                size: 24,
                                weight: 'bold'
                            }
                        },
                        legend: {
                            position: 'left'
                        }
                    }
                }
            });

            new Chart(document.getElementById('EquipmentStatus'), {
                type: 'pie',
                data: {
                    labels: equipmentLabels,
                    datasets: [{
                        data: equipmentValues,
                        backgroundColor: ['#4CAF50', '#FF6384', '#36A2EB', '#FFCE56'],
                        borderColor: '#fff',
                        borderWidth: 4
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Equipment Status',
                            font: {
                                size: 24,
                                weight: 'bold'
                            }
                        },
                        legend: {
                            position: 'left'
                        }
                    }
                }
            });

            const ctx = document.getElementById('roomUsageChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: dailyLabels,
                    datasets: [{
                        label: 'Visits per Day',
                        data: dailyValues,
                        backgroundColor: '#8b1717da',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            stepSize: 1
                        }
                    }
                }
            });

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
            toggleBtn.onclick = function() {
                chatContainer.classList.add('active');
                toggleBtn.style.display = 'none';
                showFacultyList();
            }

            // Close chat
            closeBtn.onclick = function() {
                chatContainer.classList.remove('active');
                toggleBtn.style.display = 'flex';
            }

            // Back button
            backBtn.onclick = function() {
                currentFacultyId = null;
                chatMessages.style.display = 'none';
                chatInput.style.display = 'none';
                facultyListDiv.style.display = 'block';
                chatTitle.textContent = 'Select Faculty';
                backBtn.style.display = 'none';
            }

            // Show faculty list
            function showFacultyList() {
                chatMessages.style.display = 'none';
                chatInput.style.display = 'none';
                facultyListDiv.style.display = 'block';
                backBtn.style.display = 'none';
                chatTitle.textContent = 'Select Faculty';

                fetch('chat_api.php?action=get_faculty')
                    .then(res => res.json())
                    .then(data => {
                        facultyListDiv.innerHTML = '';
                        data.forEach(fac => {
                            const div = document.createElement('div');
                            div.classList.add('faculty-item');
                            div.textContent = fac.FirstName + ' ' + fac.LastName + ' (' + fac.PhoneNumber + ')';
                            div.onclick = function() {
                                openChat(fac.UserID, fac.FirstName + ' ' + fac.LastName);
                            }
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

                fetch(`chat_api.php?action=get_messages&faculty_id=${currentFacultyId}`)
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

                        // Scroll behavior
                        if (!chatMessages.dataset.hasScrolled || forceScroll) {
                            // First load or forced scroll
                            chatMessages.scrollTop = chatMessages.scrollHeight;
                            chatMessages.dataset.hasScrolled = true;
                        } else {
                            // Only scroll if user is already at bottom
                            const isAtBottom = chatMessages.scrollHeight - chatMessages.scrollTop === chatMessages.clientHeight;
                            if (isAtBottom) chatMessages.scrollTop = chatMessages.scrollHeight;
                        }
                    });
            }

            // Send chat message
            function sendChat() {
                const msgInput = document.getElementById('chat-text');
                const msg = msgInput.value.trim();
                if (!msg || !currentFacultyId) return;

                fetch('chat_api.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: 'action=send_message&receiver_id=' + currentFacultyId + '&message=' + encodeURIComponent(msg)
                    }).then(res => res.json())
                    .then(resp => {
                        if (resp.success) {
                            msgInput.value = '';
                            loadChat(true); // force scroll to bottom after sending
                        }
                    });
            }


            // Auto-refresh chat
            setInterval(loadChat, 1000);
            window.sendChat = sendChat;
        };

        const chatInput = document.getElementById('chat-text'); // your message input
        chatInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) { // Enter without Shift
                e.preventDefault(); // prevent newline
                sendChat(); // call your send function
            }
        });
    </script>

</body>

</html>