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

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (isset($data['event_id'])) {
    $eventID = $data['event_id'];

    // Debug log
    error_log("Received EventID for removal: $eventID");

    $stmt = $conn->prepare("DELETE FROM Classrooms WHERE EventID = ?");
    $stmt->bind_param("i", $eventID);
    if ($stmt->execute()) {
        echo 'success';
    } else {
        echo 'error';
    }
    $stmt->close();
} else {
    echo 'error';
}

$conn->close();
?>