<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'config.php';

if (isset($_POST['employee_id'])) {
    // Clear attendance for a specific employee
    $employee_id = intval($_POST['employee_id']);
    $stmt = $conn->prepare("DELETE FROM attendance WHERE EmployeeID = ?");
    $stmt->bind_param("i", $employee_id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Attendance cleared for Employee ID $employee_id"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error: " . $stmt->error]);
    }
    $stmt->close();
} else {
    // Clear all attendance records if no employee_id is provided
    if ($conn->query("DELETE FROM attendance")) {
        echo json_encode(["status" => "success", "message" => "All attendance records cleared."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error clearing attendance records."]);
    }
}

$conn->close();
?>
