<?php include 'session_verify.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Classrooms</title>
    <link rel="stylesheet" href="/css/classrooms.css">
</head>

<body>
    <?php include 'navbar.php'; ?>
    <a href="dashboard.php">Back</a>
    <div class="calendar" id="calendar">
        <div class="header">
            <button onclick="changeMonth(-1)">Previous</button>
            <h2 id="monthYear"></h2>
            <button onclick="changeMonth(1)">Next</button>
        </div>
        <div class="day-names">
            <div>Sun</div>
            <div>Mon</div>
            <div>Tue</div>
            <div>Wed</div>
            <div>Thu</div>
            <div>Fri</div>
            <div>Sat</div>
        </div>
    </div>

    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2 id="selectedDate"></h2>
            <div class="tab">
                <button class="tablinks" onclick="openTab(event, 'setEventTab')">Set an Event</button>
                <button class="tablinks" onclick="openTab(event, 'eventsTab')">Events</button>
            </div>
            <div id="setEventTab" class="tabcontent">
                <div id="classroomContainer">
                    <ul id="classroomList" class="classroom-list"></ul>
                </div>
            </div>
            <div id="eventsTab" class="tabcontent">
                <ul id="eventsList" class="classroom-list"></ul>
            </div>
        </div>
    </div>

    <div id="setEventModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeSetEventModal()">&times;</span>
            <h2>Set Event Details</h2>
            <p>Selected Date: <span id="selectedEventDate"></span></p>
            <p>Selected Classroom: <span id="selectedClassroom"></span></p>
            <label for="groupName">Study Group Name:</label>
            <select id="groupNameSelect"></select><br><br>
            <label for="groupSize">Number of People:</label>
            <select id="groupSize">
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
                <option value="6">6</option>
            </select><br><br>
            <button onclick="setEvent()">Set</button>
        </div>
    </div>
    <script src="/javascript/classrooms.js"></script>
</body>

</html>