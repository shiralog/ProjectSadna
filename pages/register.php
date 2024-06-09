<?php

require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $firstName = $_POST['FirstName'];
    $lastName = $_POST['LastName'];
    $age = $_POST['Age'];
    $id = $_POST['ID'];
    $region = $_POST['Region'];
    $faculty = $_POST['Faculty'];
    $gender = $_POST['Gender'];
    $emailAddress = $_POST['Email'];
    $password = $_POST['Password'];
    $phoneNumber = $_POST['PhoneNumber'];
    $profileImagePath = isset($_POST['ProfileImagePath']) ? $_POST['ProfileImagePath'] : NULL;  // Check if ProfileImagePath is set
    $partnerType = $_POST['PartnerType'];

    // Create connection
    $conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
    // Check connection
    if ($conn->connect_error) {
        echo "NOT OK: Connection failed";
        exit();
    }

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO Students (FirstName, LastName, Age, ID, Region, Faculty, Gender, EmailAddress, Password, PhoneNumber, ProfileImagePath, PartnerType) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if ($stmt === false) {
        echo "NOT OK: " . $conn->error;
        exit();
    }
    $stmt->bind_param("ssisssssssss", $firstName, $lastName, $age, $id, $region, $faculty, $gender, $emailAddress, $password, $phoneNumber, $profileImagePath, $partnerType);

    // Execute the statement
    if ($stmt->execute()) {
        echo trim("OK");
    } else {
        echo trim("NOT OK: " . $stmt->error);
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>