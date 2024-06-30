<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

// Get the list of student IDs from the input
$data = json_decode(file_get_contents('php://input'), true);
$studentIDs = $data['studentIDs'];

$response = [];

if (!isset($studentIDs) || !is_array($studentIDs)) {
    $response['error'] = "Invalid input: studentIDs must be an array.";
    echo json_encode($response);
    exit;
}

// Create a new mysqli connection
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
    $response['error'] = "Connection failed: " . $conn->connect_error;
    echo json_encode($response);
    exit;
}

// Prepare the SQL statement
$sql = "INSERT INTO Notifications (ID, Status) VALUES (?, ?) ON DUPLICATE KEY UPDATE Status = VALUES(Status)";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    $response['error'] = "Preparation failed: " . $conn->error;
    echo json_encode($response);
    exit;
}

// Insert each student ID with status set to true
$status = true;
foreach ($studentIDs as $studentID) {
    $stmt->bind_param("is", $studentID, $status);

    if (!$stmt->execute()) {
        $response['error'] = "Execution failed: " . $stmt->error;
        echo json_encode($response);
        exit;
    }
}

$stmt->close();
$conn->close();

$response['success'] = true;
$response['message'] = "Notifications added or updated successfully.";
echo json_encode($response);
?>