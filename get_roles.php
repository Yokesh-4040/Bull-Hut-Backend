<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Asia/Kolkata');

include 'config.php';

// Fetch roles from the roles table
$query = "SELECT RoleID, RoleName FROM roles";
$result = $conn->query($query);

$roles = [];
while ($row = $result->fetch_assoc()) {
    $roles[] = $row;
}

if (count($roles) > 0) {
    echo json_encode(["status" => "success", "data" => $roles]);
} else {
    echo json_encode(["status" => "error", "message" => "No roles found."]);
}

$conn->close();
?>
