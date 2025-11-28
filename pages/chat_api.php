<?php
// --------------------
// chat_api.php
// --------------------
header('Content-Type: application/json');
ini_set('display_errors', 0); // prevent HTML errors
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

session_start();
require 'camsdatabase.php';

try {
    $pdo = (new Database())->getConnection();
    $user_id = $_SESSION['UserID'] ?? null;
    $role = $_SESSION['Role'] ?? null;

    if (!$user_id || !$role) {
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }

    // ----------------------
    // GET Requests
    // ----------------------
    if (isset($_GET['action'])) {

        // 1. Get faculty/admin list
        if ($_GET['action'] === 'get_faculty') {
            $target_role = ($role === 'Faculty') ? 'Admin' : 'Faculty';
            $stmt = $pdo->prepare("SELECT UserID, FirstName, LastName, PhoneNumber FROM users WHERE Role = :role");
            $stmt->execute([':role' => $target_role]);
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
            exit;
        }

        // 2. Get messages
        if ($_GET['action'] === 'get_messages') {
            $other_id = $_GET['faculty_id'] ?? null;
            if (!$other_id) {
                echo json_encode([]);
                exit;
            }

            // mark as read
            if (isset($_GET['mark_read']) && $_GET['mark_read'] == '1') {
                $update = $pdo->prepare("UPDATE chat_messages SET status='read' WHERE receiver_id=:user AND sender_id=:other AND status='unread'");
                $update->execute([':user' => $user_id, ':other' => $other_id]);
            }

            $stmt = $pdo->prepare("
                SELECT cm.*, u.FirstName AS sender_name
                FROM chat_messages cm
                JOIN users u ON cm.sender_id = u.UserID
                WHERE (sender_id=:user AND receiver_id=:other)
                   OR (sender_id=:other AND receiver_id=:user)
                ORDER BY timestamp ASC
            ");
            $stmt->execute([':user' => $user_id, ':other' => $other_id]);
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
            exit;
        }

        // 3. Fetch unread count
        if ($_GET['action'] === 'fetch_unread_count') {
            $other_id = $_GET['faculty_id'] ?? null;
            if (!$other_id) {
                echo json_encode(['unread' => 0]);
                exit;
            }

            $stmt = $pdo->prepare("SELECT COUNT(*) AS unread FROM chat_messages WHERE sender_id=:other AND receiver_id=:user AND status='unread'");
            $stmt->execute([':other' => $other_id, ':user' => $user_id]);
            $count = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode(['unread' => (int) $count['unread']]);
            exit;
        }

        echo json_encode(['error' => 'Invalid action']);
        exit;
    }

    // ----------------------
    // POST Requests
    // ----------------------
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'send_message') {

        $message = trim($_POST['message'] ?? '');
        if ($message === '') {
            echo json_encode(['error' => 'Empty message']);
            exit;
        }

        // Determine receiver
        if ($role === 'Faculty') {
            $stmt = $pdo->prepare("SELECT UserID FROM users WHERE Role='Admin' LIMIT 1");
            $stmt->execute();
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$admin) {
                echo json_encode(['error' => 'No Admin available']);
                exit;
            }
            $receiver_id = $admin['UserID'];
        } elseif ($role === 'Admin') {
            $receiver_id = $_POST['receiver_id'] ?? null;
            if (!$receiver_id) {
                echo json_encode(['error' => 'No faculty selected']);
                exit;
            }
        } else {
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        // Insert message
        // Insert message without specifying status (database default will be used)
        $stmt = $pdo->prepare("
    INSERT INTO chat_messages (sender_id, receiver_id, message)
    VALUES (:sender, :receiver, :msg)
");
        $stmt->execute([
            ':sender' => $user_id,
            ':receiver' => $receiver_id,
            ':msg' => $message
        ]);


        echo json_encode(['success' => true]);
        exit;
    }

    echo json_encode(['error' => 'Invalid request']);
    exit;

} catch (PDOException $e) {
    // Catch any database errors
    echo json_encode(['error' => 'Database error']);
    exit;
} catch (Exception $e) {
    echo json_encode(['error' => 'Server error']);
    exit;
}
?>