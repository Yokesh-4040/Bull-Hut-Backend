<?php
$servername = "localhost";  // or your hosting server
$username = "u293850383_4040_inventory";
$password = "P&]f+NxU6x";
$database = "u293850383_inventory";  // Inventory database name

$inventory_conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($inventory_conn->connect_error) {
    die("Connection failed: " . $inventory_conn->connect_error);
}
?>
