<?php
require_once 'camsdatabase.php';
require_once 'cams-sp.php';

$db = new Database();
$conn = $db->getConnection();

$crud = new Crud($conn);

if (isset($_POST["add"])) {
$firstname = $_POST["fname"];
$lastname = $_POST["lname"];
$phonenumber = $_POST["phone"];
$email = $_POST["email"];
$password = $_POST["password"];

if($crud->addFaculty($firstname, $lastname, $phonenumber, $email, $password)) {
    echo 'what a nice';
} else {
    echo 'not a nice';
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

<
    <body>
    <main>

        <div class="d-flex justify-content-end mb-3 add-user-btn">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                Add User
            </button>
        </div>

        <?php
        $rowsPerPage = 10; // number of users per page
        $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
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
        <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addFacultyModalLabel" aria-hidden="true">
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
                                    <input type="number" id="phone" name="phone" class="form-control" placeholder="Phone Number" required>
                                    <button class="btn btn-outline-secondary" type="button" id="getCodeBtn">Get Code</button>
                                </div>
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
                        </form>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="add" form="addFacultyForm" class="btn btn-primary">Add</button>
                    </div>
                </div>
            </div>
        </div>

        <?php if (!empty($errorMessage) || (isset($_GET['added']) && $_GET['added'] == 1)) : ?>
            <script>
                <?php if (!empty($errorMessage)) : ?>
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: '<?php echo $errorMessage; ?>'
                    });
                <?php endif; ?>

                <?php if (isset($_GET['added']) && $_GET['added'] == 1) : ?>
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'User added successfully!'
                    });
                <?php endif; ?>
            </script>
        <?php endif; ?>

        <script>
            document.getElementById('getCodeBtn').addEventListener('click', function() {
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
                <button id="verifyBtn" class="swal2-confirm swal2-styled" style="flex:1;">Verify</button>
            </div>
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
                            Swal.fire(`You entered: ${code}`, '', 'success');
                        });
                    }
                });
            });
        </script>

    </main>
    </body>


</html>