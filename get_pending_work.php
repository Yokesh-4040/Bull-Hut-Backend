<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Asia/Kolkata');
include 'config.php';

$query = "SELECT dw.WorkID, e.FirstName, e.LastName, dw.DozensWorked, dw.RatePerDozen, dw.Date
          FROM daily_work dw
          JOIN employees e ON dw.EmployeeID = e.EmployeeID
          WHERE dw.Approved = FALSE";
$result = $conn->query($query);

$pendingWork = [];
while ($row = $result->fetch_assoc()) {
    $pendingWork[] = $row;
}

if (count($pendingWork) > 0) {
    echo json_encode(["status" => "success", "data" => $pendingWork]);
} else {
    echo json_encode(["status" => "success", "message" => "No pending work found."]);
}

$conn->close();
?>
