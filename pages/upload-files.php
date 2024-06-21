<?php
session_start(); // Start the session

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

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $uploadDir = 'uploads/';
    $uploadedFiles = [];

    foreach ($_FILES['file']['tmp_name'] as $key => $tmpName) {
        $fileName = basename($_FILES['file']['name'][$key]);
        $uploadFile = $uploadDir . $fileName;

        if (move_uploaded_file($tmpName, $uploadFile)) {
            $uploadedFiles[] = $fileName;
        } else {
            echo json_encode(["error" => "Error uploading file: $fileName"]);
            exit;
        }
    }

    echo json_encode(["success" => true, "files" => $uploadedFiles]);
} else {
    echo json_encode(["error" => "No files uploaded."]);
}
?>