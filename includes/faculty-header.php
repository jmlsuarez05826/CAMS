<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Header</title>
</head>

<body>
    <header>
        <div class="navbar">
            <div class="menu-icon" id="menu-toggle">&#9776;</div> <!-- ☰ -->
            <h1>Classroom Management System</h1>
        </div>

        <nav class="sidebar" id="sidebar">
            <ul>
                <li><a href="#">Dashboard</a></li>
                <li><a href="#">Patients</a></li>
                <li><a href="#">Appointments</a></li>
                <li><a href="#">Logout</a></li>
            </ul>
        </nav>
    </header>
    <script>
        document.getElementById('menu-toggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('active');
        });
    </script>

</body>

</html>