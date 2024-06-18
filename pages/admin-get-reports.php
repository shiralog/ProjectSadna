<?php
require_once 'config.php';

// Fetch all reports
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT TicketID, IssueTopic, IssueContent, DateOfSubmission, ResponseMessage FROM Reports WHERE Status = 'Pending'";
$result = $conn->query($sql);

$reports = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $reports[] = $row;
    }
}

$conn->close();
echo json_encode($reports);
?>