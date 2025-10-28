<?php
require_once 'camsdatabase.php';

class Crud
{
    private $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function addFaculty($firstname, $lastname, $phonenumber, $email, $password)
    {
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
            echo "Database Error: " . $e->getMessage();

        }
    }

    public function getAllUsers()
    {
        // Direct query to retrieve all users so no need for sp, can be changed
        $stmt = $this->conn->prepare("SELECT UserID, FirstName, LastName, PhoneNumber, Email, Role FROM users ORDER BY UserID ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUsersPaginated($limit, $offset)
    {
        $stmt = $this->conn->prepare("SELECT * FROM users ORDER BY UserID LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUsersCount()
    {
        $stmt = $this->conn->query("SELECT COUNT(*) as total FROM users");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }
}


