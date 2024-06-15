<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Study Groups</title>
    <style>
        .groupCard {
            border: 1px solid #ccc;
            padding: 10px;
            margin: 10px;
            border-radius: 5px;
        }
        .groupCard p {
            margin: 5px 0;
        }
        .buttonContainer {
            display: flex;
            gap: 10px;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0,0,0);
            background-color: rgba(0,0,0,0.4);
            padding-top: 60px;
        }
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <a href="dashboard.php">Back</a>
    <h1>Create a Study Group</h1>
    <form id="createGroupForm">
        <label for="groupName">Group Name:</label>
        <input type="text" id="groupName" name="groupName" required><br><br>

        <label for="groupDescription">Group Description:</label>
        <textarea id="groupDescription" name="groupDescription"></textarea><br><br>

        <button type="submit">Create Group</button>
    </form>
    <div id="response"></div>

    <h1>Your Study Groups</h1>
    <div id="groupsContainer"></div>

    <!-- The Modal -->
    <div id="partnerModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Select a Partner to Add</h2>
            <div id="partnersList"></div>
        </div>
    </div>

    <script>
        // Pass the PHP session variable to JavaScript
        const userID = <?php session_start(); echo json_encode($_SESSION['ID']); ?>;
        console.log(userID);
    </script>
    <script src="/javascript/study-group.js"></script>
</body>
</html>