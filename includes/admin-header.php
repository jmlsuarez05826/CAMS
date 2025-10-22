<body>
    <header>
        <nav id="topbar" class="navbar">
            <!-- Hamburger / Sidebar toggle -->
            <div class="hamburger">
                
            <p id="menu-toggle">&#9776;</p>

              <!-- Centered page title -->
            <div class="title">Dashboard</div>
            </div>


            <!-- Account dropdown -->
            <div class="account-dropdown">
                <span id="account-name">John Doe &#9662;</span> <!-- ▼ icon -->
                <ul class="dropdown-menu" id="dropdown-menu">
                    <li><a href="#">Profile</a></li>
                    <li><a href="#">Settings</a></li>
                    <li><a href="#">Logout</a></li>
                </ul>
            </div>
        </nav>

        <!-- Sidebar (hidden by default) -->
        <aside class="sidebar" id="sidebar">
            <!-- Logo + Title container -->
            <div class="header-content">
                <img src="../images/BSU_logo (3).webp" alt="Logo" class="sidebar-logo">
                <span class="sidebar-title">Classroom Management System</span>
            </div>

            <!-- Sidebar links -->
            <ul>
                <li><a href="../pages/admin-dash.php">Dashboard</a></li>
                <li><a href="#">Manage Classrooms</a></li>
                <li><a href="../pages/user_accounts.php">User Accounts</a></li>
            </ul>
        </aside>

    </header>


    <script>
    // Sidebar toggle
    const hamburger = document.getElementById('menu-toggle');
    const sidebar = document.getElementById('sidebar');
    const topbar = document.getElementById('topbar'); // <-- added: reference to navbar

    hamburger.addEventListener('click', (e) => {
        e.stopPropagation(); // Prevent the click from bubbling up to document
        sidebar.classList.toggle('active');

        // <-- added: shift/reduce navbar when sidebar is toggled
        if (sidebar.classList.contains('active')) {
            topbar.style.marginLeft = '250px';             // moves navbar to the right
            topbar.style.width = 'calc(100% - 250px)';     // shrinks width
        } else {
            topbar.style.marginLeft = '0';                 // reset position
            topbar.style.width = '100%';                   // full width
        }
    });

    // Close sidebar when clicking outside
    document.addEventListener('click', (e) => {
        // Check if sidebar is open and click is outside sidebar and hamburger
        if (sidebar.classList.contains('active') &&
            !sidebar.contains(e.target) &&
            !hamburger.contains(e.target)) {
            sidebar.classList.remove('active');

            // <-- added: reset navbar if sidebar is closed by clicking outside
            topbar.style.marginLeft = '0';
            topbar.style.width = '100%';
        }
    });

    // Account dropdown toggle
    const accountName = document.getElementById('account-name');
    const dropdownMenu = document.getElementById('dropdown-menu');

    accountName.addEventListener('click', (e) => {
        e.stopPropagation(); // Prevent closing immediately if inside dropdown
        dropdownMenu.classList.toggle('active');
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', (e) => {
        if (dropdownMenu.classList.contains('active') &&
            !accountName.contains(e.target) &&
            !dropdownMenu.contains(e.target)) {
            dropdownMenu.classList.remove('active');
        }
    });
</script>



</body>

</html>