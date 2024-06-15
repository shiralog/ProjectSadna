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
            margin: 10px 0;
        }
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }
        .modal-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 20px;
            border-radius: 5px;
            max-height: 70%;
            overflow-y: auto;
        }
        .close-btn {
            float: right;
            cursor: pointer;
            color: red;
            font-size: 20px;
        }
    </style>
</head>
<body>
    <a href="dashboard.php">Back</a>
    <div>
        <h1>Create a Study Group</h1>
        <form id="createGroupForm">
            <label for="groupName">Group Name:</label>
            <input type="text" id="groupName" name="groupName" required><br><br>

            <label for="groupDescription">Group Description:</label>
            <textarea id="groupDescription" name="groupDescription"></textarea><br><br>

            <button type="submit">Create Group</button>
        </form>
        <div id="response"></div>
    </div>

    <h1>Study Groups</h1>
    <div id="groupsContainer"></div>

    <!-- Modal for adding/removing partners -->
    <div id="modal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal()">&times;</span>
            <div id="modalContent"></div>
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