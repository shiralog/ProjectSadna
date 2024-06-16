<?php
session_start();
require_once 'config.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if the user is logged in and has a valid session ID
if (!isset($_SESSION['ID'])) {
    echo json_encode(["error" => "User not authenticated"]);
    exit;
}

$userID = $_SESSION['ID'];

// Get the list of group IDs from the POST request
$requestBody = file_get_contents('php://input');
$data = json_decode($requestBody, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(["error" => "Invalid JSON input"]);
    exit;
}

$groupIDs = isset($data['group_ids']) ? $data['group_ids'] : [];
if (empty($groupIDs)) {
    echo json_encode(["error" => "No group IDs provided"]);
    exit;
}

// Create a connection to the database
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check the connection
if ($conn->connect_error) {
    echo json_encode(["error" => "Connection failed: " . $conn->connect_error]);
    exit;
}

// Prepare the SQL query
$placeholders = implode(',', array_fill(0, count($groupIDs), '?'));
$sql = "SELECT GroupID FROM StudyGroups 
        WHERE GroupID IN ($placeholders)
        AND (GroupManagerID = ? OR StudentID2 = ? OR StudentID3 = ? OR StudentID4 = ? OR StudentID5 = ? OR StudentID6 = ?)";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(["error" => "SQL prepare error: " . $conn->error]);
    exit;
}

// Bind the parameters
$types = str_repeat('i', count($groupIDs)) . 'iiiiii';  // Assuming GroupID and user IDs are integers
$params = array_merge($groupIDs, array_fill(0, 6, $userID));
$stmt->bind_param($types, ...$params);

// Execute the statement
if (!$stmt->execute()) {
    echo json_encode(["error" => "SQL execute error: " . $stmt->error]);
    exit;
}

// Get the result set
$result = $stmt->get_result();
if (!$result) {
    echo json_encode(["error" => "SQL get_result error: " . $stmt->error]);
    exit;
}

// Fetch the group IDs the user is part of
$userGroups = [];
while ($row = $result->fetch_assoc()) {
    $userGroups[] = $row['GroupID'];
}

// Close statement and connection
$stmt->close();
$conn->close();

// Prepare the response
$response = [];
foreach ($groupIDs as $groupID) {
    $response[$groupID] = in_array($groupID, $userGroups);
}

// Output the response as JSON
echo json_encode($response);
?>