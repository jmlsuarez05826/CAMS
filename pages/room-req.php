<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>


    <link rel="stylesheet" href="../assets/css/room-req.css">

    <?php
    session_start();
    require_once '../includes/admin-sidebar.php';
        require_once '../pages/camsdatabase.php';
    require_once '../pages/cams-sp.php';

    if (!isset($_SESSION['UserID']) || empty($_SESSION['UserID'])) {
    header("Location: ../pages/login.php");
    exit();
}

// Optional: Check if user has admin role
if (!isset($_SESSION['Role']) || $_SESSION['Role'] !== 'Admin') {
    // Not an admin, redirect or show error
    header("Location: ../pages/login.php");
    exit();
}
    ?>
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
        </div>
    </header>
    <!--Table goes here -->
    
<div class="content">
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
  // Script for the time in 12-hour format with AM/PM
        function updateTime() {
            const now = new Date();
            let hours = now.getHours();
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const ampm = hours >= 12 ? 'PM' : 'AM';

            // Convert 24-hour to 12-hour format
            hours = hours % 12;
            hours = hours ? hours : 12; // the hour '0' should be '12'
            hours = String(hours).padStart(2, '0');

            document.getElementById('time').textContent = `${hours}:${minutes} ${ampm}`;
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