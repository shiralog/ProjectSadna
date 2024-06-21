<?php include 'session_verify.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports</title>
    <link rel="stylesheet" href="/css/reports.css">
</head>

<body>
    <?php include 'navbar.php'; ?>
    <a href="dashboard.php">Back</a>
    <h1>Report an Issue</h1>
    <form id="reportForm">
        <label for="fullName">Full Name:</label>
        <input type="text" id="fullName" name="fullName" required><br>
        <label for="issueTopic">Issue Topic:</label>
        <input type="text" id="issueTopic" name="issueTopic" required><br>
        <label for="issueContent">Issue Content:</label>
        <textarea id="issueContent" name="issueContent" rows="4" required></textarea><br>
        <button type="submit">Submit Issue</button>
    </form>

    <hr>

    <h2>My Reports</h2>
    <table id="myReportsTable">
        <thead>
            <tr>
                <th>Ticket ID</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <!-- Rows will be inserted here by JavaScript -->
        </tbody>
    </table>

    <h2>Search Issue Status</h2>
    <form id="searchForm">
        <label for="ticketID">Ticket ID:</label>
        <input type="text" id="ticketID" name="ticketID" required>
        <button type="submit">Search</button>
    </form>
    <div id="searchResults"></div>

    <div id="issueDetails"></div>

    <script src="/javascript/reports.js"></script>
</body>

</html>