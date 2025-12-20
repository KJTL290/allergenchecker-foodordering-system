<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "allergypass_db";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Database Connection Failed"]));
}

// Set timezone for database connection
$conn->query("SET time_zone = '+00:00'"); // UTC

// Check and create email column if it doesn't exist
$columns = $conn->query("SHOW COLUMNS FROM users LIKE 'email'");
if ($columns->num_rows == 0) {
    $conn->query("ALTER TABLE users ADD COLUMN email VARCHAR(100) UNIQUE");
}

// Check and create reset_token column if it doesn't exist
$resetTokenColumn = $conn->query("SHOW COLUMNS FROM users LIKE 'reset_token'");
if ($resetTokenColumn->num_rows == 0) {
    $conn->query("ALTER TABLE users ADD COLUMN reset_token VARCHAR(255)");
}

// Check and create reset_token_expiry column if it doesn't exist
$resetExpiryColumn = $conn->query("SHOW COLUMNS FROM users LIKE 'reset_token_expiry'");
if ($resetExpiryColumn->num_rows == 0) {
    $conn->query("ALTER TABLE users ADD COLUMN reset_token_expiry DATETIME");
}

?>