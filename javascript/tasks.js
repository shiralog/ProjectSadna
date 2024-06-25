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
    if (tasks.length === 0) {
        tasksDiv.innerHTML = `There are no tasks at the moment.`
    } else {
        tasksDiv.innerHTML = tasks.map(task => {
            let taskDiv = `<div class='task'>
            <h3>${task.Title}</h3>
            <p>${task.Description}</p>`;

            if (task.DueDate) {
                taskDiv += `<p>Due Date: ${task.DueDate}</p>`;
            }

            taskDiv += `
            <button class="btn" onclick="openShareModal(${task.TaskID})">Share</button>
            <button class="btn" onclick="deleteTask(${task.TaskID})">Delete</button>`;

            if (!task.IsCompleted) {
                taskDiv += `
                <button class="btn" onclick="openEditModal(${task.TaskID}, '${task.Title}', '${task.Description}', '${task.DueDate}')">Edit</button>
                <button class="btn" onclick="markTaskCompleted(${task.TaskID})">Mark as complete</button>
                `;
            } else {
                taskDiv += `<p id='task-completed'>COMPLETED</p>`;
            }

            taskDiv += `</div>`;

            return taskDiv;
        }).join('');
    }
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
        let archivedTaskDiv = `
        <div class="archived-task">
            <h3>${task.Title}</h3>
            <p>${task.Description}</p>`;

        if (task.DueDate) {
            archivedTaskDiv += `<p>Due Date: ${task.DueDate}</p>`;
        }

        archivedTaskDiv += `</div>`;

        return archivedTaskDiv;
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