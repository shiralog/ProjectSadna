document.getElementById('reportForm').addEventListener('submit', function (event) {
    event.preventDefault();
    submitIssue();
});

function submitIssue() {
    const fullName = document.getElementById('fullName').value;
    const email = document.getElementById('email').value;
    const issueTopic = document.getElementById('issueTopic').value;
    const issueContent = document.getElementById('issueContent').value;

    fetch('submit-issue.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            fullName,
            email,
            issueTopic,
            issueContent
        }),
    })
        .then(response => response.json())
        .then(data => {
            const ticketID = data.ticketID;
            document.getElementById('issueDetails').innerHTML = `<p>Submitted Issue. Ticket ID: ${ticketID}</p>`;
        })
        .catch(error => console.error('Error:', error));
}

function searchIssue() {
    const ticketID = document.getElementById('ticketID').value;

    fetch(`search-issue.php?ticketID=${ticketID}`)
        .then(response => response.json())
        .then(data => {
            if (data) {
                const issueDetails = `
                <table>
                    <tr>
                        <th>Ticket ID</th>
                        <th>Date of Submission</th>
                        <th>Topic of Issue</th>
                        <th>Status</th>
                    </tr>
                    <tr>
                        <td>${data.ticketID}</td>
                        <td>${data.dateOfSubmission}</td>
                        <td>${data.issueTopic}</td>
                        <td>${data.status}</td>
                    </tr>
                </table>
            `;
                document.getElementById('issueDetails').innerHTML = issueDetails;
            } else {
                document.getElementById('issueDetails').innerHTML = '<p>No issue found with that Ticket ID.</p>';
            }
        })
        .catch(error => console.error('Error:', error));
}