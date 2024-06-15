// // document.addEventListener("DOMContentLoaded", function () {
// //     loadUserGroups();

// //     document.getElementById('createGroupForm').addEventListener('submit', function (event) {
// //         event.preventDefault();

// //         const formData = new FormData(this);

// //         fetch('create-group.php', {
// //             method: 'POST',
// //             body: formData
// //         })
// //             .then(response => response.json())
// //             .then(data => {
// //                 const responseDiv = document.getElementById('response');
// //                 if (data.success) {
// //                     responseDiv.innerHTML = `<p>${data.message}</p>`;
// //                     loadUserGroups(); // Reload the groups
// //                 } else {
// //                     responseDiv.innerHTML = `<p>Error: ${data.error}</p>`;
// //                 }
// //             })
// //             .catch(error => {
// //                 console.error('Error:', error);
// //                 const responseDiv = document.getElementById('response');
// //                 responseDiv.innerHTML = '<p>An error occurred while creating the group.</p>';
// //             });
// //     });
// // });

// // function loadUserGroups() {
// //     fetch('get-study-groups.php')
// //         .then(response => response.json())
// //         .then(groups => {
// //             const groupsContainer = document.getElementById('groupsContainer');
// //             if (groups.error) {
// //                 groupsContainer.innerHTML = `<p>Error: ${groups.error}</p>`;
// //             } else if (groups.length > 0) {
// //                 const groupsList = groups.map(group => `
// //                     <div class="groupCard">
// //                         <h2>${group.GroupName}</h2>
// //                         <p>${group.GroupDescription || 'No description'}</p>
// //                         <p><strong>Group ID:</strong> ${group.GroupID}</p>
// //                         <p><strong>Manager ID:</strong> ${group.GroupManagerID}</p>
// //                         <p><strong>Number of Students:</strong> ${group.NumberOfStudents}</p>
// //                     </div>
// //                 `).join('');
// //                 groupsContainer.innerHTML = groupsList;
// //             } else {
// //                 groupsContainer.innerHTML = '<p>No groups found.</p>';
// //             }
// //         })
// //         .catch(error => {
// //             console.error('Error fetching groups:', error);
// //             const groupsContainer = document.getElementById('groupsContainer');
// //             groupsContainer.innerHTML = '<p>An error occurred while fetching the groups.</p>';
// //         });
// // }

// document.addEventListener("DOMContentLoaded", function () {
//     loadUserGroups();

//     document.getElementById('createGroupForm').addEventListener('submit', function (event) {
//         event.preventDefault();

//         const formData = new FormData(this);

//         fetch('create-group.php', {
//             method: 'POST',
//             body: formData
//         })
//             .then(response => response.json())
//             .then(data => {
//                 const responseDiv = document.getElementById('response');
//                 if (data.success) {
//                     responseDiv.innerHTML = `<p>${data.message}</p>`;
//                     loadUserGroups(); // Reload the groups
//                 } else {
//                     responseDiv.innerHTML = `<p>Error: ${data.error}</p>`;
//                 }
//             })
//             .catch(error => {
//                 console.error('Error:', error);
//                 const responseDiv = document.getElementById('response');
//                 responseDiv.innerHTML = '<p>An error occurred while creating the group.</p>';
//             });
//     });
// });

// function loadUserGroups() {
//     fetch('get-groups.php')
//         .then(response => response.json())
//         .then(groups => {
//             const groupsContainer = document.getElementById('groupsContainer');
//             if (groups.error) {
//                 groupsContainer.innerHTML = `<p>Error: ${groups.error}</p>`;
//             } else if (groups.length > 0) {
//                 const groupsList = groups.map(group => {
//                     const numberOfStudents = [group.StudentID2, group.StudentID3, group.StudentID4, group.StudentID5, group.StudentID6].filter(id => id !== null).length + 1;
//                     const isManager = group.GroupManagerID == userID;
//                     const isGroupFull = numberOfStudents >= 6;
//                     const isOnlyStudent = numberOfStudents === 1;

//                     return `
//                         <div class="groupCard">
//                             <h2>${group.GroupName}</h2>
//                             <p>${group.GroupDescription || 'No description'}</p>
//                             <p><strong>Group ID:</strong> ${group.GroupID}</p>
//                             <p><strong>Manager ID:</strong> ${group.GroupManagerID}</p>
//                             <p><strong>Number of Students:</strong> ${numberOfStudents}/6</p>
//                             ${isManager ? `<p>You are this group's manager</p>` : ''}
//                             <div class="buttonContainer">
//                                 ${isManager && !isGroupFull ? '<button onclick="addPartner(\'' + group.GroupID + '\')">Add Partner</button>' : ''}
//                                 ${isManager && !isOnlyStudent ? '<button onclick="removePartner(\'' + group.GroupID + '\')">Remove Partner</button>' : ''}
//                             </div>
//                         </div>
//                     `;
//                 }).join('');
//                 groupsContainer.innerHTML = groupsList;
//             } else {
//                 groupsContainer.innerHTML = '<p>No groups found.</p>';
//             }
//         })
//         .catch(error => {
//             console.error('Error fetching groups:', error);
//             const groupsContainer = document.getElementById('groupsContainer');
//             groupsContainer.innerHTML = '<p>An error occurred while fetching the groups.</p>';
//         });
// }

// function addPartner(groupID) {
//     const newPartnerID = prompt("Enter the ID of the new partner:");
//     if (newPartnerID) {
//         const formData = new FormData();
//         formData.append('groupID', groupID);
//         formData.append('newPartnerID', newPartnerID);

//         fetch('add-partner.php', {
//             method: 'POST',
//             body: formData
//         })
//             .then(response => response.json())
//             .then(data => {
//                 if (data.success) {
//                     alert(data.message);
//                     loadUserGroups(); // Reload the groups
//                 } else {
//                     alert('Error: ' + data.error);
//                 }
//             })
//             .catch(error => {
//                 console.error('Error:', error);
//                 alert('An error occurred while adding the partner.');
//             });
//     }
// }

// function removePartner(groupID) {
//     const partnerID = prompt("Enter the ID of the partner to remove:");
//     if (partnerID) {
//         const formData = new FormData();
//         formData.append('groupID', groupID);
//         formData.append('partnerID', partnerID);

//         fetch('remove-partner.php', {
//             method: 'POST',
//             body: formData
//         })
//             .then(response => response.json())
//             .then(data => {
//                 if (data.success) {
//                     alert(data.message);
//                     loadUserGroups(); // Reload the groups
//                 } else {
//                     alert('Error: ' + data.error);
//                 }
//             })
//             .catch(error => {
//                 console.error('Error:', error);
//                 alert('An error occurred while removing the partner.');
//             });
//     }
// }

document.addEventListener("DOMContentLoaded", function () {
    loadUserGroups();

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
                    loadUserGroups(); // Reload the groups
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

    // Modal functionality
    const modal = document.getElementById("partnerModal");
    const span = document.getElementsByClassName("close")[0];

    span.onclick = function () {
        modal.style.display = "none";
    }

    window.onclick = function (event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
});

function loadUserGroups() {
    fetch('get-study-groups.php')
        .then(response => response.json())
        .then(groups => {
            const groupsContainer = document.getElementById('groupsContainer');
            if (groups.error) {
                groupsContainer.innerHTML = `<p>Error: ${groups.error}</p>`;
            } else if (groups.length > 0) {
                // const userID = <? php echo json_encode($_SESSION['ID']); ?>;
                console.log(userID);
                const groupsList = groups.map(group => {
                    const numberOfStudents = [group.StudentID2, group.StudentID3, group.StudentID4, group.StudentID5, group.StudentID6].filter(id => id !== null).length + 1;
                    const isManager = group.GroupManagerID == userID;
                    const isGroupFull = numberOfStudents >= 6;
                    const isOnlyStudent = numberOfStudents === 1;

                    return `
                        <div class="groupCard">
                            <h2>${group.GroupName}</h2>
                            <p>${group.GroupDescription || 'No description'}</p>
                            <p><strong>Group ID:</strong> ${group.GroupID}</p>
                            <p><strong>Manager ID:</strong> ${group.GroupManagerID}</p>
                            <p><strong>Number of Students:</strong> ${numberOfStudents}/6</p>
                            ${isManager ? `<p>You are this group's manager</p>` : ''}
                            <div class="buttonContainer">
                                ${isManager && !isGroupFull ? '<button onclick="showPartnerModal(\'' + group.GroupID + '\')">Add Partner</button>' : ''}
                                ${isManager && !isOnlyStudent ? '<button onclick="removePartner(\'' + group.GroupID + '\')">Remove Partner</button>' : ''}
                            </div>
                        </div>
                    `;
                }).join('');
                groupsContainer.innerHTML = groupsList;
            } else {
                groupsContainer.innerHTML = '<p>No groups found.</p>';
            }
        })
        .catch(error => {
            console.error('Error fetching groups:', error);
            const groupsContainer = document.getElementById('groupsContainer');
            groupsContainer.innerHTML = '<p>An error occurred while fetching the groups.</p>';
        });
}

function showPartnerModal(groupID) {
    fetch('get-partners.php')
        .then(response => response.json())
        .then(partners => {
            const partnersListDiv = document.getElementById('partnersList');
            if (partners.error) {
                partnersListDiv.innerHTML = `<p>Error: ${partners.error}</p>`;
            } else if (partners.length > 0) {
                const partnersList = partners.map(partner => `
                    <div>
                        <p>${partner.firstName} ${partner.lastName}</p>
                        <button onclick="addPartnerToGroup('${groupID}', '${partner.ID}')">Add</button>
                    </div>
                `).join('');
                partnersListDiv.innerHTML = partnersList;
            } else {
                partnersListDiv.innerHTML = '<p>No partners found.</p>';
            }

            // Display the modal
            const modal = document.getElementById("partnerModal");
            modal.style.display = "block";
        })
        .catch(error => {
            console.error('Error fetching partners:', error);
            const partnersListDiv = document.getElementById('partnersList');
            partnersListDiv.innerHTML = '<p>An error occurred while fetching the partners.</p>';
        });
}

function addPartnerToGroup(groupID, partnerID) {
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
                const modal = document.getElementById("partnerModal");
                modal.style.display = "none";
                loadUserGroups(); // Reload the groups
            } else {
                alert('Error: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while adding the partner.');
        });
}

function removePartner(groupID) {
    const partnerID = prompt("Enter the ID of the partner to remove:");
    if (partnerID) {
        const formData = new FormData();
        formData.append('groupID', groupID);
        formData.append('partnerID', partnerID);

        fetch('remove-partner.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    loadUserGroups(); // Reload the groups
                } else {
                    alert('Error: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while removing the partner.');
            });
    }
}