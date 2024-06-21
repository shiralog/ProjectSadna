document.addEventListener("DOMContentLoaded", function () {
    loadStudyGroups();

    document.getElementById('createGroupForm').addEventListener('submit', function (event) {
        event.preventDefault();

        const formData = new FormData(this);

        fetch('create-study-group.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                const responseDiv = document.getElementById('response');
                if (data.success) {
                    responseDiv.innerHTML = `<p>${data.message}</p>`;
                    loadStudyGroups(); // Reload the groups
                } else {
                    responseDiv.innerHTML = `<p>Error: ${data.error}</p>`;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                const responseDiv = document.getElementById('response');
                responseDiv.innerHTML = '<p>An error occurred while creating the group.</p>';
            });
    });

});

function loadStudyGroups() {
    // Fetch and display groups when the page loads
    fetch('get-study-groups.php')
        .then(response => response.json())
        .then(data => {
            const groupsDiv = document.getElementById('groupsContainer');
            if (!groupsDiv) {  // Changed line
                console.error('groupsContainer element not found.');
                return;
            }
            if (data.error) {
                groupsDiv.innerHTML = `<p>Error: ${data.error}</p>`;
            } else if (data.length > 0) {
                const groupsList = data.map(group => {
                    let groupManagerText = '';
                    let addPartnerButton = '';
                    let removePartnerButton = '';
                    let viewGroupMembersButton = '';
                    let deleteGroup = '';

                    if (group.NumberOfStudents > 0) {
                        viewGroupMembersButton = `<button class="viewGroupMembers" data-groupID="${group.GroupID}">View Members</button>`;
                    }

                    if (group.GroupManagerID === userID) {
                        deleteGroup = `<button class="deleteGroupButton" data-groupID="${group.GroupID}">Delete Group</button>`;
                    }

                    if (group.GroupManagerID === userID || group[`IsManager${getIsManagerColumn(group, userID)}`]) {
                        groupManagerText = `<p>You are this group's manager</p>`;
                        if (group.NumberOfStudents < 6) {
                            addPartnerButton = `<button class="addPartnerButton" data-groupID="${group.GroupID}">Add Partner</button>`;
                        }
                        if (group.NumberOfStudents > 1) {
                            removePartnerButton = `<button class="removePartnerButton" data-groupID="${group.GroupID}">Remove Partner</button>`;
                        }
                    }

                    return `
                        <div class="groupCard" data-groupID="${group.GroupID}">
                            <p>Group Name: ${group.GroupName}</p>
                            <p>Group Description: ${group.GroupDescription}</p>
                            <p>Number of Students: ${group.NumberOfStudents}/6</p>
                            ${groupManagerText}
                            ${viewGroupMembersButton}
                            <button class="shareFiles" data-GroupPassword="${group.GroupPassword}" data-groupID="${group.GroupID}">Share Files</button>
                            ${addPartnerButton}
                            ${removePartnerButton}
                            ${deleteGroup}
                        </div>
                    `;
                }).join('');
                groupsDiv.innerHTML = groupsList;

                // Add event listeners to the add and remove buttons
                document.querySelectorAll('.addPartnerButton').forEach(button => {
                    button.addEventListener('click', function () {
                        const groupID = this.getAttribute('data-groupID');
                        showAddPartnerModal(groupID);
                    });
                });

                document.querySelectorAll('.removePartnerButton').forEach(button => {
                    button.addEventListener('click', function () {
                        const groupID = this.getAttribute('data-groupID');
                        showRemovePartnerModal(groupID);
                    });
                });

                document.querySelectorAll('.viewGroupMembers').forEach(button => {
                    button.addEventListener('click', function () {
                        const groupID = this.getAttribute('data-groupID');
                        viewGroupMembers(groupID);
                    });
                });

                document.querySelectorAll('.deleteGroupButton').forEach(button => {
                    button.addEventListener('click', function () {
                        const groupID = this.getAttribute('data-groupID');
                        deleteGroup(groupID);
                    });
                });

                document.querySelectorAll('.shareFiles').forEach(button => {
                    button.addEventListener('click', function () {
                        const groupID = this.getAttribute('data-groupID');
                        const groupPassword = this.getAttribute('data-GroupPassword');
                        const enteredPassword = prompt("Enter the group password to share files:");

                        if (enteredPassword === groupPassword) {
                            openShareFilesPopup(groupID);
                        } else {
                            alert('Incorrect password.');
                        }
                    });
                });
            } else {
                groupsDiv.innerHTML = '<p>No groups found.</p>';
            }
        })
        .catch(error => {
            console.error('Error fetching groups:', error);
            const groupsDiv = document.getElementById('groupsContainer');
            groupsDiv.innerHTML = '<p>An error occurred while fetching the groups.</p>';
        });
}

function openShareFilesPopup(groupID) {
    const modalContent = `
        <div class="tabs">
            <button class="tablinks" onclick="openTab(event, 'UploadFiles')">Upload Files</button>
            <button class="tablinks" onclick="openTab(event, 'GetFiles')">Get Files</button>
        </div>
        <div id="UploadFiles" class="tabcontent" style="display: block;">
            <h3>Upload Files</h3>
            <form id="uploadFilesForm" enctype="multipart/form-data">
                <input type="file" id="fileInput" name="file" multiple required><br><br>
                <button type="submit">Upload</button>
            </form>
        </div>
        <div id="GetFiles" class="tabcontent">
            <h3>Get Files</h3>
            <div id="filesList"></div>
        </div>
    `;

    document.getElementById('modalContent').innerHTML = modalContent;
    document.getElementById('modal').style.display = 'flex';

    // Add event listener for the file upload form
    document.getElementById('uploadFilesForm').addEventListener('submit', function (event) {
        event.preventDefault();
        const formData = new FormData(this);
        formData.append('group_id', groupID);

        fetch('https://anatln.mtacloud.co.il/SadnaAPI/upload.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                console.log(data);
                if (data.status === 'success') {
                    alert('Files uploaded successfully.');
                } else {
                    alert('Error uploading files: ' + data.message);
                }
            })
            .then(() => {
                fetchFiles(groupID);
            })
            .catch(error => {
                console.error('Error uploading files:', error);
                alert('An error occurred while uploading files.');
            });
    });

    fetchFiles(groupID);
}

function fetchFiles(groupID) {
    // Fetch and display the list of files
    fetch(`https://anatln.mtacloud.co.il/SadnaAPI/files.php?group_id=${groupID}`)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'error') {
                if (data.message === 'Group not found.') {
                    document.getElementById('filesList').innerHTML = `<p>There are no files here at the moment.</p>`;
                } else {
                    document.getElementById('filesList').innerHTML = `<p>Error occurred while fetching the files.</p>`;
                }
            } else {
                const filesArray = Object.values(data.files);
                console.log(filesArray);
                console.log(filesArray.length);
                if (filesArray.length === 0) {
                    document.getElementById('filesList').innerHTML = `<p>There are no files here at the moment.</p>`;
                }
                console.log(filesArray);
                const filesList = filesArray.map(file =>
                    `<div><a href="https://anatln.mtacloud.co.il/SadnaAPI/download.php?group_id=${groupID}&file_name=${file}" download>${file}</a></div>`
                ).join('');
                document.getElementById('filesList').innerHTML = filesList;
            }
        })
        .catch(error => {
            console.error('Error fetching files:', error);
            document.getElementById('filesList').innerHTML = '<p>An error occurred while fetching the files.</p>';
        });
}

function openTab(evt, tabName) {
    // Hide all tab content
    const tabcontent = document.getElementsByClassName('tabcontent');
    for (let i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = 'none';
    }

    // Remove the 'active' class from all tab links
    const tablinks = document.getElementsByClassName('tablinks');
    for (let i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(' active', '');
    }

    // Show the current tab, and add an 'active' class to the button that opened the tab
    document.getElementById(tabName).style.display = 'block';
    evt.currentTarget.className += ' active';
}

function closeModal() {
    document.getElementById('modal').style.display = 'none';
}

window.onclick = function (event) {
    const modal = document.getElementById('modal');
    if (event.target == modal) {
        modal.style.display = 'none';
    }
}

function deleteGroup(groupID) {
    if (confirm("Are you sure you want to delete this group?")) {
        fetch('delete-group.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ groupID: groupID })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Group deleted successfully.');
                    loadStudyGroups(); // Reload the groups
                } else {
                    alert('Error deleting group: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error deleting group:', error);
                alert('An error occurred while deleting the group.');
            });
    }
}

function getIsManagerColumn(group, userID) {
    for (let i = 2; i <= 6; i++) {
        const studentIDColumn = `StudentID${i}`;
        const isManagerColumn = `IsManager${i}`;
        if (group[studentIDColumn] === userID && group[isManagerColumn]) {
            return i;
        }
    }
    return null;
}

function showAddPartnerModal(groupID) {  // Changed line
    // Fetch partners and current group members
    fetch(`get-group-members.php?groupID=${groupID}`)
        .then(response => response.json())
        .then(groupMembers => {
            fetch('get-partners.php')
                .then(response => response.json())
                .then(partners => {
                    // Filter partners who are not already in the group and have mutual likes
                    const nonGroupMembers = partners.filter(partner => {
                        // Check if the partner is not already a group member
                        const isGroupMember = groupMembers.some(member => member.ID.toString() === partner.ID);
                        return !isGroupMember;
                    });

                    if (nonGroupMembers.length === 0) {
                        // Display a message if there are no partners to add
                        document.getElementById('modalContent').innerHTML = '<p>No partners available to add.</p>';
                        document.getElementById('modal').style.display = 'flex';
                    } else {
                        // Display partners to add
                        let modalContent = '<h2>Select a partner to add:</h2>';
                        modalContent += '<div class="partner-list">';
                        nonGroupMembers.forEach(partner => {
                            modalContent += `<div>${partner.firstName} ${partner.lastName} <button onclick="addPartner(${groupID}, ${partner.ID})">Add</button></div>`;
                        });
                        modalContent += '</div>';
                        document.getElementById('modalContent').innerHTML = modalContent;
                        document.getElementById('modal').style.display = 'flex';
                    }
                })
                .catch(error => {
                    console.error('Error fetching partners:', error);
                    alert('An error occurred while fetching partners.');
                });
        })
        .catch(error => {
            console.error('Error fetching group members:', error);
            alert('An error occurred while fetching group members.');
        });
}

function showRemovePartnerModal(groupID) {  // Changed line
    // Fetch group members
    fetch(`get-group-members.php?groupID=${groupID}`)
        .then(response => response.json())
        .then(groupMembers => {
            let modalContent = '<h2>Select a partner to remove:</h2>';
            modalContent += '<div class="partner-list">';
            const groupCreatorID = groupMembers[0].ID;
            groupMembers.forEach(member => {
                if (member.ID === groupCreatorID)
                    modalContent += `<div>${member.firstName} ${member.lastName} ( Group creator can't be removed )</div>`;
                else
                    modalContent += `<div>${member.firstName} ${member.lastName} <button onclick="removePartner(${groupID}, ${member.ID})">Remove</button></div>`;
            });
            modalContent += '</div>';
            document.getElementById('modalContent').innerHTML = modalContent;
            document.getElementById('modal').style.display = 'flex';
        });
}

function addPartner(groupID, partnerID) {
    const formData = new FormData();
    formData.append('groupID', groupID);
    formData.append('newPartnerID', partnerID);

    fetch('add-partner.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                closeModal();
                location.reload(); // Reload to update the group display
            } else {
                alert(data.error);
            }
        });
}

function removePartner(groupID, partnerID) {
    const formData = new FormData();
    formData.append('groupID', groupID);
    formData.append('removePartnerID', partnerID);

    fetch('remove-partner.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                closeModal();
                location.reload(); // Reload to update the group display
            } else {
                alert(data.error);
            }
        });
}

function viewGroupMembers(groupID) {
    fetch(`get-group-members.php?groupID=${groupID}`)
        .then(response => response.json())
        .then(groupMembers => {
            console.log(groupMembers);
            let modalContent = '<h2>Group Members:</h2>';
            modalContent += '<div class="member-list">';
            let isLoggedInManager = false;
            groupMembers.forEach(member => {
                if (member.ID === userID) {
                    isLoggedInManager = member.isManager;
                }
            })

            groupMembers.forEach(member => {
                modalContent += `<div>${member.firstName} ${member.lastName}`;
                // Check if member is already a manager
                if (isLoggedInManager && !member.isManager) {
                    modalContent += ` <button onclick="makeManager(${groupID}, ${member.ID})">Make Manager</button>`;
                }

                modalContent += `</div>`;
            });
            modalContent += '</div>';
            document.getElementById('modalContent').innerHTML = modalContent;
            document.getElementById('modal').style.display = 'flex';
        })
        .catch(error => {
            console.error('Error fetching group members:', error);
            alert('An error occurred while fetching group members.');
        });
}

function makeManager(groupID, memberID) {
    fetch('make-manager.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ groupID, memberID })
    })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                alert('Member successfully made manager.');
                // Optionally refresh the group members list or update UI
                viewGroupMembers(groupID);
            } else {
                alert('Failed to make member manager. Please try again.');
            }
        })
        .catch(error => {
            console.error('Error making member manager:', error);
            alert('An error occurred while making member manager.');
        });
}

function closeModal() {
    document.getElementById('modal').style.display = 'none';
}

window.onclick = function (event) {
    const modal = document.getElementById('modal');
    if (event.target == modal) {
        modal.style.display = 'none';
    }
}