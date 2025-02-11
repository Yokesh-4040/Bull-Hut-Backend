<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Asia/Kolkata');

include 'config_inventory.php';  // Corrected to use the inventory config

$query = "SELECT DiameterID, Size, Color FROM roll_diameter_chart";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    $diameters = array();
    while ($row = $result->fetch_assoc()) {
        $diameters[] = $row;
    }
    echo json_encode(["status" => "success", "data" => $diameters]);
} else {
    echo json_encode(["status" => "success", "data" => []]);
}

$conn->close();
?>
