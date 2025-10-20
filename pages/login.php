<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            font-family: 'Ibarra Real Nova', serif;
            background: #f5f5f5;
        }
    </style>
    <link rel="preload"  href="../images/BSU_BG_(2).webp" as="image"> <!-- Preload to bg img to improve performance -->
    <link rel="stylesheet" href="../assets/css/login.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Ibarra+Real+Nova:wght@400;700&display=swap" rel="stylesheet">

</head>

<body>

    <!-- Navbar -->
    <header>
        <div class="header-content">
            <img src="../images/BSU_logo (3).webp" alt="Logo">
            <nav class="navbar"> Classroom Management System </nav>
        </div>
    </header>


    <!-- Login Container -->
    <main class="login-page">
        <div class="login-container">
            <h2>Login</h2>

            <form action="" method="post">

                <input type="text" id="username" name="username" placeholder="Username" required>


                <input type="password" id="password" name="password" placeholder="Password" required>

                <button type="submit">Login</button>
            </form>

            <a href="forgot-password.php" class="forgot-password">Forgot Password?</a>

        </div>

    </main>

</body>

</html>