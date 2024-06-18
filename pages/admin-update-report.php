<?php

require_once 'config.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['ticketID']) || !isset($data['responseMessage'])) {
    echo json_encode(["error" => "Invalid input"]);
    exit;
}

$ticketID = $data['ticketID'];
$responseMessage = $data['responseMessage'];

$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if ($conn->connect_error) {
    echo json_encode(["error" => "Connection failed: " . $conn->connect_error]);
    exit;
}

$sql = "UPDATE Reports SET ResponseMessage = ?, Status = 'Resolved' WHERE TicketID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $responseMessage, $ticketID);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["error" => "Error: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>