<?php
// Set the content type to JSON
header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

$uploadDir = 'uploads/';
$files = array_diff(scandir($uploadDir), array('.', '..'));

echo json_encode(["files" => $files]);
?>