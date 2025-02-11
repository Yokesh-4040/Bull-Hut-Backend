<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Asia/Kolkata');

include 'config.php';

// Example: Set password for each employee
$default_password = password_hash("password123", PASSWORD_DEFAULT);  // Default password for all users

// Update all employees with this password
$stmt = $conn->prepare("UPDATE employees SET Password = ?");
$stmt->bind_param("s", $default_password);

if ($stmt->execute()) {
    echo "Passwords updated successfully for all employees!";
} else {
    echo "Error updating passwords: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
