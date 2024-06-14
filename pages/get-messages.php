<?php
session_start(); // Start the session

require_once 'config.php';

// Set the content type to JSON
header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Assuming the session variable is already set
if (!isset($_SESSION['ID'])) {
    echo json_encode(["error" => "User ID not set in session."]);
    exit;
}

$userID = $_SESSION['ID'];
$partnerID = $_GET['partnerID'];

// Create connection
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
    echo json_encode(["error" => "Connection failed: " . $conn->connect_error]);
    exit;
}

// SQL query to fetch messages between the logged-in user and the partner
$sql = "
    SELECT senderID, receiverID, message, timestamp
    FROM ChatMessages
    WHERE (senderID = ? AND receiverID = ?) OR (senderID = ? AND receiverID = ?)
    ORDER BY timestamp ASC
";

// Prepare and bind the statement
$stmt = $conn->prepare($sql);
$stmt->bind_param("iiii", $userID, $partnerID, $partnerID, $userID);

// Execute the statement
$stmt->execute();

// Get the result
$result = $stmt->get_result();

// Fetch the messages
$messages = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }
}

// Close the statement and connection
$stmt->close();
$conn->close();

// Return the result as JSON
echo json_encode($messages);
?>