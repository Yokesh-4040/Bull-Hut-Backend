<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'config.php';

// Calculate the current week's start and end dates (Sunday to Saturday)
$weekStartDate = date('Y-m-d', strtotime('last Sunday'));
$weekEndDate = date('Y-m-d', strtotime('this Saturday'));

// Query to get total dozens and amount for each employee
$query = \"SELECT EmployeeID, SUM(DozensWorked) AS TotalDozens, SUM(DozensWorked * RatePerDozen) AS TotalAmount
          FROM daily_work
          WHERE Date BETWEEN ? AND ?
          GROUP BY EmployeeID\";

$stmt = $conn->prepare($query);
$stmt->bind_param(\"ss\", $weekStartDate, $weekEndDate);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $employee_id = $row['EmployeeID'];
    $total_dozens = $row['TotalDozens'];
    $total_amount = $row['TotalAmount'];

    // Insert payout record
    $insertStmt = $conn->prepare(\"INSERT INTO salary_payouts (EmployeeID, WeekStartDate, WeekEndDate, TotalDozens, TotalAmount)
                                  VALUES (?, ?, ?, ?, ?)\");
    $insertStmt->bind_param(\"issid\", $employee_id, $weekStartDate, $weekEndDate, $total_dozens, $total_amount);

    if ($insertStmt->execute()) {
        echo json_encode([\"status\" => \"success\", \"message\" => \"Payout processed for Employee ID: $employee_id.\"]);\n";
    } else {
        echo json_encode([\"status\" => \"error\", \"message\" => \"Error processing payout for Employee ID: $employee_id.\"]);\n";
    }
    $insertStmt->close();
}

$stmt->close();
$conn->close();
?>
