<?php include 'session_verify.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chats</title>
    <style>
        #messages {
            max-height: 700px; /* Adjust this value as needed */
            overflow-y: auto;
        }

        .mainContainer {
            display: flex;
        }

        .mainContainer div {
            flex: 1;
        }

        /* General container styling */
        #partnersContainer {
            display: flex;
            flex-direction: column;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
            padding: 20px;
        }

        /* Card styling */
        .partnerCard {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            max-width: 200px;
            overflow: hidden;
            text-align: center;
            transition: transform 0.3s;
            cursor: pointer;
        }

        .partnerCard:hover {
            transform: scale(1.05);
        }

        /* Profile picture styling */
        .partnerCard img {
            border-bottom: 1px solid #ddd;
            width: 100%;
            height: auto;
            object-fit: cover;
        }

        /* Text styling */
        .partnerCard p {
            margin: 10px 0;
            font-size: 16px;
            color: #333;
        }

        /* Chat container styling */
        #chatContainer {
            padding: 20px;
        }

        /* Message styling */
        .message {
            border-bottom: 1px solid #ddd;
            padding: 10px 0;
        }

        .timestamp {
            font-size: 12px;
            color: #999;
        }
    </style>
</head>

<body>
    <?php include 'navbar.php'; ?>
    <div class="mainContainer">
        <div>
            <h3>My partners: </h3>
            <div id="partnersContainer"></div>
        </div>
        <div id="chat">
            <h2>Currently chatting with: <span id="chatTitle"></span></span></h2>
            <div id="chatContainer">
                <div id="messages"></div>
            </div>
        </div>
    </div>
    <a href="/pages/dashboard.php">Back</a>

    <script>
        // Pass the PHP session variable to JavaScript
        const userID = <?php session_start(); echo json_encode($_SESSION['ID']); ?>;
    </script>
    <script src="/javascript/load-partners.js"></script>
    <script src="/javascript/chat.js"></script>
</body>

</html>