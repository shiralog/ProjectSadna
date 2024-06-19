<?php

require_once 'config.php';
require_once '../sendgrid-php/sendgrid-php.php';

use SendGrid\Mail\Mail;

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['ticketID']) || !isset($data['responseMessage']) || !isset($data['userID'])) {
    echo json_encode(["error" => "Invalid input"]);
    exit;
}

$ticketID = $data['ticketID'];
$userID = $data['userID'];
$responseMessage = $data['responseMessage'];

$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if ($conn->connect_error) {
    echo json_encode(["error" => "Connection failed: " . $conn->connect_error]);
    exit;
}

// Fetch user's email address
$emailQuery = "SELECT EmailAddress FROM Students WHERE ID = ?";
$emailStmt = $conn->prepare($emailQuery);
$emailStmt->bind_param("s", $userID);
$emailStmt->execute();
$emailStmt->bind_result($emailAddress);
$emailStmt->fetch();
$emailStmt->close();

if (empty($emailAddress)) {
    echo json_encode(["error" => "User email not found"]);
    $conn->close();
    exit;
}

// Update report
$sql = "UPDATE Reports SET ResponseMessage = ?, Status = 'Resolved' WHERE TicketID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $responseMessage, $ticketID);

if ($stmt->execute()) {
    // Send email notification to the user
    $emailSent = sendReplyNotificationEmail($emailAddress, $ticketID, $responseMessage);
    if ($emailSent) {
        echo json_encode(["success" => true, "email" => "sent"]);
    } else {
        echo json_encode(["success" => true, "email" => "Email failed: Unknown error"]);
    }
} else {
    echo json_encode(["error" => "Error: " . $stmt->error]);
}

$stmt->close();
$conn->close();

// Function to send notification email using SendGrid
function sendReplyNotificationEmail($emailAddress, $ticketID, $responseMessage) {
    $email = new Mail();
    $email->setFrom("projectsadna854@gmail.com", "SadnaProject");
    $email->setSubject("Your Ticket #" . $ticketID . " Has Been Replied");

    // HTML content for the notification email
    $htmlContent = '
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; }
                .container { max-width: 600px; margin: 0 auto; }
                .header { background-color: #f0f0f0; padding: 20px; }
                .content { padding: 20px; }
                .footer { background-color: #f0f0f0; padding: 10px; text-align: center; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>Your Ticket #' . htmlspecialchars($ticketID) . ' Has Been Replied</h1>
                </div>
                <div class="content">
                    <p>Dear user,</p>
                    <p>Your ticket with the ID <strong>' . htmlspecialchars($ticketID) . '</strong> has been replied with the following response:</p>
                    <p><em>"' . htmlspecialchars($responseMessage) . '"</em></p>
                    <p>If you have any further questions, please do not hesitate to contact us.</p>
                </div>
                <div class="footer">
                    <p>Thank you for using our service!</p>
                </div>
            </div>
        </body>
        </html>
    ';

    $email->addTo($emailAddress);
    $email->addContent("text/html", $htmlContent);

    $sendgrid = new \SendGrid('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');

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
?>