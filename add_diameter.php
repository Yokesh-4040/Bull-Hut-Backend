<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Asia/Kolkata');

// Include inventory database config
include 'config_inventory.php';

// Include attendance system config to verify EmployeeID
include 'config.php';

if (isset($_POST['diameter_id'], $_POST['size'], $_POST['color'], $_POST['editor_id'])) {
    $diameter_id = $_POST['diameter_id'];
    $size = $_POST['size'];
    $color = $_POST['color'];
    $editor_id = intval($_POST['editor_id']);

    // Verify if EditorID exists in the employees table from attendance system
    $verify_query = "SELECT EmployeeID FROM employees WHERE EmployeeID = ?";
    $verify_stmt = $conn->prepare($verify_query);
    $verify_stmt->bind_param("i", $editor_id);
    $verify_stmt->execute();
    $verify_result = $verify_stmt->get_result();

    if ($verify_result->num_rows == 0) {
        echo json_encode(["status" => "error", "message" => "Invalid Employee ID. Cannot add diameter."]);
        exit();
    }

    $verify_stmt->close();

    // Switch to inventory database connection for adding diameter
    include 'config_inventory.php';

    $query = "INSERT INTO roll_diameter_chart (DiameterID, Size, Color) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sss", $diameter_id, $size, $color);
    
try {
        if ($stmt->execute()) {
            // Log the addition in rolls_inventory_history
            $history_query = "INSERT INTO rolls_inventory_history (EditorID, DiameterID, Action) VALUES (?, ?, 'INSERT_DIAMETER')";
            $history_stmt = $conn->prepare($history_query);
            $history_stmt->bind_param("is", $editor_id, $diameter_id);
            $history_stmt->execute();
            $history_stmt->close();

            echo json_encode(["status" => "success", "message" => "Diameter added successfully."]);
        }
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1062) { // Duplicate entry error code
            echo json_encode(["status" => "error", "message" => "Duplicate entry detected. Diameter ID already exists."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error adding diameter: " . $e->getMessage()]);
        }
    }

    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Required fields are missing."]);
}

$conn->close();
?>
