<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Asia/Kolkata');

include 'config.php';

// Get today's date
$today = date('Y-m-d');

// Query to fetch today's earnings
$query = "SELECT e.FirstName, e.LastName, d.TotalDozens, d.TotalAmount
          FROM daily_earnings d
          JOIN employees e ON d.EmployeeID = e.EmployeeID
          WHERE d.Date = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $today);
$stmt->execute();
$result = $stmt->get_result();

$earnings = [];
while ($row = $result->fetch_assoc()) {
    $earnings[] = $row;
}

if (count($earnings) > 0) {
    echo json_encode(["status" => "success", "data" => $earnings], JSON_PRETTY_PRINT);
} else {
    echo json_encode(["status" => "success", "message" => "No earnings found for today."]);
}

$stmt->close();
$conn->close();
?>
