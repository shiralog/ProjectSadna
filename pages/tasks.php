<?php include 'session_verify.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Management</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/navbar.css">
    <link rel="stylesheet" href="/css/tasks.css">
</head>

<body>
    <?php include 'navbar.php'; ?>

    <div class="container">
        <div class="left-section">
            <h1>Create a New Task</h1>
            <form id="taskForm">
                <div class="form-group">
                    <label for="title">Task Title:</label>
                    <input type="text" id="title" name="title" required>
                </div>
                <div class="form-group">
                    <label for="description">Task Description (Optional):</label>
                    <textarea id="description" name="description"></textarea>
                </div>
                <div class="form-group">
                    <label for="due_date">Task Due Date (Optional):</label>
                    <input type="date" id="due_date" name="due_date">
                </div>
                <button class="btn" type="submit" class="btn">Create Task</button>
            </form>
        </div>

        <div class="right-section">
            <h1>My Tasks</h1>
            <div id="tasks" class="tasks-container"></div>
            <button class="btn" onclick="openArchivedTasksModal()">Archived Completed Tasks</button>
        </div>
    </div>

    <!-- Modal background -->
    <div id="modalBackground" onclick="closeModal()"></div>

    <!-- Modal for sharing tasks -->
    <div id="shareModal" class="modal">
        <button class="close-btn" onclick="closeModal()">&times;</button>
        <h2>Share Task</h2>
        <form id="shareForm">
            <div class="form-group">
                <label for="studyGroup">Select Study Group:</label>
                <select id="studyGroup" name="studyGroup" required></select>
            </div>
            <div class="form-group">
                <label id="selectMemberLabel" for="groupMember">Select Member:</label>
                <select id="groupMember" name="groupMember" required></select>
            </div>
            <button id="shareButton" type="submit" class="btn">Share Task</button>
            <p id="shareMessage"></p>
        </form>
    </div>

    <!-- Modal for editing tasks -->
    <div id="editModal" class="modal">
        <button class="close-btn" onclick="closeModal()">&times;</button>
        <h2>Edit Task</h2>
        <form id="editForm">
            <input type="hidden" id="editTaskID" name="taskID">
            <div class="form-group">
                <label for="editTitle">Task Title:</label>
                <input type="text" id="editTitle" name="title" required>
            </div>
            <div class="form-group">
                <label for="editDescription">Task Description (Optional):</label>
                <textarea id="editDescription" name="description"></textarea>
            </div>
            <div class="form-group">
                <label for="editDueDate">Task Due Date (Optional):</label>
                <input type="date" id="editDueDate" name="due_date">
            </div>
            <button type="submit" class="btn">Save Changes</button>
        </form>
    </div>

    <!-- Modal for archived tasks -->
    <div id="archivedTasksModal" class="modal">
        <button class="close-btn" onclick="closeModal()">&times;</button>
        <h2 style="color: #007bff;">Archived Completed Tasks</h2>
        <div id="archivedTasks" class="archived-tasks"></div>
    </div>

    <script src="/javascript/tasks.js"></script>
</body>

</html>
