<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['ID'])) {
    echo json_encode(["error" => "User ID not set in session."]);
    exit;
}

$taskID = $_POST['taskID'];
$title = $_POST['title'];
$description = $_POST['description'];
$due_date = !empty($_POST['due_date']) ? $_POST['due_date'] : null;

$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if ($conn->connect_error) {
    echo json_encode(["error" => "Connection failed: " . $conn->connect_error]);
    exit;
}

$sql = "UPDATE Tasks SET Title = ?, Description = ?, DueDate = ? WHERE TaskID = ?";
$stmt = $conn->prepare($sql);

// Use a ternary operator to set $due_date to null if it's empty
if ($due_date) {
    $stmt->bind_param('sssi', $title, $description, $due_date, $taskID);
} else {
    $stmt->bind_param('sssi', $title, $description, $due_date, $taskID);
}

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Task updated successfully."]);
} else {
    echo json_encode(["error" => "Error: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>