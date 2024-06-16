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

// Fetch group members with their manager status and group creator's details
$sql = "
    SELECT g.GroupManagerID, s.ID, s.firstName, s.lastName,
           g.StudentID2, g.StudentID3, g.StudentID4, g.StudentID5, g.StudentID6,
           g.IsManager2, g.IsManager3, g.IsManager4, g.IsManager5, g.IsManager6,
           gm.firstName as managerFirstName, gm.lastName as managerLastName
    FROM StudyGroups g
    LEFT JOIN Students s ON s.ID IN (g.StudentID2, g.StudentID3, g.StudentID4, g.StudentID5, g.StudentID6)
    LEFT JOIN Students gm ON g.GroupManagerID = gm.ID
    WHERE g.GroupID = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $groupID);
$stmt->execute();
$result = $stmt->get_result();

$members = [];
$groupManagerDetails = null;
while ($row = $result->fetch_assoc()) {
    // Capture the group manager details
    if (!$groupManagerDetails) {
        $groupManagerDetails = [
            'ID' => $row['GroupManagerID'],
            'firstName' => $row['managerFirstName'],
            'lastName' => $row['managerLastName'],
            'isGroupCreator' => true,
            'isManager' => true // Group creator is always a manager
        ];
    }

    // Find which StudentID column this member belongs to
    if ($row['ID'] !== null && $row['GroupManagerID'] != $row['ID']) {
        for ($i = 2; $i <= 6; $i++) {
            $studentIDColumnName = "StudentID" . $i;
            if ($row[$studentIDColumnName] == $row['ID']) {
                // Determine the corresponding IsManager column
                $isManagerColumnName = "IsManager" . $i;
                $row['isManager'] = (bool) $row[$isManagerColumnName];
                $row['isGroupCreator'] = false;
                $members[] = [
                    'ID' => $row['ID'],
                    'firstName' => $row['firstName'],
                    'lastName' => $row['lastName'],
                    'isGroupCreator' => false,
                    'isManager' => $row['isManager']
                ]; // Collect members without duplicates
                break;
            }
        }
    }
}

$stmt->close();
$conn->close();

// Prepare the final output with group manager details as the first item
$output = [];
if ($groupManagerDetails) {
    $output[] = $groupManagerDetails;
}
$output = array_merge($output, $members);

echo json_encode($output); // Return the final list
?>