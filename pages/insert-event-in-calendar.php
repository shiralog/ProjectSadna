<?php
session_start(); // Start the session

require_once 'config.php';

// Set the content type to JSON
header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if userIdList and eventId keys exist in the POST data
if (!isset($_POST['userIdList']) || !isset($_POST['eventId'])) {
    echo json_encode(["error" => "User IDs and Event ID are required."]);
    exit;
}

// Decode JSON string to array of user IDs
$userIdList = json_decode($_POST['userIdList'], true);
if (!$userIdList) {
    echo json_encode(["error" => "Invalid JSON format for user IDs."]);
    exit;
}

$eventId = $_POST['eventId'];

// Create connection
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
    echo json_encode(["error" => "Connection failed: " . $conn->connect_error]);
    exit;
}

// Prepare the query to insert event IDs for each user ID into EventsInCalendar
$sql = "INSERT INTO EventsInCalendar (UserID, EventID) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    echo json_encode(["error" => "Prepare statement failed: " . $conn->error]);
    exit;
}

// Bind parameters and execute the statement for each userID
$success = true;
foreach ($userIdList as $userId) {
    $stmt->bind_param('ii', $userId, $eventId);
    $stmt->execute();
    if ($stmt->affected_rows <= 0) {
        $success = false;
    }
}

// Close the statement and connection
$stmt->close();
$conn->close();

// Return the result as JSON
if ($success) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["error" => "Failed to insert event IDs for some users."]);
}
?>