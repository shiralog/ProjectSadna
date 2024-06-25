<?php include 'session_verify.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Study Groups</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/navbar.css">
    <link rel="stylesheet" href="/css/studygroups.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    <!-- Modal background -->
    <div id="modalBackground" onclick="closeModal()"></div>

    <div class="container">
        <div class="left-section">
            <h1>Create a Study Group</h1>
            <form id="createGroupForm">
                <div class="form-group">
                    <label for="groupName">Group Name:</label>
                    <input type="text" id="groupName" name="groupName" required>
                </div>
                <div class="form-group">
                    <label for="groupDescription">Group Description:</label>
                    <textarea id="groupDescription" name="groupDescription"></textarea>
                </div>
                <div class="form-group">
                    <label for="groupPassword">Group Password:</label>
                    <input type="text" id="groupPassword" name="groupPassword" required>
                </div>
                <button type="submit" class="btn">Create Group</button>
            </form>
            <div id="response"></div>
        </div>

        <div class="right-section">
            <h1>Study Groups</h1>
            <div id="groupsContainer" class="groups-container"></div>

            <!-- Modal for adding/removing partners -->
            <div id="modal" class="modal">
                <div class="modal-content">
                    <button class="close-btn" onclick="closeModal()">&times;</button>
                    <div id="modalContent"></div>
                </div>
            </div>
        </div>
    </div>

    
    <script>
        // Pass the PHP session variable to JavaScript
        const userID = <?php echo json_encode($_SESSION['ID']); ?>;
    </script>
    <script src="/javascript/study-group.js"></script>
</body>
</html>
