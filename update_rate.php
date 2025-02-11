<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'config.php';

if (isset($_POST['employee_id']) && isset($_POST['rate_per_dozen'])) {
    $employee_id = intval($_POST['employee_id']);
    $rate_per_dozen = floatval($_POST['rate_per_dozen']);

    $stmt = $conn->prepare("UPDATE daily_work SET RatePerDozen = ? WHERE EmployeeID = ?");
    $stmt->bind_param("di", $rate_per_dozen, $employee_id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Rate updated successfully for Employee ID: $employee_id."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error: " . $stmt->error]);
    }
    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Required fields are missing."]);
}

$conn->close();
?>
