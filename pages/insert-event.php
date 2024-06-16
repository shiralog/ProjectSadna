<?php
require_once 'config.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Create connection
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
    echo json_encode(["error" => "Connection failed: " . $conn->connect_error]);
    exit;
}

// Insert event into Classrooms table
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve POST data
    $groupID = $_POST['groupID'];
    $groupName = $_POST['groupName'];
    $groupSize = $_POST['groupSize'];
    $selectedDate = $_POST['selectedDate'];
    $selectedClassroom = $_POST['selectedClassroom'];

    // Format date string into MySQL date format (YYYY-MM-DD)
    $formattedDate = date('Y-m-d', strtotime($selectedDate));

    // Prepare SQL statement to insert data
    $stmt = $conn->prepare("INSERT INTO Classrooms (StudyGroupID, StudyGroupName, ClassroomID, Date, NumberOfStudents)
                           VALUES (?, ?, ?, ?, ?)");

    // Bind parameters
    $stmt->bind_param('ssssi', $groupID, $groupName, $selectedClassroom, $formattedDate, $groupSize);

    // Execute the statement
    if ($stmt->execute()) {
        echo "Event inserted successfully!";
    } else {
        echo "Error: " . $conn->error;
    }

    // Close statement
    $stmt->close();
}

// Close connection
$conn->close();
?>