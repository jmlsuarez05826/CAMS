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

            <script src="../js/user_accounts.js"></script>

</body>


</html>