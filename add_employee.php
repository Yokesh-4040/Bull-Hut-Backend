<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Asia/Kolkata');

include 'config.php';

// Check if required fields are provided
if (isset($_POST['first_name'], $_POST['last_name'], $_POST['role'], $_POST['company_id'])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = isset($_POST['email']) && !empty(trim($_POST['email'])) ? $_POST['email'] : null;
    $role = $_POST['role'];
    $company_id = intval($_POST['company_id']);

    // Step 1: Check if the role exists
    $roleQuery = "SELECT RoleID FROM roles WHERE RoleName = ?";
    $roleStmt = $conn->prepare($roleQuery);
    $roleStmt->bind_param("s", $role);
    $roleStmt->execute();
    $roleResult = $roleStmt->get_result();
    $roleData = $roleResult->fetch_assoc();

    // Step 2: Insert role if it doesn't exist
    if (!$roleData) {
        $insertRoleQuery = "INSERT INTO roles (RoleName) VALUES (?)";
        $insertRoleStmt = $conn->prepare($insertRoleQuery);
        $insertRoleStmt->bind_param("s", $role);

        if ($insertRoleStmt->execute()) {
            $role_id = $insertRoleStmt->insert_id;
        } else {
            echo json_encode(["status" => "error", "message" => "Error inserting new role: " . $insertRoleStmt->error]);
            exit();
        }
        $insertRoleStmt->close();
    } else {
        $role_id = $roleData['RoleID'];
    }

    // Step 3: Insert the new employee with RoleID and CompanyID
    $insertEmployeeQuery = "INSERT INTO employees (FirstName, LastName, Email, RoleID, CompanyID) VALUES (?, ?, ?, ?, ?)";
    $insertEmployeeStmt = $conn->prepare($insertEmployeeQuery);
    $insertEmployeeStmt->bind_param("sssii", $first_name, $last_name, $email, $role_id, $company_id);

    if ($insertEmployeeStmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Employee added successfully with role: $role."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error adding employee: " . $insertEmployeeStmt->error]);
    }

    $roleStmt->close();
    $insertEmployeeStmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Required fields are missing."]);
}

$conn->close();
?>