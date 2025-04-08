<?php
include 'db_connection.php';

// Only run this once then delete the file!
$name = "Admin User";
$email = "admin@evefurniture.com";
$password = "admin123"; 
$user_type = "admin";

// Hash the password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Insert admin account
$stmt = $conn->prepare("INSERT INTO users (name, email, password, user_type) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $name, $email, $hashed_password, $user_type);

if ($stmt->execute()) {
    echo "Admin account created successfully!<br>";
    echo "Email: $email<br>";
    echo "Password: $password<br>";
    echo "<strong>IMPORTANT:</strong> Delete this file after use!";
} else {
    echo "Error creating admin account: " . $conn->error;
}

$conn->close();
?>