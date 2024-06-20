<?php
session_start();

require_once 'config.php';

header('Content-Type: application/json');

// Ensure the user is logged in
if (!isset($_SESSION['ID'])) {
    echo json_encode(['success' => false, 'error' => 'User not logged in.']);
    exit;
}

// Create a new mysqli connection
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
    echo json_encode(["error" => "Connection failed: " . $conn->connect_error]);
    exit;
}

$userID = $_SESSION['ID'];

// Get the input data
$input = json_decode(file_get_contents('php://input'), true);
$groupID = $input['groupID'] ?? null;

if (!$groupID) {
    echo json_encode(['success' => false, 'error' => 'Invalid group ID.']);
    exit;
}

// Check if the user is the group manager
$query = $conn->prepare("SELECT GroupManagerID FROM StudyGroups WHERE GroupID = ?");
$query->bind_param("i", $groupID);
$query->execute();
$result = $query->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'error' => 'Group not found.']);
    exit;
}

$row = $result->fetch_assoc();
$groupManagerID = $row['GroupManagerID'];

if ($userID != $groupManagerID) {
    echo json_encode(['success' => false, 'error' => 'Only the group manager can delete the group.']);
    exit;
}

// Delete the group
$deleteQuery = $conn->prepare("DELETE FROM StudyGroups WHERE GroupID = ?");
$deleteQuery->bind_param("i", $groupID);

if ($deleteQuery->execute()) {
    echo json_encode(['success' => true, 'message' => 'Group deleted successfully.']);
} else {
    echo json_encode(['success' => false, 'error' => 'Error deleting the group.']);
}

$deleteQuery->close();
$conn->close();
?>