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
        try {
            $stmt = $this->conn->prepare("CALL GetAllUsers()");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Database Error: " . $e->getMessage();
        }
    }



    public function getUsersPaginated($limit, $offset)
    {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE status = 'active' ORDER BY UserID LIMIT :limit OFFSET :offset");
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

    public function getEquipmentCount()
    {
        $stmt = $this->conn->query("SELECT COUNT(*) as total FROM equipment_units");
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

    public function editEquipment($equipmentID, $equipmentname, $quantity, $equipmentIMG)
    {
        // If no new image uploaded, pass NULL
        if (empty($equipmentIMG)) {
            $equipmentIMG = null;
        }

        $stmt = $this->conn->prepare("CALL editEquipment(?, ?, ?, ?)");
        $stmt->execute([$equipmentID, $equipmentname, $quantity, $equipmentIMG]);
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

    public function getSchedulesByRoom($roomID, $dayOfWeek = null, $weekType = null)
    {
        $query = "SELECT * FROM schedules WHERE RoomID = ?";
        $params = [$roomID];

        if ($dayOfWeek) {
            $query .= " AND DayOfWeek = ?";
            $params[] = $dayOfWeek;
        }

        if ($weekType) {
            $query .= " AND WeekType = ?";
            $params[] = $weekType;
        }

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addSchedule($roomID, $subject, $instructor, $timeFrom, $timeTo, $section, $weekType, $dayOfWeek)
    {
        $stmt = $this->conn->prepare("CALL addSchedule (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$roomID, $subject, $instructor, $timeFrom, $timeTo, $section, $weekType, $dayOfWeek]);
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
    public function getRoomStatus($roomID, $weekType)
    {
        $stmt = $this->conn->prepare("CALL getRoomStatus(?, ?)");
        $stmt->execute([$roomID, $weekType]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $result['RoomStatus'] ?? "Available";
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

public function reserveEquipment($unitID, $userID, $date, $timeFrom, $timeTo)
{
    $stmt = $this->conn->prepare("CALL reserveEquipment(?, ?, ?, ?, ?)");
    return $stmt->execute([$unitID, $userID, $date, $timeFrom, $timeTo]);
}


public function reserveClassroom($roomID, $userID, $subject, $section, $date, $dayOfWeek, $weekType, $timeFrom, $timeTo)
{
    // The CALL statement must have 8 placeholders (?)
    $stmt = $this->conn->prepare("CALL reserveClassroom(?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    // The execute array must pass all 8 variables in the exact order of the SP
    return $stmt->execute([
        $roomID, 
        $userID, 
        $subject, 
        $section,
        $date,
        $dayOfWeek, // 5th in line for SP
        $weekType,  // 6th in line for SP
        $timeFrom,  // 7th in line for SP
        $timeTo     // 8th in line for SP
    ]);
}

    function getUserReservations($userID)
    {
        $stmt = $this->conn->prepare("
        SELECT er.*, e.EquipmentName
        FROM equipment_reservations er
        JOIN equipment_units eu ON er.UnitID = eu.UnitID
        JOIN equipments e ON eu.EquipmentID = e.EquipmentID
        WHERE er.UserID = ?
    ");
        $stmt->execute([$userID]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function cancelReservation($reservationID)
    {
        $stmt = $this->conn->prepare("CALL cancelReservation(?)");
        $stmt->execute([$reservationID]);
        return true;
    }

    public function getUserPendingUnitReservations($userID)
    {
        $stmt = $this->conn->prepare("SELECT UnitID FROM reservations WHERE UserID = :userID AND Status = 'pending'");
        $stmt->execute(['userID' => $userID]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN); // return array of UnitIDs
    }
    function getEquipmentUnits($equipmentID)
    {
        $stmt = $this->conn->prepare("SELECT * FROM equipment_units WHERE EquipmentID = ?");
        $stmt->execute([$equipmentID]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteUser($userId)
    {
        try {
            $stmt = $this->conn->prepare("CALL DeleteUser(?)");
            $stmt->execute([$userId]);

            // For soft delete, no row is returned, but you can check affected rows
            return $stmt->rowCount() > 0;

        } catch (PDOException $e) {
            echo "Database Error: " . $e->getMessage();
            return false;
        }
    }

    public function updateUser($userId, $firstName, $lastName, $email)
    {
        try {
            $stmt = $this->conn->prepare("CALL UpdateUser(?, ?, ?, ?)");
            $stmt->execute([
                $userId,
                $firstName,
                $lastName,
                $email
            ]);

            return $stmt->rowCount() > 0;

        } catch (PDOException $e) {
            echo "Database Error: " . $e->getMessage();
            return false;
        }
    }



    public function approveEquipmentRequest($id)
    {
        $stmt = $this->conn->prepare("CALL approveEquipmentReq(?)");
        $stmt->execute([$id]);
        return true;
    }

    public function rejectEquipmentRequest($id)
    {
        $stmt = $this->conn->prepare("CALL rejectEquipmentReq(?)");
        $stmt->execute([$id]);
        return true;
    }

    public function getRoomStatusCounts($weekType = 'Odd') {
    $stmt = $this->conn->prepare("
        SELECT RoomStatus, COUNT(*) AS count
        FROM (
            SELECT r.RoomID,
                CASE 
                    WHEN EXISTS (
                        SELECT 1
                        FROM schedules s
                        WHERE s.RoomID = r.RoomID
                          AND s.DayOfWeek = DAYNAME(NOW())
                          AND s.WeekType = ?
                          AND TIME(NOW()) BETWEEN s.TimeFrom AND s.TimeTo
                    )
                    THEN 'Occupied'
                    ELSE 'Available'
                END AS RoomStatus
            FROM rooms r
        ) AS room_statuses
        GROUP BY RoomStatus
    ");
    $stmt->execute([$weekType]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);

    
}

public function getEquipmentStatusCounts() {
    $stmt = $this->conn->query("
        SELECT Status, COUNT(*) AS count
        FROM equipment_units
        GROUP BY Status
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function getUserClassroomReservations($userID) {
    $stmt = $this->conn->prepare("
        SELECT cr.*, r.RoomNumber, cr.Subject
        FROM classroom_reservations cr
        JOIN rooms r ON cr.RoomID = r.RoomID
        WHERE cr.UserID = ?
    ");
    $stmt->execute([$userID]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function approveClassroomRequest($id) {
    $stmt = $this->conn->prepare("CALL approveClassroomReq(?)");
    $stmt->execute([$id]);
    return true;
}

public function rejectClassroomRequest($id) {
    $stmt = $this->conn->prepare("CALL rejectClassroomReq(?)");
    $stmt->execute([$id]);
    return true;
}

public function getEquipmentRequests($limit, $offset)
    {
        $stmt = $this->conn->prepare("
        SELECT 
            er.*,
            e.EquipmentName,
            CONCAT(u.FirstName, ' ', u.LastName) AS Requester
        FROM equipment_reservations er
        JOIN equipment_units eu ON er.UnitID = eu.UnitID
        JOIN equipments e ON eu.EquipmentID = e.EquipmentID
        JOIN users u ON er.UserID = u.UserID
        WHERE er.Status IN ('Pending', 'Approved', 'Rejected')
        ORDER BY er.ReservationID DESC
        LIMIT :limit OFFSET :offset
    ");

        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getEquipmentRequestsCount()
    {
        $stmt = $this->conn->query("SELECT COUNT(*) as total FROM equipment_reservations WHERE Status != 'Cancelled'");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    public function getClassroomRequests($limit, $offset)
    {
        $stmt = $this->conn->prepare("
        SELECT 
            cr.*,
            r.RoomNumber,
            CONCAT(u.FirstName, ' ', u.LastName) AS Requester,
            CONCAT(cr.TimeFrom, '-', cr.TimeTo) AS Time
        FROM classroom_reservations cr
        JOIN rooms r ON cr.RoomID = r.RoomID
        JOIN users u ON cr.UserID = u.UserID
        WHERE cr.Status != 'Rejected'
        ORDER BY cr.CreatedAt DESC
        LIMIT :limit OFFSET :offset
    ");

        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getClassroomRequestsCount()
    {
        $stmt = $this->conn->query("SELECT COUNT(*) as total FROM classroom_reservations WHERE Status != 'Rejected'");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    public function getPendingRoomRequests()
    {
        $sql = "SELECT COUNT(*) AS total FROM classroom_reservations WHERE Status = 'Pending'";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
}



