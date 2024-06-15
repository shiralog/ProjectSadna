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

                    if (group.GroupManagerID === userID) {
                        groupManagerText = `<p>You are this group's manager</p>`;
                        if (group.NumberOfStudents < 6) {
                            addPartnerButton = `<button class="addPartnerButton" data-groupID="${group.GroupID}">Add Partner</button>`;
                        }
                        if (group.NumberOfStudents > 1) {
                            removePartnerButton = `<button class="removePartnerButton" data-groupID="${group.GroupID}">Remove Partner</button>`;
                        }
                        if (group.NumberOfStudents > 0) {
                            viewGroupMembersButton = `<button class="viewGroupMembers" data-groupID="${group.GroupID}">View Members</button>`;
                        }
                    }

                    return `
                        <div class="groupCard" data-groupID="${group.GroupID}">
                            <p>Group Name: ${group.GroupName}</p>
                            <p>Group Description: ${group.GroupDescription}</p>
                            <p>Number of Students: ${group.NumberOfStudents}/6</p>
                            ${groupManagerText}
                            ${viewGroupMembersButton}
                            ${addPartnerButton}
                            ${removePartnerButton}
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
            groupMembers.forEach(member => {
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
            let modalContent = '<h2>Group Members:</h2>';
            modalContent += '<div class="member-list">';
            groupMembers.forEach(member => {
                modalContent += `<div>${member.firstName} ${member.lastName}`;

                // Check if member is already a manager
                if (!member.isManager) {
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