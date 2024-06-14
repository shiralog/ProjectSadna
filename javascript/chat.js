function openChat(partnerID, partnerFullName) {
    console.log(partnerID);
    fetch(`get-messages.php?partnerID=${partnerID}`)
        .then(response => response.json())
        .then(messages => {
            console.log(messages);

            const chatContainer = document.getElementById('chatContainer');
            const messagesDivContent = messages.map(message => `
                <div class="message">
                    <p><strong>${message.senderID == userID ? 'You' : partnerFullName}:</strong> ${message.message}</p>
                    <p class="timestamp">${new Date(message.timestamp).toLocaleString()}</p>
                </div>
            `).join('');

            // Display the chat form
            chatContainer.innerHTML = `
                <div id="messages">${messagesDivContent}</div>
                <form id="chatForm">
                    <input type="text" id="chatInput" placeholder="Type your message..." required>
                    <button type="submit">Send</button>
                </form>
            `;

            // Scroll to the bottom of the chat
            const messagesDiv = document.getElementById('messages');
            messagesDiv.scrollTop = messagesDiv.scrollHeight;

            // Add event listener to the form
            document.getElementById('chatForm').addEventListener('submit', function (event) {
                event.preventDefault();
                sendMessage(partnerID, partnerFullName);
            });
        })
        .catch(error => {
            console.error('Error fetching messages:', error);
        });
}

function sendMessage(partnerID, partnerFullName) {
    const messageInput = document.getElementById('chatInput');
    const message = messageInput.value;

    const formData = new FormData();
    formData.append('receiverID', partnerID);
    formData.append('message', message);

    fetch('send-message.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                openChat(partnerID, partnerFullName); // Refresh the chat to show the new message
            } else {
                console.error('Error sending message:', result.error);
            }
        })
        .catch(error => {
            console.error('Error sending message:', error);
        });

    messageInput.value = ''; // Clear the input box
}