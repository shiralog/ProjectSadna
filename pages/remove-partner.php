<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if user ID is set in session
if (!isset($_SESSION['ID'])) {
    echo json_encode(["error" => "User ID not set in session."]);
    exit;
}

// Check if required data (groupID and removePartnerID) is provided
if (!isset($_POST['groupID']) || !isset($_POST['removePartnerID'])) {
    echo json_encode(["error" => "Required data not provided."]);
    exit;
}

$groupID = $_POST['groupID'];
$removePartnerID = $_POST['removePartnerID'];

// Create connection
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
    echo json_encode(["error" => "Connection failed: " . $conn->connect_error]);
    exit;
}

// Find the column where the partner ID is stored and set it to NULL
$columns = ['StudentID2', 'StudentID3', 'StudentID4', 'StudentID5', 'StudentID6'];
$columnToUpdate = null;

foreach ($columns as $column) {
    $sql = "SELECT $column FROM StudyGroups WHERE GroupID = ? AND $column = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $groupID, $removePartnerID);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $columnToUpdate = $column;
        break;
    }
}

if ($columnToUpdate === null) {
    echo json_encode(["error" => "Student not found in the group."]);
    exit;
}

// Determine the corresponding IsManagerX column
$columnNumber = substr($columnToUpdate, -1); // Extracts the number from column name (2, 3, 4, etc.)
$isManagerColumn = "IsManager" . $columnNumber;

// Update the StudentID and IsManager columns to NULL
$sql = "UPDATE StudyGroups SET $columnToUpdate = NULL, $isManagerColumn = NULL, NumberOfStudents = NumberOfStudents - 1 WHERE GroupID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $groupID);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Partner removed successfully."]);
} else {
    echo json_encode(["error" => "Failed to remove partner."]);
}

$stmt->close();
$conn->close();
?>