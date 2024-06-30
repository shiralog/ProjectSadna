<?php include 'session_verify.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Classrooms</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/navbar.css">
    <link rel="stylesheet" href="/css/classrooms.css">
</head>

<body>
    <?php include 'navbar.php'; ?>

    <div class="container">
        <div id="showMyEventsButton">
            <button class="btn" onclick="showMyEvents()">My Events</button>
        </div>
        <div>
            <p id="notification"></p>
        </div>
        <div class="calendar" id="calendar">
            <div class="header">
                <button class="btn" onclick="changeMonth(-1)">Previous</button>
                <h2 id="monthYear"></h2>
                <button class="btn" onclick="changeMonth(1)">Next</button>
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
                    <h4>Please choose a classroom from the list:</h4>
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
            <p><strong>Selected Date:</strong> <span id="selectedEventDate"></span></p>
            <p><strong>Hours:</strong> <span id="selectedClassroomHours"></span></p>
            <p><strong>Selected Classroom:</strong> <span id="selectedClassroom"></span></p>
            <label for="groupName"><strong>Study Group Name:</strong></label>
            <select id="groupNameSelect"></select><br><br>
            <label for="groupSize"><strong>Number of People:</strong></label>
            <select id="groupSize">
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
                <option value="6">6</option>
            </select><br><br>
            <button class="btn" onclick="setEvent()">Set</button>
        </div>
    </div>

    <div id="shareEventOptionsModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeShareEventOptionsModal()">&times;</span>
            <h2>Share Event</h2>
            <button class="btn" id="shareForMyselfBtn">For myself</button>
            <button class="btn" id="shareForGroupBtn">Myself and all group members</button>
        </div>
    </div>

    <div id="userEventsModal" class="modal" style="display: none;">
        <div class="modal-content">
            <span class="close" onclick="closeMyEventsModal()">&times;</span>
            <div id="userEventsContent">
                <!-- Events will be dynamically added here -->
            </div>
        </div>
    </div>


    <script>
        let isConnectedToOutlook = <?php echo isset($_SESSION['access_token']) ? 'true' : 'false'; ?>;
        let sessionToken = "<?php echo isset($_SESSION['access_token']) ? $_SESSION['access_token'] : ""; ?>";
        let userID = <?php echo $_SESSION['ID'] ?>;
    </script>

    <!-- Include client_id from CalendarAPISecrets.php -->
    <?php require_once 'CalendarAPISecrets.php';?>
    <script>
        let client_id = "<?php echo client_id; ?>";
    </script>

    <!-- Include your main JavaScript file -->
    <script src="/javascript/classrooms.js"></script>

</body>

</html>