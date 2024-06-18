<?php
session_start(); // Start the session

require_once 'config.php';

// Set the content type to JSON
header('Content-Type: application/json');

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

// Get Ticket ID from query parameter
$ticketID = $_GET['ticketID'] ?? '';

// Search for issue in Reports table
$sql = "SELECT TicketID, DateOfSubmission, IssueTopic, Status FROM Reports WHERE TicketID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $ticketID);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->bind_result($ticketID, $dateOfSubmission, $issueTopic, $status);
    $stmt->fetch();
    echo json_encode([
        "ticketID" => $ticketID,
        "dateOfSubmission" => $dateOfSubmission,
        "issueTopic" => $issueTopic,
        "status" => $status
    ]);
} else {
    echo json_encode(null); // Return null if no matching ticket found
}

$stmt->close();
$conn->close();
?>