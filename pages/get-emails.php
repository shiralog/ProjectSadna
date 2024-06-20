<?php
session_start(); // Start the session

require_once 'config.php';

// Set the content type to JSON
header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if IDs key exists in the POST data
if (!isset($_POST['ids']) || empty($_POST['ids'])) {
    echo json_encode(["error" => "No IDs provided."]);
    exit;
}

// Decode JSON string to array of IDs
$ids = json_decode($_POST['ids'], true);
if (!$ids) {
    echo json_encode(["error" => "Invalid JSON format"]);
    exit;
}

// Create connection
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
    echo json_encode(["error" => "Connection failed: " . $conn->connect_error]);
    exit;
}

// Prepare the query to fetch emails for the specified IDs from Students table
$placeholders = implode(',', array_fill(0, count($ids), '?'));
$sql = "SELECT EmailAddress FROM Students WHERE ID IN ($placeholders)";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    echo json_encode(["error" => "Prepare statement failed: " . $conn->error]);
    exit;
}

// Bind the parameters dynamically
$stmt->bind_param(str_repeat('i', count($ids)), ...$ids);
$stmt->execute();
$result = $stmt->get_result();

$emails = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $emails[] = $row['EmailAddress'];
    }
}

// Close the statement and connection
$stmt->close();
$conn->close();

// Return the result as JSON
echo json_encode($emails);
?>