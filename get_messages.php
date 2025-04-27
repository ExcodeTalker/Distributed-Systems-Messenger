<?php session_start();
if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access.");
}

// Assisted by GitHub Copilot (2025) Encryption & Decryption Available at: https://github.com/copilot/c/83d4f84d-23a7-4593-b1f2-25407f05ab10 
// Assisted by GitHub Copilot (2025) File Handling Available at: https://github.com/copilot/c/b5727aa1-a67d-4fb1-b604-81b5ec3605d5

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

        // Check if there is a file associated with the message
        if (!empty($row['file_path'])) {
            $file_path = htmlspecialchars($row['file_path']); // Sanitize file path
            $file_name = basename($file_path); // Extract the file name
            $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION)); // Get the file extension

            // Display preview or download link based on file type
            echo "<div>";
            if (in_array($file_extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                // Display image preview
                echo "<img src='" . $file_path . "' alt='" . $file_name . "' style='max-width: 300px; max-height: 300px; display: block; margin-top: 10px;' />";
            } elseif ($file_extension === 'pdf') {
                // Display PDF embedded preview
                echo "<embed src='" . $file_path . "' type='application/pdf' width='600px' height='400px' style='display: block; margin-top: 10px;' />";
            } else {
                // For other file types, provide a download link
                echo "<a href='" . $file_path . "' download='" . $file_name . "'>Download File: " . $file_name . "</a>";
            }
            echo "</div>";
        }
    }
} else {
    echo "<div>No messages yet.</div>";
}
?>