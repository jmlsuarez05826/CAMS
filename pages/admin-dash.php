<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <link rel="stylesheet" href="../assets/css/admin-dash.css">
    <link rel="stylesheet" href="../assets/css/room-req.css">


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

    <div class="chart-row">

        <div class="chart-container">
            <div class="chart-title">Total Room Requests</div>
            <div class="chart-info">
                <span class="chart-number">123</span>
                <i class="bi bi-building chart-icon"></i>
            </div>
        </div>

        <div class="chart-container">
            <div class="chart-title">Total Rooms</div>
            <div class="chart-info">
                <span class="chart-number">123</span>
                <i class="bi bi-door-open chart-icon"></i>
            </div>
        </div>

        <div class="chart-container">
            <div class="chart-title">Total Equipments</div>
            <div class="chart-info">
                <span class="chart-number">45</span>
                <i class="bi bi-hourglass chart-icon"></i>
            </div>
        </div>

        <div class="chart-container">
            <div class="chart-title">Total Accounts</div>
            <div class="chart-info">
                <span class="chart-number">78</span>
                <i class="bi bi-person-fill chart-icon"></i>
            </div>
        </div>

    </div>

    <!-- Below are two Containers for graphs -->

    <!-- Second row (2 containers) -->
    <div class="bar-row">
        <div class="bar-container">
            <canvas id="idkChart"></canvas>
        </div>

        <div class="bar-container">
            <canvas id="barChart"></canvas>
        </div>
    </div>

    <!-- Third row  Single Container-->
    <div class="single-row">
        <div class="single-container">
            <div class="chart-title">A table</div>
            <canvas id="roomUsageChart"></canvas>
        </div>
    </div>


    <script>
        //script for the time
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
        updateTime()
    </script>

</body>