<?php
require_once 'config.php'; // Include your database configuration file

// Set the content type to JSON
header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if the student ID is provided in the GET request
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

// Prepare the query to check the status of the student ID in the Notifications table
$sql = "SELECT Status FROM Notifications WHERE ID = ?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    echo json_encode(["error" => "Prepare statement failed: " . $conn->error]);
    exit;
}

$stmt->bind_param('i', $studentID);
$stmt->execute();
$stmt->bind_result($status);
$stmt->fetch();

// Close the statement and connection
$stmt->close();
$conn->close();

// Return the status as JSON
if (isset($status)) {
    echo json_encode(["status" => $status ? true : false]);
} else {
    echo json_encode(["error" => "Student ID not found in Notifications table."]);
}
?>