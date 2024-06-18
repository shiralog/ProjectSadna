<?php
session_start();
if (!isset($_SESSION['firstName'])) {
    // Redirect to login page if the user is not logged in
    header("Location: /index.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
</head>
<body>
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['firstName']); ?>!</h1>
    <p>This is your dashboard.</p>


    <a href="partner-finder.html">Find partners!</a>
    <a href="chats.php">Chat with your partners</a>
    <a href="study-groups.php">Study Groups</a>
    <a href="classrooms.html">Classrooms</a>
    <a href="tasks.php">Tasks</a>
    <a href="reports.html">Report an issue</a>

    <form action="logout.php" method="post">
        <button type="submit">Logout</button>
    </form>
</body>
</html>
