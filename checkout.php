<?php
// checkout.php
date_default_timezone_set('Asia/Kolkata');
include 'config.php';


// Assuming the employee ID is sent via POST
if (isset($_POST['employee_id'])) {
    $employee_id = intval($_POST['employee_id']);
    $currentTime = date('Y-m-d H:i:s');
    $currentDate = date('Y-m-d');

    // Find the latest attendance record for today where CheckOutTime is null
    $stmt = $conn->prepare("SELECT AttendanceID FROM attendance WHERE EmployeeID = ? AND Date = ? AND CheckOutTime IS NULL ORDER BY CheckInTime DESC LIMIT 1");
    $stmt->bind_param("is", $employee_id, $currentDate);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $attendance_id = $row['AttendanceID'];
        // Update the record with the check-out time
        $updateStmt = $conn->prepare("UPDATE attendance SET CheckOutTime = ? WHERE AttendanceID = ?");
        $updateStmt->bind_param("si", $currentTime, $attendance_id);

        if ($updateStmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Check-out successful"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error: " . $updateStmt->error]);
        }
        $updateStmt->close();
    } else {
        echo json_encode(["status" => "error", "message" => "No active check-in found for today"]);
    }
    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Employee ID not provided"]);
}

$conn->close();
?>
