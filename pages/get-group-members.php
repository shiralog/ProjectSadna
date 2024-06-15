<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_GET['groupID'])) {
    echo json_encode(["error" => "Group ID not provided."]);
    exit;
}

$groupID = $_GET['groupID'];

// Create connection
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
    echo json_encode(["error" => "Connection failed: " . $conn->connect_error]);
    exit;
}

// Fetch group members
$sql = "
    SELECT s.ID, s.firstName, s.lastName
    FROM Students s
    JOIN StudyGroups g ON (
        s.ID = g.StudentID2 OR s.ID = g.StudentID3 OR 
        s.ID = g.StudentID4 OR s.ID = g.StudentID5 OR s.ID = g.StudentID6
    )
    WHERE g.GroupID = ? AND s.ID IS NOT NULL
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $groupID);
$stmt->execute();
$result = $stmt->get_result();

$members = [];
while ($row = $result->fetch_assoc()) {
    $members[] = $row;
}

$stmt->close();
$conn->close();

echo json_encode($members);
?>