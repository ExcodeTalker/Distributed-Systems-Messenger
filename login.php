<?php
session_start();
include_once 'database.php';

$database = new Database();
$db = $database->getConnection();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_name = htmlspecialchars(strip_tags($_POST['user_name']));
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE user_name = :user_name";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":user_name", $user_name);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['user_name'] = $user['user_name'];
        header("Location: index.php");
        exit;
    } else {
        echo "Invalid login credentials.";
    }
}
?>

<form method="post">
    <input type="text" name="user_name" placeholder="Username" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Login</button>
</form>
