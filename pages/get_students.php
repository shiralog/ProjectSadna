<?php
session_start();

require_once 'config.php';

$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch students from database
$sql = "SELECT * FROM Students";
$result = $conn->query($sql);

$students = array();

if ($result->num_rows > 0) {
    // Output data of each row
    while($row = $result->fetch_assoc()) {
        $students[] = array(
            'firstName' => $row['FirstName'],
            'lastName' => $row['LastName'],
            'age' => $row['Age'],
            'region' => $row['Region'],
            'faculty' => $row['Faculty'],
            'gender' => $row['Gender'],
            'profileImagePath' => $row['ProfileImagePath'],
            'partnerType' => $row['PartnerType'],
            'id' => $row['ID'],
        );
    }
}
////////////
// Fetch likes status from database for the logged in user
$likes = array();

if (isset($_SESSION['ID'])) {
    $loggedInID = $_SESSION['ID'];
    $sql = "SELECT ToID, LikeStatus FROM StudentLikes WHERE FromID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $loggedInID);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $likes[$row['ToID']] = $row['LikeStatus'];
    }

    $stmt->close();
} else {
    $loggedInID = null;
}
////////////
// Close connection
$conn->close();

// Prepare the response data

// $response = array(
//     'students' => $students,
//     'loggedInID' => isset($_SESSION['ID']) ? $_SESSION['ID'] : null // Add loggedInID field
// );
$response = array(
    'students' => $students,
    'loggedInID' => $loggedInID,
    'likes' => $likes
);

// Send JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>