<?php
session_start();
if (!isset($_SESSION['UserID'])) {
    echo json_encode([]);
    exit;
}
require_once 'camsdatabase.php';

$db = new Database();
$conn = $db->getConnection();

$reservations = [];

// 1. Equipment reservations
$query1 = $conn->prepare("
   SELECT e.ReservationID, u.FirstName, u.LastName, e.ReservationDate, e.TimeFrom, e.TimeTo
FROM equipment_reservations e
JOIN users u ON e.UserID = u.UserID
WHERE e.Status = 'Pending' AND e.is_notified = 0
ORDER BY e.ReservationID DESC

");
$query1->execute();
$equip = $query1->fetchAll(PDO::FETCH_ASSOC);

foreach ($equip as $row) {
    $row['type'] = 'equipment';
    $row['full_name'] = $row['FirstName'] . ' ' . $row['LastName'];
    $reservations[] = $row;
}

// Mark as notified
$update1 = $conn->prepare("
    UPDATE equipment_reservations
    SET is_notified = 1
    WHERE Status = 'Pending' AND is_notified = 0
");
$update1->execute();


// 2. Classroom reservations
$query2 = $conn->prepare("
    SELECT ReservationID, UserID, Subject, ReservationDate, TimeFrom, TimeTo
    FROM classroom_reservations
    WHERE Status = 'Pending'
      AND is_notified = 0
    ORDER BY ReservationID DESC
");
$query2->execute();
$class = $query2->fetchAll(PDO::FETCH_ASSOC);

foreach ($class as $row) {
    $row['type'] = 'classroom';
    $reservations[] = $row;
}

// Mark as notified
$update2 = $conn->prepare("
    UPDATE classroom_reservations
    SET is_notified = 1
    WHERE Status = 'Pending' AND is_notified = 0
");
$update2->execute();

echo json_encode($reservations);
