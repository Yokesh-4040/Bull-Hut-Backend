<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'config.php';

$data = json_decode(file_get_contents('php://input'), true);

$employeeId = $data['employeeId'];
$firstName = $data['firstName'];
$lastName = $data['lastName'];
$email = $data['email'];
$phone = $data['phone'];
$companyId = $data['companyId'];
$roleId = $data['roleId'];

$stmt = $conn->prepare("UPDATE employees SET FirstName = ?, LastName = ?, Email = ?, Phone = ?, CompanyID = ?, RoleID = ? WHERE EmployeeID = ?");
$stmt->bind_param("ssssiii", $firstName, $lastName, $email, $phone, $companyId, $roleId, $employeeId);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Employee updated successfully."]);
} else {
    echo json_encode(["status" => "error", "message" => "Error: " . $stmt->error]);
}

$stmt->close();
$conn->close();

