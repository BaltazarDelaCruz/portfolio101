<?php
session_start();
include('../config/portfolioDB.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $messageIds = $data['message_ids'] ?? [];

    if (!empty($messageIds)) {
        $placeholders = implode(',', array_fill(0, count($messageIds), '?'));
        $stmt = $pdo->prepare("DELETE FROM contact_messages WHERE id IN ($placeholders)");
        $success = $stmt->execute($messageIds);

        echo json_encode(['success' => $success]);
    } else {
        echo json_encode(['success' => false, 'error' => 'No messages selected.']);
    }

  

    
}
?>
