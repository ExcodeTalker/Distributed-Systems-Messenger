<?php
class Message {
    private $conn;
    private $table_name = "messages";

    public $user_from;
    public $user_to;
    public $message;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function sendMessage() {
    $query = "INSERT INTO " . $this->table_name . " 
              SET user_from=:user_from, user_to=:user_to, message=:message, date_sent=NOW()";
    $stmt = $this->conn->prepare($query);

    // Sanitize user inputs
    $this->user_from = htmlspecialchars(strip_tags($this->user_from));
    $this->user_to = htmlspecialchars(strip_tags($this->user_to));
    $this->message = htmlspecialchars(strip_tags($this->message));
		
	// Assisted by GitHub Copilot (2025) Available at: https://github.com/copilot/c/83d4f84d-23a7-4593-b1f2-25407f05ab10

    // Encryption settings
    $ciphering = "AES-128-CTR";
    $iv_length = openssl_cipher_iv_length($ciphering);
    $options = 0;

    // Generate a random IV for secure encryption
    $encryption_iv = openssl_random_pseudo_bytes($iv_length);

    // Store the encryption key (should ideally be stored securely, e.g., in an environment variable)
    $encryption_key = "GeeksforGeeks";

    // Encrypt the message
    $encrypted_message = openssl_encrypt(
        $this->message, 
        $ciphering, 
        $encryption_key, 
        $options, 
        $encryption_iv
    );

    // Convert IV to a format suitable for storage (e.g., base64)
    $encoded_iv = base64_encode($encryption_iv);

    // Append the IV to the encrypted message for decryption later
    $final_message = $encoded_iv . "::" . $encrypted_message;

    // Bind parameters
    $stmt->bindParam(":user_from", $this->user_from);
    $stmt->bindParam(":user_to", $this->user_to);
    $stmt->bindParam(":message", $final_message);

    // Execute the query and return the result
    return $stmt->execute();
}

    public function getMessagesWithUsernames($user_from, $user_to) {
        $query = "SELECT m.*, u1.user_name AS sender_name, u2.user_name AS receiver_name 
                  FROM messages m
                  JOIN users u1 ON m.user_from = u1.user_id
                  JOIN users u2 ON m.user_to = u2.user_id
                  WHERE (m.user_from=:user_from AND m.user_to=:user_to) 
                     OR (m.user_from=:user_to AND m.user_to=:user_from)
                  ORDER BY m.date_sent ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_from", $user_from);
        $stmt->bindParam(":user_to", $user_to);
        $stmt->execute();

        return $stmt;
    }
}
?>
