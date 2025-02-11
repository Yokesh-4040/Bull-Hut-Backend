<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Asia/Kolkata');

include 'config.php';
header('Content-Type: application/json');

$token = $_POST['token'] ?? null;
$new_password = $_POST['new_password'] ?? null;
$confirm_password = $_POST['confirm_password'] ?? null;

if (!$token || !$new_password || !$confirm_password) {
    die(json_encode(["status" => "error", "message" => "All fields are required."]));
}

if ($new_password !== $confirm_password) {
    die(json_encode(["status" => "error", "message" => "Passwords do not match."]));
}

// Check if token is valid
$tokenQuery = $conn->prepare("SELECT Email, Expiry FROM password_resets WHERE Token = ?");
$tokenQuery->bind_param("s", $token);
$tokenQuery->execute();
$tokenResult = $tokenQuery->get_result();

if ($tokenResult->num_rows > 0) {
    $resetData = $tokenResult->fetch_assoc();
    $current_time = date('Y-m-d H:i:s');

    if ($resetData['Expiry'] < $current_time) {
        die(json_encode(["status" => "error", "message" => "Reset link has expired."]));
    }

    // Update password in the employees table
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    $updateQuery = $conn->prepare("UPDATE employees SET Password = ? WHERE Email = ?");
    $updateQuery->bind_param("ss", $hashed_password, $resetData['Email']);

    if ($updateQuery->execute()) {
        echo json_encode(["status" => "success", "message" => "Password reset successfully!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to reset password."]);
    }

    // Delete the used token
    $conn->query("DELETE FROM password_resets WHERE Token = '$token'");

} else {
    echo json_encode(["status" => "error", "message" => "Invalid reset token."]);
}

$conn->close();
?>
