<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'config_inventory.php';

if (isset($_POST['lot_id'], $_POST['diameter_id'], $_POST['number_of_rolls'], $_POST['weight'], $_POST['editor_id'])) {
    $lot_id = intval($_POST['lot_id']);
    $diameter_id = $_POST['diameter_id'];
    $number_of_rolls = intval($_POST['number_of_rolls']);
    $weight = floatval($_POST['weight']);  // Weight added as mandatory
    $editor_id = intval($_POST['editor_id']);

    // Check if total rolls exceed the lot limit
    $total_rolls_query = "SELECT SUM(NumberOfRolls) AS total_rolls FROM rolls_inventory WHERE LotID = ? AND DiameterID != ?";
    $total_stmt = $conn->prepare($total_rolls_query);
    $total_stmt->bind_param("is", $lot_id, $diameter_id);
    $total_stmt->execute();
    $total_result = $total_stmt->get_result();
    $total_data = $total_result->fetch_assoc();
    $current_total = $total_data['total_rolls'] + $number_of_rolls;

    // Get the maximum allowed rolls for the lot
    $lot_query = "SELECT NumberOfRolls FROM lots WHERE LotID = ?";
    $lot_stmt = $conn->prepare($lot_query);
    $lot_stmt->bind_param("i", $lot_id);
    $lot_stmt->execute();
    $lot_result = $lot_stmt->get_result();
    $lot_data = $lot_result->fetch_assoc();
    $lot_limit = $lot_data['NumberOfRolls'];

    if ($current_total > $lot_limit) {
        echo json_encode(["status" => "error", "message" => "Total rolls exceed the limit defined in the lot."]);
        exit();
    }

    // Insert or update roll record with weight
    $query = "INSERT INTO rolls_inventory (LotID, DiameterID, NumberOfRolls, Weight, EditorID)
              VALUES (?, ?, ?, ?, ?)
              ON DUPLICATE KEY UPDATE NumberOfRolls = VALUES(NumberOfRolls), Weight = VALUES(Weight), EditorID = VALUES(EditorID)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("isidi", $lot_id, $diameter_id, $number_of_rolls, $weight, $editor_id);

    if ($stmt->execute()) {
        // Log the addition or update in rolls_inventory_history
        $history_query = "INSERT INTO rolls_inventory_history (EditorID, LotID, DiameterID, Action) VALUES (?, ?, ?, 'INSERT_OR_UPDATE_ROLL')";
        $history_stmt = $conn->prepare($history_query);
        $history_stmt->bind_param("iis", $editor_id, $lot_id, $diameter_id);
        $history_stmt->execute();
        $history_stmt->close();

        echo json_encode(["status" => "success", "message" => "Roll added or updated successfully with weight."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error updating roll: " . $stmt->error]);
    }

    $stmt->close();
    $total_stmt->close();
    $lot_stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Required fields are missing, including weight."]);
}

$conn->close();
?>