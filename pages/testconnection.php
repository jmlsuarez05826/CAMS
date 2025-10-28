<?php
require_once 'camsdatabase.php'; 

$db = new Database();
$conn = $db->getConnection();


if ($conn) { //for testing connection only
    echo "Database connection successful!";
} else {
    echo "Database connection failed.";
}

?>
