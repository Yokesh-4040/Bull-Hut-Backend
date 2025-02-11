<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Asia/Kolkata');

include 'config_inventory.php';

// Check if the employee is logged in
if (!isset($_SESSION['employee_id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized. Please log in."]);
    exit();
}

$editor_id = $_SESSION['employee_id'];  // Get the employee ID from session

if (isset($_POST['lot_id'], $_POST['company_id'])) {
    $lot_id = intval($_POST['lot_id']);
    $company_id = intval($_POST['company_id']);

    // Update the lot with the new company assignment
    $stmt = $inventory_conn->prepare("UPDATE lots SET CompanyID = ? WHERE LotID = ?");
    $stmt->bind_param("ii", $company_id, $lot_id);

    if ($stmt->execute()) {
        // Log the company assignment in history
        $historyStmt = $conn->prepare("INSERT INTO rolls_inventory_history (EditorID, LotID, Action) VALUES (?, ?, 'ASSIGN_COMPANY')");
        $historyStmt->bind_param("ii", $editor_id, $lot_id);
        $historyStmt->execute();

        echo json_encode(["status" => "success", "message" => "Company assigned successfully and recorded in history."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error assigning company: " . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Lot ID and Company ID are required."]);
}

$inventory_conn->close();
?>
