<?php
require_once 'config.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Create connection
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
    echo json_encode(["error" => "Connection failed: " . $conn->connect_error]);
    exit;
}

// Fetch events for the selected date
$date = $_GET['date'] ?? '';

if (!$date) {
    echo json_encode(["error" => "No date parameter provided"]);
    exit;
}

// Log the received date parameter for debugging
error_log("Received date parameter: " . $date);

// Prepare SQL statement to retrieve events
$sql = "SELECT * FROM Classrooms WHERE Date = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(["error" => "SQL prepare error: " . $conn->error]);
    exit;
}

$stmt->bind_param("s", $date);

// Execute the statement
if (!$stmt->execute()) {
    echo json_encode(["error" => "SQL execute error: " . $stmt->error]);
    exit;
}

// Get the result set
$result = $stmt->get_result();
if (!$result) {
    echo json_encode(["error" => "SQL get_result error: " . $stmt->error]);
    exit;
}

// Fetch data as associative array
$events = [];
while ($row = $result->fetch_assoc()) {
    $events[] = $row;
}

// Log the events array for debugging
error_log("Fetched events: " . print_r($events, true));

// Close statement and connection
$stmt->close();
$conn->close();

// Output events as JSON
echo json_encode($events);
?>