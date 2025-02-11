<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Asia/Kolkata');

header('Content-Type: application/json');

$phone = $_POST['phone'] ?? $_GET['phone'] ?? null;

if (!$phone) {
    echo json_encode(["status" => "error", "message" => "Phone number is required."]);
    exit();
}

$authKey = '440660AWHEihovW67a32fffP1';
$senderId = 'msg91';  // Ensure you have a valid sender ID from MSG91
$otp = rand(100000, 999999);  // Generate 6-digit OTP

// Save OTP in the session or database for verification
session_start();
$_SESSION['otp'][$phone] = $otp;

// Prepare MSG91 API request
$url = "https://api.msg91.com/api/v5/otp";
$data = [
    "authkey" => $authKey,
    "mobile" => $phone,
    "otp" => $otp,
    "sender" => $senderId,
    "message" => "Your OTP code is $otp"
];

$options = [
    "http" => [
        "header"  => "Content-Type: application/json\r\n",
        "method"  => "POST",
        "content" => json_encode($data)
    ]
];

$context  = stream_context_create($options);
$response = file_get_contents($url, false, $context);

if ($response === FALSE) {
    echo json_encode(["status" => "error", "message" => "Failed to send OTP."]);
    exit();
}

echo json_encode(["status" => "success", "message" => "OTP sent successfully."]);
?>
