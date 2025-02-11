<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Asia/Kolkata');

header('Content-Type: application/json');

// Database Connection
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        echo json_encode(["status" => "error", "message" => "Email and password are required."]);
        exit;
    }

    // Check if user exists
    $stmt = $conn->prepare("SELECT EmployeeID, FirstName, Email, Password FROM employees WHERE Email = ?");
    if (!$stmt) {
        echo json_encode(["status" => "error", "message" => "Failed to prepare SQL statement."]);
        exit;
    }
    
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        if (password_verify($password, $user['Password'])) {
            // Successful login
            echo json_encode([
                "status" => "success", 
                "user" => [
                    "EmployeeID" => $user['EmployeeID'],
                    "FirstName" => $user['FirstName'],
                    "Email" => $user['Email']
                ]
            ]);
        } else {
            echo json_encode(["status" => "error", "message" => "Invalid password."]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Email not found."]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}
?>
