<?php
session_start();

// If $_SESSION['ID'] is set, redirect to dashboard.php
if (isset($_SESSION['ID'])) {
    header("Location: /pages/dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find you partner</title>
</head>

<body>
    <h1>HELLO</h1>
    <h3>Login:</h3>
    <form id="loginForm">
        <label for="email">Email: </label>
        <input type="email" name="email" required>

        <label for="password">Password: </label>
        <input type="password" name="password" required>

        <button type="submit">Login</button>
    </form>
    <p id="message"></p>
    <a href="/pages/registration.html">Click here to register</a>

    <script src="/javascript/login.js"></script>
</body>

</html>