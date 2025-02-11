<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Asia/Kolkata');
header('Content-Type: application/json');

include 'config.php';  // Using the unified config for database connection

$query = "SELECT CompanyID, CompanyName FROM companies";
$result = $attendance_conn->query($query);

$companies = [];
while ($row = $result->fetch_assoc()) {
    $companies[] = $row;
}

echo json_encode(["status" => "success", "data" => $companies]);

$attendance_conn->close();
?>
