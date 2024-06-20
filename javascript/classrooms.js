let currentMonth = new Date().getMonth();
let currentYear = new Date().getFullYear();
const monthNames = ["January", "February", "March", "April", "May", "June",
    "July", "August", "September", "October", "November", "December"];

const classrooms = [
    {
        "classroom": "101",
        "working_hours": "8:00 AM - 5:00 PM"
    },
    {
        "classroom": "102",
        "working_hours": "8:30 AM - 5:30 PM"
    },
    {
        "classroom": "103",
        "working_hours": "9:00 AM - 6:00 PM"
    },
    {
        "classroom": "104",
        "working_hours": "8:00 AM - 4:00 PM"
    },
    {
        "classroom": "105",
        "working_hours": "8:00 AM - 5:00 PM"
    },
    {
        "classroom": "106",
        "working_hours": "9:00 AM - 6:00 PM"
    },
    {
        "classroom": "107",
        "working_hours": "8:00 AM - 3:00 PM"
    },
    {
        "classroom": "108",
        "working_hours": "10:00 AM - 7:00 PM"
    },
    {
        "classroom": "109",
        "working_hours": "8:00 AM - 5:00 PM"
    },
    {
        "classroom": "110",
        "working_hours": "8:30 AM - 4:30 PM"
    },
    {
        "classroom": "111",
        "working_hours": "9:00 AM - 6:00 PM"
    },
    {
        "classroom": "112",
        "working_hours": "8:00 AM - 4:00 PM"
    },
    {
        "classroom": "113",
        "working_hours": "8:00 AM - 5:00 PM"
    },
    {
        "classroom": "114",
        "working_hours": "9:30 AM - 5:30 PM"
    },
    {
        "classroom": "115",
        "working_hours": "8:00 AM - 3:00 PM"
    },
    {
        "classroom": "116",
        "working_hours": "8:00 AM - 4:00 PM"
    },
    {
        "classroom": "117",
        "working_hours": "9:00 AM - 5:00 PM"
    },
    {
        "classroom": "118",
        "working_hours": "8:00 AM - 6:00 PM"
    },
    {
        "classroom": "119",
        "working_hours": "8:30 AM - 4:30 PM"
    },
    {
        "classroom": "120",
        "working_hours": "8:00 AM - 5:00 PM"
    }
];

function renderCalendar(month, year) {
    const calendar = document.getElementById('calendar');
    const monthYear = document.getElementById('monthYear');
    monthYear.textContent = `${monthNames[month]} ${year}`;

    const firstDay = new Date(year, month).getDay();
    const daysInMonth = 32 - new Date(year, month, 32).getDate();

    // Clear previous cells
    const days = calendar.querySelectorAll('.day');
    days.forEach(day => day.remove());

    // Create blank cells for days before the first day of the month
    for (let i = 0; i < firstDay; i++) {
        const cell = document.createElement('div');
        cell.classList.add('day');
        calendar.appendChild(cell);
    }

    // Create cells for each day of the month
    for (let i = 1; i <= daysInMonth; i++) {
        const cell = document.createElement('div');
        cell.classList.add('day');
        const dateKey = `${month}-${i}-${year}`;
        if (classrooms.length > 0) {
            cell.classList.add('occupied');
        }
        cell.textContent = i;
        cell.onclick = function () {
            showModal(month, i, year);
        };
        calendar.appendChild(cell);
    }
}

function changeMonth(delta) {
    currentMonth += delta;
    if (currentMonth < 0) {
        currentMonth = 11;
        currentYear--;
    } else if (currentMonth > 11) {
        currentMonth = 0;
        currentYear++;
    }
    renderCalendar(currentMonth, currentYear);
}

let occupiedClassrooms = [];

function showModal(month, day, year) {
    const modal = document.getElementById('myModal');
    const selectedDate = document.getElementById('selectedDate');
    const setEventTab = document.getElementById('setEventTab');
    const eventsTab = document.getElementById('eventsTab');
    const tablinks = document.getElementsByClassName("tablinks");

    // Format the date as YYYY-MM-DD
    const formattedDate = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
    selectedDate.textContent = formattedDate;

    // Display Set an Event tab by default
    openTab(event, 'setEventTab');

    // Clear previous list items
    const classroomList = document.getElementById('classroomList');
    classroomList.innerHTML = '';

    // Fetch and display events for the selected date
    fetchEvents(formattedDate).then(() => {
        // Filter out occupied classrooms
        const availableClassrooms = classrooms.filter(classroomElement => !occupiedClassrooms.includes(classroomElement.classroom));
        console.log(availableClassrooms);
        availableClassrooms.forEach(classroom => {
            const listItem = document.createElement('li');
            listItem.textContent = `${classroom.classroom} (${classroom.working_hours})`;
            listItem.onclick = function () {
                selectClassroom(classroom.classroom, formattedDate, classroom.working_hours);
            };
            classroomList.appendChild(listItem);
        });
    });

    modal.style.display = 'block';
}

function convertToDate(dateStr) {
    const date = new Date(dateStr);
    const year = date.getFullYear();
    const month = ('0' + (date.getMonth() + 1)).slice(-2); // Months are zero-based
    const day = ('0' + date.getDate()).slice(-2);
    return `${year}-${month}-${day}`;
}

function openTab(evt, tabName) {
    const tabcontent = document.getElementsByClassName("tabcontent");
    for (let i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }
    const tablinks = document.getElementsByClassName("tablinks");
    for (let i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }
    document.getElementById(tabName).style.display = "block";
    evt.currentTarget.className += " active";

    if (tabName === 'eventsTab') {
        const selectedDate = document.getElementById('selectedDate').textContent;
        console.log("what" + selectedDate);
        // Convert date to "YYYY-MM-DD" format for fetching events
        const formattedDate = convertToDate(selectedDate);
        fetchEvents(formattedDate);
    }
}

function selectClassroom(classroom, date, workingHours) {
    closeModal();
    openSetEventModal(classroom, date, workingHours);
}

function openSetEventModal(classroom, date, workingHours) {
    const setEventModal = document.getElementById('setEventModal');
    setEventModal.style.display = 'block';
    // Set the selected date and classroom in the set event modal
    document.getElementById('selectedEventDate').textContent = date;
    document.getElementById('selectedClassroom').textContent = classroom;
    document.getElementById('selectedClassroomHours').textContent = workingHours;

    // Fetch group names from backend (PHP script)
    fetchGroupNames().then(groupNames => {
        const groupNameSelect = document.getElementById('groupNameSelect');
        groupNameSelect.innerHTML = '';
        groupNames.forEach(group => {
            const option = document.createElement('option');
            option.textContent = group.GroupName + " " + group.GroupID;
            groupNameSelect.appendChild(option);
        });
    }).catch(error => {
        console.error('Error fetching group names:', error);
        // Optionally handle errors here
    });

    // Populate the number of people dropdown
    const groupSizeSelect = document.getElementById('groupSize');
    groupSizeSelect.value = '1'; // Set default value
}

async function fetchGroupNames() {
    try {
        const response = await fetch('get-group-names.php');
        if (!response.ok) {
            throw new Error('Failed to fetch group names');
        }
        const groupNames = await response.json();
        return groupNames;
    } catch (error) {
        throw error;
    }
}

function closeSetEventModal() {
    const setEventModal = document.getElementById('setEventModal');
    setEventModal.style.display = 'none';
}

function setEvent() {
    const groupNameSelect = document.getElementById('groupNameSelect');
    const groupName = groupNameSelect.options[groupNameSelect.selectedIndex].text;
    const groupSizeSelect = document.getElementById('groupSize');
    const groupSize = groupSizeSelect.options[groupSizeSelect.selectedIndex].value;
    const selectedDate = document.getElementById('selectedDate').textContent;
    const selectedClassroom = document.getElementById('selectedClassroom').textContent;
    const selectedClassroomHours = document.getElementById('selectedClassroomHours').textContent;

    // Prepare data to send to PHP
    const formData = new FormData();
    formData.append('groupID', groupName.split(" ").pop());
    const lastSpaceIndex = groupName.lastIndexOf(' ');
    formData.append('groupName', groupName.substring(0, lastSpaceIndex));
    formData.append('groupSize', groupSize);
    formData.append('selectedDate', selectedDate);
    formData.append('selectedClassroom', selectedClassroom);
    formData.append('selectedClassroomHours', selectedClassroomHours);

    // Send AJAX request to insert-event.php
    fetch('insert-event.php', {
        method: 'POST',
        body: formData
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to insert event');
            }
            return response.text();
        })
        .then(data => {
            console.log(data); // Log success message (optional)
            // Optionally, you can update the UI or perform any other actions on success
            closeSetEventModal(); // Close the modal after successful insertion
        })
        .catch(error => {
            console.error('Error inserting event:', error);
            // Optionally handle errors here (e.g., show an error message to the user)
        });
}

function closeModal() {
    const modal = document.getElementById('myModal');
    modal.style.display = 'none';
}

window.onclick = function (event) {
    const modal = document.getElementById('myModal');
    if (event.target == modal) {
        modal.style.display = 'none';
    }
}

async function fetchEvents(date) {
    console.log("Fetching events for: " + date);
    try {
        const responseEvents = await fetch(`get-classroom-events.php?date=${encodeURIComponent(date)}`);
        const dataEvents = await responseEvents.json();
        console.log("Fetched events data:");

        const groupsToCheck = [];
        occupiedClassrooms = dataEvents.map(event => {
            groupsToCheck.push(event.StudyGroupID);
            return event.ClassroomID.toString();
        });

        const responseGroups = await fetch('check-if-user-is-in-groups.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ group_ids: groupsToCheck })
        });
        const dataGroups = await responseGroups.json();
        console.log("Groups check result:");
        console.log(dataGroups);

        const eventsList = document.getElementById('eventsList');
        eventsList.innerHTML = '';  // Clear previous events

        if (dataEvents.length === 0) {
            const noEventsMessage = document.createElement('li');
            noEventsMessage.textContent = "No events on this day";
            eventsList.appendChild(noEventsMessage);
        } else {
            dataEvents.forEach(event => {
                const eventItem = document.createElement('li');
                console.log("Event object:");
                console.log(event);

                if (dataGroups[event.StudyGroupID]) {
                    eventItem.innerHTML = `
                Group ID: ${event.StudyGroupID}, 
                Group Name: ${event.StudyGroupName}, 
                Classroom: ${event.ClassroomID}, 
                Students: ${event.NumberOfStudents} 
                <button onClick="changeNumOfStudents(${event.EventID})">Edit number of students</button> 
                <button onClick="removeEvent(${event.EventID},${event.ClassroomID})">Delete</button>`;
                } else {
                    eventItem.innerHTML = `
                Group ID: ${event.StudyGroupID}, 
                Group Name: ${event.StudyGroupName}, 
                Classroom: ${event.ClassroomID}, 
                Students: ${event.NumberOfStudents}`;
                }
                eventsList.appendChild(eventItem);
            });
        }
    } catch (error) {
        console.error('Error fetching events:', error);
    }
}

function changeNumOfStudents(eventID) {
    console.log("changeNumOfStudents called with EventID:", eventID); // Log EventID

    // Function to validate input and prompt until valid input is provided
    function promptForNumberOfStudents() {
        let input = prompt("Enter the new number of students (between 1 and 6):");
        if (input === null) {
            return null; // User clicked Cancel
        }
        input = parseInt(input);
        if (isNaN(input) || input < 1 || input > 6) {
            alert("Please enter a number between 1 and 6.");
            return promptForNumberOfStudents(); // Prompt again if input is invalid
        }
        return input;
    }

    const newNumberOfStudents = promptForNumberOfStudents();

    if (newNumberOfStudents !== null) {
        fetch('change-num-of-students-event.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ event_id: eventID, new_number_of_students: newNumberOfStudents })
        })
            .then(response => response.text())
            .then(data => {
                if (data === 'success') {
                    alert('Number of students updated successfully.');
                    if (document.getElementById('selectedDate').textContent === '') {
                        console.log("my events", document.getElementById('selectedDateMyEvents').textContent);
                        fetchEvents(document.getElementById('selectedDateMyEvents').textContent);
                        showMyEvents();
                    } else {
                        console.log("not my events", document.getElementById('selectedDate').textContent);
                        fetchEvents(document.getElementById('selectedDate').textContent);
                    }
                } else {
                    alert('Error updating number of students.');
                }
            })
            .catch(error => console.error('Error:', error));
    }
}

function removeEvent(eventID, classroomID) {
    console.log("removeEvent called with EventID:", eventID);  // Log EventID
    if (confirm("Are you sure you want to delete this event?")) {
        fetch('remove-event.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ event_id: eventID })
        })
            .then(response => response.text())
            .then(data => {
                if (data === 'success') {
                    alert('Event removed successfully.');
                    fetchEvents(document.getElementById('selectedDate').textContent);
                } else {
                    alert('Error removing event.');
                }
            }).then(() => {
                // Filter out occupied classrooms
                const classroomList = document.getElementById('classroomList');
                classroomList.innerHTML = '';
                const updatedOccupiedClassrooms = occupiedClassrooms.filter(classroom => classroom !== classroomID.toString())
                const availableClassrooms = classrooms.filter(classroomElement => !updatedOccupiedClassrooms.includes(classroomElement.classroom));
                availableClassrooms.forEach(classroom => {
                    const listItem = document.createElement('li');
                    listItem.textContent = `${classroom.classroom} (${classroom.working_hours})`;
                    listItem.onclick = function () {
                        selectClassroom(classroom.classroom, formattedDate, classroom.working_hours);
                    };
                    classroomList.appendChild(listItem);
                });
            })

            .catch(error => console.error('Error:', error));
    }
}

function showMyEvents() {
    // Fetch group IDs for the logged-in user
    fetchGroupIDs().then(groupIDs => {
        const formData = new FormData();
        formData.append('group_ids', JSON.stringify(groupIDs)); // Convert array to JSON string

        // Send AJAX request to fetch user events
        fetch('get-user-events.php', {
            method: 'POST',
            body: formData
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Failed to fetch user events');
                }
                return response.json();
            })
            .then(data => {
                console.log('Fetched events:', data);
                displayUserEvents(data, isConnectedToOutlook);
                // Process the events data as needed
            })
            .catch(error => {
                console.error('Error fetching events:', error);
                // Handle error scenarios
            });
    }).catch(error => {
        console.error('Error fetching group IDs:', error);
        // Handle error scenarios
    });
}

async function fetchGroupIDs() {
    try {
        const response = await fetch('get-study-groups.php');
        if (!response.ok) {
            throw new Error('Failed to fetch group IDs');
        }
        const groupIDs = await response.json();
        return groupIDs.map(group => group.GroupID);
    } catch (error) {
        throw error;
    }
}

function displayUserEvents(events, isConnectedToOutlook) {
    const userEventsModal = document.getElementById('userEventsModal');
    const modalContent = userEventsModal.querySelector('.modal-content');

    // Clear existing content
    modalContent.innerHTML = '';

    // Close button
    const closeBtn = document.createElement('span');
    closeBtn.classList.add('close');
    closeBtn.onclick = () => {
        userEventsModal.style.display = 'none';
        // Optionally, remove modal from DOM
        // userEventsModal.remove();
    };
    closeBtn.textContent = '×';
    modalContent.appendChild(closeBtn);

    // Outlook connection status
    const outlookStatus = document.createElement('div');
    outlookStatus.textContent = 'Outlook connected: ';
    const outlookIcon = document.createElement('span');
    outlookIcon.textContent = isConnectedToOutlook ? '✔️' : '❌';
    outlookIcon.classList.add('outlook-status');
    outlookStatus.appendChild(outlookIcon);
    modalContent.appendChild(outlookStatus);

    // Connect to Outlook button
    const connectToOutlookBtn = document.createElement('button');
    connectToOutlookBtn.id = 'connect-outlook-btn';
    connectToOutlookBtn.textContent = isConnectedToOutlook ? 'Connected' : 'Connect to Outlook';
    if (!isConnectedToOutlook) {
        connectToOutlookBtn.onclick = () => {
            // Initiate Outlook OAuth authentication
            window.location.href = 'https://login.microsoftonline.com/common/oauth2/v2.0/authorize' +
                `?client_id=${client_id}` + // Replace with your client ID
                '&response_type=code' +
                '&redirect_uri=http://localhost:3000/pages/callback.php' + // Replace with your redirect URI
                '&scope=Calendars.ReadWrite' + // Requesting calendar permissions
                '&state=12345'; // Optional: Secure state parameter to prevent CSRF
        };
    } else {
        connectToOutlookBtn.disabled = true;
    }
    modalContent.appendChild(connectToOutlookBtn);

    // Header for events
    const header = document.createElement('h2');
    header.textContent = 'Your Events';
    modalContent.appendChild(header);

    // List of events
    const eventsList = document.createElement('ul');
    eventsList.classList.add('events-list');
    if (events.length !== 0) {
        events.forEach(event => {
            console.log(event);
            const listItem = document.createElement('li');
            const eventInfo = document.createElement('div');
            eventInfo.classList.add('event-info');

            // Formatting the event details
            eventInfo.innerHTML = `
            <p><strong>Group Name:</strong> ${event.StudyGroupName}</p>
            <p><strong>Classroom:</strong> ${event.ClassroomID}</p>
            <p><strong>Number of Students:</strong> ${event.NumberOfStudents}</p>
            <p id='selectedDateMyEvents'><strong>Date:</strong> ${event.Date}</p>
            <p><strong>Hours:</strong> ${event.Hours}</p>
            <button onClick="changeNumOfStudents(${event.EventID})">Edit number of students</button> 
            <button onClick="removeEvent(${event.EventID},${event.ClassroomID})">Delete</button>
        `;

            // Share event button (enabled if connected to Outlook)
            const shareEventBtn = document.createElement('button');
            shareEventBtn.classList.add('share-event');
            shareEventBtn.textContent = isConnectedToOutlook ? 'Share event' : 'Outlook not connected';
            shareEventBtn.disabled = !isConnectedToOutlook;
            shareEventBtn.style.cursor = isConnectedToOutlook ? 'pointer' : 'not-allowed';

            shareEventBtn.onclick = async () => {
                try {
                    // Check if Outlook is connected
                    if (!isConnectedToOutlook) {
                        alert('Outlook is not connected.');
                        return;
                    }

                    const modal = document.getElementById('shareEventOptionsModal');
                    userEventsModal.style.display = 'none';
                    modal.style.display = 'block';
                    const shareForMyselfBtn = document.getElementById('shareForMyselfBtn');
                    const shareForGroupBtn = document.getElementById('shareForGroupBtn');

                    shareForMyselfBtn.onclick = () => shareEvent(event, false);
                    shareForGroupBtn.onclick = () => shareEvent(event, true);

                } catch (error) {
                    console.error('Error sharing event:', error);
                    alert('Failed to share event. Please try again later.');
                }
            };

            eventInfo.appendChild(shareEventBtn);
            listItem.appendChild(eventInfo);
            eventsList.appendChild(listItem);
        });

        modalContent.appendChild(eventsList);
    } else {
        header.style.display = 'none';
        outlookIcon.style.display = 'none';
        outlookStatus.style.display = 'none';
        connectToOutlookBtn.style.display = 'none';
        const message = document.createElement('h2')
        message.textContent = "No events.. 🙁"
        modalContent.appendChild(message);
    }

    // Display the modal
    userEventsModal.style.display = 'block';
}


async function shareEvent(event, shareWithGroup) {
    try {
        // Assuming Hours variable is available and holds a string like "8:00 AM - 5:00 PM"
        const hours = event.Hours; // Add this line to access the Hours variable for each event
        const [startTime, endTime] = hours.split(' - ');

        // Function to format date and time to ISO string
        const formatDateTime = (date, time) => {
            const [hour, minute] = time.split(/[: ]/);
            const period = time.split(' ')[1];
            let hour24 = parseInt(hour);
            if (period === 'PM' && hour24 < 12) hour24 += 12;
            if (period === 'AM' && hour24 === 12) hour24 = 0;
            const dateTime = new Date(date);
            dateTime.setHours(hour24);
            dateTime.setMinutes(parseInt(minute));
            return dateTime.toISOString();
        };

        // Construct event data for Microsoft Graph API
        const eventData = {
            subject: `Group Name: ${event.StudyGroupName}`,
            start: {
                dateTime: formatDateTime(event.Date, startTime), // Format start dateTime
                timeZone: 'UTC' // Update timezone as necessary
            },
            end: {
                dateTime: formatDateTime(event.Date, endTime), // Format end dateTime
                timeZone: 'UTC' // Update timezone as necessary
            },
            location: {
                displayName: `Classroom: ${event.ClassroomID}`
            }
        };

        let membersIDs = [userID];
        if (shareWithGroup) {
            try {
                const response = await fetch(`get-group-members.php?groupID=${event.StudyGroupID}`);
                const groupMembers = await response.json();

                membersIDs = groupMembers.map(groupMember => groupMember.ID);
                const membersIDWithoutLoggedInUser = groupMembers.filter(member => member.ID !== userID).map(groupMember => groupMember.ID);

                const formData = new URLSearchParams();
                formData.append('ids', JSON.stringify(membersIDWithoutLoggedInUser));

                const responseEmails = await fetch('get-emails.php', { method: 'POST', body: formData });
                const emails = await responseEmails.json();
                console.log(emails);

                eventData.attendees = emails.map(email => ({ emailAddress: { address: email }, type: 'required' }));
            } catch (error) {
                console.log('error: ', error);
            }
        }

        const formDataCheck = new URLSearchParams();
        formDataCheck.append('userId', userID);
        formDataCheck.append('eventId', event.EventID);

        // Check if the event is already shared
        const checkResponse = await fetch('check-event-shared.php', {
            method: 'POST',
            body: formDataCheck
        });

        const checkResult = await checkResponse.json();
        console.log(checkResult);

        if (checkResult.shared) {
            alert('You have shared this event already');
            return;
        }

        // Call Microsoft Graph API to create event
        const response = await fetch('https://graph.microsoft.com/v1.0/me/events', {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${sessionToken}`, // Use the stored access token
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(eventData)
        });

        if (!response.ok) {
            const errorResponse = await response.json();
            // Check for invalid token error and prompt re-authentication
            if (errorResponse.error && errorResponse.error.code === 'InvalidAuthenticationToken') {
                alert('Your session has expired. Please authenticate with Microsoft again.');
                // Redirect to Microsoft OAuth authentication
                window.location.href = 'https://login.microsoftonline.com/common/oauth2/v2.0/authorize' +
                    `?client_id=${client_id}` + // Replace with your client ID
                    '&response_type=code' +
                    '&redirect_uri=http://localhost:3000/pages/callback.php' + // Replace with your redirect URI
                    '&scope=Calendars.ReadWrite' + // Requesting calendar permissions
                    '&state=12345'; // Optional: Forces consent prompt every time
                return;
            }
            throw new Error(`Failed to share event: ${response.status} - ${response.statusText}`);
        }

        const formDataInsert = new URLSearchParams();
        formDataInsert.append('userIdList', JSON.stringify(membersIDs));
        formDataInsert.append('eventId', event.EventID);

        // Insert event into EventsInCalendar table
        const responseInsert = await fetch('insert-event-in-calendar.php', {
            method: 'POST',
            body: formDataInsert
        });

        if (!responseInsert.ok) {
            throw new Error(`Failed to insert membersIDs into EventsInCalendar table: ${response.status} - ${response.statusText}`);
        }

        const result = await response.json();
        console.log('Insert membersIDs into EventsInCalendar table:', result);

        alert('Event shared successfully with Outlook calendar!');
    } catch (error) {
        console.error('Error sharing event:', error);
        alert('Failed to share event. Please try again later.');
    } finally {
        closeShareEventOptionsModal();
    }
};

const closeShareEventOptionsModal = () => {
    const modal = document.getElementById('shareEventOptionsModal');
    modal.style.display = 'none';
};

// Close modal on outside click
window.onclick = function (event) {
    const userEventsModal = document.getElementById('userEventsModal');
    const myModal = document.getElementById('myModal');

    if (event.target == userEventsModal) {
        userEventsModal.style.display = 'none';
    }

    if (event.target == myModal) {
        myModal.style.display = 'none';
    }
};


renderCalendar(currentMonth, currentYear);