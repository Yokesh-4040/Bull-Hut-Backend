<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'config.php';

// Fetch employee data with company and role details
$result = $conn->query("
    SELECT 
        e.EmployeeID, 
        e.FirstName, 
        e.LastName, 
        e.Email, 
        e.Phone, 
        c.CompanyName, 
        r.RoleName
    FROM employees e
    LEFT JOIN companies c ON e.CompanyID = c.CompanyID
    LEFT JOIN roles r ON e.RoleID = r.RoleID
    ORDER BY e.EmployeeID ASC
");

$employees = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $employees[] = $row;
    }
    echo json_encode(["status" => "success", "data" => $employees], JSON_PRETTY_PRINT);
} else {
    echo json_encode(["status" => "success", "message" => "No employee records found."]);
}

$conn->close();
?>
