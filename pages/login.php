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
        

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <!-- Script for the modal confirmation -->
        <!-- SweetAlert2 CDN -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <!-- SweetAlert2 CDN -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <!-- SweetAlert2 CDN -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
                                                html: `
                                        <input type="text" id="otpCode" class="swal2-input" placeholder="Enter code">
                                    `,
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




    </main>

</body>

</html>