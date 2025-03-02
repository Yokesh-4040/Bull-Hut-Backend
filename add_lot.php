<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Asia/Kolkata');

include 'config_inventory.php';

// Check if required fields are provided
if (isset($_POST['lot_name'], $_POST['number_of_rolls'], $_POST['editor_id'])) {
    $lot_name = $_POST['lot_name'];
    $number_of_rolls = intval($_POST['number_of_rolls']);
    $editor_id = intval($_POST['editor_id']);
    $company_id = isset($_POST['company_id']) ? intval($_POST['company_id']) : null;

    // Insert the new lot into the lots table
    $stmt = $inventory_conn->prepare("INSERT INTO lots (LotName, NumberOfRolls, CompanyID) VALUES (?, ?, ?)");
    $stmt->bind_param("sii", $lot_name, $number_of_rolls, $company_id);

    if ($stmt->execute()) {
        $lot_id = $stmt->insert_id;

        // Log the lot addition to rolls_inventory_history
        $action = "Added new lot: $lot_name with $number_of_rolls rolls";
        $logStmt = $inventory_conn->prepare("INSERT INTO rolls_inventory_history (EditorID, LotID, Action) VALUES (?, ?, ?)");
        $logStmt->bind_param("iis", $editor_id, $lot_id, $action);

        if ($logStmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Lot added successfully and logged in history."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Lot added but failed to log history: " . $logStmt->error]);
        }

        $logStmt->close();
    } else {
        echo json_encode(["status" => "error", "message" => "Error adding lot: " . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Required fields are missing."]);
}

$inventory_conn->close();
?>
