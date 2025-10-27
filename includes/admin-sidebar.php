<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
            <ul>
                <li>
                    <a href="../pages/admin-dash.php">
                        <img src="../images/dashboard.webp" alt="Dashboard" class="sidebar-icon">
                        Dashboard
                    </a>
                </li>

                <li>
                    <a href="#">
                        <img src="../images/presentation.webp" alt="Dashboard" class="sidebar-icon">
                        Manage Classrooms
                    </a>
                </li>

                <li>
                    <a href="../pages/settings.php" style=" display: flex ; gap: 15px;">
                        <i class="bi bi-gear-wide"></i>
                        Manage Equipments
                    </a>
                </li>

                <li>
                    <a href="../pages/settings.php" style=" display: flex ; gap: 15px;">
                        <i class="bi bi-people-fill"></i>
                        User Accounts
                    </a>
                </li>

                <hr class="sidebar-divider">
            </ul>

            <div class="sidebar-footer">
                <a href="logout.php" id="logout-btn" class="btn btn-danger w-100 d-flex align-items-center custom-gap">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>

            </div>


        </nav>

    </header>
    <script>
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
                    window.location.href = '../pages/login.php'; // go to logout
                }
            });
        });
    </script>

</body>

</html>