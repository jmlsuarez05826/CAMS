<?php
require_once 'camsdatabase.php'; 

$db = new Database();
$conn = $db->getConnection();


if ($conn) { //for testing connection only
    echo "Database connection successful!";
} else {
    echo "Database connection failed.";
}
<<<<<<< HEAD

?>
=======
?>
>>>>>>> a048d73ae2422fc64d13fa52be6ee3919e4d2f56
