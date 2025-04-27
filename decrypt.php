<?php
function decryptMessage($encrypted_message, $encryption_key) {
	
	// Assisted by GitHub Copilot (2025) Encryption & Decryption Available at: https://github.com/copilot/c/83d4f84d-23a7-4593-b1f2-25407f05ab10 
	
    // Split the stored message into IV and encrypted message
    list($encoded_iv, $encrypted_message) = explode("::", $encrypted_message);

    // Decode the IV
    $decryption_iv = base64_decode($encoded_iv);

    // Decrypt the message
    $ciphering = "AES-128-CTR";
    $options = 0;

    $decrypted_message = openssl_decrypt(
        $encrypted_message,
        $ciphering,
        $encryption_key,
        $options,
        $decryption_iv
    );

    return $decrypted_message;
}
?>