<?php
session_start();
require_once 'config.php';

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

$userID = $_SESSION['ID'];

// Fetch archived tasks for the logged-in user
$sql = "SELECT TaskID, Title, Description, DueDate FROM ArchivedTasks WHERE UserID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();

$archivedTasks = [];
while ($row = $result->fetch_assoc()) {
    $archivedTasks[] = $row;
}

$stmt->close();
$conn->close();

echo json_encode($archivedTasks);
?>