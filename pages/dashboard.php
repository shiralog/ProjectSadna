<?php
include 'session_verify.php';

if (!isset($_SESSION['firstName'])) {
    // Redirect to login page if the user is not logged in
    header("Location: /index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="/css/navbar.css">
    <link rel="stylesheet" href="/css/dashboard.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="container">
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['firstName']); ?>!</h1>

        <div class="card-container">
            <a class="card" href="partner-finder.php">
                <div class="card-content">
                    <h2>Find partners!</h2>
                    <img class="icon" src="/assets/user-add.png" alt="Find partners icon" style="margin-top: 28px;">
                </div>
            </a>
            <a class="card" href="chats.php">
                <div class="card-content">
                    <h2>Chat with your partners</h2>
                    <img class="icon" src="/assets/computer.png" alt="Chat with your partners icon">
                </div>
            </a>
            <a class="card" href="study-groups.php">
                <div class="card-content">
                    <h2>Study Groups</h2>
                    <img class="icon" src="/assets/users-alt.png" alt="Study Groups icon">
                </div>
            </a>
            <a class="card" href="classrooms.php">
                <div class="card-content">
                    <h2>Classrooms</h2>
                    <img class="icon" src="/assets/desk.png" alt="Classrooms icon">
                </div>
            </a>
            <a class="card" href="tasks.php">
                <div class="card-content">
                    <h2>Tasks</h2>
                    <img class="icon" src="/assets/file-spreadsheet.png" alt="Tasks icon">
                </div>
            </a>
            <a class="card" href="reports.php">
                <div class="card-content">
                    <h2>Report an issue</h2>
                    <img class="icon" src="/assets/comment-exclamation.png" alt="Report an issue icon">
                </div>
            </a>
        </div>
    </div>
</body>
</html>
