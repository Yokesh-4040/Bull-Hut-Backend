<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'config.php';

$result = $conn->query("SELECT a.AttendanceID, e.FirstName, e.LastName, a.CheckInTime, a.CheckOutTime, a.Date 
                        FROM attendance a
                        JOIN employees e ON a.EmployeeID = e.EmployeeID
                        ORDER BY a.Date DESC");

$attendance = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $attendance[] = $row;
    }
    echo json_encode(["status" => "success", "data" => $attendance], JSON_PRETTY_PRINT);
} else {
    echo json_encode(["status" => "success", "message" => "No attendance records found."]);
}

$conn->close();
?>
