<?php
session_start();
require_once 'camsdatabase.php';

$db = new Database();
$conn = $db->getConnection();

$receiver_id = $_SESSION['UserID']; // the logged-in user

// Fetch unread messages
$query = $conn->prepare("
    SELECT chatID, sender_id, message, timestamp
    FROM chat_messages
    WHERE receiver_id = :receiver_id
      AND status = 'unread'
    ORDER BY chatID DESC
");
$query->execute(['receiver_id' => $receiver_id]);

$messages = $query->fetchAll(PDO::FETCH_ASSOC);

// Mark messages as read
$update = $conn->prepare("
    UPDATE chat_messages 
    SET status = 'read'
    WHERE receiver_id = :receiver_id
      AND status = 'unread'
");
$update->execute(['receiver_id' => $receiver_id]);

echo json_encode($messages);
?>  