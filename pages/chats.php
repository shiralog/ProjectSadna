<?php include 'session_verify.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chats</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/navbar.css">
    <link rel="stylesheet" href="/css/chats.css">
</head>

<body>
    <?php include 'navbar.php'; ?>
    <div class="container">
        <div class="leftColumn">
            <h3>My Partners</h3>
            <div id="partnersContainer">
                <!-- Sample partner card -->
                <div class="partnerCard" onclick="startChat('Partner Name')">
                    <div class="partnerInfo">
                        <!-- Add partner details here -->
                        <p>Partner Name</p>
                    </div>
                </div>
                <!-- Repeat partner cards dynamically here using PHP/JS -->
            </div>
        </div>
        <div class="rightColumn">
            <div id="chat">
                <h2 id="beforeClick">Please pick a partner to start chatting</h2>
                <h2 id="afterClick" hidden>Currently chatting with: <span id="chatTitle">No one</span></h2>
                <div id="chatContainer" hidden>
                    <!-- Messages will be dynamically loaded here -->
                    <div id="messages"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Pass the PHP session variable to JavaScript
        const userID = <?php echo json_encode($_SESSION['ID']); ?>;
    </script>
    <script src="/javascript/load-partners.js"></script>
    <script src="/javascript/chat.js"></script>
</body>

</html>
