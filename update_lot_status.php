<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Asia/Kolkata');

include 'config_inventory.php';
include 'config.php';  // For employee validation from attendance system

header('Content-Type: application/json');

// Check if all required fields are provided
if (isset($_POST['lot_id'], $_POST['status_id'], $_POST['employee_id'])) {
    $lot_id = $_POST['lot_id'];
    $status_id = $_POST['status_id'];
    $employee_id = $_POST['employee_id'];

    // Step 1: Verify the Employee's Role from attendance database
    $roleCheckQuery = "SELECT r.RoleName FROM employees e JOIN roles r ON e.RoleID = r.RoleID WHERE e.EmployeeID = ?";
    $roleStmt = $attendance_conn->prepare($roleCheckQuery);
    $roleStmt->bind_param("i", $employee_id);
    $roleStmt->execute();
    $roleResult = $roleStmt->get_result();

    if ($roleData = $roleResult->fetch_assoc()) {
        $roleName = $roleData['RoleName'];

        // Step 2: Check if the role is allowed to update status
        if (in_array($roleName, ['Admin', 'Owner', 'Manager'])) {
            // Step 3: Update Lot Status
            $updateQuery = "UPDATE lots SET StatusID = ? WHERE LotID = ?";
            $stmt = $inventory_conn->prepare($updateQuery);
            $stmt->bind_param("ii", $status_id, $lot_id);

            if ($stmt->execute()) {
                // Step 4: Record in history
                $statusNameQuery = "SELECT StatusName FROM lot_statuses WHERE StatusID = ?";
                $statusStmt = $inventory_conn->prepare($statusNameQuery);
                $statusStmt->bind_param("i", $status_id);
                $statusStmt->execute();
                $statusResult = $statusStmt->get_result();
                $statusData = $statusResult->fetch_assoc();
                $statusName = $statusData['StatusName'] ?? 'Unknown';

                // Insert into history
                $historyQuery = "INSERT INTO rolls_inventory_history (EditorID, LotID, Action, Timestamp) VALUES (?, ?, ?, NOW())";
                $historyStmt = $inventory_conn->prepare($historyQuery);
                $action = "Updated Status to $statusName";
                $historyStmt->bind_param("iis", $employee_id, $lot_id, $action);
                $historyStmt->execute();

                echo json_encode(["status" => "success", "message" => "Lot status updated successfully."]);
            } else {
                echo json_encode(["status" => "error", "message" => "Failed to update lot status."]);
            }
            $stmt->close();
        } else {
            echo json_encode(["status" => "error", "message" => "Unauthorized: Only Admin, Owner, or Manager can update the status."]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Employee not found."]);
    }

    $roleStmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Lot ID, Status ID, and Employee ID are required."]);
}

$inventory_conn->close();
$attendance_conn->close();
?>
