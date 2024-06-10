<?php
session_start(); // Start the session to access session variables

require_once 'config.php';

$response = array('status' => 'NOT OK');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fromID = $_POST['from'];
    $toID = $_POST['to'];
    $likeStatus = $_POST['status'];

    // Check connection
    $conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("INSERT INTO StudentLikes (FromID, ToID, LikeStatus) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $fromID, $toID, $likeStatus);

    if ($stmt->execute()) {
        $response['status'] = 'OK';
    }

    $stmt->close();
    $conn->close();
}

echo json_encode($response);
?>