<?php
session_start();
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
$taskID = $_POST['taskID'];
$userID = $_SESSION['ID'];

// Create a new mysqli connection
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
    echo json_encode(["error" => "Connection failed: " . $conn->connect_error]);
    exit;
}

// Function to generate a unique TaskID
function generateUniqueTaskID($conn) {
    do {
        // Generate a random number
        $uniqueID = mt_rand(100000, 999999); // Adjust the range as needed
        
        // Ensure the ID is unique in the ArchivedTasks table
        $sql = "SELECT TaskID FROM ArchivedTasks WHERE TaskID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $uniqueID);
        $stmt->execute();
        $stmt->store_result();
    } while ($stmt->num_rows > 0);

    $stmt->close();
    return $uniqueID;
}

// Fetch the task to check if it's completed
$sqlFetch = "SELECT * FROM Tasks WHERE TaskID = ? AND UserID = ?";
$stmtFetch = $conn->prepare($sqlFetch);
$stmtFetch->bind_param('ii', $taskID, $userID);
$stmtFetch->execute();
$result = $stmtFetch->get_result();

if ($result->num_rows > 0) {
    $task = $result->fetch_assoc();

    if ($task['IsCompleted']) {
        // Generate a unique TaskID for ArchivedTasks
        $newTaskID = generateUniqueTaskID($conn);

        $due_date = !empty($task['DueDate']) ? $task['DueDate'] : null;

        // Archive the task if it's completed
        $sqlArchive = "INSERT INTO ArchivedTasks (UserID, Title, Description, DueDate, TaskID) VALUES (?, ?, ?, ?, ?)";
        $stmtArchive = $conn->prepare($sqlArchive);
        $stmtArchive->bind_param('isssi', $userID, $task['Title'], $task['Description'], $due_date, $newTaskID);

        if (!$stmtArchive->execute()) {
            echo json_encode(["error" => "Error archiving task: " . $stmtArchive->error]);
            exit;
        }
    }

    // Delete the task from the Tasks table
    $sqlDelete = "DELETE FROM Tasks WHERE TaskID = ? AND UserID = ?";
    $stmtDelete = $conn->prepare($sqlDelete);
    $stmtDelete->bind_param('ii', $taskID, $userID);

    if ($stmtDelete->execute()) {
        echo json_encode(["success" => true, "message" => "Task deleted successfully."]);
    } else {
        echo json_encode(["error" => "Error deleting task: " . $stmtDelete->error]);
    }
} else {
    echo json_encode(["error" => "Task not found or you don't have permission to delete it."]);
}

// Close the statements and connection
$stmtFetch->close();
$stmtDelete->close();
if (isset($stmtArchive)) {
    $stmtArchive->close();
}
$conn->close();
?>