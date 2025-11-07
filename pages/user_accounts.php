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

    <!-- Link CSS for the header -->
    <link rel="stylesheet" href="../assets/css/admin-sidebar.css">
    <link rel="stylesheet" href="../assets/css/user_accounts.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap JS Bundle (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


    <header>
        <h2>Welcome Admin!</h2>
        <div class="search-container">
            <input type="text" placeholder="Search" class="search-field">
            <i class="bi bi-search search-icon"></i>
            <i class="bi bi-bell-fill notification-icon"></i>
        </div>
    </header>


</head>

<body>
    <main>

    <div class="d-flex justify-content-end mb-3 add-user-btn">
    <button class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#addUserModal">
        Add User
    </button>
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addAdminModal">
        Add Admin
    </button>
</div>


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


        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>First</th>
                    <th>Last</th>
                    <th>Phone Number</th>
                    <th>Email Address</th>
                    <th>Role</th>
                </tr>
            </thead>
            <tbody>

                <?php
                $counter = 1;
                foreach ($users as $row) {
                    echo "<tr>";
                    echo "<th scope='row'>{$row['UserID']}</th>";
                    echo "<td>{$row['FirstName']}</td>";
                    echo "<td>{$row['LastName']}</td>";
                    echo "<td>{$row['PhoneNumber']}</td>";
                    echo "<td>{$row['Email']}</td>";
                    echo "<td>{$row['Role']}</td>";
                    echo "</tr>";
                    $counter++;
                }
                ?>

            </tbody>
        </table>

        <nav aria-label="Page navigation example">
            <ul class="pagination">
                <!-- Previous -->
                <li class="page-item <?= ($currentPage <= 1) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $currentPage - 1 ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>

                <!-- Page numbers -->
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= ($i == $currentPage) ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>

                <!-- Next -->
                <li class="page-item <?= ($currentPage >= $totalPages) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $currentPage + 1 ?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>


        <!-- Add User Modal -->
        <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addFacultyModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addFacultyModalLabel">Add User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <form method="post" id="addFacultyForm">
                            <div class="mb-3">
                                <label>First Name</label>
                                <input type="text" name="fname" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label>Last Name</label>
                                <input type="text" name="lname" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <div class="input-group">
                                    <input type="text" id="phone" name="phone" class="form-control"
                                        placeholder="09xxxxxxxxx or 639xxxxxxxxx" pattern="^(09\d{9}|639\d{9})$"
                                        required>

                                    <button class="btn btn-outline-secondary" type="button" id="getCodeBtn" disabled>
                                        Get Code
                                    </button>
                                </div>
                                <small id="phoneError" class="text-danger d-none">
                                    Phone must start with 09 (11 digits) or 639 (12 digits).
                                </small>
                            </div>

                            <div class="mb-3">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label>Password</label>
                                <input type="text" name="password" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label>Confirm Password</label>
                                <input type="text" name="cpassword" class="form-control" required>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" name="add" class="btn btn-primary" id="addBtn"
                                    disabled>Add</button>


                            </div>
                        </form>
                    </div>


                </div>
            </div>
        </div>

        <!-- Add Admin Modal -->
<div class="modal fade" id="addAdminModal" tabindex="-1" aria-labelledby="addAdminModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Admin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <form method="post" id="addAdminForm">
                    <div class="mb-3">
                        <label>First Name</label>
                        <input type="text" name="fname" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Last Name</label>
                        <input type="text" name="lname" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="phoneAdmin" class="form-label">Phone Number</label>
                        <div class="input-group">
                            <input type="text" id="phoneAdmin" name="phone" class="form-control"
                                   placeholder="09xxxxxxxxx or 639xxxxxxxxx" pattern="^(09\d{9}|639\d{9})$" required>
                            <button class="btn btn-outline-secondary" type="button" id="getCodeBtnAdmin" disabled>
                                Get Code
                            </button>
                        </div>
                        <small id="phoneErrorAdmin" class="text-danger d-none">
                            Phone must start with 09 (11 digits) or 639 (12 digits).
                        </small>
                    </div>

                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Password</label>
                        <input type="text" name="password" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Confirm Password</label>
                        <input type="text" name="cpassword" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Admin Type</label>
                        <select name="admintype" class="form-select" required>
                            <option value="" selected disabled>Select Type</option>
                            <option value="Admin">Admin</option>
                            <option value="Superadmin">Superadmin</option>
                        </select>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="addAdmin" class="btn btn-success" id="addAdminBtn" disabled>Add Admin</button>
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
            const phoneInput = document.getElementById('phone');
            const getCodeBtn = document.getElementById('getCodeBtn');
            const phoneError = document.getElementById('phoneError');
            const addFacultyForm = document.getElementById('addFacultyForm');
            const addUserModalEl = document.getElementById('addUserModal');
            const addUserModal = new bootstrap.Modal(addUserModalEl);

            let pendingUserData = { verified: false };

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

                // Hide Add User modal
                addUserModal.hide();

                fetch('../pages/sms-otp.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `action=send&phone=${pendingUserData.phone}&fname=${pendingUserData.fname}&lname=${pendingUserData.lname}`
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === "success") {
                            showVerificationModal(pendingUserData.phone, data.otp);
                        } else {
                            Swal.fire(" Error", data.message, "error");
                            // Show Add User modal again on error
                            addUserModal.show();
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        addUserModal.show();
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
                                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
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
                                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
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
                                        addUserModal.show();
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
const addAdminModal = new bootstrap.Modal(addAdminModalEl);

let pendingAdminData = { verified: false };

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

    addAdminModal.hide();

    fetch('../pages/sms-otp.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=send&phone=${pendingAdminData.phone}&fname=${pendingAdminData.fname}&lname=${pendingAdminData.lname}`
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === "success") {
            showAdminVerificationModal(pendingAdminData.phone, data.otp);
        } else {
            Swal.fire("Error", data.message, "error");
            addAdminModal.show();
        }
    })
    .catch(err => {
        console.error(err);
        addAdminModal.show();
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
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
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
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
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