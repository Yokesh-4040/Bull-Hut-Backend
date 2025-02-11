<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Asia/Kolkata');
//Test Completed
include 'config.php';

if (isset($_POST['employee_id']) && isset($_POST['dozens_worked']) && isset($_POST['rate_per_dozen'])) {
    $employee_id = intval($_POST['employee_id']);
    $dozens_worked = intval($_POST['dozens_worked']);
    $rate_per_dozen = floatval($_POST['rate_per_dozen']);
    $currentDate = date('Y-m-d');

    // Insert the new daily work record
    $stmt = $conn->prepare("INSERT INTO daily_work (EmployeeID, Date, DozensWorked, RatePerDozen) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isid", $employee_id, $currentDate, $dozens_worked, $rate_per_dozen);

    if ($stmt->execute()) {
        // Step 2: Recalculate daily earnings for the employee
        $earningsQuery = "SELECT SUM(DozensWorked) AS TotalDozens, SUM(DozensWorked * RatePerDozen) AS TotalAmount
                          FROM daily_work
                          WHERE EmployeeID = ? AND Date = ?";
                          
        $earningsStmt = $conn->prepare($earningsQuery);
        $earningsStmt->bind_param("is", $employee_id, $currentDate);
        $earningsStmt->execute();
        $result = $earningsStmt->get_result();
        $earnings = $result->fetch_assoc();

        $total_dozens = $earnings['TotalDozens'];
        $total_amount = $earnings['TotalAmount'];

        // Step 3: Update or insert into the daily_earnings table
        $updateEarningsStmt = $conn->prepare("INSERT INTO daily_earnings (EmployeeID, Date, TotalDozens, TotalAmount)
                                              VALUES (?, ?, ?, ?)
                                              ON DUPLICATE KEY UPDATE 
                                                  TotalDozens = VALUES(TotalDozens), 
                                                  TotalAmount = VALUES(TotalAmount)");
        $updateEarningsStmt->bind_param("isid", $employee_id, $currentDate, $total_dozens, $total_amount);

        if ($updateEarningsStmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Daily work and earnings updated successfully."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error updating earnings: " . $updateEarningsStmt->error]);
        }

        $updateEarningsStmt->close();
        $earningsStmt->close();
    } else {
        echo json_encode(["status" => "error", "message" => "Error adding daily work: " . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Required fields are missing."]);
}

$conn->close();
?>
<style>
body {
    background-color: #f8f9fa;
    padding: 20px;
}
h2 {
    font-size: 2rem;
}
.status {
    padding: 5px 10px;
    border-radius: 5px;
    color: #fff;
    font-size: 0.9rem;
}
.modal-content {
    border-radius: 10px;
}
.table-hover tbody tr {
    cursor: pointer;
}
.table-hover tbody tr:hover {
    background-color: #e9f7fc;
}
.modal-footer {
    justify-content: space-between;
}
</style>