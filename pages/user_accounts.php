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
    <link rel="stylesheet" href="../assets/css/admin-header.css">

    <style>
        .login-page {
            display: flex;
            justify-content: center;
            align-items: center;
            height: calc(100vh - 80px);
            /* full viewport minus header */
        }

        /* White login box */
        .login-container {
            background-color: white;
            padding: 40px 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            width: 700px;
            /* adjust as needed */
            text-align: center;
        }

        /* Headings */
        .login-container h2 {
            margin-top: 0px;
            margin-bottom: 30px;
            font-size: 47px;
            font-family: 'Ibarra Real Nova', serif;
            color: black;
            text-align: left;
            margin-left: 30px;
        }

        /* Form fields */
        .login-container form {
            display: flex;
            flex-direction: column;
        }

        .login-container input {
            padding: 10px;
            margin-bottom: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            background-color: #E0E0E0;
        }

        /* Login button */
        .login-container button {
            padding: 10px;
            background-color: #00700D;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s ease;
            align-items: right;
            width: 100px;
        }

        .login-container button:hover {
            background-color: #a10f23;
        }

        /* Base styles for all form groups */
        .form-group-fname,
        .form-group-lname,
        .form-group-role,
        .form-group-email,
        .form-group-phone,
        .form-group-password,
        .form-group-cpassword {
            display: flex;
            /* makes label + input side by side */
            align-items: center;
            /* vertically center them */
            margin-bottom: 10px;
            padding: 5px;
        }

        /* Individual widths */
        .form-group-fname {
            width: 400px;
        }
        .form-group-lname {
            width: 400px;
        }

        .form-group-role {
            width: 300px;
        }

        .form-group-email {
            width: 450px;
        }

        .form-group-phone {
            width: 350px;
        }

        .form-group-password{
            width: 350px;
        }

        .form-group-cpassword{
            width: 350px;
        }

        /* Labels (consistent styling) */
        .form-group-fname label,
        .form-group-lname label,
        .form-group-role label,
        .form-group-email label,
        .form-group-phone label,
        .form-group-password label,
        .form-group-cpassword label {
            width: 100px;
            /* fixed label width for alignment */
            font-weight: bold;
            margin-right: 0px;
            /* space between label and input */
        }

        /* Inputs */
        .form-group-fname input,
        .form-group-lname input,
        .form-group-role input,
        .form-group-email input,
        .form-group-phone input
        .form-group-password input,
        .form-group-cpassword input {
            flex: 1;
            /* input fills remaining space */
            padding: 5px;
        }


        .button-container {
            display: flex;
            justify-content: flex-end;
            /* moves button to the right */
            margin-top: 10px;
        }
    </style>
</head>

<body>

    <?php include '../includes/admin-header.php'; ?> <!-- This brings in your navbar & sidebar -->


    <!-- User Creation Container -->
    <main class="login-page">
        <div class="login-container">
            <h2>Faculty</h2>

            <form action="" method="post">

                <form>
                    <div class="form-group-fname">
                        <label for="fname">First Name:</label>
                        <input type="text" id="fname" name="fname" placeholder="First Name" required>
                    </div>
                    <div class="form-group-lname">
                        <label for="lname">Last Name:</label>
                        <input type="text" id="lname" name="lname" placeholder="Last Name" required>
    </div>
                    <div class="form-group-phone">
                        <label for="phone">Phone Number:</label>
                        <input type="number" id="phone" name="phone" placeholder="Phone Number" required>
                    </div>

                    <div class="form-group-email">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" placeholder="Email" required>
                    </div>

                    <div class="form-group-password">
                        <label for="password">Password:</label>
                        <input type="text" id="password" name="password" placeholder="Password" required>
                    </div>

                    <div class="form-group-cpassword">
                        <label for="cpassword">Confirm Password:</label>
                        <input type="text" id="cpassword" name="cpassword" placeholder="Confirm Password" required>
                    </div>

                    <div class="button-container">
                        <button type="add" name="add">Add</button>
                    </div>

                </form>
            </form>

    </main>


</body>

</html>