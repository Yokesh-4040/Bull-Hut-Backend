<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'config_inventory.php';

$query = "SELECT LotID, LotName, CompanyID, NumberOfRolls, DateProvided, StatusID FROM lots";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    $lots = array();
    while ($row = $result->fetch_assoc()) {
        $lots[] = $row;
    }
    echo json_encode(["status" => "success", "data" => $lots]);
} else {
    echo json_encode(["status" => "success", "data" => []]);
}

$conn->close();
?>
