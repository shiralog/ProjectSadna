<?php
session_start(); // Start the session

require_once 'config.php';

// Set the content type to JSON
header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if the user is logged in
if (!isset($_SESSION['ID'])) {
    echo json_encode(["error" => "User ID not set in session."]);
    exit;
}

// Get the form data
$data = json_decode(file_get_contents('php://input'), true);
$taskID = $data['taskID'];

// Create a new mysqli connection
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
    echo json_encode(["error" => "Connection failed: " . $conn->connect_error]);
    exit;
}

// Update the task's IsCompleted status
$sql = "UPDATE Tasks SET IsCompleted = 1 WHERE TaskID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $taskID);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Task updated successfully."]);
} else {
    echo json_encode(["error" => "Error: " . $stmt->error]);
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>