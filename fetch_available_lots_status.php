<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'config_inventory.php';

$query = "SELECT StatusID, StatusName FROM lot_statuses";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    $statuses = array();
    while ($row = $result->fetch_assoc()) {
        $statuses[] = $row;
    }
    echo json_encode(["status" => "success", "data" => $statuses]);
} else {
    echo json_encode(["status" => "success", "data" => []]);
}

$conn->close();
?>
