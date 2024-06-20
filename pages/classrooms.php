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
    <button onclick="showMyEvents()">My Events</button>
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
            <p>Hours: <span id="selectedClassroomHours"></span></p>
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

    <!-- <div id="myEventsModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeMyEventsModal()">&times;</span>
            <button id="connectToOutlookBtn">Connect to Outlook</button>
            <h2>My Events</h2>
            <ul id="myEventsList" class="classroom-list"></ul>
        </div>
    </div> -->

    <div id="userEventsModal" class="modal" style="display: none;">
        <div class="modal-content">
            <span class="close" onclick="closeMyEventsModal()">&times;</span>
            <div id="userEventsContent">
                <!-- Events will be dynamically added here -->
            </div>
        </div>
    </div>


    <!-- <script>
        let isConnectedToOutlook = false;
        let sessionToken = '';
        document.addEventListener('DOMContentLoaded', () => {
            const isConnectedToOutlook = <?php echo isset($_SESSION['access_token']) ? 'true' : 'false'; ?>;
            if(isConnectedToOutlook){
                sessionToken = <?php echo isset($_SESSION['access_token']) ?>;
            }
            // const connectBtn = document.getElementById('connect-outlook-btn');

            // if (isConnectedToOutlook) {
            //     connectBtn.textContent = 'Connected to Outlook ✔️';
            //     connectBtn.disabled = true;
            //     const shareEventButtons = document.querySelectorAll('.share-event');
            //     shareEventButtons.forEach(button => {
            //         button.disabled = false;
            //         button.style.cursor = 'pointer';
            //         button.textContent = 'Share event';
            //     });
            // } else {
            //     connectBtn.textContent = 'Connect to Outlook';
            //     connectBtn.disabled = false;
            // }
        });
    </script>
    <script>
        const client_id = 
    </script>
    <script src="/javascript/classrooms.js"></script> -->

    <script>
        let isConnectedToOutlook = <?php echo isset($_SESSION['access_token']) ? 'true' : 'false'; ?>;
        let sessionToken = "<?php echo isset($_SESSION['access_token']) ? $_SESSION['access_token'] : ""; ?>";
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