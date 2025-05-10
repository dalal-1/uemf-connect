<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_SESSION['user_id'] ?? null;
    $recipientId = $_POST['recipientId'];
    $messageInput = $connexion->real_escape_string($_POST['messageInput']);

    // Validate that the sender and recipient IDs are set
    if (!$student_id || !$recipientId) {
        echo json_encode(['error' => 'Invalid sender or recipient']);
        exit();
    }

    // Insert the message into the database
    $query = "INSERT INTO messages (sender_id, receiver_id, message_text) VALUES ($student_id, $recipientId, '$messageInput')";
    $result = $connexion->query($query);

    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'Failed to insert message into the database']);
    }
} else {
    echo json_encode(['error' => 'Invalid request method']);
}
?>
