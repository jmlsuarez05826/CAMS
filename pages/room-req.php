<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>


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

    <!--Table goes here -->
    <div class="table-container">
        <table class="requests-table">
            <thead>
                <tr>
                    <th><input type="checkbox" id="selectAll"></th>
                    <th>ID</th>
                    <th>Room No</th>
                    <th>Requester</th>
                    <th>Req. Time</th>
                    <th>Submitted</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><input type="checkbox"></td>
                    <td>1</td>
                    <td>101</td>
                    <td>John Doe</td>
                    <td>Nov 5, 10:00 AM</td>
                    <td>Nov 2, 2:30 PM</td>
                    <td><span class="badge bg-success">Approved</span></td>
                </tr>
                <tr>
                    <td><input type="checkbox"></td>
                    <td>2</td>
                    <td>102</td>
                    <td>Jane Smith</td>
                    <td>Nov 6, 1:00 PM</td>
                    <td>Nov 3, 11:00 AM</td>
                    <td><span class="badge bg-warning text-dark">Pending</span></td>
                </tr>
            </tbody>
        </table>
    </div>


    <!-- Footer Action Bar -->
    <div id="actionFooter" class="action-footer hidden">
        <div class="footer-content">
            <span id="selectedCount">0 selected</span>

            <div class="action-buttons">
                <button class="btn btn-success btn-sm">Approve</button>
                <button class="btn btn-danger btn-sm">Reject</button>
            </div>
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
        updateTime();



        //script for a smooth popup of the action bar/footer
        const checkboxes = document.querySelectorAll('tbody input[type="checkbox"]');
        const footer = document.getElementById("actionFooter");
        const countText = document.getElementById("selectedCount");

        function updateFooter() {
            const selected = document.querySelectorAll('tbody input[type="checkbox"]:checked').length;

            if (selected > 0) {
                countText.textContent = `${selected} selected`;
                footer.classList.add("show");
            } else {
                footer.classList.remove("show");
            }
        }

        checkboxes.forEach(cb => {
            cb.addEventListener("change", updateFooter);
        });
    </script>

</body>