<?php
session_start();
require_once 'config.php'; // Adjust this to your configuration file

// Validate session and permissions (ensure user is group manager, etc.)

// Retrieve POST data
$data = json_decode(file_get_contents('php://input'), true);
$groupID = $data['groupID'];
$memberID = $data['memberID'];

// Create connection
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Find the column name where memberID is stored (StudentID2, StudentID3, etc.)
$sql = "SELECT COLUMN_NAME
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = '" . DB_NAME . "'
        AND TABLE_NAME = 'StudyGroups'
        AND COLUMN_NAME LIKE 'StudentID%'
        AND COLUMN_NAME <> 'StudentID1'"; // Exclude GroupManagerID

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $columnName = $row['COLUMN_NAME'];
        
        // Check if this is the correct column for the memberID
        $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM StudyGroups WHERE GroupID = ? AND {$columnName} = ?");
        $stmt->bind_param("ii", $groupID, $memberID);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count > 0) {
            // Update the IsManagerX column associated with this memberID
            $updateStmt = $conn->prepare("UPDATE StudyGroups SET IsManager" . substr($columnName, -1) . " = TRUE WHERE GroupID = ? AND {$columnName} = ?");
            $updateStmt->bind_param("ii", $groupID, $memberID);

            if ($updateStmt->execute()) {
                $response = ["success" => true];
            } else {
                $response = ["success" => false, "error" => $conn->error];
            }

            $updateStmt->close();
            break; // Exit the loop once updated
        }
    }
} else {
    $response = ["success" => false, "error" => "No matching StudentID column found."];
}

// Close connection
$conn->close();

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>