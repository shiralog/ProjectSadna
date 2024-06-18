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

// Get raw POST data
$data = json_decode(file_get_contents('php://input'), true);

// Check if data is properly received
if (!isset($data['fullName'], $data['email'], $data['issueTopic'], $data['issueContent'])) {
    echo json_encode(["error" => "Incomplete form data received."]);
    exit;
}

// Create a new mysqli connection
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
    echo json_encode(["error" => "Connection failed: " . $conn->connect_error]);
    exit;
}

// Generate a unique Ticket ID
function generateUniqueTicketID($conn) {
    do {
        $uniqueID = mt_rand(100000, 999999); // Generate random number
        $sql = "SELECT TicketID FROM Reports WHERE TicketID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $uniqueID);
        $stmt->execute();
        $stmt->store_result();
    } while ($stmt->num_rows > 0);

    $stmt->close();
    return $uniqueID;
}

// Get form data
$userID = $_SESSION['ID'];
$fullName = $data['fullName'];
$email = $data['email'];
$issueTopic = $data['issueTopic'];
$issueContent = $data['issueContent'];
$ticketID = generateUniqueTicketID($conn);
$dateOfSubmission = date("Y-m-d H:i:s");
$status = 'Pending';

// Insert into Reports table
$sql = "INSERT INTO Reports (UserID, FullName, Email, IssueTopic, IssueContent, TicketID, DateOfSubmission, Status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("issssiss", $userID, $fullName, $email, $issueTopic, $issueContent, $ticketID, $dateOfSubmission, $status);

if ($stmt->execute()) {
    echo json_encode(["ticketID" => $ticketID]);
} else {
    echo json_encode(["error" => "Error: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>