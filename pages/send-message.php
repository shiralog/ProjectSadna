<?php
session_start(); // Start the session

require_once 'config.php';

// Set the content type to JSON
header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Assuming the session variable is already set
if (!isset($_SESSION['ID'])) {
    echo json_encode(["error" => "User ID not set in session."]);
    exit;
}

$senderID = $_SESSION['ID'];
$receiverID = $_POST['receiverID'];
$message = $_POST['message'];

// Create connection
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
    echo json_encode(["error" => "Connection failed: " . $conn->connect_error]);
    exit;
}

// SQL query to insert the new message
$sql = "INSERT INTO ChatMessages (senderID, receiverID, message, timestamp) VALUES (?, ?, ?, NOW())";

// Prepare and bind the statement
$stmt = $conn->prepare($sql);
$stmt->bind_param("iis", $senderID, $receiverID, $message);

// Execute the statement
if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["error" => "Error: " . $stmt->error]);
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>