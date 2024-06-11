<?php
session_start(); // Start the session

require_once 'config.php';

// Set the content type to JSON
header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);


// Assuming the session variable is already set
if (!isset($_SESSION['ID'])) {
    echo json_encode(["error" => "User ID not set in session."]);
    exit;
}

$userID = $_SESSION['ID'];

// Create connection
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);


// Check connection
if ($conn->connect_error) {
    echo json_encode(["error" => "User ID not set in session."]);
    exit;
}

// SQL query to fetch all ToID values that meet the criteria
$sql = "
    SELECT DISTINCT s1.ToID
    FROM StudentLikes s1
    JOIN StudentLikes s2 ON s1.FromID = s2.ToID AND s1.ToID = s2.FromID
    WHERE s1.FromID = ? AND s1.LikeStatus = 'Like' AND s2.ToID = ? AND s2.LikeStatus = 'Like'
";

// Prepare and bind the statement
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $userID, $userID);

// Execute the statement
$stmt->execute();

// Get the result
$result = $stmt->get_result();

// Fetch the ToIDs
$toIDs = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $toIDs[] = $row["ToID"];
    }
}

// Close the statement and connection
$stmt->close();

// If there are no matching IDs, return an empty array
if (empty($toIDs)) {
    echo json_encode([]);
    $conn->close();
    exit;
}

// Convert the IDs to a comma-separated string for the SQL query
$toIDsString = implode(',', $toIDs);

// SQL query to fetch firstName, lastName, and profilePathPicture from Students table
$sql = "
    SELECT ID, FirstName, LastName, ProfileImagePath
    FROM Students
    WHERE ID IN ($toIDsString)
";


$result = $conn->query($sql);

// Check for query error
if ($result === FALSE) {
    echo json_encode(["error" => "Query failed: " . $conn->error]);
    $conn->close();
    exit;
}

// Fetch the student details
$students = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $students[] = [
            "ID" => $row["ID"],
            "firstName" => $row["FirstName"],
            "lastName" => $row["LastName"],
            "profileImagePath" => $row["ProfileImagePath"]
        ];
    }
}

// Close the connection
$conn->close();

// Return the result as JSON
echo json_encode($students);
?>