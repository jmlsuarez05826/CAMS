<?php
session_start();
require 'camsdatabase.php';

$pdo = (new Database())->getConnection();
$user_id = $_SESSION['UserID'] ?? null;
$role = $_SESSION['Role'] ?? null;

header('Content-Type: application/json');

// ===================================
// AUTH CHECK
// ===================================
if (!$user_id || !$role) {
    exit(json_encode(['error' => 'Unauthorized']));
}

// ===================================
// GET REQUESTS
// ===================================


if (isset($_GET['action'])) {

        if (isset($_GET['action']) && $_GET['action'] === 'fetch_unread_count') {
        $stmt = $pdo->prepare("
        SELECT COUNT(*) AS unread 
        FROM chat_messages 
        WHERE receiver_id = :user AND status='unread'
    ");
        $stmt->execute([':user' => $user_id]);
        $count = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode(['unread' => (int) $count['unread']]);
        exit;
    }




    // 1. GET FACULTY LIST
    if ($_GET['action'] === 'get_faculty') {
        $stmt = $pdo->prepare("
            SELECT UserID, FirstName, LastName, PhoneNumber
            FROM users
            WHERE Role = 'Faculty'
        ");
        $stmt->execute();
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        exit;
    }

    // 2. GET MESSAGES
    if ($_GET['action'] === 'get_messages') {
        $other_id = $_GET['faculty_id'] ?? null;
        if (!$other_id)
            exit(json_encode([]));

        // Mark as read if requested
        if (isset($_GET['mark_read']) && $_GET['mark_read'] == '1') {
            $update = $pdo->prepare("
                UPDATE chat_messages
                SET status = 'read'
                WHERE receiver_id = :user
                  AND sender_id   = :other
                  AND status      = 'unread'
            ");
            $update->execute([
                ':user' => $user_id,
                ':other' => $other_id
            ]);
        }

        // Fetch messages
        $stmt = $pdo->prepare("
            SELECT cm.*, u.FirstName AS sender_name
            FROM chat_messages cm
            JOIN users u ON cm.sender_id = u.UserID
            WHERE (sender_id = :user AND receiver_id = :other)
               OR (sender_id = :other AND receiver_id = :user)
            ORDER BY timestamp ASC
        ");
        $stmt->execute([
            ':user' => $user_id,
            ':other' => $other_id
        ]);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        exit;
    }

    // 3. FETCH UNREAD COUNT
    if ($_GET['action'] === 'fetch_unread_count') {
        $other_id = $_GET['faculty_id'] ?? null;
        if (!$other_id)
            exit(json_encode(['unread' => 0]));

        $stmt = $pdo->prepare("
            SELECT COUNT(*) AS unread
            FROM chat_messages
            WHERE sender_id   = :other
              AND receiver_id = :user
              AND status      = 'unread'
        ");
        $stmt->execute([
            ':other' => $other_id,
            ':user' => $user_id
        ]);
        $count = $stmt->fetch(PDO::FETCH_ASSOC);

        echo json_encode(['unread' => (int) $count['unread']]);
        exit;
    }

    // Invalid action
    exit(json_encode(['error' => 'Invalid action']));
}

// ===================================
// POST REQUESTS (SEND MESSAGE)
// ===================================
if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['action'])
    && $_POST['action'] === 'send_message'
) {

    $message = $_POST['message'] ?? '';
    if (empty($message)) {
        exit(json_encode(['error' => 'Empty message']));
    }

    // Determine receiver
    if ($role === "Faculty") {
        $stmt = $pdo->prepare("SELECT UserID FROM users WHERE Role='Admin' LIMIT 1");
        $stmt->execute();
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);
        $receiver_id = $admin['UserID'];
    } elseif ($role === "Admin") {
        $receiver_id = $_POST['receiver_id'] ?? null;
        if (!$receiver_id)
            exit(json_encode(['error' => 'No faculty selected']));
    } else {
        exit(json_encode(['error' => 'Unauthorized']));
    }

    // Insert message
    $stmt = $pdo->prepare("
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

// ===================================
// DEFAULT RESPONSE
// ===================================
echo json_encode(['error' => 'Invalid request']);
exit;
?>