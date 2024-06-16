<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if the user is logged in and retrieve their ID
if (!isset($_SESSION['ID'])) {
    echo json_encode(["error" => "User not logged in."]);
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

// Fetch study group names that the user is part of
$sql = "
    SELECT g.GroupID, g.GroupName
    FROM StudyGroups g
    WHERE g.GroupManagerID = ? OR 
          EXISTS (
              SELECT 1 FROM StudyGroups s
              WHERE s.GroupID = g.GroupID AND
                    (s.GroupManagerID = ? OR s.StudentID2 = ? OR s.StudentID3 = ? OR 
                     s.StudentID4 = ? OR s.StudentID5 = ? OR 
                     s.StudentID6 = ?)
          )
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iiiiiii",$userID, $userID, $userID, $userID, $userID, $userID, $userID);
$stmt->execute();
$result = $stmt->get_result();

$groups = [];
while ($row = $result->fetch_assoc()) {
    $groups[] = [
        'GroupID' => $row['GroupID'],
        'GroupName' => $row['GroupName']
    ];
}

$stmt->close();
$conn->close();

echo json_encode($groups);
?>