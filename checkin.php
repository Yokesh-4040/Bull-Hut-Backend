<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Asia/Kolkata');

include 'config.php';

// Assuming the employee ID is sent via POST (e.g., from a Unity HTTP request)
if (isset($_POST['employee_id'])) {
    $employee_id = intval($_POST['employee_id']);
    $currentTime = date('Y-m-d H:i:s');
    $currentDate = date('Y-m-d');

    // Insert a new record for check-in
    $stmt = $conn->prepare("INSERT INTO attendance (EmployeeID, CheckInTime, Date) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $employee_id, $currentTime, $currentDate);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Check-in successful"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error: " . $stmt->error]);
    }
    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Employee ID not provided"]);
}

$conn->close();
?>
