<?php

include 'config.php';

$result = $conn->query("SELECT COUNT(*) as count FROM employees");
$row = $result->fetch_assoc();
echo json_encode(["count" => $row['count']]);

$conn->close();

