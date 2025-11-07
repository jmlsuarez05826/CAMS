<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<html>

<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">


    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="../assets/css/admin-sidebar.css">

</head>


<body>
    <header>

        <!-- Sidebar (hidden by default) -->
        <nav class="sidebar" id="sidebar">
            <!-- Logo + Title container -->
            <div class="header-content">
                <img src="../images/BSU_logo (3).webp" alt="Logo" class="sidebar-logo">
                <span class="sidebar-title">Classroom Management System</span>
            </div>

            <!-- Sidebar links -->
            <ul >
                <li>
                    <a href="../pages/admin-dash.php"
                        class="<?= $currentPage === 'admin-dash.php' ? 'active' : '' ?>">
                        <img src="../images/dashboard.webp" alt="Dashboard" class="sidebar-icon">
                        Dashboard
                    </a>
                </li>

                <li>
                    <a href="../pages/class-management.php"
                        class="<?= $currentPage === 'class-management.php' ? 'active' : '' ?>">
                        <img src="../images/presentation.webp" alt="Dashboard" class="sidebar-icon">
                        Manage Classrooms
                    </a>
                </li>

                <li>
                    <a href="../pages/room-req.php" style=" display: flex ; gap: 15px;"
                        class="<?= $currentPage === 'room-req.php' ? 'active' : '' ?>">
                        <i class="bi bi-card-checklist"></i>
                        Classroom Requests
                    </a>
                </li>

                <li>
                    <a href="../pages/equipment-req.php" style=" display: flex ; gap: 15px;"
                        class="<?= $currentPage === 'equipment-req.php' ? 'active' : '' ?>">
                        <i class="bi bi-easel2"></i>
                        Equipment Requests
                    </a>
                </li>

                <li>
                    <a href="../pages/equipment-management.php" style=" display: flex ; gap: 15px;"
                        class="<?= $currentPage === 'equipment-management.php' ? 'active' : '' ?>">
                        <i class="bi bi-gear-wide"></i>
                        Manage Equipments
                    </a>
                </li>

                <li>

                    <a href="user_accounts.php" style=" display: flex ; gap: 15px;"
                        class="<?= $currentPage === 'user_accounts.php' ? 'active' : '' ?>">
                        <i class="bi bi-people-fill"></i>
                        User Accounts
                    </a>
                </li>

                <hr class="sidebar-divider">
            </ul>

            <div class="sidebar-footer">
                <a href="logout.php" id="logout-btn" class="d-flex align-items-center custom-gap sidebar-logout">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
            </div>


        </nav>

    </header>
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
                    window.location.href = '../pages/login.php'; // go to logout
                }
            });
        });

        document.getElementById()
    </script>
</body>

</html>