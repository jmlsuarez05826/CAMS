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


    // Fetch users for table
    $users = $crud->getAllUsers();
    ?>

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
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                Add User
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
                    <th>#</th>
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
                                <button type="submit" name="add" class="btn btn-primary">Add</button>

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
            document.getElementById('getCodeBtn').addEventListener('click', function () {
                // Optionally hide the modal first
                const modalEl = document.getElementById('addUserModal');
                const modal = bootstrap.Modal.getInstance(modalEl);
                modal.hide();

                Swal.fire({
                    title: 'Enter Verification Code',
                    html: `
           <input type="text" id="verificationCode" class="swal2-input" placeholder="Verification Code" style="margin-bottom:20px">
            <div style="display:flex; gap:5px; justify-content:flex-end; margin-top:5px;"> 
            <button id="resendBtn" class="swal2-styled" style="flex:1;">Resend</button> 
            <button id="verifyBtn" class="swal2-confirm swal2-styled" style="flex:1;">Verify</button> </div>
        `,
                    showCloseButton: true,
                    showConfirmButton: false,
                    didOpen: () => {
                        const popup = Swal.getPopup();
                        popup.querySelector('#verificationCode').focus();

                        popup.querySelector('#resendBtn').addEventListener('click', () => {
                            Swal.fire('Code resent!', '', 'success');
                        });

                        popup.querySelector('#verifyBtn').addEventListener('click', () => {
                            const code = popup.querySelector('#verificationCode').value;

                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: `You entered: ${code}`,
                            }).then(() => {
                                // Show the Bootstrap modal again
                                const modal = new bootstrap.Modal(modalEl);
                                modal.show();
                            });
                        });
                    }
                });
            });

            //For otp verification
            document.getElementById('getCodeBtn').addEventListener('click', function () {
                const phone = document.getElementById('phone').value;
                const fname = document.querySelector('input[name="fname"]').value;
                const lname = document.querySelector('input[name="lname"]').value;

                fetch('sms-otp.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `phone=${phone}&fname=${fname}&lname=${lname}`
                })
                    .then(res => res.json())
                    .then(data => {
                        console.log(data);

                        if (data.status === "success") {
                            Swal.fire("OTP Sent!", "Check your phone for the verification code", "success");
                        } else {
                            Swal.fire("Error", data.message, "error");
                        }
                    })
                    .catch(err => console.error(err));
            });


            const phoneField = document.getElementById("phone");
            const getCodeBtn = document.getElementById("getCodeBtn");
            const phoneError = document.getElementById("phoneError");

            // Regex: Starts with 09 (11 digits) OR 639 (12 digits)
            const regex = /^(09\d{9}|639\d{9})$/;

            phoneField.addEventListener("input", function () {
                this.value = this.value.replace(/[^0-9]/g, ""); // Only digits allowed
                const isValid = regex.test(this.value);

                if (isValid) {
                    this.classList.remove("is-invalid");
                    phoneError.classList.add("d-none");
                    getCodeBtn.disabled = false; // Enable button
                    getCodeBtn.style.backgroundColor = "blue";
                    getCodeBtn.style.color = "white";
                } else {
                    if (this.value.length > 0) {
                        this.classList.add("is-invalid");
                        phoneError.classList.remove("d-none");
                    }
                    getCodeBtn.disabled = true; // Disable button
                }
            });



        </script>

    </main>
</body>


</html>