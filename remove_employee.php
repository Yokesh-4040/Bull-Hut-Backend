<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'config.php';

if (isset($_POST['employee_id'])) {
    $employee_id = intval($_POST['employee_id']);

    $stmt = $conn->prepare("DELETE FROM employees WHERE EmployeeID = ?");
    $stmt->bind_param("i", $employee_id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Employee removed successfully."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error: " . $stmt->error]);
    }
    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Employee ID not provided."]);
}

$conn->close();
?>
