<?php

require_once 'config.php';
require_once '../sendgrid-php/sendgrid-php.php';

use SendGrid\Mail\Mail;

// Function to send welcome email using SendGrid
function sendWelcomeEmail($emailAddress, $fullName) {
    $email = new Mail();
    $email->setFrom("projectsadna854@gmail.com", "SadnaProject");
    $email->setSubject("Welcome to Our Website!");

    // HTML content for the welcome email
    $htmlContent = '
        <html>
        <head>
            <style>
                /* Add your custom CSS styles here */
                body { font-family: Arial, sans-serif; display:flex; justift-content:center;}
                .container { max-width: 600px; margin: 0 auto; }
                .header { background-color: #f0f0f0; padding: 20px; }
                .content { padding: 20px; }
                .footer { background-color: #f0f0f0; padding: 10px; text-align: center; }
                img { width: 300px;  }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>Welcome to Our Website, ' . htmlspecialchars($fullName) . '!</h1>
                </div>
                <div class="content">
                    <p>Thank you for signing up on our website. We are excited to have you on board!</p>
                </div>
                <div class="image">
                    <img src="https://i.imgur.com/0ZxOsKC.png" alt="Welcome Image">
                </div>
                <div class="footer">
                    <p>If you have any questions, please don\'t hesitate to contact us.</p>
                </div>
            </div>
        </body>
        </html>
    ';

    $email->addTo($emailAddress, $fullName);
    $email->addContent("text/html", $htmlContent);

    $sendgrid = new \SendGrid(SENDGRID_API_KEY);

    try {
        $response = $sendgrid->send($email);
        if ($response->statusCode() >= 200 && $response->statusCode() < 300) {
            return true; // Email sent successfully
        } else {
            throw new Exception("Failed to send email: " . $response->body());
        }
    } catch (Exception $e) {
        error_log('Caught exception: ' . $e->getMessage());
        return false; // Email sending failed
    }
}

// Check if form data is submitted
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
    $partnerType = $_POST['PartnerType'];
    $profileImagePath = NULL;

    // Handle file upload
    if (isset($_FILES['ProfileImagePath']) && $_FILES['ProfileImagePath']['error'] == UPLOAD_ERR_OK) {
        $imageDir = dirname(__DIR__) . '/images/';
        $imageName = basename($_FILES['ProfileImagePath']['name']);
        $imagePath = $imageDir . $imageName;
        $imageURL = '/images/' . $imageName;

        if (move_uploaded_file($_FILES['ProfileImagePath']['tmp_name'], $imagePath)) {
            $profileImagePath = $imageURL;
        } else {
            $response = [
                "register" => "Registration failed: Error uploading image",
                "email" => "Email failed: Error uploading image"
            ];
            header('Content-Type: application/json');
            echo json_encode($response);
            exit();
        }
    }

    // Create connection
    $conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
    // Check connection
    if ($conn->connect_error) {
        $response = [
            "register" => "Registration failed: Connection error",
            "email" => "Email failed: Connection error"
        ];
    } else {
        // Prepare and bind
        $stmt = $conn->prepare("INSERT INTO Students (FirstName, LastName, Age, ID, Region, Faculty, Gender, EmailAddress, Password, PhoneNumber, ProfileImagePath, PartnerType) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if ($stmt === false) {
            $response = [
                "register" => "Registration failed: Error preparing query",
                "email" => "Email failed: Error preparing query"
            ];
        } else {
            $stmt->bind_param("ssisssssssss", $firstName, $lastName, $age, $id, $region, $faculty, $gender, $emailAddress, $password, $phoneNumber, $profileImagePath, $partnerType);

            // Execute the statement
            try {
                $stmt->execute();
                $registrationStatus = "Registered successfully";

                // Send welcome email
                $emailSent = sendWelcomeEmail($emailAddress, "$firstName $lastName");
                $emailStatus = $emailSent ? "sent" : "Email failed: Unknown error";
            } catch (mysqli_sql_exception $e) {
                if ($e->getCode() === 1062) { // Duplicate entry error code
                    $registrationStatus = "Registration failed: Duplicate entry of one or more fields";
                } else {
                    $registrationStatus = "Registration failed: " . $e->getMessage();
                }
                $emailStatus = "Email failed: User registration failed";
            }

            // Close the statement
            $stmt->close();
        }

        // Close the connection
        $conn->close();

        // Return JSON response
        $response = [
            "register" => $registrationStatus,
            "email" => $emailStatus
        ];
    }

    header('Content-Type: application/json');
    echo json_encode($response);
}

?>