<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Asia/Kolkata');
header('Content-Type: application/json');

include 'config_inventory.php';

$query = "SELECT StatusID, StatusName FROM lot_statuses";
$result = $inventory_conn->query($query);

$statuses = [];
while ($row = $result->fetch_assoc()) {
    $statuses[] = $row;
}

echo json_encode(["status" => "success", "data" => $statuses]);

$inventory_conn->close();
?>
