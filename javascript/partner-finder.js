let students = []; // Global array to hold student data
let filteredStudents = []; // Global array to hold filtered student data
let currentIndex = 0; // Global variable to track current index
let loggedUserID;

function fetchLoggedInLikes(likesData) {
    students.forEach(student => {
        student['status'] = likesData[student['id']] == undefined ? '' : likesData[student['id']];
    });
}

// Fetch student data from database
function fetchStudentData() {
    fetch('get-students.php')
        .then(response => response.json())
        .then(data => {
            loggedUserID = data['loggedInID'].toString();
            students = data['students'].filter(student => student['id'] !== loggedUserID);
            fetchLoggedInLikes(data['likes']);
            applyFilters();
        })
        .catch(error => console.error('Error fetching student data:', error));
}

function updateStatus(from, to, status) {
    const formData = new FormData();
    formData.append('from', from);
    formData.append('to', to);
    formData.append('status', status);

    fetch('update_status.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            console.log(data.status); // Log the response status ("OK" or "NOT OK")
        })
        .catch(error => console.error('Error updating student status:', error));

}

// Function to apply filters
function applyFilters() {
    const ageRange = document.getElementById('ageRange').value;
    const region = document.getElementById('region').value;
    const faculty = document.getElementById('faculty').value;
    const gender = document.getElementById('gender').value;
    const partnerType = document.getElementById('partnerType').value;

    let ageMin;
    let ageMax;

    switch (ageRange) {
        case '18-25':
            ageMin = 18;
            ageMax = 25;
            break;
        case '25-30':
            ageMin = 25;
            ageMax = 30;
            break;
        case '30-35':
            ageMin = 30;
            ageMax = 35;
            break;

        case 'other':
            ageMin = 36;
            ageMax = 120;
            break;
        default:
            ageMin = 0;
            ageMax = 120;
    }

    filteredStudents = students.filter(student => {
        return (
            (!ageMin || student.age >= ageMin) &&
            (!ageMax || student.age <= ageMax) &&
            (!region || student.region === region) &&
            (!faculty || student.faculty === faculty) &&
            (!gender || student.gender === gender) &&
            (!partnerType || student.partnerType === partnerType)
        );
    });

    currentIndex = 0; // Reset index to show the first filtered student
    displayStudent();
}

// Function to reset filters and show all students
function resetFilters() {
    document.getElementById('ageRange').value = '';
    document.getElementById('region').value = '';
    document.getElementById('faculty').value = '';
    document.getElementById('gender').value = '';
    document.getElementById('partnerType').value = '';

    filteredStudents = students; // Reset filtered students to all students
    currentIndex = 0; // Reset index
    displayStudent();
}

// Function to display student information on cards
function displayStudent() {
    const studentContainer = document.getElementById('studentContainer');
    studentContainer.innerHTML = ''; // Clear previous content

    if (filteredStudents.length === 0) {
        studentContainer.innerHTML = '<p>No students found with the selected filters.</p>';
        return;
    }

    const student = filteredStudents[currentIndex];

    let studentPartnerType;
    switch (student.partnerType) {
        case 'Study_Tests':
            studentPartnerType = "to study for tests";
            break;
        case 'Silent_Study':
            studentPartnerType = "for a silent study";
            break;
        case 'Submit_Chores':
            studentPartnerType = "to submit chores";
            break;
    }

    const card = document.createElement('div');
    card.classList.add('card');
    card.innerHTML = `
        <p>${currentIndex + 1}/${filteredStudents.length}</p>
        <h3>${student.firstName + " " + student.lastName}</h3>
        <p>Gender: ${student.gender}</p>
        <p>Age: ${student.age}</p>
        <p>Faculty: ${student.faculty}</p>
        <p>Region: ${student.region}</p>
        <p>I'm looking ${studentPartnerType}</p>
        <p>Profile Image Path: ${student.profileImagePath}</p>
        <p id='status'> ${student.status} </p>
        <button onclick="previousStudent()">Previous</button>
        <button onclick="nextStudent()">Next</button>
    `;

    if (student.status == '') {
        card.innerHTML += `<button id='likeBtn' onclick="handleStatus('Like', ${student.id})">Like</button>
        <button id='dislikeBtn' onclick="handleStatus('Dislike', ${student.id})">Dislike</button>`;
    }

    studentContainer.appendChild(card);
}

function handleStatus(newStatus, partnerID) {
    const status = document.getElementById('status');
    const likeBtn = document.getElementById('likeBtn');
    const dislikeBtn = document.getElementById('dislikeBtn');
    status.textContent = newStatus;
    filteredStudents[currentIndex]['status'] = newStatus;
    dislikeBtn.style.display = 'none';
    likeBtn.style.display = 'none';
    updateStatus(loggedUserID, partnerID, newStatus);
}

// Function to move to the previous student
function previousStudent() {
    currentIndex = (currentIndex - 1 + filteredStudents.length) % filteredStudents.length; // Decrement index and wrap around if necessary
    displayStudent();
}

// Function to move to the next student
function nextStudent() {
    currentIndex = (currentIndex + 1) % filteredStudents.length; // Increment index and wrap around if necessary
    displayStudent();
}

// Fetch student data when the page loads
fetchStudentData();
