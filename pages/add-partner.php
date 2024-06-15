<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if user ID is set in the session
if (!isset($_SESSION['ID'])) {
    echo json_encode(["error" => "User ID not set in session."]);
    exit;
}

if (!isset($_POST['groupID']) || !isset($_POST['newPartnerID'])) {
    echo json_encode(["error" => "Group ID or Partner ID not provided."]);
    exit;
}

$groupID = $_POST['groupID'];
$newPartnerID = $_POST['newPartnerID'];

// Create connection
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
    echo json_encode(["error" => "Connection failed: " . $conn->connect_error]);
    exit;
}

// Find the first NULL StudentID column
$columns = ['StudentID2', 'StudentID3', 'StudentID4', 'StudentID5', 'StudentID6'];
$columnToUpdate = null;

foreach ($columns as $column) {
    $sql = "SELECT $column FROM StudyGroups WHERE GroupID = ? AND $column IS NULL LIMIT 1";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        echo json_encode(["error" => "Prepare failed: " . $conn->error]);
        exit;
    }
    $stmt->bind_param("i", $groupID);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $columnToUpdate = $column;
        break;
    }
}

if ($columnToUpdate === null) {
    echo json_encode(["error" => "No empty spot found in the group."]);
    exit;
}

// Update the first available StudentID column
$sql = "UPDATE StudyGroups SET $columnToUpdate = ?, NumberOfStudents = NumberOfStudents + 1 WHERE GroupID = ?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    echo json_encode(["error" => "Prepare failed: " . $conn->error]);
    exit;
}
$stmt->bind_param("ii", $newPartnerID, $groupID);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Partner added successfully."]);
} else {
    echo json_encode(["error" => "Failed to add partner."]);
}

$stmt->close();
$conn->close();
?>