<?php include 'session_verify.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Finder</title>
    <link rel="stylesheet" href="/css/partner-finder.css">
    <style>
        .card {
            width: 300px;
            border: 1px solid #ccc;
            padding: 10px;
            margin: 10px;
            display: inline-block;
        }

        .filter-bar {
            margin: 10px;
            padding: 10px;
            border: 1px solid #ccc;
        }

        .filter-bar input,
        .filter-bar select {
            margin: 5px;
        }
    </style>
</head>

<body>
    <?php include 'navbar.php'; ?>
    <div class="filter-bar">
        <label for="ageRange">Age Range:</label>
        <select id="ageRange">
            <option value="">Any</option>
            <!-- Add region options here -->
            <option value="18-25">18-25</option>
            <option value="25-30">25-30</option>
            <option value="30-35">30-35</option>
            <option value="other">Other</option>
        </select>

        <label for="region">Region:</label>
        <select id="region">
            <option value="">Any</option>
            <!-- Add region options here -->
            <option value="South">South</option>
            <option value="Center">Center</option>
            <option value="North">North</option>
        </select>

        <label for="faculty">Faculty:</label>
        <select id="faculty">
            <option value="">Any</option>
            <!-- Add faculty options here -->
            <option value="Art">Art</option>
            <option value="Engineering">Engineering</option>
            <option value="Law">Law</option>
            <option value="Business">Business</option>
            <option value="Education">Education</option>
            <option value="Social_Sciences">Social Sciences</option>
            <option value="computer_Science">Computer Science</option>
            <option value="Nursing">Nursing</option>
            <option value="Pharmacy">Pharmacy</option>
            <option value="Veterinary">Veterinary</option>
            <option value="Environmental_Studies">Environmental Studies</option>
            <option value="Sports_Science">Sports Science</option>
        </select>

        <label for="gender">Gender:</label>
        <select id="gender">
            <option value="">Any</option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
        </select>

        <label for="partnerType">Partner Type:</label>
        <select id="partnerType">
            <option value="">Any</option>
            <!-- Add partner type options here -->
            <option value="Silent_Study">For a silent study</option>
            <option value="Study_Tests">To study for tests</option>
            <option value="Submit_Chores">To submit chores</option>
        </select>

        <button onclick="applyFilters()">Apply Filters</button>
        <button onclick="resetFilters()">Reset Filters</button>
    </div>

    <div id="studentContainer"></div>

    <a href="dashboard.php">Back</a>

    <script src="/javascript/partner-finder.js"></script>
</body>

</html>