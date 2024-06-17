<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Management</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Modal styles */
        #shareModal {
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
        }

        /* Close button styles */
        #shareModal .close-btn {
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

    <!-- Modal background -->
    <div id="modalBackground" onclick="closeModal()"></div>

    <!-- Modal for sharing tasks -->
    <div id="shareModal">
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
    <script>
        // Pass the PHP session variable to JavaScript
        const userID = <?php session_start(); echo json_encode($_SESSION['ID']); ?>;
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            fetchTasks();
            document.getElementById('taskForm').addEventListener('submit', createTask);
            document.getElementById('shareForm').addEventListener('submit', shareTask);
        });

        async function fetchTasks() {
            const response = await fetch('get-tasks.php');
            const tasks = await response.json();
            const tasksDiv = document.getElementById('tasks');
            tasksDiv.innerHTML = tasks.map(task => {

                let taskDiv = `<div>
                <h3>${task.Title}</h3>
                <p>${task.Description}</p>`;

                if (task.DueDate){
                    taskDiv += `<p>Due Date: ${task.DueDate}</p>`;
                }

                taskDiv += `
                <button onclick="openShareModal(${task.TaskID})">Share</button>
                <button onclick="markTaskCompleted(${task.TaskID})">Mark as complete</button>`;

                if(task.IsCompleted){
                    taskDiv += `<p>COMPLETED</p>`
                }

                taskDiv += `</div>`;

                return taskDiv;

            //     if (task.DueDate) {
            //         return `
            //     <div>
            //         <h3>${task.Title}</h3>
            //         <p>${task.Description}</p>
            //         <p>Due Date: ${task.DueDate}</p>
            //         <button onclick="openShareModal(${task.TaskID})">Share</button>
            //         <button onclick="markTaskCompleted(${task.TaskID})">Mark as complete</button>
            //     </div>
            // `;
            //     } else {
            //         return `
            //     <div>
            //         <h3>${task.Title}</h3>
            //         <p>${task.Description}</p>
            //         <button onclick="openShareModal(${task.TaskID})">Share</button>
            //         <button onclick="markTaskCompleted(${task.TaskID})">Mark as complete</button>
            //     </div>
            // `;
            //     }




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
            console.log("Adding event listener to studyGroup select");
            studyGroupSelect.addEventListener('change', fetchGroupMembers);

            // Trigger fetchGroupMembers manually for the initial group selection
            fetchGroupMembers();
        }

        async function fetchGroupMembers() {
            console.log("fetchGroupMembers called");
            const groupID = document.getElementById('studyGroup').value;
            console.log("Fetching group members for groupID:", groupID);
            const response = await fetch(`get-group-members.php?groupID=${groupID}`);
            const members = await response.json();
            console.log(members);
            console.log(userID);
            
            const membersWithoutLoggedInUser = members.filter(member => member.ID !== userID);
            const groupMemberSelect = document.getElementById('groupMember');
            
            if(membersWithoutLoggedInUser.length === 0){
                groupMemberSelect.style.display = 'none';
                document.getElementById('selectMemberLabel').style.display = 'none';
                document.getElementById('shareButton').style.display = 'none';
                document.getElementById('shareMessage').textContent = "You are the only member of this group.";
            }else{
                groupMemberSelect.style.display = 'inline';
                document.getElementById('selectMemberLabel').style.display = 'inline';
                document.getElementById('shareButton').style.display = 'block';
                groupMemberSelect.innerHTML = membersWithoutLoggedInUser.map(member => `
                <option value="${member.ID}">${member.firstName} ${member.lastName}</option>
                `).join('');
            }
            
        }

        async function shareTask(event) {
            event.preventDefault();
            const taskID = document.getElementById('shareForm').dataset.taskID;
            console.log(taskID);
            const memberID = document.getElementById('groupMember').value;
            console.log(memberID);
            await fetch('share-task.php', {
                method: 'POST',
                body: JSON.stringify({ taskID, memberID }),
                headers: {
                    'Content-Type': 'application/json'
                }
            });
            closeModal();
        }

        async function markTaskCompleted(taskID){
            await fetch('update-task-status.php', {
                method: 'POST',
                body: JSON.stringify({ taskID }),
                headers: {
                    'Content-Type': 'application/json'
                }
            });
            fetchTasks();
        }

        function closeModal() {
            document.getElementById('modalBackground').style.display = 'none';
            document.getElementById('shareModal').style.display = 'none';
        }
    </script>
</body>

</html>