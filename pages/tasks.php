<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Management</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            width: 300px;
            max-width: 100%;
            overflow-y: auto; /* Allow scrolling when content exceeds modal height */
            max-height: 80vh; /* Limit maximum height to 80% of viewport height */
        }

        /* Close button styles */
        .close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: none;
            border: none;
            font-size: 1.5em;
            cursor: pointer;
        }

        /* Modal background */
        #modalBackground {
            display: none;
            position: fixed;
            z-index: 999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        /* Style for archived tasks container */
        #archivedTasks {
            max-height: 400px; /* Limit height of container */
            overflow-y: auto; /* Enable scrolling */
        }

        /* Style for each archived task */
        .archived-task {
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
    </style>
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

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            fetchTasks();
            document.getElementById('taskForm').addEventListener('submit', createTask);
            document.getElementById('shareForm').addEventListener('submit', shareTask);
            document.getElementById('editForm').addEventListener('submit', updateTask);
        });

        async function fetchTasks() {
            const response = await fetch('get-tasks.php');
            const tasks = await response.json();
            const tasksDiv = document.getElementById('tasks');
            tasksDiv.innerHTML = tasks.map(task => {
                let taskDiv = `<div>
                <h3>${task.Title}</h3>
                <p>${task.Description}</p>`;

                if (task.DueDate) {
                    taskDiv += `<p>Due Date: ${task.DueDate}</p>`;
                }

                taskDiv += `
                <button onclick="openShareModal(${task.TaskID})">Share</button>
                <button onclick="deleteTask(${task.TaskID})">Delete</button>`;

                if (!task.IsCompleted) {
                    taskDiv += `
                    <button onclick="openEditModal(${task.TaskID}, '${task.Title}', '${task.Description}', '${task.DueDate}')">Edit</button>
                    <button onclick="markTaskCompleted(${task.TaskID})">Mark as complete</button>
                    `;
                } else {
                    taskDiv += `<p>COMPLETED</p>`;
                }

                taskDiv += `</div>`;

                return taskDiv;
            }).join('');
        }

        async function createTask(event) {
            event.preventDefault();
            const formData = new FormData(event.target);
            await fetch('create-task.php', {
                method: 'POST',
                body: formData
            });
            fetchTasks();
            event.target.reset();
        }

        async function deleteTask(TaskID) {
            const formData = new FormData();
            formData.append("taskID", TaskID);
            await fetch('delete-task.php', {
                method: 'POST',
                body: formData
            });
            fetchTasks();
        }

        async function openShareModal(taskID) {
            const response = await fetch('get-study-groups.php');
            const groups = await response.json();
            const studyGroupSelect = document.getElementById('studyGroup');
            studyGroupSelect.innerHTML = groups.map(group => `
                <option value="${group.GroupID}">${group.GroupName}</option>
            `).join('');
            document.getElementById('shareForm').dataset.taskID = taskID;
            document.getElementById('modalBackground').style.display = 'block';
            document.getElementById('shareModal').style.display = 'block';

            // Add event listener for studyGroup change
            studyGroupSelect.addEventListener('change', fetchGroupMembers);

            // Trigger fetchGroupMembers manually for the initial group selection
            fetchGroupMembers();
        }

        async function fetchGroupMembers() {
            const groupID = document.getElementById('studyGroup').value;
            const response = await fetch(`get-group-members.php?groupID=${groupID}`);
            const members = await response.json();
            const groupMemberSelect = document.getElementById('groupMember');
            groupMemberSelect.innerHTML = members.map(member => `
                <option value="${member.ID}">${member.firstName} ${member.lastName}</option>
            `).join('');
        }

        async function shareTask(event) {
            event.preventDefault();
            const taskID = document.getElementById('shareForm').dataset.taskID;
            const memberID = document.getElementById('groupMember').value;
            await fetch('share-task.php', {
                method: 'POST',
                body: JSON.stringify({ taskID, memberID }),
                headers: {
                    'Content-Type': 'application/json'
                }
            });
            closeModal();
        }

        async function markTaskCompleted(taskID) {
            await fetch('update-task-status.php', {
                method: 'POST',
                body: JSON.stringify({ taskID }),
                headers: {
                    'Content-Type': 'application/json'
                }
            });
            fetchTasks();
        }

        function openEditModal(taskID, title, description, dueDate) {
            document.getElementById('editTaskID').value = taskID;
            document.getElementById('editTitle').value = title;
            document.getElementById('editDescription').value = description;
            document.getElementById('editDueDate').value = dueDate || '';
            document.getElementById('modalBackground').style.display = 'block';
	    document.getElementById('editModal').style.display = 'block';
        }
async function updateTask(event) {
            event.preventDefault();
            const formData = new FormData(event.target);
            await fetch('update-task.php', {
                method: 'POST',
                body: formData
            });
            fetchTasks();
            closeModal();
        }

        async function openArchivedTasksModal() {
            const response = await fetch('get-archived-tasks.php');
            const archivedTasks = await response.json();
            const archivedTasksDiv = document.getElementById('archivedTasks');
            archivedTasksDiv.innerHTML = archivedTasks.map(task => {
                return `
                <div class="archived-task">
                    <h3>${task.Title}</h3>
                    <p>${task.Description}</p>
                    <p>Due Date: ${task.DueDate}</p>
                </div>
                `;
            }).join('');

            document.getElementById('modalBackground').style.display = 'block';
            document.getElementById('archivedTasksModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('modalBackground').style.display = 'none';
            document.getElementById('shareModal').style.display = 'none';
            document.getElementById('editModal').style.display = 'none';
            document.getElementById('archivedTasksModal').style.display = 'none';
        }

        // Close the modal when clicking outside of it
        window.onclick = function (event) {
            const shareModal = document.getElementById('shareModal');
            const editModal = document.getElementById('editModal');
            const archivedTasksModal = document.getElementById('archivedTasksModal');
            const modalBackground = document.getElementById('modalBackground');
            if (event.target == modalBackground) {
                closeModal();
            }
        }
    </script>
</body>

</html>
