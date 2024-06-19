document.addEventListener('DOMContentLoaded', fetchMyReports);

document.getElementById('reportForm').addEventListener('submit', function (event) {
    event.preventDefault();
    submitIssue();
    fetchMyReports();
});

document.getElementById('searchForm').addEventListener('submit', function (event) {
    event.preventDefault();
    searchIssue();
});

function submitIssue() {
    const fullName = document.getElementById('fullName').value;
    const issueTopic = document.getElementById('issueTopic').value;
    const issueContent = document.getElementById('issueContent').value;

    fetch('submit-issue.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            fullName,
            issueTopic,
            issueContent
        }),
    })
        .then(response => response.json())
        .then(data => {
            if (data.ticketID) {
                document.getElementById('issueDetails').innerHTML = `<p>Submitted Issue. Ticket ID: ${data.ticketID}</p>`;
            } else {
                document.getElementById('issueDetails').innerHTML = `<p>Error: ${data.error}</p>`;
            }
        })
        .catch(error => console.error('Error:', error));
}

function fetchMyReports() {
    fetch('get-my-reports.php')
        .then(response => response.json())
        .then(data => {
            const myReportsTable = document.getElementById('myReportsTable').getElementsByTagName('tbody')[0];
            myReportsTable.innerHTML = ''; // Clear previous content
            data.forEach(report => {
                const row = myReportsTable.insertRow();
                row.insertCell(0).textContent = report.TicketID;
                row.insertCell(1).textContent = report.Status;
            });
        })
        .catch(error => console.error('Error:', error));
}

function searchIssue() {
    const ticketID = document.getElementById('ticketID').value;

    fetch('search-issue.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            ticketID
        }),
    })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                document.getElementById('searchResults').innerHTML = `<p>${data.error}</p>`;
            } else {
                document.getElementById('searchResults').innerHTML = `
                <table>
                    <thead>
                        <tr>
                            <th>Ticket ID</th>
                            <th>Date of Submission</th>
                            <th>Topic of Issue</th>
                            <th>Status</th>
                            <th>Response Message</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>${data.TicketID}</td>
                            <td>${data.DateOfSubmission}</td>
                            <td>${data.IssueTopic}</td>
                            <td>${data.Status}</td>
                            <td>${data.ResponseMessage || 'No response yet'}</td>
                        </tr>
                    </tbody>
                </table>
            `;
            }
        })
        .catch(error => console.error('Error:', error));
}
