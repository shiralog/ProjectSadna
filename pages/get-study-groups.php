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

$userID = $_SESSION['ID'];

// Create connection
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
    echo json_encode(["error" => "Connection failed: " . $conn->connect_error]);
    exit;
}

// SQL query to fetch all groups the user is part of
$sql = "
    SELECT * FROM StudyGroups 
    WHERE GroupManagerID = ? 
    OR StudentID2 = ? OR StudentID3 = ? OR StudentID4 = ? OR StudentID5 = ? OR StudentID6 = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iiiiii", $userID, $userID, $userID, $userID, $userID, $userID);
$stmt->execute();
$result = $stmt->get_result();

$groups = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $groups[] = $row;
    }
}

// Close the statement and connection
$stmt->close();
$conn->close();

// Return the result as JSON
echo json_encode($groups);
?>