<?php
session_start(); // Start the session

require_once 'config.php';

// Set the content type to JSON
header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if userId and eventId keys exist in the POST data
if (!isset($_POST['userId']) || !isset($_POST['eventId'])) {
    echo json_encode(["error" => "User ID and Event ID are required."]);
    exit;
}

$userId = $_POST['userId'];
$eventId = $_POST['eventId'];

// Create connection
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
    echo json_encode(["error" => "Connection failed: " . $conn->connect_error]);
    exit;
}

// Prepare the query to check if the event is already shared
$sql = "SELECT * FROM EventsInCalendar WHERE UserID = ? AND EventID = ?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    echo json_encode(["error" => "Prepare statement failed: " . $conn->error]);
    exit;
}

$stmt->bind_param('ii', $userId, $eventId);
$stmt->execute();
$result = $stmt->get_result();

$isShared = $result->num_rows > 0;

// Close the statement and connection
$stmt->close();
$conn->close();

// Return the result as JSON
echo json_encode(["shared" => $isShared]);
?>