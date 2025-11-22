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
        $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUsersCount()
    {
        $stmt = $this->conn->query("SELECT COUNT(*) as total FROM users");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    public function getRoomsCount()
    {
        $stmt = $this->conn->query("SELECT COUNT(*) as total FROM rooms");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    public function getBuildings()
    {
        try {
            $stmt = $this->conn->prepare("CALL GetBuildings()");
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            return $result;
        } catch (PDOException $e) {
            echo "Database Error: " . $e->getMessage();
            return [];
        }
    }

    public function getFloors()
    {
        $stmt = $this->conn->prepare("SELECT * FROM floors");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRooms()
    {
        $stmt = $this->conn->prepare("SELECT * FROM rooms");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function addRoom($floorID, $roomnumber)
    {

        $stmt = $this->conn->prepare("CALL addRoom(?, ?)");
        $stmt->execute([$floorID, $roomnumber]);
        return true;
    }

    public function addFloor($buildingID)
    {

        $stmt = $this->conn->prepare("CALL addFloor(?)");
        $stmt->execute([$buildingID]);
        return true;
    }



    public function addEquipment($equipmentname, $quantity)
    {

        $stmt = $this->conn->prepare("CALL addEquipment(?, ?)");
        $stmt->execute([$equipmentname, $quantity]);
        return true;
    }

    public function getEquipments()
    {
        $stmt = $this->conn->prepare("SELECT * FROM equipments");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function editEquipment($equipmentID, $equipmentname, $quantity)
    {

        $stmt = $this->conn->prepare("CALL editEquipment(?, ?, ?)");
        $stmt->execute([$equipmentID, $equipmentname, $quantity]);
        return true;
    }

    public function deleteEquipment($equipmentID)
    {

        $stmt = $this->conn->prepare("CALL deleteEquipment(?)");
        $stmt->execute([$equipmentID]);
        return true;
    }

    public function addAdmin($firstname, $lastname, $phonenumber, $email, $password, $admintype)
    {
        try {

            $stmt = $this->conn->prepare("CALL addAdmin(?, ?, ?, ?, ?, ?)");

            $stmt->execute([
                $firstname,
                $lastname,
                $phonenumber,
                $email,
                $password,
                $admintype
            ]);
            return true;
        } catch (PDOException $e) {
            echo "Database Error: " . $e->getMessage();

        }
    }

    public function addBuilding($buildingName, $buildingImage)
    {

        $stmt = $this->conn->prepare("CALL addBuilding(?, ?)");
        $stmt->execute([$buildingName, $buildingImage]);
        return true;
    }


    public function editBuilding($buildingID, $buildingName, $buildingImage = null)
    {
        $stmt = $this->conn->prepare("CALL editBuilding(?, ?, ?)");
        $stmt->execute([$buildingID, $buildingName, $buildingImage]); // use the parameter, don't assign
        return true;
    }

    public function getSchedulesByRoom($roomID)
    {
        $stmt = $this->conn->prepare("SELECT * FROM Schedules WHERE RoomID = ?");
        $stmt->execute([$roomID]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addSchedule($roomID, $subject, $instructor, $timeFrom, $timeTo, $section)
    {
        $stmt = $this->conn->prepare("CALL addSchedule (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$roomID, $subject, $instructor, $timeFrom, $timeTo, $section]);
        return true;
    }

    public function getMaxFloorNumber($buildingID)
    {
        $sql = "SELECT COALESCE(MAX(FloorNumber), 0) AS MaxFloor
            FROM Floors
            WHERE BuildingID = :buildingID";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':buildingID', $buildingID, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $row['MaxFloor'];
    }

    public function addBuildingWithFloors($buildingName, $buildingImage, $floorCount)
    {
        try {
            $stmt = $this->conn->prepare("CALL addBuildingWithFloors(?, ?, ?)");
            $stmt->execute([$buildingName, $buildingImage, $floorCount]);

            // Optionally get the new building ID from the SELECT in SP
            $newID = $stmt->fetch(PDO::FETCH_ASSOC)['NewBuildingID'] ?? null;

            return $newID;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function getRoomStatus()
    {
        try {

            $stmt = $this->conn->prepare("CALL GetRoomStatus()");
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;



        } catch (PDOException $e) {
            echo "Database Error: " . $e->getMessage();
        }
    }

    public function getEquipmentStatus()
    {
        try {
            $stmt = $this->conn->prepare("CALL GetEquipmentStatus()");
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        } catch (PDOException $e) {
            echo "Database Error: " . $e->getMessage();
        }
    }

    public function userLogin($number, $password)
    {
        try {
            $stmt = $this->conn->prepare("CALL UserLogin(?, ?)");
            $stmt->execute([
                $number,
                $password

            ]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            return $user;


        } catch (PDOException $e) {
            echo "Database Error: " . $e->getMessage();
        }
    }



}


