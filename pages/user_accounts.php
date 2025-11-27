<?php
session_start();

if (!isset($_SESSION['UserID']) || empty($_SESSION['UserID'])) {
    header("Location: ../pages/login.php");
    exit();
}

if (!isset($_SESSION['Role']) || $_SESSION['Role'] !== 'Admin') {
    header("Location: ../pages/login.php");
    exit();
}

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

// Handle delete request
if (isset($_POST["deleteUser"])) {
    $userId = $_POST["deleteUser"];
    if ($crud->deleteUser($userId)) {
        header("Location: " . $_SERVER['PHP_SELF'] . "?deleted=1");
        exit();
    }
}
if (isset($_POST["updateUser"])) {
    $userId = $_POST["UserID"];
    $fname = $_POST["fname"];
    $lname = $_POST["lname"];
    $phone = $_POST["phone"];
    $email = $_POST["email"];

    if ($crud->updateUser($userId, $fname, $lname, $email)) {
        header("Location: " . $_SERVER['PHP_SELF'] . "?updated=1");
        exit();
    }
}




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
                    <input type="text" id="search-field" placeholder="Search">
                </div>

                <div class="topbar-right">
                    <div class="notification-icon">
                        <i class="bi bi-bell-fill notification-icon"></i>
                    </div>

                    <div class="profile-info">
                        <i class="bi bi-person-circle profile-icon"></i>
                        <div class="profile-text">
                            <p class="profile-name">
                                <?php echo $_SESSION['FirstName'] . " " . $_SESSION['LastName']; ?>
                            </p>
                            <p class="profile-number"> <?php echo $_SESSION['PhoneNumber'] ?></p>
                            <p class="profile-time" id="time"></p>
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
                <!-- Place this div below your table -->
                <div id="no-results" style="display:none; text-align:center; margin-top:20px;">
                    <img src="../images/no-results.png" alt="No Results"
                        style="width:70px; height:auto; margin-bottom:10px;">
                    <p>No users found</p>
                </div>


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
                                <input type="text" name="fname" placeholder="First Name..." required>
                            </div>

                            <div class="form-group">
                                <label>Last Name</label>
                                <input type="text" name="lname" placeholder="Last Name..." required>
                            </div>

                            <div class="form-group">
                                <label>Phone Number</label>
                                <div class="input-group">

                                    <div class="input-group phone-group">
                                        <input ttype="text" id="phone" name="phone"
                                            placeholder="09xxxxxxxxx or 639xxxxxxxxx" placeholder="Enter phone number">
                                        <button type="button" id="getCodeBtn" class="btn btn-danger" disabled>Get
                                            Code</button>
                                    </div>
                                    <small id="phoneError" class="text-danger">
                                        Phone must start with 09 (11 digits) or 639 (12 digits).
                                    </small>
                                </div>



                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" name="email" placeholder="Email..." required>
                                </div>

                                <div class="form-group">
                                    <label>Password</label>
                                    <input type="text" name="password" placeholder="Password..." required>
                                </div>

                                <div class="form-group">
                                    <label>Confirm Password</label>
                                    <input type="text" name="cpassword" placeholder="Confirm Password..." required>
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





    </main>
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

                                    <div class="input-group phone-group">
                                        <input ttype="text" id="phoneAdmin" name="phone"
                                            placeholder="09xxxxxxxxx or 639xxxxxxxxx" placeholder="Enter phone number">
                                        <button type="button" id="getCodeBtnAdmin" class="btn btn-danger" disabled>Get
                                            Code</button>
                                    </div>
                                    <small id="phoneError" class="text-danger">
                                        Phone must start with 09 (11 digits) or 639 (12 digits).
                                    </small>
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
                                    <button type="button" class="btn-close-modal"
                                        id="closeAddAdminFooter">Close</button>
                                    <button type="submit" name="addAdmin" id="addAdminBtn" disabled>Add Admin</button>
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
        document.addEventListener('DOMContentLoaded', () => {
            const searchInput = document.getElementById('search-field');
            const table = document.querySelector('.users-table tbody');
            const noResultsDiv = document.getElementById('no-results');

            searchInput.addEventListener('input', function () {
                const query = this.value.toLowerCase();
                let anyVisible = false;

                Array.from(table.rows).forEach(row => {
                    const rowText = Array.from(row.cells)
                        .map(cell => cell.textContent.toLowerCase())
                        .join(' ');

                    if (rowText.includes(query)) {
                        row.style.display = '';
                        anyVisible = true;
                    } else {
                        row.style.display = 'none';
                    }
                });

                noResultsDiv.style.display = anyVisible ? 'none' : 'block';
            });
        });

    </script>

    <script>



        // Script for real-time day & 12-hour format time
        function updateTimeDay() {
            const now = new Date();

            // Get day
            const days = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
            const day = days[now.getDay()];

            // Get hours and minutes
            let hours = now.getHours();
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            const ampm = hours >= 12 ? 'PM' : 'AM';

            // Convert 24-hour to 12-hour format
            hours = hours % 12;
            hours = hours ? hours : 12; // the hour '0' should be '12'
            hours = String(hours).padStart(2, '0');

            // Set the text content
            document.getElementById('time').textContent = `${day}, ${hours}:${minutes}:${seconds} ${ampm}`;
        }

        // Update every second
        setInterval(updateTimeDay, 1000);

        // Initial call
        updateTimeDay();


        // Delete Logic with SweetAlert2
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                let userId = this.getAttribute('data-id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "This will mark the user as inactive.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        let form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '';
                        let input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'deleteUser';
                        input.value = userId;
                        form.appendChild(input);
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            });
        });

        // Update Logic with SweetAlert2
        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                let userId = this.getAttribute('data-id');

                Swal.fire({
                    title: 'Update User',
                    html:
                        '<input id="fname" class="swal2-input" placeholder="First Name">' +
                        '<input id="lname" class="swal2-input" placeholder="Last Name">' +
                        '<input id="phone" class="swal2-input" placeholder="Phone">' +
                        '<input id="email" class="swal2-input" placeholder="Email">',
                    focusConfirm: false,
                    showCancelButton: true,
                    confirmButtonText: 'Save',
                    preConfirm: () => {
                        return {
                            fname: document.getElementById('fname').value,
                            lname: document.getElementById('lname').value,
                            phone: document.getElementById('phone').value,
                            email: document.getElementById('email').value
                        }
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        let form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '';

                        let flagInput = document.createElement('input');
                        flagInput.type = 'hidden';
                        flagInput.name = 'updateUser';
                        flagInput.value = '1'; // just a flag
                        form.appendChild(flagInput);

                        let idInput = document.createElement('input');
                        idInput.type = 'hidden';
                        idInput.name = 'UserID'; // matches PHP handler
                        idInput.value = userId;
                        form.appendChild(idInput);


                        let fnameInput = document.createElement('input');
                        fnameInput.type = 'hidden';
                        fnameInput.name = 'fname';
                        fnameInput.value = result.value.fname;
                        form.appendChild(fnameInput);

                        let lnameInput = document.createElement('input');
                        lnameInput.type = 'hidden';
                        lnameInput.name = 'lname';
                        lnameInput.value = result.value.lname;
                        form.appendChild(lnameInput);

                        let phoneInput = document.createElement('input');
                        phoneInput.type = 'hidden';
                        phoneInput.name = 'phone';
                        phoneInput.value = result.value.phone;
                        form.appendChild(phoneInput);

                        let emailInput = document.createElement('input');
                        emailInput.type = 'hidden';
                        emailInput.name = 'email';
                        emailInput.value = result.value.email;
                        form.appendChild(emailInput);

                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            });
        });


        // DEBUG: Track modal triggers
        document.querySelectorAll('[data-bs-toggle="modal"]').forEach(btn => {

            console.log("⏺ BUTTON FOUND:", btn);  // DEBUG

            btn.addEventListener('click', (e) => {
                e.preventDefault();   // DEBUG: stop default behavior first
                e.stopPropagation();  // DEBUG: stop Bootstrap from interfering

                const target = btn.getAttribute('data-bs-target');
                console.log("➡ CLICKED. TARGET:", target);  // DEBUG

                const modal = document.querySelector(target);

                if (!modal) {
                    console.error("❌ Modal NOT FOUND:", target);
                    return;
                }

                console.log("✅ Modal FOUND:", modal);

                // Force open your custom modal
                modal.style.display = 'flex';

                console.log("🎉 Modal DISPLAY applied:", modal.style.display);
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
              addUserModalEl.style.display = 'none';


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
                        addUserModalEl.style.display = 'flex';

                    }
                })
                .catch(err => {
                    console.error(err);
                    // Show custom modal again
                     addUserModalEl.style.display = 'flex';

                });
        });

        // Show OTP modal
        function showVerificationModal(phone, otpValue) {
            addUserModalEl.style.display = 'none';
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

                                    Swal.fire("Verified!", "Phone number confirmed!", "success").then(() => {
                                        // ✅ This runs only after clicking OK on Swal

                                        pendingUserData.verified = true;

                                        // ENABLE ADD BUTTON
                                        const addBtn = document.getElementById("addBtn");
                                        addBtn.disabled = false;
                                        addBtn.style.backgroundColor = "#0d6efd";
                                        addBtn.style.cursor = "pointer";

                                        // SHOW THE ADD USER MODAL AFTER CONFIRMATION
                                        addUserModalEl.style.display = 'flex';
                                    });

                                } else {

                                    Swal.fire("Incorrect Code", result.message, "error").then(() => {
                                        // SHOW MODAL AFTER OK EVEN ON ERROR
                                        addUserModalEl.style.display = 'flex';
                                    });
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


        const closeAddAdminBtns = document.querySelectorAll('#closeAddAdminModal, #closeAddAdminFooter');

        closeAddAdminBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                addAdminModalEl.style.display = 'none';
            });
        });



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
                                    Swal.fire("Verified!", "Phone number confirmed!", "success").then(() => {
                                        pendingAdminData.verified = true;

                                        const addBtn = document.getElementById("addAdminBtn");
                                        addBtn.disabled = false;
                                        addBtn.style.backgroundColor = "#198754";
                                        addBtn.style.cursor = "pointer";

                                        // Show the modal AFTER Swal closes
                                        addAdminModalEl.style.display = 'flex';
                                    });

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


</body>


</html>