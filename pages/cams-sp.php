<?php
require_once 'camsdatabase.php';

class Crud {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function addFaculty ($firstname, $lastname, $phonenumber, $email, $password) {
        try {
           
            $stmt = $this->conn->prepare("CALL addFaculty(?, ?, ?, ?, ?)");
            
            $stmt->execute([
                $firstname,      
                $lastname,  
                $phonenumber,    
                $email,        
                $password,     
            ]);
            return true;
        } catch (PDOException $e) {
            throw $e;
        }
    }
}
?>