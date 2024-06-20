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

// Function to generate a unique EventID
function generateUniqueEventID($conn) {
    do {
        // Generate a random number between 100000 and 999999
        $eventID = rand(100000, 999999);
        
        // Declare $count here to ensure it is scoped correctly
        $count = 0;

        // Prepare and execute a query to check if the EventID already exists
        $stmt = $conn->prepare("SELECT COUNT(*) FROM Classrooms WHERE EventID = ?");
        $stmt->bind_param('i', $eventID);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        // If count is 0, the EventID is unique
    } while ($count > 0);

    return $eventID;
}

// Insert event into Classrooms table
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve POST data
    $groupID = $_POST['groupID'];
    $groupName = $_POST['groupName'];
    $groupSize = $_POST['groupSize'];
    $selectedDate = $_POST['selectedDate'];
    $selectedClassroom = $_POST['selectedClassroom'];
    $selectedClassroomHours = $_POST['selectedClassroomHours'];

    // Format date string into MySQL date format (YYYY-MM-DD)
    $formattedDate = date('Y-m-d', strtotime($selectedDate));

    // Generate a unique EventID
    $eventID = generateUniqueEventID($conn);

    // Prepare SQL statement to insert data
    $stmt = $conn->prepare("INSERT INTO Classrooms (StudyGroupID, StudyGroupName, ClassroomID, Hours, Date, NumberOfStudents, EventID)
                           VALUES (?, ?, ?, ?, ?, ?, ?)");

    // Bind parameters
    $stmt->bind_param('sssssii', $groupID, $groupName, $selectedClassroom, $selectedClassroomHours, $formattedDate, $groupSize, $eventID);

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