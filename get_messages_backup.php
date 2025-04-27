<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access.");
}

// Assisted by GitHub Copilot (2025) Available at: https://github.com/copilot/c/83d4f84d-23a7-4593-b1f2-25407f05ab10

include_once 'database.php';
include_once 'message.php';
include_once 'decrypt.php'; // Include the decryption file

$database = new Database();
$db = $database->getConnection();
$message = new Message($db);

$user_id = $_SESSION['user_id'];
$user_to = isset($_GET['user_to']) ? $_GET['user_to'] : 0;

// Encryption key (should be the same key used during encryption)
$encryption_key = "GeeksforGeeks";

$stmt = $message->getMessagesWithUsernames($user_id, $user_to);
$num = $stmt->rowCount();

if ($num > 0) {
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Decrypt the message before displaying
        $decrypted_message = decryptMessage($row['message'], $encryption_key);

        echo "<div><strong>" . htmlspecialchars($row['sender_name']) . ":</strong> " . htmlspecialchars($decrypted_message);
        echo " <span style='font-size: 0.8em; color: gray;'>(" . $row['date_sent'] . ")</span></div>";
    }
} else {
    echo "<div>No messages yet.</div>";
}
?>