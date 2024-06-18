<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Management</title>
    <link rel="stylesheet" href="/css/tasks.css">
</head>

<body>
    <a href="dashboard.php">Back</a>
    <h1>My Tasks</h1>
    <div id="tasks"></div>

    <h2>Create New Task</h2>
    <form id="taskForm">
        <label for="title">Task Title:</label>
        <input type="text" id="title" name="title" required><br>
        <label for="description">Task Description (Optional):</label>
        <textarea id="description" name="description"></textarea><br>
        <label for="due_date">Task Due Date (Optional):</label>
        <input type="date" id="due_date" name="due_date"><br>
        <button type="submit">Create Task</button>
    </form>

    <!-- Button to open Archived Tasks Modal -->
    <button onclick="openArchivedTasksModal()">Archived Completed Tasks</button>

    <!-- Modal background -->
    <div id="modalBackground" onclick="closeModal()"></div>

    <!-- Modal for sharing tasks -->
    <div id="shareModal" class="modal">
        <button class="close-btn" onclick="closeModal()">&times;</button>
        <h2>Share Task</h2>
        <form id="shareForm">
            <label for="studyGroup">Select Study Group:</label>
            <select id="studyGroup" name="studyGroup" required></select><br>
            <label id="selectMemberLabel" for="groupMember">Select Member:</label>
            <select id="groupMember" name="groupMember" required></select><br>
            <button id="shareButton" type="submit">Share Task</button>
            <p id="shareMessage"></p>
        </form>
    </div>

    <!-- Modal for editing tasks -->
    <div id="editModal" class="modal">
        <button class="close-btn" onclick="closeModal()">&times;</button>
        <h2>Edit Task</h2>
        <form id="editForm">
            <input type="hidden" id="editTaskID" name="taskID">
            <label for="editTitle">Task Title:</label>
            <input type="text" id="editTitle" name="title" required><br>
            <label for="editDescription">Task Description (Optional):</label>
            <textarea id="editDescription" name="description"></textarea><br>
            <label for="editDueDate">Task Due Date (Optional):</label>
            <input type="date" id="editDueDate" name="due_date"><br>
            <button type="submit">Save Changes</button>
        </form>
    </div>

    <!-- Modal for archived tasks -->
    <div id="archivedTasksModal" class="modal">
        <button class="close-btn" onclick="closeModal()">&times;</button>
        <h2>Archived Completed Tasks</h2>
        <div id="archivedTasks"></div>
    </div>

    <script src="/javascript/tasks.js"></script>
</body>
</html>