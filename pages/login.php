<?php

require_once 'config.php';

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Create connection
    $conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

    // Check connection
    if ($conn->connect_error) {
        echo "Error no connection";
        exit();
    }

    // Prepare and bind
    $stmt = $conn->prepare("SELECT * FROM Students WHERE EmailAddress = ? AND Password = ?");
    if ($stmt === false) {
        echo "Error occurred while preparing the query";
        exit();
    }
    $stmt->bind_param("ss", $email, $password);

    // Execute the statement
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the user exists
    if ($result->num_rows > 0) {
        // Fetch the result as an associative array
        $row = $result->fetch_assoc();
        // Assign the FirstName to the session variable
        $_SESSION['firstName'] = $row['FirstName'];
        $_SESSION['ID'] = $row['ID'];
        echo "Logged in successfully";
    } else {
        echo "Login failed";
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>