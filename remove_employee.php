<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'config.php';

$data = json_decode(file_get_contents('php://input'), true);

$employeeId = $data['employee_id'];

$stmt = $conn->prepare("DELETE FROM employees WHERE EmployeeID = ?");
$stmt->bind_param("i", $employeeId);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Employee removed successfully."]);
} else {
    echo json_encode(["status" => "error", "message" => "Error: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
