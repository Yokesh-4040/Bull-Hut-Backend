<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Asia/Kolkata');

header('Content-Type: application/json');

include 'config_inventory.php';  // This connects to the inventory database

// Connect to attendance database for company details
$attendance_conn = new mysqli("localhost", "u293850383_attendance_use", "4040_Attendance", "u293850383_attendance_sys");
if ($attendance_conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Failed to connect to attendance database: " . $attendance_conn->connect_error]));
}

// Check Inventory Database Connection
if ($inventory_conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Failed to connect to inventory database: " . $inventory_conn->connect_error]));
}

// Fetch all lots
$lotsQuery = "SELECT l.LotID, l.LotName, l.CompanyID, l.NumberOfRolls, l.DateProvided, s.StatusName 
              FROM lots l 
              LEFT JOIN lot_statuses s ON l.StatusID = s.StatusID";
$lotsResult = $inventory_conn->query($lotsQuery);

if (!$lotsResult) {
    die(json_encode(["status" => "error", "message" => "Failed to fetch lots: " . $inventory_conn->error]));
}

$lots = [];

while ($lot = $lotsResult->fetch_assoc()) {
    $lotID = $lot['LotID'];

    // Fetch Company Name
    $companyName = "Not Assigned";
    if ($lot['CompanyID']) {
        $companyQuery = "SELECT CompanyName FROM companies WHERE CompanyID = ?";
        $companyStmt = $attendance_conn->prepare($companyQuery);
        if ($companyStmt) {
            $companyStmt->bind_param("i", $lot['CompanyID']);
            $companyStmt->execute();
            $companyResult = $companyStmt->get_result();
            if ($companyData = $companyResult->fetch_assoc()) {
                $companyName = $companyData['CompanyName'];
            }
            $companyStmt->close();
        }
    }
    $lot['CompanyName'] = $companyName;

    // Fetch Rolls Inventory
    $rollsQuery = "SELECT DiameterID, NumberOfRolls, Weight FROM rolls_inventory WHERE LotID = ?";
    $rollsStmt = $inventory_conn->prepare($rollsQuery);
    if ($rollsStmt) {
        $rollsStmt->bind_param("i", $lotID);
        $rollsStmt->execute();
        $rollsResult = $rollsStmt->get_result();
        $rolls = [];
        while ($roll = $rollsResult->fetch_assoc()) {
            $rolls[] = $roll;
        }
        $lot['RollsInventory'] = $rolls;
        $rollsStmt->close();
    }

    // Fetch Edit History
    $historyQuery = "SELECT EditorID, Action, Timestamp FROM rolls_inventory_history WHERE LotID = ? ORDER BY Timestamp DESC";
    $historyStmt = $inventory_conn->prepare($historyQuery);
    if ($historyStmt) {
        $historyStmt->bind_param("i", $lotID);
        $historyStmt->execute();
        $historyResult = $historyStmt->get_result();
        $history = [];
        while ($entry = $historyResult->fetch_assoc()) {
            $history[] = $entry;
        }
        $lot['EditHistory'] = $history;
        $historyStmt->close();
    }

    $lots[] = $lot;
}

// Return JSON response
echo json_encode(["status" => "success", "data" => $lots]);

$inventory_conn->close();
$attendance_conn->close();
?>
