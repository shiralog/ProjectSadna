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

// Create a new mysqli connection
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
    echo json_encode(["error" => "Connection failed: " . $conn->connect_error]);
    exit;
}

// Generate a unique TaskID
function generateUniqueTaskID($conn) {
    do {
        // Generate a random number
        $uniqueID = mt_rand(100000, 999999); // Adjust the range as needed
        
        // Ensure the ID is unique in the Tasks table
        $sql = "SELECT TaskID FROM Tasks WHERE TaskID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $uniqueID);
        $stmt->execute();
        $stmt->store_result();
    } while ($stmt->num_rows > 0);

    $stmt->close();
    return $uniqueID;
}

// Get the form data
$title = $_POST['title'];
$description = $_POST['description'] ?? null;
$due_date = !empty($_POST['due_date']) ? $_POST['due_date'] : null;
$userID = $_SESSION['ID'];
$taskID = generateUniqueTaskID($conn);
$defaultIsCompleted = 0;

// Insert the new task into the Tasks table
$sql = "INSERT INTO Tasks (UserID, Title, Description, DueDate, TaskID, IsCompleted) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

// Use a ternary operator to set $due_date to null if it's empty
if ($due_date) {
    $stmt->bind_param('isssii', $userID, $title, $description, $due_date, $taskID, $defaultIsCompleted);
} else {
    $stmt->bind_param('isssii', $userID, $title, $description, $due_date, $taskID, $defaultIsCompleted);
}

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Task created successfully."]);
} else {
    echo json_encode(["error" => "Error: " . $stmt->error]);
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>