<?php
session_start();
require 'camsdatabase.php'; // adjust path
$pdo = (new Database())->getConnection();

$user_id = $_SESSION['UserID'];
$role = $_SESSION['Role'] ?? null;

if (!$user_id || !$role) exit(json_encode(['error' => 'Unauthorized']));

header('Content-Type: application/json');

// Handle GET requests
if (isset($_GET['action'])) {

    // 1. Get faculty list
    if ($_GET['action'] == 'get_faculty') {

        $stmt = $pdo->prepare("SELECT UserID, FirstName, LastName, PhoneNumber FROM users WHERE Role='Faculty'");
        $stmt->execute();
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        exit;
    }

    // 2. Get chat messages
    if ($_GET['action'] == 'get_messages') {
        $other_id = $_GET['faculty_id'] ?? null;

        if ($role == "Faculty") {
            // Faculty chats with single admin
            $stmt = $pdo->prepare("SELECT UserID FROM users WHERE Role='Admin' LIMIT 1");
            $stmt->execute();
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);
            $other_id = $admin['UserID'];
        } else if ($role == "Admin") {
            if (!$other_id) exit(json_encode([]));
        } else exit(json_encode([]));

        $stmt = $pdo->prepare("
            SELECT cm.*, u.FirstName AS sender_name
            FROM chat_messages cm
            JOIN users u ON cm.sender_id = u.UserID
            WHERE (sender_id=:user AND receiver_id=:other) OR (sender_id=:other AND receiver_id=:user)
            ORDER BY timestamp ASC
        ");
        $stmt->execute([':user' => $user_id, ':other' => $other_id]);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        exit;
    }

    exit(json_encode(['error' => 'Invalid action']));
}

// Handle POST request (send message)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'send_message') {
    $message = $_POST['message'] ?? '';
    if (empty($message)) exit(json_encode(['error' => 'Empty message']));

    // Determine receiver
    if ($role == "Faculty") {
        $stmt = $pdo->prepare("SELECT UserID FROM users WHERE Role='Admin' LIMIT 1");
        $stmt->execute();
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);
        $receiver_id = $admin['UserID'];
    } else if ($role == "Admin") {
        $receiver_id = $_POST['receiver_id'] ?? null;
        if (!$receiver_id) exit(json_encode(['error' => 'No faculty selected']));
    } else exit(json_encode(['error' => 'Unauthorized']));

    $stmt = $pdo->prepare("INSERT INTO chat_messages (sender_id, receiver_id, message) VALUES (:sender, :receiver, :msg)");
    $stmt->execute([':sender' => $user_id, ':receiver' => $receiver_id, ':msg' => $message]);

    echo json_encode(['success' => true]);
    exit;
}

// If no valid request
echo json_encode(['error' => 'Invalid request']);
exit;
