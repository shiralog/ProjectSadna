<?php include 'session_verify.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Finder</title>
    <link rel="stylesheet" href="/css/navbar.css">
    <link rel="stylesheet" href="/css/partner-finder.css">
</head>

<body>
    <?php include 'navbar.php'; ?>

    <div class="container">
        <div class="filter-bar">
            <label for="ageRange">Age Range:</label>
            <select id="ageRange">
                <option value="">Any</option>
                <option value="18-25">18-25</option>
                <option value="25-30">25-30</option>
                <option value="30-35">30-35</option>
                <option value="other">Other</option>
            </select>

            <label for="region">Region:</label>
            <select id="region">
                <option value="">Any</option>
                <option value="South">South</option>
                <option value="Center">Center</option>
                <option value="North">North</option>
            </select>

            <label for="faculty">Faculty:</label>
            <select id="faculty">
                <option value="">Any</option>
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
                <option value="Silent_Study">For a silent study</option>
                <option value="Study_Tests">To study for tests</option>
                <option value="Submit_Chores">To submit chores</option>
            </select>

            <button onclick="applyFilters()">Apply Filters</button>
            <button onclick="resetFilters()">Reset Filters</button>
        </div>

        <div id="studentContainer" class="cards-container"></div>
    </div>

    <script src="/javascript/partner-finder.js"></script>
</body>

</html>
