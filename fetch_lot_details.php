<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Asia/Kolkata');

header('Content-Type: application/json');

// Include inventory database connection
include 'config_inventory.php';

// Check if inventory DB connection is established
if (!isset($inventory_conn) || $inventory_conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Failed to connect to inventory database: " . $inventory_conn->connect_error]));
}

// Check if Lot ID is provided
if (!isset($_GET['lot_id'])) {
    echo json_encode(["status" => "error", "message" => "Lot ID is required."]);
    exit();
}

$lotID = intval($_GET['lot_id']);

// Fetch Lot Details from Inventory DB
$lotQuery = $inventory_conn->prepare("
    SELECT l.LotID, l.LotName, s.StatusName, l.CompanyID
    FROM lots l
    LEFT JOIN lot_statuses s ON l.StatusID = s.StatusID
    WHERE l.LotID = ?
");

if (!$lotQuery) {
    echo json_encode(["status" => "error", "message" => "Prepare failed: " . $inventory_conn->error]);
    exit();
}

$lotQuery->bind_param("i", $lotID);
$lotQuery->execute();
$lotResult = $lotQuery->get_result();

if ($lotResult->num_rows === 0) {
    echo json_encode(["status" => "error", "message" => "Lot not found."]);
    exit();
}

$lotDetails = $lotResult->fetch_assoc();

// Fetch Company Name from Attendance DB if CompanyID exists
$companyName = "Not Assigned";
if (!empty($lotDetails['CompanyID'])) {
    include 'config.php';  // Attendance database connection

    if (!isset($attendance_conn) || $attendance_conn->connect_error) {
        die(json_encode(["status" => "error", "message" => "Failed to connect to attendance database: " . $attendance_conn->connect_error]));
    }

    $companyQuery = $attendance_conn->prepare("SELECT CompanyName FROM companies WHERE CompanyID = ?");
    $companyQuery->bind_param("i", $lotDetails['CompanyID']);
    $companyQuery->execute();
    $companyResult = $companyQuery->get_result();
    $companyData = $companyResult->fetch_assoc();
    $companyName = $companyData['CompanyName'] ?? "Unknown Company";
    $companyQuery->close();
}

$lotDetails['CompanyName'] = $companyName;

// Fetch Edit History from Inventory DB
$historyQuery = $inventory_conn->prepare("
    SELECT EditorID, Action, Timestamp 
    FROM rolls_inventory_history 
    WHERE LotID = ? 
    ORDER BY Timestamp DESC
");

if (!$historyQuery) {
    echo json_encode(["status" => "error", "message" => "Prepare failed for history: " . $inventory_conn->error]);
    exit();
}

$historyQuery->bind_param("i", $lotID);
$historyQuery->execute();
$historyResult = $historyQuery->get_result();

$editHistory = [];

// Fetch employee details from Attendance DB
while ($entry = $historyResult->fetch_assoc()) {
    $editorID = $entry['EditorID'];

    // Fetch Editor Details from Attendance DB
    $editorQuery = $attendance_conn->prepare("SELECT FirstName, Email, Phone FROM employees WHERE EmployeeID = ?");
    $editorQuery->bind_param("i", $editorID);
    $editorQuery->execute();
    $editorResult = $editorQuery->get_result();
    $editorData = $editorResult->fetch_assoc();

    $entry['FirstName'] = $editorData['FirstName'] ?? 'Unknown';
    $entry['Email'] = $editorData['Email'] ?? 'Not Provided';
    $entry['Phone'] = $editorData['Phone'] ?? 'Not Provided';

    $editHistory[] = $entry;
    $editorQuery->close();
}

// Final Response
$response = [
    "status" => "success",
    "data" => [
        "LotDetails" => $lotDetails,
        "EditHistory" => $editHistory
    ]
];

echo json_encode($response);

// Close connections
$lotQuery->close();
$historyQuery->close();
$inventory_conn->close();
if (isset($attendance_conn)) {
    $attendance_conn->close();
}
?>
