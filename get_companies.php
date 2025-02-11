<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'config.php';

// Fetch the list of companies from the database
$query = "SELECT CompanyID, CompanyName FROM companies";
$result = $attendance_conn->query($query);

$companies = [];
while ($row = $result->fetch_assoc()) {
    $companies[] = $row;
}

if (count($companies) > 0) {
    echo json_encode(["status" => "success", "data" => $companies]);
} else {
    echo json_encode(["status" => "error", "message" => "No companies found."]);
}

$attendance_conn->close();
?>