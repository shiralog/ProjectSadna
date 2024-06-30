<?php
session_start(); // Start the session

require_once 'config.php';

// Set the content type to JSON
header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if studentID is provided in the GET data
if (!isset($_GET['studentID'])) {
    echo json_encode(["error" => "Student ID is required."]);
    exit;
}

$studentID = $_GET['studentID'];

// Create connection
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
    echo json_encode(["error" => "Connection failed: " . $conn->connect_error]);
    exit;
}

// Prepare the query to update the notification status
$sql = "UPDATE Notifications SET Status = false WHERE ID = ?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    echo json_encode(["error" => "Prepare statement failed: " . $conn->error]);
    exit;
}

$stmt->bind_param('i', $studentID);
$success = $stmt->execute();

// Close the statement and connection
$stmt->close();
$conn->close();

// Return the result as JSON
if ($success) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["error" => "Failed to update notification status."]);
}
?>