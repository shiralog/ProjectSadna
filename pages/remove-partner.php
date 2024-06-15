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

$groupID = $_POST['groupID'];
$partnerID = $_POST['partnerID']; // ID of the partner to be removed

// Create connection
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
    echo json_encode(["error" => "Connection failed: " . $conn->connect_error]);
    exit;
}

// SQL query to remove a partner
$sql = "
    UPDATE StudyGroups
    SET 
        StudentID2 = CASE WHEN StudentID2 = ? THEN NULL ELSE StudentID2 END,
        StudentID3 = CASE WHEN StudentID3 = ? THEN NULL ELSE StudentID3 END,
        StudentID4 = CASE WHEN StudentID4 = ? THEN NULL ELSE StudentID4 END,
        StudentID5 = CASE WHEN StudentID5 = ? THEN NULL ELSE StudentID5 END,
        StudentID6 = CASE WHEN StudentID6 = ? THEN NULL ELSE StudentID6 END,
        NumberOfStudents = NumberOfStudents - 1
    WHERE GroupID = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iiiiis", $partnerID, $partnerID, $partnerID, $partnerID, $partnerID, $groupID);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Partner removed successfully."]);
} else {
    echo json_encode(["error" => "Error: " . $stmt->error]);
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>