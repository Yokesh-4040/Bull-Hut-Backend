<?php

include 'config_inventory.php';

$result = $inventory_conn->query("SELECT COUNT(*) as count FROM lots WHERE status = 'inside'");
$row = $result->fetch_assoc();
echo json_encode(["count" => $row['count']]);

$inventory_conn->close();

