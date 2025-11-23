<?php
session_start();
require_once '../pages/camsdatabase.php';
$db = new Database();
$conn = $db->getConnection();

// Get logged-in user info
$user_id = $_SESSION['UserID'];
$role = $_SESSION['Role'];

// AJAX requests
if (isset($_GET['action'])) {

    // Return faculty list (for Admin only, faculty sees only Admin)
    if ($_GET['action'] == 'get_faculty') {
        if ($role == 'Admin') {
            $stmt = $conn->prepare("SELECT UserID, FirstName, LastName, PhoneNumber FROM users WHERE Role='Faculty'");
        } else if ($role == 'Faculty') {
            // Faculty only sees Admin
            $stmt = $conn->prepare("SELECT UserID, FirstName, LastName, PhoneNumber FROM users WHERE Role='Admin'");
        } else exit("Unauthorized");

        $stmt->execute();
        $faculty = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($faculty);
        exit;
    }

    // Fetch messages
    if ($_GET['action'] == 'fetch_messages') {
        $other_id = $_GET['faculty_id'] ?? null;
        if (!$other_id) exit("No recipient selected");

        $stmt = $conn->prepare("
            SELECT cm.*, u.FirstName AS sender_name
            FROM chat_messages cm
            JOIN users u ON cm.sender_id = u.UserID
            WHERE (sender_id = :user AND receiver_id = :other) OR (sender_id = :other AND receiver_id = :user)
            ORDER BY timestamp ASC
        ");
        $stmt->execute([':user'=>$user_id, ':other'=>$other_id]);
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($messages);
        exit;
    }
}

// Sending message
if (isset($_POST['action']) && $_POST['action'] == 'send_message') {
    $receiver_id = $_POST['receiver_id'] ?? null;
    $message = $_POST['message'] ?? '';
    if (!$receiver_id || !$message) exit("Invalid request");

    $stmt = $conn->prepare("INSERT INTO chat_messages (sender_id, receiver_id, message) VALUES (:sender, :receiver, :msg)");
    $stmt->execute([':sender'=>$user_id, ':receiver'=>$receiver_id, ':msg'=>$message]);
    echo "success";
    exit;
}
?>