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

                    if (group.GroupManagerID === userID) {
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
            } else {
                groupsDiv.innerHTML = '<p>No groups found.</p>';
            }
        })
        .catch(error => {
            const groupsDiv = document.getElementById('groupsContainer');
            groupsDiv.innerHTML = '<p>An error occurred while fetching the groups.</p>';
            console.error('Error fetching groups:', error);
        });
}

function showAddPartnerModal(groupID) {
    fetch('get-partners.php')
        .then(response => response.json())
        .then(partners => {
            let modalContent = '<h2>Select a partner to add:</h2>';
            partners.forEach(partner => {
                modalContent += `<div>${partner.firstName} ${partner.lastName} <button onclick="addPartner(${groupID}, ${partner.ID})">Add</button></div>`;
            });
            document.getElementById('modalContent').innerHTML = modalContent;
            document.getElementById('modal').style.display = 'block';
        });
}

function showRemovePartnerModal(groupID) {
    fetch('get-group-members.php?groupID=' + groupID)
        .then(response => response.json())
        .then(members => {
            let modalContent = '<h2>Select a partner to remove:</h2>';
            members.forEach(member => {
                modalContent += `<div>${member.firstName} ${member.lastName} <button onclick="removePartner(${groupID}, ${member.ID})">Remove</button></div>`;
            });
            document.getElementById('modalContent').innerHTML = modalContent;
            document.getElementById('modal').style.display = 'block';
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

function closeModal() {
    document.getElementById('modal').style.display = 'none';
}