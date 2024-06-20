<?php
session_start(); // Start the session

require_once 'config.php';

// Set the content type to JSON
header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if the group_ids key exists in the POST data
if (!isset($_POST['group_ids']) || empty($_POST['group_ids'])) {
    echo json_encode(["error" => "No group IDs provided."]);
    exit;
}

try {
    // Decode JSON string to array
    $groupIDs = json_decode($_POST['group_ids'], true);
    if (!$groupIDs) {
        throw new Exception('Invalid JSON format');
    }
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}

// Create connection
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
    echo json_encode(["error" => "Connection failed: " . $conn->connect_error]);
    exit;
}

// Prepare the query to fetch events for the specified group IDs
$placeholders = implode(',', array_fill(0, count($groupIDs), '?'));
$sql = "SELECT * FROM Classrooms WHERE StudyGroupID IN ($placeholders)";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    echo json_encode(["error" => "Prepare statement failed: " . $conn->error]);
    exit;
}

// Bind the parameters dynamically
$stmt->bind_param(str_repeat('i', count($groupIDs)), ...$groupIDs);
$stmt->execute();
$result = $stmt->get_result();

$events = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $events[] = $row;
    }
}

// Close the statement and connection
$stmt->close();
$conn->close();

// Return the result as JSON
echo json_encode($events);
?>