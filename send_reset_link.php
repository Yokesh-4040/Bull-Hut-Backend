<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Asia/Kolkata');

include 'config.php';  // Attendance DB
header('Content-Type: application/json');

$email = $_POST['email'] ?? null;

if (!$email) {
    echo json_encode(["status" => "error", "message" => "Email is required."]);
    exit;
}

// Check if email exists in the database
$query = $conn->prepare("SELECT EmployeeID, FirstName FROM employees WHERE Email = ?");
$query->bind_param("s", $email);
$query->execute();
$result = $query->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $token = bin2hex(random_bytes(50));  // Generate a unique reset token
    $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

    // Save token in the database
    $insertToken = $conn->prepare("INSERT INTO password_resets (Email, Token, Expiry) VALUES (?, ?, ?)");
    $insertToken->bind_param("sss", $email, $token, $expiry);
    $insertToken->execute();

    // Prepare reset link
    $resetLink = "https://fourtyfourty.in/bull_hut/reset_password_form.php?token=$token";
    
    // Send reset email
    $subject = "Password Reset Request";
    $message = "Hello " . $user['FirstName'] . ",\n\nClick the link below to reset your password:\n\n$resetLink\n\nThis link will expire in 1 hour.";
    $headers = "From: no-reply@fourtyfourty.in";

    if (mail($email, $subject, $message, $headers)) {
        echo json_encode(["status" => "success", "message" => "Password reset link sent to your email."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to send reset email."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Email not found."]);
}

$query->close();
$conn->close();
?>
