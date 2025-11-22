<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — CAMS</title>
    <link rel="stylesheet" href="../assets/css/login.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Ibarra+Real+Nova:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link href='https://fonts.googleapis.com/css?family=Poppins' rel='stylesheet'>
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

</head>

<body>


    <?php
    session_start();
    require_once '../pages/camsdatabase.php';
    require_once '../pages/cams-sp.php';



    if (isset($_POST['login'])) {

        $number = $_POST['num'];
        $pass = $_POST['password'];

        $database = new Database();
        $conn = $database->getConnection();

        $crud = new Crud();
        $user = $crud->userLogin($number, $pass);

        if ($user) {
            $_SESSION['UserID'] = $user['UserID'];
            $_SESSION['FirstName'] = $user['FirstName'];
            $_SESSION['PhoneNumber'] = $user['PhoneNumber'];
            $_SESSION['LastName'] = $user['LastName'];
            $_SESSION['Role'] = $user['Role'];
            $_SESSION['AdminType'] = $user['AdminType'];

            echo "
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Login Successful!',
                text: 'Welcome, {$user['FirstName']}',
                timer: 1500,
                showConfirmButton: false,
                scrollbarPadding: false, // prevents layout shift
                allowOutsideClick: false,
                didOpen: () => {
                document.body.style.overflow = 'hidden';
                },
                willClose: () => {
                document.body.style.overflow = '';
                }

            }).then(() => {";

            if ($user['Role'] === 'Admin') {
                if ($user['AdminType'] === 'Superadmin') {
                    echo "window.location.href = '../pages/user_accounts.php';";
                } else {
                    echo "window.location.href = '../pages/admin-dash.php';";
                }
            } elseif ($user['Role'] === 'Faculty') {
                echo "window.location.href = '../pages/faculty-reservation.php';";
            }

            echo "});
        </script>";
        } else {
            echo "
            <script>
            Swal.fire({
             icon: 'error',
    title: 'Login Failed',
    text: 'Invalid number or password',
    scrollbarPadding: false,
    allowOutsideClick: false
});
</script>";
        }
    }
    ?>


    <!-- Login Page Container -->
    <div class="login-container">

        <!-- Left Image / Branding -->
        <div class="left-content">
            <a class="go-back" href="../pages/landingpage.php">
                <i class="bi bi-arrow-left-circle-fill"></i>
                <span>GO BACK</span>
            </a>
            <img src="../images/BSU.webp" alt="BSU Logo" class="logo-img">
            <h2>Welcome to CAMS</h2>
            <p>Classroom Availability Management System</p>
        </div>

        <!-- Right Form -->
        <div class="right-content">
            <h2>Login</h2>
            <form id="loginForm" action="" method="POST">
                <div class="input-box">
                    <input type="text" name="num" id="num" placeholder="Phone Number">
                    <span class="input-group-text"><i class="bi bi-person-circle"></i></span>
                </div>

                <div class="input-box">
                    <input type="password" name="password" id="password" placeholder="Password">
                    <span class="input-group-text"><i class="bi bi-eye-slash" id="eyeIcon"></i></span>
                </div>

                <a href="forgot-password.php" class="forgot-password">Forgot Password?</a>

                <div class="g-recaptcha" id="captcha" data-sitekey="6LezbQUsAAAAAMbmcqBhWd1PKyAv2Bx0ZfcYLRKC"
                    data-callback="enableLoginBtn" data-expired-callback="disableLoginBtn"
                    style="display:flex; justify-content:center; align-items:center;">
                </div>

                <button type="submit" name="login" id="loginbtn" disabled>LOGIN</button>
            </form>
        </div>


        <script>
            // Password show/hide
            const eyeIcon = document.getElementById("eyeIcon");
            const password = document.getElementById("password");

            eyeIcon.onclick = function() {
                if (password.type === "password") {
                    password.type = "text";
                    eyeIcon.classList.replace("bi-eye-slash", "bi-eye");
                } else {
                    password.type = "password";
                    eyeIcon.classList.replace("bi-eye", "bi-eye-slash");
                }
            }

            function enableLoginBtn() {
                const btn = document.getElementById("loginbtn");
                btn.disabled = false;
                btn.classList.remove("disabled");
                btn.classList.add("enabled");
            }

            function disableLoginBtn() {
                const btn = document.getElementById("loginbtn");
                btn.disabled = true;
                btn.classList.remove("enabled");
                btn.classList.add("disabled");
            }
        </script>


        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const forgotLink = document.querySelector('.forgot-password');

                forgotLink.addEventListener('click', function(event) {
                    event.preventDefault(); // prevent navigation

                    // Step 1: Confirmation
                    Swal.fire({
                        title: 'Change Password?',
                        text: 'Do you want to reset your password?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, proceed',
                        cancelButtonText: 'Cancel',
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                    }).then((result) => {
                        if (result.isConfirmed) {

                            // Step 2: Phone number modal
                            Swal.fire({
                                title: 'Verify Phone Number',
                                html: `
                        <input type="text" id="phoneNumber" class="swal2-input" placeholder="Enter your phone number">
                        <div style="display:flex; justify-content:center; gap:10px; margin-top:10px;">
                            <button id="verifyBtn" class="swal2-confirm swal2-styled" style="background-color:#3085d6;">Verify</button>
                            <button id="resendBtn" class="swal2-cancel swal2-styled" style="background-color:#6c757d;">Resend</button>
                        </div>
                    `,
                                showConfirmButton: false,
                                allowOutsideClick: false,
                                didOpen: () => {
                                    const popup = Swal.getPopup();
                                    const verifyBtn = popup.querySelector('#verifyBtn');
                                    const resendBtn = popup.querySelector('#resendBtn');
                                    const phoneInput = popup.querySelector('#phoneNumber');

                                    // Verify button click
                                    verifyBtn.addEventListener('click', () => {
                                        const phone = phoneInput.value.trim();
                                        if (phone === '') {
                                            Swal.showValidationMessage('Please enter your phone number');
                                            return;
                                        }

                                        // Simulate sending code
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Code Sent!',
                                            text: 'A verification code has been sent to your phone.',
                                            confirmButtonText: 'Next',
                                        }).then(() => {

                                            // Step 3: OTP input modal
                                            Swal.fire({
                                                title: 'Enter Verification Code',
                                                html: `<input type="text" id="otpCode" class="swal2-input" placeholder="Enter code">`,
                                                confirmButtonText: 'Verify Code',
                                                cancelButtonText: 'Cancel',
                                                showCancelButton: true,
                                                allowOutsideClick: false,
                                                preConfirm: () => {
                                                    const code = Swal.getPopup().querySelector('#otpCode').value.trim();
                                                    if (code === '') {
                                                        Swal.showValidationMessage('Please enter the code.');
                                                        return false;
                                                    }
                                                    // Simulate checking the code
                                                    if (code !== '123456') { // example validation
                                                        Swal.showValidationMessage('Invalid verification code.');
                                                        return false;
                                                    }
                                                }
                                            }).then((otpResult) => {
                                                if (otpResult.isConfirmed) {
                                                    Swal.fire({
                                                        icon: 'success',
                                                        title: 'Verified!',
                                                        text: 'Redirecting to password reset page...',
                                                        showConfirmButton: false,
                                                        timer: 2000,
                                                        willClose: () => {
                                                            window.location.href = 'forgot-password.php';
                                                        }
                                                    });
                                                }
                                            });

                                        });
                                    });

                                    // Resend button click
                                    resendBtn.addEventListener('click', () => {
                                        Swal.fire({
                                            icon: 'info',
                                            title: 'Code Resent!',
                                            text: 'A new code has been sent to your number.',
                                            confirmButtonText: 'OK'
                                        });
                                    });

                                }
                            });

                        }
                    });
                });
            });
        </script>

</body>

</html>