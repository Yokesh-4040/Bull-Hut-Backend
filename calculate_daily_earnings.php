<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'config.php';

// Get today's date
$today = date('Y-m-d');

// Query to sum dozens worked and calculate total amount for each employee
$query = \"SELECT EmployeeID, SUM(DozensWorked) AS TotalDozens, SUM(DozensWorked * RatePerDozen) AS TotalAmount
          FROM daily_work
          WHERE Date = ?
          GROUP BY EmployeeID\";

$stmt = $conn->prepare($query);
$stmt->bind_param(\"s\", $today);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $employee_id = $row['EmployeeID'];
    $total_dozens = $row['TotalDozens'];
    $total_amount = $row['TotalAmount'];

    // Insert or update daily earnings
    $insertStmt = $conn->prepare(\"INSERT INTO daily_earnings (EmployeeID, Date, TotalDozens, TotalAmount)
                                  VALUES (?, ?, ?, ?)
                                  ON DUPLICATE KEY UPDATE TotalDozens = VALUES(TotalDozens), TotalAmount = VALUES(TotalAmount)\");
    $insertStmt->bind_param(\"isid\", $employee_id, $today, $total_dozens, $total_amount);

    if ($insertStmt->execute()) {
        echo json_encode([\"status\" => \"success\", \"message\" => \"Daily earnings calculated for Employee ID: $employee_id.\"]);\n";
    } else {
        echo json_encode([\"status\" => \"error\", \"message\" => \"Error calculating earnings for Employee ID: $employee_id.\"]);\n";
    }
    $insertStmt->close();
}

$stmt->close();
$conn->close();
?>
