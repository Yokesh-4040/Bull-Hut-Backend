<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Asia/Kolkata');

include 'config.php';

if (isset($_POST['approver_id']) && isset($_POST['daily_work_id'])) {
    $approver_id = intval($_POST['approver_id']);
    $daily_work_id = intval($_POST['daily_work_id']);

    // Debug Approver ID
    echo json_encode(["status" => "debug", "message" => "Received approver_id: $approver_id, daily_work_id: $daily_work_id"]);

    // Check the approver's role
    $roleQuery = "SELECT r.RoleName, e.RoleID FROM employees e 
                  JOIN roles r ON e.RoleID = r.RoleID 
                  WHERE e.EmployeeID = ?";
    $stmt = $conn->prepare($roleQuery);

    if (!$stmt) {
        echo json_encode(["status" => "error", "message" => "Role query preparation failed: " . $conn->error]);
        exit();
    }

    $stmt->bind_param("i", $approver_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $approver = $result->fetch_assoc();

    if ($approver) {
        $roleName = $approver['RoleName'];
        $roleID = $approver['RoleID'];
        echo json_encode(["status" => "debug", "message" => "Approver found: RoleID = $roleID, RoleName = $roleName"]);

        if (in_array($roleName, ['Manager', 'Admin', 'Owner'])) {
            // Approve the daily work
            $approveQuery = "UPDATE daily_work SET Approved = TRUE WHERE WorkID = ?";
            $approveStmt = $conn->prepare($approveQuery);

            if (!$approveStmt) {
                echo json_encode(["status" => "error", "message" => "Approval query preparation failed: " . $conn->error]);
                exit();
            }

            $approveStmt->bind_param("i", $daily_work_id);

            if ($approveStmt->execute()) {
                echo json_encode(["status" => "success", "message" => "Daily work approved successfully."]);
            } else {
                echo json_encode(["status" => "error", "message" => "Error executing approval: " . $approveStmt->error]);
            }
            $approveStmt->close();
        } else {
            echo json_encode(["status" => "error", "message" => "You do not have permission to approve work. Role: $roleName"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Approver not found. Check if RoleID is linked correctly."]);
    }

    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Required fields are missing. approver_id: $approver_id, daily_work_id: $daily_work_id"]);
}

$conn->close();
?>
