<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Asia/Kolkata');

header('Content-Type: application/json');

$phone = $_POST['phone'] ?? null;
$enteredOtp = $_POST['otp'] ?? null;

if (!$phone || !$enteredOtp) {
    echo json_encode(["status" => "error", "message" => "Phone number and OTP are required."]);
    exit();
}

// Check OTP from session
session_start();
$savedOtp = $_SESSION['otp'][$phone] ?? null;

if ($enteredOtp == $savedOtp) {
    echo json_encode(["status" => "success", "message" => "OTP verified successfully!"]);
    unset($_SESSION['otp'][$phone]);  // Clear OTP after successful verification
} else {
    echo json_encode(["status" => "error", "message" => "Invalid OTP."]);
}
?>
