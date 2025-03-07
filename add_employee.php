<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'config.php';

$data = json_decode(file_get_contents('php://input'), true);

$firstName = $data['firstName'];
$lastName = $data['lastName'];
$email = $data['email'];
$phone = $data['phone'];
$companyId = $data['companyId'];
$roleId = $data['roleId'];

$stmt = $conn->prepare("INSERT INTO employees (FirstName, LastName, Email, Phone, CompanyID, RoleID) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssii", $firstName, $lastName, $email, $phone, $companyId, $roleId);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Employee added successfully."]);
} else {
    echo json_encode(["status" => "error", "message" => "Error: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
