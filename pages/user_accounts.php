<?php

require_once '../pages/camsdatabase.php';
require_once '../pages/cams-sp.php';
require_once '../includes/admin-sidebar.php';
require_once '../pages/sms-otp.php';

$crud = new Crud();

// Handle form submission first
if (isset($_POST["add"])) {
    $firstname = $_POST["fname"];
    $lastname = $_POST["lname"];
    $phonenumber = $_POST["phone"];
    $email = $_POST["email"];
    $password = $_POST["password"];

    try {
        if ($crud->addFaculty($firstname, $lastname, $phonenumber, $email, $password)) {
            header("Location: " . $_SERVER['PHP_SELF'] . "?added=1");
            exit();
        }
    } catch (PDOException $e) {
        $errorMessage = $e->getMessage();
    }
}


// Handle form submission first
if (isset($_POST["addAdmin"])) {
    $firstname = $_POST["fname"];
    $lastname = $_POST["lname"];
    $phonenumber = $_POST["phone"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $admintype = $_POST["admintype"];

    try {
        if ($crud->addAdmin($firstname, $lastname, $phonenumber, $email, $password, $admintype)) {
            header("Location: " . $_SERVER['PHP_SELF'] . "?added=1");
            exit();
        }
    } catch (PDOException $e) {
        $errorMessage = $e->getMessage();
    }
}
// Fetch users for table
$users = $crud->getAllUsers();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php $pageTitle = "Dashboard Home";
            echo $pageTitle; ?></title>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="../assets/css/user_accounts.css">



</head>

<body>

    <div class="main-content">
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

    <main>
        <?php
        $rowsPerPage = 10; // number of users per page
        $currentPage = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $offset = ($currentPage - 1) * $rowsPerPage;

        // Fetch only the users for this page
        $users = $crud->getUsersPaginated($rowsPerPage, $offset);

        // Get total user count for calculating total pages
        $totalUsers = $crud->getUsersCount();
        $totalPages = ceil($totalUsers / $rowsPerPage);
        ?>

        <div class="table-container">

            <div class="add-user-btn">
                <button class="btn add-user" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    Add User
                </button>
                <button class="btn add-admin" data-bs-toggle="modal" data-bs-target="#addAdminModal">
                    Add Admin
                </button>
            </div>

            <div class="table-wrapper">
                <table class="users-table">
                    <thead>
                        <tr>
                            <th>First</th>
                            <th>Last</th>
                            <th>Phone Number</th>
                            <th>Email Address</th>
                            <th>Role</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php
                        $counter = 1;
                        foreach ($users as $row) {
                            echo "<tr>";
                            echo "<td>{$row['FirstName']}</td>";
                            echo "<td>{$row['LastName']}</td>";
                            echo "<td>{$row['PhoneNumber']}</td>";
                            echo "<td>{$row['Email']}</td>";
                            echo "<td>{$row['Role']}</td>";


                            //Action buttons
                            echo "<td class='action-buttons'>
                                        <button class='edit-btn' data-id='{$row['UserID']}'>
                                         <i class='bi bi-pencil-square'></i>
                                        </button>

                                        <button class='delete-btn' data-id='{$row['UserID']}'>
                                         <i class='bi bi-trash-fill'></i>
                                        </button>
                                    </td>";
                            echo "</tr>";
                            $counter++;
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <nav class="custom-pagination">
                <ul>
                    <!-- Previous -->
                    <li class="<?= ($currentPage <= 1) ? 'disabled' : '' ?>">
                        <a href="?page=<?= $currentPage - 1 ?>">&laquo;</a>
                    </li>

                    <!-- Page numbers -->
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="<?= ($i == $currentPage) ? 'active' : '' ?>">
                            <a href="?page=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>

                    <!-- Next -->
                    <li class="<?= ($currentPage >= $totalPages) ? 'disabled' : '' ?>">
                        <a href="?page=<?= $currentPage + 1 ?>">&raquo;</a>
                    </li>
                </ul>
            </nav>
        </div>
        </div>

        <!-- Add User Modal -->
        <div class="custom-modal" id="addUserModal">
            <div class="custom-modal-dialog">
                <div class="custom-modal-content">
                    <div class="custom-modal-header">
                        <h5 class="custom-modal-title">Add User</h5>
                        <button type="button" class="custom-close" id="closeAddUserModal">&times;</button>
                    </div>

                    <div class="custom-modal-body">
                        <form method="post" id="addFacultyForm">
                            <div class="form-group">
                                <label>First Name</label>
                                <input type="text" name="fname" required>
                            </div>

                            <div class="form-group">
                                <label>Last Name</label>
                                <input type="text" name="lname" required>
                            </div>

                            <div class="form-group">
                                <label>Phone Number</label>
                                <div class="input-group">
                                    <input type="text" id="phone" name="phone" placeholder="09xxxxxxxxx or 639xxxxxxxxx" required>
                                    <button type="button" id="getCodeBtn" disabled>Get Code</button>
                                </div>
                                <small id="phoneError" class="text-danger">Phone must start with 09 (11 digits) or 639 (12 digits).</small>
                            </div>

                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" name="email" required>
                            </div>

                            <div class="form-group">
                                <label>Password</label>
                                <input type="text" name="password" required>
                            </div>

                            <div class="form-group">
                                <label>Confirm Password</label>
                                <input type="text" name="cpassword" required>
                            </div>

                            <div class="custom-modal-footer">
                                <button type="button" class="btn-close-modal" id="closeAddUserFooter">Close</button>
                                <button type="submit" name="add" id="addBtn" disabled>Add</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>


        <!-- Add Admin Modal (Custom) -->
        <div class="custom-modal" id="addAdminModal">
            <div class="custom-modal-dialog">
                <div class="custom-modal-content">
                    <div class="custom-modal-header">
                        <h5 class="custom-modal-title">Add Admin</h5>
                        <button type="button" class="custom-close" id="closeAddAdminModal">&times;</button>
                    </div>

                    <div class="custom-modal-body">
                        <form method="post" id="addAdminForm">
                            <div class="form-group">
                                <label>First Name</label>
                                <input type="text" name="fname" required>
                            </div>

                            <div class="form-group">
                                <label>Last Name</label>
                                <input type="text" name="lname" required>
                            </div>

                            <div class="form-group">
                                <label>Phone Number</label>
                                <div class="input-group">
                                    <input type="text" id="phoneAdmin" name="phone" placeholder="09xxxxxxxxx or 639xxxxxxxxx" required>
                                    <button type="button" id="getCodeBtnAdmin" disabled>Get Code</button>
                                </div>
                                <small id="phoneErrorAdmin" class="text-danger">Phone must start with 09 (11 digits) or 639 (12 digits).</small>
                            </div>

                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" name="email" required>
                            </div>

                            <div class="form-group">
                                <label>Password</label>
                                <input type="text" name="password" required>
                            </div>

                            <div class="form-group">
                                <label>Confirm Password</label>
                                <input type="text" name="cpassword" required>
                            </div>

                            <div class="form-group">
                                <label>Admin Type</label>
                                <select name="admintype" required>
                                    <option value="" selected disabled>Select Type</option>
                                    <option value="Admin">Admin</option>
                                    <option value="Superadmin">Superadmin</option>
                                </select>
                            </div>

                            <div class="custom-modal-footer">
                                <button type="button" class="btn-close-modal" id="closeAddAdminFooter">Close</button>
                                <button type="submit" id="addAdminBtn" disabled>Add Admin</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>



        <?php if (!empty($errorMessage) || (isset($_GET['added']) && $_GET['added'] == 1)): ?>
            <script>
                <?php if (!empty($errorMessage)): ?>
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: '<?php echo $errorMessage; ?>'
                    });
                <?php endif; ?>

                <?php if (isset($_GET['added']) && $_GET['added'] == 1): ?>
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'User added successfully!'
                    });
                <?php endif; ?>
            </script>
        <?php endif; ?>

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
            //script for the add user modal

            // Open modal
            document.querySelectorAll('[data-bs-toggle="modal"]').forEach(btn => {
                btn.addEventListener('click', () => {
                    const target = btn.getAttribute('data-bs-target');
                    document.querySelector(target).style.display = 'flex';
                });
            });

            // Close modal
            document.querySelectorAll('.custom-close, .btn-close-modal').forEach(btn => {
                btn.addEventListener('click', () => {
                    btn.closest('.custom-modal').style.display = 'none';
                });
            });

            // Optional: close modal if clicking outside content
            document.querySelectorAll('.custom-modal').forEach(modal => {
                modal.addEventListener('click', e => {
                    if (e.target === modal) modal.style.display = 'none';
                });
            });



            const phoneInput = document.getElementById('phone');
            const getCodeBtn = document.getElementById('getCodeBtn');
            const phoneError = document.getElementById('phoneError');
            const addFacultyForm = document.getElementById('addFacultyForm');
            const addUserModalEl = document.getElementById('addUserModal');


            let pendingUserData = {
                verified: false
            };

            // Enable/disable Get Code button
            phoneInput.addEventListener('input', () => {
                const phoneVal = phoneInput.value.trim();
                const regex = /^(09\d{9}|639\d{9})$/;
                if (regex.test(phoneVal)) {
                    getCodeBtn.disabled = false;
                    phoneError.classList.add('d-none');
                } else {
                    getCodeBtn.disabled = true;
                    phoneError.classList.remove('d-none');
                }
            });

            // Send OTP
            getCodeBtn.addEventListener('click', () => {
                pendingUserData = {
                    fname: document.querySelector('input[name="fname"]').value.trim(),
                    lname: document.querySelector('input[name="lname"]').value.trim(),
                    phone: phoneInput.value.trim(),
                    email: document.querySelector('input[name="email"]').value.trim(),
                    password: document.querySelector('input[name="password"]').value.trim(),
                    verified: false
                };


                // Hide custom modal
                addUserModalEl.classList.remove('show');


                fetch('../pages/sms-otp.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `action=send&phone=${pendingUserData.phone}&fname=${pendingUserData.fname}&lname=${pendingUserData.lname}`
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === "success") {
                            showVerificationModal(pendingUserData.phone, data.otp);
                        } else {
                            Swal.fire(" Error", data.message, "error");
                            // Show custom modal again
                            addUserModalEl.classList.add('show');

                        }
                    })
                    .catch(err => {
                        console.error(err);
                        // Show custom modal again
                        addUserModalEl.classList.add('show');

                    });
            });

            // Show OTP modal
            function showVerificationModal(phone, otpValue) {
                Swal.fire({
                    title: 'Verification Code',
                    html: `
            <p id="showOtp" style="font-size:14px; color:#555;">OTP: ${otpValue}</p>
            <input type="text" id="verificationCode" class="swal2-input" placeholder="Enter 6-digit code" maxlength="6" style="margin-bottom:20px; text-align:center; letter-spacing:5px">
            <div style="display:flex; gap:5px; justify-content:flex-end; margin-top:5px;">
                <button id="resendBtn" class="swal2-styled" style="flex:1;">Resend</button>
                <button id="verifyBtn" class="swal2-confirm swal2-styled" style="flex:1;">Verify</button>
            </div>
        `,
                    showCloseButton: true,
                    showConfirmButton: false,
                    didOpen: () => {
                        const popup = Swal.getPopup();
                        const inputField = popup.querySelector('#verificationCode');
                        inputField.focus();
                        inputField.addEventListener('input', () => {
                            inputField.value = inputField.value.replace(/\D/g, '').slice(0, 6);
                        });

                        // Resend OTP
                        popup.querySelector('#resendBtn').addEventListener('click', () => {
                            fetch('../pages/sms-otp.php', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/x-www-form-urlencoded'
                                    },
                                    body: `action=send&phone=${phone}&fname=${pendingUserData.fname}&lname=${pendingUserData.lname}`
                                })
                                .then(res => res.json())
                                .then(resp => {
                                    if (resp.status === "success") {
                                        otpValue = resp.otp || '';
                                        popup.querySelector('#showOtp').textContent = `OTP: ${otpValue}`;
                                        let msg = popup.querySelector('#resendMessage');
                                        if (!msg) {
                                            msg = document.createElement('p');
                                            msg.id = 'resendMessage';
                                            msg.style.color = 'green';
                                            msg.style.fontSize = '13px';
                                            msg.style.marginTop = '5px';
                                            popup.querySelector('#resendBtn').parentElement.appendChild(msg);
                                        }
                                        msg.textContent = 'OTP Resent!';
                                    } else {
                                        Swal.fire(' Error resending OTP', '', 'error');
                                    }
                                });
                        });

                        // Verify OTP
                        popup.querySelector('#verifyBtn').addEventListener('click', () => {
                            const code = inputField.value.trim();
                            if (code.length !== 6) {
                                Swal.fire(" Invalid Code", "OTP must be 6 digits", "error");
                                return;
                            }

                            fetch('../pages/sms-otp.php', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/x-www-form-urlencoded'
                                    },
                                    body: `action=verify&phone=${phone}&otp=${code}`
                                })
                                .then(res => res.json())
                                .then(result => {
                                    if (result.status === "verified") {
                                        Swal.fire(" Verified!", "Phone number confirmed!", "success");
                                        pendingUserData.verified = true;

                                        const addBtn = document.getElementById("addBtn");
                                        addBtn.disabled = false;
                                        addBtn.style.backgroundColor = "#0d6efd";
                                        addBtn.style.cursor = "pointer";

                                        // Show Add User modal again after verification
                                        // Show custom modal again
                                        addUserModalEl.style.display = 'block';

                                    } else {
                                        Swal.fire(" Incorrect Code", result.message, "error");
                                    }
                                });
                        });
                    }
                });
            }

            // Prevent submission if phone not verified
            addFacultyForm.addEventListener('submit', (e) => {
                if (!pendingUserData.verified) {
                    e.preventDefault();
                    Swal.fire(" Verification Required", "Please verify your phone number first.", "warning");
                }
            });

            const phoneInputAdmin = document.getElementById('phoneAdmin');
            const getCodeBtnAdmin = document.getElementById('getCodeBtnAdmin');
            const phoneErrorAdmin = document.getElementById('phoneErrorAdmin');
            const addAdminForm = document.getElementById('addAdminForm');
            const addAdminModalEl = document.getElementById('addAdminModal');


            let pendingAdminData = {
                verified: false
            };

            // Enable/disable Get Code button
            phoneInputAdmin.addEventListener('input', () => {
                const phoneVal = phoneInputAdmin.value.trim();
                const regex = /^(09\d{9}|639\d{9})$/;
                if (regex.test(phoneVal)) {
                    getCodeBtnAdmin.disabled = false;
                    phoneErrorAdmin.classList.add('d-none');
                } else {
                    getCodeBtnAdmin.disabled = true;
                    phoneErrorAdmin.classList.remove('d-none');
                }
            });

            // Send OTP
            getCodeBtnAdmin.addEventListener('click', () => {
                pendingAdminData = {
                    fname: document.querySelector('#addAdminForm input[name="fname"]').value.trim(),
                    lname: document.querySelector('#addAdminForm input[name="lname"]').value.trim(),
                    phone: phoneInputAdmin.value.trim(),
                    email: document.querySelector('#addAdminForm input[name="email"]').value.trim(),
                    password: document.querySelector('#addAdminForm input[name="password"]').value.trim(),
                    verified: false
                };

                addAdminModalEl.style.display = 'none';


                fetch('../pages/sms-otp.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `action=send&phone=${pendingAdminData.phone}&fname=${pendingAdminData.fname}&lname=${pendingAdminData.lname}`
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === "success") {
                            showAdminVerificationModal(pendingAdminData.phone, data.otp);
                        } else {
                            Swal.fire("Error", data.message, "error");
                            addAdminModalEl.style.display = 'flex';

                        }
                    })
                    .catch(err => {
                        console.error(err);
                        addAdminModalEl.style.display = 'flex';

                    });
            });

            // OTP verification function (similar to Add User)
            function showAdminVerificationModal(phone, otpValue) {
                Swal.fire({
                    title: 'Verification Code',
                    html: `
            <p id="showOtp" style="font-size:14px; color:#555;">OTP: ${otpValue}</p>
            <input type="text" id="verificationCodeAdmin" class="swal2-input" placeholder="Enter 6-digit code" maxlength="6" style="margin-bottom:20px; text-align:center; letter-spacing:5px">
            <div style="display:flex; gap:5px; justify-content:flex-end; margin-top:5px;">
                <button id="resendBtnAdmin" class="swal2-styled" style="flex:1;">Resend</button>
                <button id="verifyBtnAdmin" class="swal2-confirm swal2-styled" style="flex:1;">Verify</button>
            </div>
        `,
                    showCloseButton: true,
                    showConfirmButton: false,
                    didOpen: () => {
                        const popup = Swal.getPopup();
                        const inputField = popup.querySelector('#verificationCodeAdmin');
                        inputField.focus();
                        inputField.addEventListener('input', () => {
                            inputField.value = inputField.value.replace(/\D/g, '').slice(0, 6);
                        });

                        // Resend OTP
                        popup.querySelector('#resendBtnAdmin').addEventListener('click', () => {
                            fetch('../pages/sms-otp.php', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/x-www-form-urlencoded'
                                    },
                                    body: `action=send&phone=${phone}&fname=${pendingAdminData.fname}&lname=${pendingAdminData.lname}`
                                })
                                .then(res => res.json())
                                .then(resp => {
                                    if (resp.status === "success") {
                                        otpValue = resp.otp || '';
                                        popup.querySelector('#showOtp').textContent = `OTP: ${otpValue}`;
                                    } else Swal.fire('Error resending OTP', '', 'error');
                                });
                        });

                        // Verify OTP
                        popup.querySelector('#verifyBtnAdmin').addEventListener('click', () => {
                            const code = inputField.value.trim();
                            if (code.length !== 6) {
                                Swal.fire("Invalid Code", "OTP must be 6 digits", "error");
                                return;
                            }
                            fetch('../pages/sms-otp.php', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/x-www-form-urlencoded'
                                    },
                                    body: `action=verify&phone=${phone}&otp=${code}`
                                })
                                .then(res => res.json())
                                .then(result => {
                                    if (result.status === "verified") {
                                        Swal.fire("Verified!", "Phone number confirmed!", "success");
                                        pendingAdminData.verified = true;

                                        const addBtn = document.getElementById("addAdminBtn");
                                        addBtn.disabled = false;
                                        addBtn.style.backgroundColor = "#198754";
                                        addBtn.style.cursor = "pointer";

                                        addAdminModal.show();
                                    } else Swal.fire("Incorrect Code", result.message, "error");
                                });
                        });
                    }
                });
            }

            // Prevent submission if phone not verified
            addAdminForm.addEventListener('submit', (e) => {
                if (!pendingAdminData.verified) {
                    e.preventDefault();
                    Swal.fire("Verification Required", "Please verify your phone number first.", "warning");
                }
            });
        </script>


    </main>
</body>


</html>