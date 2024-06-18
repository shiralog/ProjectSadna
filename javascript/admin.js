document.addEventListener('DOMContentLoaded', function () {
    document.body.style.display = 'none';
    let username = prompt("Enter username:");
    let password = prompt("Enter password:");

    while (username !== "admin" && password !== "admin") {
        alert("Invalid username or password. Please try again.");
        username = prompt("Enter username:");
        password = prompt("Enter password:");
    }
    alert("Login successful!");
    document.body.style.display = 'block';
    fetchReports();
});

function submitResponse(ticketID) {
    const responseMessage = document.getElementById('response_' + ticketID).value;

    if (responseMessage.trim() === '') {
        alert('Response cannot be empty');
        return;
    }

    fetch('admin-update-report.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            ticketID: ticketID,
            responseMessage: responseMessage,
        }),
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Response submitted successfully');
                fetchReports();
            } else {
                alert('Error: ' + data.error);
            }
        })
        .catch(error => console.error('Error:', error));
}

function fetchReports() {
    fetch('admin-get-reports.php')
        .then(response => response.json())
        .then(reports => {
            const reportsTableBody = document.querySelector('#reportsTable tbody');
            reportsTableBody.innerHTML = ''; // Clear existing rows

            reports.forEach(report => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${report.IssueTopic}</td>
                    <td>${report.IssueContent}</td>
                    <td>${report.TicketID}</td>
                    <td>${report.DateOfSubmission}</td>
                    <td>
                        <input type="text" placeholder="Enter response" id="response_${report.TicketID}">
                        <button onclick="submitResponse(${report.TicketID})">Submit</button>
                    </td>
                `;
                reportsTableBody.appendChild(row);
            });
        })
        .catch(error => console.error('Error fetching reports:', error));
}
