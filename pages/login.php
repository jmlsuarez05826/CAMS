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
    <link rel="preload" href="../images/BSU_BG_(2).webp" as="image"> <!-- Preload to bg img to improve performance -->
    <link rel="stylesheet" href="../assets/css/login.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Ibarra+Real+Nova:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
</head>

<body>

    <?php
    require_once '../pages/camsdatabase.php';

    if (isset($_POST['login'])) {

        $name = $_POST['username'];
        $pass = $_POST['password'];

        $database = new Database();
        $conn = $database->getConnection();



        $sql = "SELECT * FROM users WHERE FirstName=:name AND Password=:password";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':password', $pass);


        $stmt->execute();

        if ($stmt->rowCount() > 0) {

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            session_start();
            $SESSION['user'] = $user['FirstName'];

            echo "
    <script>
    Swal.fire({
        icon: 'success',
        title: 'Login Succesful',
        text: 'Welcome, {$user['FirstName']}!',
        showConfirmButtom:false,
        timer: 2000
        }).then(() => {
        window.location.href = 'user_accounts.php';
        });
    </script>";
} else {
            // Wrong password
            echo "
            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Password or Username',
                    text: 'Please check your password and username then try again.'
                }).then(() => {
                    window.history.back();
                });
            </script>
            ";
        }
    } 
?>


    <main class="login-page">
        <div class="login-container">
            <div class="left-content">
                <img src="../images/BSU.webp" alt="bsu logo"
                    style="height: 25em; max-width: 100%; background-repeat: no-repeat;  mix-blend-mode: multiply;">
            </div>
            <div class="right-content">
                <h2>LOGIN</h2>

                <form action="" method="POST">
                    <div class="input-box">
                        <input type="text" class="form-control" id="username" name="username" placeholder="Username"
                            required>
                        <span class="input-group-text"><i class="bi bi-person-circle"></i></span>
                    </div>

                    <div class="input-box">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password"
                            required>
                        <span class="input-group-text"><i class="bi bi-eye-slash" id="eyeIcon"></i></span>
                    </div>


                    <a href="forgot-password.php" class="forgot-password">Forgot Password?</a>

                    <button type="submit" name="login">LOGIN</button>
                </form>

            </div>

        </div>

    </main>

    <script>
        let eyeIcon = document.getElementById("eyeIcon");
        let password = document.getElementById("password");

        eyeIcon.onclick = function () {
            if (password.type === "password") {
                password.type = "text";
                eyeIcon.classList.replace("bi-eye-slash", "bi-eye");
            } else {
                password.type = "password";
                eyeIcon.classList.replace("bi-eye", "bi-eye-slash");

            }

        }

    </script>

</body>

</html>