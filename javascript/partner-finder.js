let students = []; // Global array to hold student data
let currentIndex = 0; // Global variable to track current index
let loggedUserID;

function fetchLoggedInLikes(likesData) {
    console.log(likesData);
    students.forEach(student => {
        // student['status'] = likesData[student['id']];
        student['status'] = likesData[student['id']] == undefined ? '' : likesData[student['id']];
    });
}

// fetch student data from database
function fetchStudentData() {
    fetch('get-students.php')
        .then(response => response.json())
        .then(data => {
            console.log(data);
            loggedUserID = data['loggedInID'].toString();
            students = data['students'].filter(student => student['id'] !== loggedUserID);; // Store fetched data in the global array
            fetchLoggedInLikes(data['likes']);
            console.log(students);
            displayStudent();
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

// Function to display student information on cards
function displayStudent() {
    const studentContainer = document.getElementById('studentContainer');
    studentContainer.innerHTML = ''; // Clear previous content
    const student = students[currentIndex];
    const card = document.createElement('div');
    card.classList.add('card');
    card.innerHTML = `
        <p>${currentIndex + 1}/${students.length}</p>
        <h3>${student.firstName + " " + student.lastName}</h3>
        <p>Gender: ${student.gender}</p>
        <p>Age: ${student.age}</p>
        <p>Faculty: ${student.faculty}</p>
        <p>Region: ${student.region}</p>
        <p>Partner Type: ${student.partnerType}</p>
        <p>Profile Image Path: ${student.profileImagePath}</p>
        <p id='status'> ${student.status} </p>
        <button onclick="previousStudent()">Previous</button>
        <button onclick="nextStudent()">Next</button>
    `;
    if (student.status == '') {
        card.innerHTML += `<button id='likeBtn' onclick="handleStatus('Like', ${student.id})">Like</button>
        <button id='dislikeBtn' onclick="handleStatus('Dislike', ${student.id})">Dislike</button>`
    }

    studentContainer.appendChild(card);
}

function handleStatus(newStatus, partnerID) {
    const status = document.getElementById('status');
    const likeBtn = document.getElementById('likeBtn');
    const dislikeBtn = document.getElementById('dislikeBtn');
    status.textContent = newStatus;
    students[currentIndex]['status'] = newStatus;
    dislikeBtn.style.display = 'none';
    likeBtn.style.display = 'none';
    updateStatus(loggedUserID, partnerID, newStatus);
}

// Function to move to the previous student
function previousStudent() {
    currentIndex = (currentIndex - 1 + students.length) % students.length; // Decrement index and wrap around if necessary
    displayStudent();
}

// Function to move to the next student
function nextStudent() {
    currentIndex = (currentIndex + 1) % students.length; // Increment index and wrap around if necessary
    displayStudent();
}

// Fetch student data when the page loads
fetchStudentData();
