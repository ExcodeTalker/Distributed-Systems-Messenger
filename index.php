<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include_once 'database.php';

$database = new Database();
$db = $database->getConnection();

// Fetch list of users for dropdown (excluding the logged-in user)
$query = "SELECT user_id, user_name FROM users WHERE user_id != :current_user";
$stmt = $db->prepare($query);
$stmt->bindParam(':current_user', $_SESSION['user_id']);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Instant Messenger</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>

    <p>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>! <a href="logout.php">Logout</a></p>

    <!-- Select Recipient -->
    <label for="recipient">Chat with:</label>
    <select id="recipient">
        <option value="">-- Select a user --</option>
        <?php foreach ($users as $user): ?>
            <option value="<?php echo $user['user_id']; ?>"><?php echo htmlspecialchars($user['user_name']); ?></option>
        <?php endforeach; ?>
    </select>

    <div id="chat-container">
        <div id="message-container">Select a user to start chat...</div>

        <form id="message-form" enctype="multipart/form-data">
            <input type="text" id="message-input" placeholder="Type your message...">
            <label for="file-upload" id="file-upload-label">📎 Upload</label>
            <input type="file" id="file-upload" style="display: none;">
            <button type="button" id="send-button" onclick="sendMessage()">Send</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        var selectedUser = "";

        // Listen for recipient selection
        $("#recipient").change(function () {
            selectedUser = $(this).val();
            if (selectedUser) {
                loadMessages();
            } else {
                $("#message-container").html("Select a user to start chat...");
            }
        });

        function sendMessage() {
            var message = $('#message-input').val();
            var file = $('#file-upload')[0].files[0];

            if (!selectedUser) {
                alert("Please select a recipient first.");
                return;
            }

            if (message.trim() === "" && !file) {
                alert("Please enter a message or upload a file!");
                return;
            }

            /*var formData = new FormData();
            formData.append('message', message);
            formData.append('user_from', <?php echo $_SESSION['user_id']; ?>);
            formData.append('user_to', selectedUser);
			formData.append('file', file);*/
			
			formData = new FormData();
            formData.append('message', message);
            formData.append('user_from', <?php echo $_SESSION['user_id']; ?>);
            formData.append('user_to', selectedUser);
			formData.append('file', $('#file-upload')[0].files[0]);

            if (file) {
                formData.append('file', file);
            }

            $.ajax({
                url: 'send_message.php',
                type: 'POST',
                data: formData,
				cache:false,
                contentType: false,
                processData: false,
                success: function(response) {
                    $('#message-input').val('');
                    $('#file-upload').val('');
                    loadMessages();
                }
            });
        }

        function loadMessages() {
            if (!selectedUser) return;

            $.ajax({
                url: 'get_messages.php',
                type: 'GET',
                data: { user_to: selectedUser },
                success: function(response) {
                    $('#message-container').html(response);
                }
            });
        }

        setInterval(loadMessages, 1000);
    </script>

</body>
</html>
