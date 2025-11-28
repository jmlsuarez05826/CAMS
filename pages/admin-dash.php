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

date_default_timezone_set('Asia/Manila');
$weekNumber = date('W');
$weekType = ($weekNumber % 2 === 0) ? 'Even' : 'Odd';

$totalUsers = $crud->getUsersCount();
$totalRooms = $crud->getRoomsCount();
$totalEquipment = $crud->getEquipmentCount();
$equipmentStatus = $crud->getEquipmentStatus();

$roomStatusCounts = $crud->getRoomStatusCounts($weekType);
$equipmentStatusCounts = $crud->getEquipmentStatusCounts();

// Prepare labels and values for Chart.js
$roomLabels = [];
$roomValues = [];
foreach ($roomStatusCounts as $row) {
    $roomLabels[] = $row['RoomStatus'];
    $roomValues[] = $row['count'];
}

$equipmentLabels = [];
$equipmentValues = [];
foreach ($equipmentStatusCounts as $row) {
    $equipmentLabels[] = $row['Status'];
    $equipmentValues[] = $row['count'];
}



$labels_e = [];
$data_e = [];
foreach ($equipmentStatus as $row) {
    $labels_e[] = $row['status'];
    $data_e[] = $row['total'];
}


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


$firstname = $_SESSION['FirstName'] ?? null;
$lastname = $_SESSION['LastName'] ?? null;
$number = $_SESSION['PhoneNumber'] ?? null;
$user_id = $_SESSION['UserID'] ?? null;
$role = $_SESSION['Role'] ?? null;


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
            <h2 class="system-title">Welcome <?= $firstname; ?>!</h2>
            <div class="search-field">
                <i class="bi bi-search search-icon"></i>
                <input type="text" placeholder="Search">
            </div>
            <div class="topbar-right">

                <div class="profile-info">
                    <i class="bi bi-person-circle profile-icon"></i>
                    <div class="profile-text">
                        <p class="profile-name">
                            <?php echo $_SESSION['FirstName'] . " " . $_SESSION['LastName']; ?>
                        </p>
                        <p class="profile-number"> <?php echo $_SESSION['PhoneNumber'] ?></p>
                        <p class="profile-time" id="time"></p>
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
                    <span class="chart-number"><?= $totalEquipment ?></span>
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


    <script>
        window.onload = function() {

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

            // Chart.js

            const equipmentLabels = <?= $labels_json_e; ?>;
            const equipmentValues = <?= $data_json_e; ?>;
            const dailyLabels = <?= $labels_json_d; ?>;
            const dailyValues = <?= $data_json_d; ?>;

            // Room Status Chart
            new Chart(document.getElementById('RoomStatus'), {
                type: 'pie',
                data: {
                    labels: <?= json_encode($roomLabels) ?>,
                    datasets: [{
                        data: <?= json_encode($roomValues) ?>,
                        backgroundColor: ['#4CAF50', '#FF6384', '#36A2EB', '#FFCE56'],
                        borderColor: '#fff',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Room Status (<?= $weekType ?> Week)',
                            font: {
                                size: 20,
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
        }
    </script>

    <script>
        (function() {
            // -------------------
            // DOM Elements
            // -------------------
            const chatContainer = document.getElementById('chat-container');
            const toggleBtn = document.getElementById('chat-toggle');
            const closeBtn = document.getElementById('close-btn');
            const backBtn = document.getElementById('back-btn');
            const chatTitle = document.getElementById('chat-title');
            const facultyListDiv = document.getElementById('faculty-list');
            const chatMessages = document.getElementById('chat-messages');
            const chatInputDiv = document.getElementById('chat-input');
            const chatInput = document.getElementById('chat-text');
            const notifDot = document.getElementById('chat-notif');

            const userId = <?= json_encode($_SESSION['UserID']); ?>;
            let currentFacultyId = null;

            // -------------------
            // Open / Close Chat
            // -------------------
            toggleBtn.addEventListener('click', () => {
                chatContainer.classList.add('active');
                toggleBtn.style.display = 'none';
                showFacultyList();
            });

            closeBtn.addEventListener('click', () => {
                chatContainer.classList.remove('active');
                toggleBtn.style.display = 'flex';
            });

            backBtn.addEventListener('click', () => {
                currentFacultyId = null;
                chatMessages.style.display = 'none';
                chatInputDiv.style.display = 'none';
                facultyListDiv.style.display = 'block';
                chatTitle.textContent = 'Contact';
                backBtn.style.display = 'none';
            });

            // -------------------
            // Show Faculty/Admin List
            // -------------------
            function showFacultyList() {
                chatMessages.style.display = 'none';
                chatInputDiv.style.display = 'none';
                facultyListDiv.style.display = 'block';
                backBtn.style.display = 'none';
                chatTitle.textContent = 'Contact';

                fetch('chat_api.php?action=get_faculty')
                    .then(res => res.json())
                    .then(data => {
                        facultyListDiv.innerHTML = '';
                        data.forEach(fac => {
                            const div = document.createElement('div');
                            div.classList.add('faculty-item');
                            div.textContent = fac.FirstName + ' ' + fac.LastName;

                            // Check unread messages
                            fetch(`chat_api.php?action=fetch_unread_count&faculty_id=${fac.UserID}`)
                                .then(res => res.json())
                                .then(countData => {
                                    div.style.fontWeight = countData.unread > 0 ? 'bold' : 'normal';
                                });

                            div.addEventListener('click', () => {
                                openChat(fac.UserID, fac.FirstName + ' ' + fac.LastName);
                            });

                            facultyListDiv.appendChild(div);
                        });
                    });
            }

            // -------------------
            // Open Chat
            // -------------------
            function openChat(facultyId, facultyName) {
                currentFacultyId = facultyId;
                facultyListDiv.style.display = 'none';
                chatMessages.style.display = 'flex';
                chatInputDiv.style.display = 'flex';
                backBtn.style.display = 'inline';
                chatTitle.textContent = facultyName;

                loadChat(true, true);
            }

            // -------------------
            // Load Chat Messages
            // -------------------
            function loadChat(forceScroll = false, markRead = false) {
                if (!currentFacultyId) return;

                let url = `chat_api.php?action=get_messages&faculty_id=${currentFacultyId}`;
                if (markRead) url += '&mark_read=1';

                fetch(url)
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

                        // Scroll to bottom
                        if (!chatMessages.dataset.hasScrolled || forceScroll) {
                            chatMessages.scrollTop = chatMessages.scrollHeight;
                            chatMessages.dataset.hasScrolled = true;
                        } else {
                            const isAtBottom = chatMessages.scrollHeight - chatMessages.scrollTop === chatMessages.clientHeight;
                            if (isAtBottom) chatMessages.scrollTop = chatMessages.scrollHeight;
                        }
                    });
            }

            // -------------------
            // Send Chat Message
            // -------------------
            function sendChat() {
                const msg = chatInput.value.trim();
                if (!msg) return;
                if (!currentFacultyId) {
                    alert('Please select a faculty/admin to send message.');
                    return;
                }

                fetch('chat_api.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: 'action=send_message&receiver_id=' + encodeURIComponent(currentFacultyId) +
                            '&message=' + encodeURIComponent(msg)
                    })
                    .then(res => res.json())
                    .then(resp => {
                        if (resp.success) {
                            chatInput.value = '';
                            loadChat(true);
                        } else if (resp.error) {
                            console.error(resp.error);
                            alert('Error sending message: ' + resp.error);
                        }
                    })
                    .catch(err => {
                        console.error('Fetch error:', err);
                        alert('Could not send message. Check console.');
                    });
            }
            window.sendChat = sendChat;

            // -------------------
            // Enter key to send
            // -------------------
            chatInput.addEventListener('keydown', e => {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    sendChat();
                }
            });

            // -------------------
            // Auto-refresh chat
            // -------------------
            setInterval(() => {
                if (currentFacultyId) loadChat();
            }, 1000);

            // -------------------
            // Notification Dot
            // -------------------
            function updateNotifDot() {
                fetch('chat_api.php?action=get_faculty')
                    .then(res => res.json())
                    .then(data => {
                        let totalUnread = 0;
                        const promises = data.map(fac =>
                            fetch(`chat_api.php?action=fetch_unread_count&faculty_id=${fac.UserID}`)
                            .then(res => res.json())
                            .then(c => totalUnread += c.unread)
                        );
                        Promise.all(promises).then(() => {
                            notifDot.style.display = totalUnread > 0 ? 'block' : 'none';
                        });
                    })
                    .catch(err => console.error(err));
            }
            setInterval(updateNotifDot, 3000);
            updateNotifDot();

        })();

        setInterval(() => {
            location.reload();
        }, 60000);
    </script>



</body>

</html>