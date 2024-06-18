<?php
session_start(); // Start the session

require_once 'config.php';

// Set the content type to JSON
header('Content-Type: application/json');

// Get the raw POST data
$data = json_decode(file_get_contents('php://input'), true);

// Check if TicketID is set
if (!isset($data['ticketID'])) {
    echo json_encode(["error" => "Ticket ID not provided."]);
    exit;
}

// Create a new mysqli connection
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
    echo json_encode(["error" => "Connection failed: " . $conn->connect_error]);
    exit;
}

$ticketID = $data['ticketID'];

// Fetch the issue details including ResponseMessage
$sql = "SELECT TicketID, DateOfSubmission, IssueTopic, Status, ResponseMessage FROM Reports WHERE TicketID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $ticketID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $issue = $result->fetch_assoc();
    echo json_encode($issue);
} else {
    echo json_encode(["error" => "No issue found with the provided Ticket ID."]);
}

$stmt->close();
$conn->close();
?>