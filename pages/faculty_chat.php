<?php
session_start();
require_once '../pages/camsdatabase.php';
$db = new Database();
$conn = $db->getConnection();

// Get logged-in user info
$user_id = $_SESSION['UserID'] ?? null;
$role = $_SESSION['Role'] ?? null;

header('Content-Type: application/json');

// Ensure user is logged in
if (!$user_id || !$role) {
    echo json_encode(['error'=>'Unauthorized']);
    exit;
}

// ----------------- GET requests -----------------
if (isset($_GET['action'])) {

    // Return faculty/admin list
    if ($_GET['action'] == 'get_faculty') {
        if ($role == 'Admin') {
            $stmt = $conn->prepare("SELECT UserID, FirstName, LastName, PhoneNumber FROM users WHERE Role='Faculty'");
        } elseif ($role == 'Faculty') {
            $stmt = $conn->prepare("SELECT UserID, FirstName, LastName, PhoneNumber FROM users WHERE Role='Admin'");
        } else {
            echo json_encode(['error'=>'Unauthorized']);
            exit;
        }
        $stmt->execute();
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        exit;
    }

    // Fetch chat messages
    if ($_GET['action'] == 'fetch_messages') {
        $other_id = $_GET['faculty_id'] ?? null;
        if (!$other_id) { echo json_encode([]); exit; }

        // Mark messages as read
        $update = $conn->prepare("UPDATE chat_messages SET status='read' WHERE receiver_id=:user AND sender_id=:other AND status='unread'");
        $update->execute([':user'=>$user_id, ':other'=>$other_id]);

        $stmt = $conn->prepare("
            SELECT cm.*, u.FirstName AS sender_name
            FROM chat_messages cm
            JOIN users u ON cm.sender_id = u.UserID
            WHERE (sender_id = :user AND receiver_id = :other) OR (sender_id = :other AND receiver_id = :user)
            ORDER BY timestamp ASC
        ");
        $stmt->execute([':user'=>$user_id, ':other'=>$other_id]);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        exit;
    }

    echo json_encode(['error'=>'Invalid action']);
    exit;
}

// ----------------- POST requests -----------------
if (isset($_POST['action']) && $_POST['action'] == 'send_message') {
    $receiver_id = $_POST['receiver_id'] ?? null;
    $message = trim($_POST['message'] ?? '');
    if (!$receiver_id || $message === '') {
        echo json_encode(['error'=>'Invalid request']);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO chat_messages (sender_id, receiver_id, message) VALUES (:sender, :receiver, :msg)");
    if ($stmt->execute([':sender'=>$user_id, ':receiver'=>$receiver_id, ':msg'=>$message])) {
        echo json_encode(['success'=>true]);
    } else {
        echo json_encode(['error'=>'Database error']);
    }
    exit;
}

// Default response
echo json_encode(['error'=>'Invalid request']);
exit;
?>
