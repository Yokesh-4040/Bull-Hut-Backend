<?php
// config.php
$servername = "127.0.0.1:3306";
$username = "u293850383_attendance_use"; // Your DB username
$password = "4040_Attendance"; // Your DB password
$dbname = "u293850383_attendance_sys"; // Your DB name

// Create connection using mysqli or PDO; here, we use mysqli
$attendance_conn = new mysqli($servername, $username, $password, $dbname);
//$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($attendance_conn->connect_error) {
    die("Connection failed: " . $attendance_conn->connect_error);
}
$conn = $attendance_conn;
?>
<?php

