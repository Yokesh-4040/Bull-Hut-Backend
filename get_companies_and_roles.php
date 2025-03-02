<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'config.php';

$companies = [];
$roles = [];

// Fetch companies
$companyResult = $conn->query("SELECT CompanyID, CompanyName FROM companies");
if ($companyResult->num_rows > 0) {
    while ($row = $companyResult->fetch_assoc()) {
        $companies[] = $row;
    }
}

// Fetch roles
$roleResult = $conn->query("SELECT RoleID, RoleName FROM roles");
if ($roleResult->num_rows > 0) {
    while ($row = $roleResult->fetch_assoc()) {
        $roles[] = $row;
    }
}

echo json_encode(["status" => "success", "companies" => $companies, "roles" => $roles]);

$conn->close();
?>
