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
$data = json_decode(file_get_contents('php://input'), true);
$taskID = $data['taskID'];
$memberID = $data['memberID'];

// Create a new mysqli connection
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
    echo json_encode(["error" => "Connection failed: " . $conn->connect_error]);
    exit;
}

// Fetch the task details from the Tasks table
$sql = "SELECT Title, Description, DueDate FROM Tasks WHERE TaskID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $taskID);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($title, $description, $due_date);
$stmt->fetch();

if ($stmt->num_rows === 0) {
    echo json_encode(["error" => "Task not found."]);
    $stmt->close();
    $conn->close();
    exit;
}

$stmt->close();

// Generate a unique TaskID for the new task
function generateUniqueTaskID($conn) {
    do {
        $uniqueID = mt_rand(100000, 999999); // Adjust the range as needed
        $sql = "SELECT TaskID FROM Tasks WHERE TaskID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $uniqueID);
        $stmt->execute();
        $stmt->store_result();
    } while ($stmt->num_rows > 0);

    $stmt->close();
    return $uniqueID;
}

$newTaskID = generateUniqueTaskID($conn);
$defaultIsCompleted = 0;

// Insert the task into the Tasks table with the new TaskID and memberID
$sql = "INSERT INTO Tasks (UserID, Title, Description, DueDate, TaskID, IsCompleted) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param('isssii', $memberID, $title, $description, $due_date, $newTaskID, $defaultIsCompleted);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Task shared successfully."]);
} else {
    echo json_encode(["error" => "Error: " . $stmt->error]);
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>