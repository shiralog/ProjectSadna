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

// Create a new mysqli connection
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
    echo json_encode(["error" => "Connection failed: " . $conn->connect_error]);
    exit;
}

// Fetch tasks for the logged-in user
$userID = $_SESSION['ID'];
$sql = "SELECT * FROM Tasks WHERE UserID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $userID);
$stmt->execute();
$result = $stmt->get_result();
$tasks = $result->fetch_all(MYSQLI_ASSOC);

// Close the statement and connection
$stmt->close();
$conn->close();

echo json_encode($tasks);
?>