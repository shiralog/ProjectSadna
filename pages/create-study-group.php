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
$groupName = $_POST['groupName'];
$groupDescription = $_POST['groupDescription'] ?? null;
$groupManagerID = $_SESSION['ID'];
$numberOfStudents = 1;
$groupPassword = $_POST['groupPassword'];

// Create a new mysqli connection
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
    echo json_encode(["error" => "Connection failed: " . $conn->connect_error]);
    exit;
}

// Generate a unique GroupID
function generateUniqueGroupID($conn) {
    do {
        // Generate a random number
        $uniqueID = mt_rand(100000, 999999); // Adjust the range as needed
        
        // Ensure the ID is unique in the StudyGroups table
        $sql = "SELECT GroupID FROM StudyGroups WHERE GroupID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $uniqueID);
        $stmt->execute();
        $stmt->store_result();
    } while ($stmt->num_rows > 0);

    $stmt->close();
    return $uniqueID;
}


$groupID = generateUniqueGroupID($conn);

// Insert the new group into the StudyGroups table
$sql = "INSERT INTO StudyGroups (GroupName, GroupID, GroupDescription, GroupPassword, GroupManagerID, NumberOfStudents)
        VALUES (?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssi", $groupName, $groupID, $groupDescription, $groupPassword, $groupManagerID, $numberOfStudents);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Group created successfully."]);
} else {
    echo json_encode(["error" => "Error: " . $stmt->error]);
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>