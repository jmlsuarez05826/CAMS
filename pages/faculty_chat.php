<?php
session_start();
require_once '../pages/camsdatabase.php';
$db = new Database();
$conn = $db->getConnection();

// ------------------
// Session validation
// ------------------
$user_id = $_SESSION['UserID'] ?? null;
$role = $_SESSION['Role'] ?? null;
if (!$user_id || !$role) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// ------------------
// Handle GET requests
// ------------------
if (isset($_GET['action'])) {

    // 1. Get faculty/admin list with unread count
    if ($_GET['action'] == 'get_faculty') {
        if ($role == 'Admin') {
            $stmt = $conn->prepare("SELECT UserID, FirstName, LastName, PhoneNumber FROM users WHERE Role='Faculty'");
        } elseif ($role == 'Faculty') {
            $stmt = $conn->prepare("SELECT UserID, FirstName, LastName, PhoneNumber FROM users WHERE Role='Admin' LIMIT 1");
        } else {
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch unread counts for each user
        foreach ($users as &$u) {
            $countStmt = $conn->prepare("
                SELECT COUNT(*) AS unread
                FROM chat_messages
                WHERE sender_id = :other AND receiver_id = :user AND status='unread'
            ");
            $countStmt->execute([':other' => $u['UserID'], ':user' => $user_id]);
            $count = $countStmt->fetch(PDO::FETCH_ASSOC);
            $u['unread'] = (int)$count['unread']; // add unread count to each user
        }

        echo json_encode($users);
        exit;
    }

    // 2. Fetch chat messages
    if ($_GET['action'] == 'fetch_messages') {
        $other_id = $_GET['faculty_id'] ?? null;
        if (!$other_id) {
            echo json_encode([]);
            exit;
        }

        // Mark as read if requested
        if (isset($_GET['mark_read']) && $_GET['mark_read'] == '1') {
            $update = $conn->prepare("
                UPDATE chat_messages
                SET status = 'read'
                WHERE receiver_id = :user AND sender_id = :other AND status='unread'
            ");
            $update->execute([
                ':user' => $user_id,
                ':other' => $other_id
            ]);
        }

        // Fetch all messages
        $stmt = $conn->prepare("
            SELECT cm.*, u.FirstName AS sender_name
            FROM chat_messages cm
            JOIN users u ON cm.sender_id = u.UserID
            WHERE (sender_id = :user AND receiver_id = :other)
               OR (sender_id = :other AND receiver_id = :user)
            ORDER BY timestamp ASC
        ");
        $stmt->execute([':user' => $user_id, ':other' => $other_id]);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        exit;
    }
}

// ------------------
// Handle POST requests (send message)
// ------------------
if (isset($_POST['action']) && $_POST['action'] == 'send_message') {
    $receiver_id = $_POST['receiver_id'] ?? null;
    $message = $_POST['message'] ?? '';
    if (!$receiver_id || !$message) {
        echo json_encode(['error' => 'Invalid request']);
        exit;
    }

    $stmt = $conn->prepare("
        INSERT INTO chat_messages (sender_id, receiver_id, message, status)
        VALUES (:sender, :receiver, :msg, 'unread')
    ");
    $stmt->execute([
        ':sender' => $user_id,
        ':receiver' => $receiver_id,
        ':msg' => $message
    ]);

    echo json_encode(['success' => true]);
    exit;
}
?>
