document.getElementById('registerForm').addEventListener('submit', function (event) {
    event.preventDefault();

    var formData = new FormData(this);

    fetch('register-sendgrid.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            const messageDiv = document.getElementById('message');
            messageDiv.innerHTML = ''; // Clear previous messages
            if (data.register.startsWith("Registered successfully")) {
                messageDiv.classList.add('success-message');
                messageDiv.classList.remove('error-message');
                messageDiv.innerHTML += `<p>${data.register}</p>`;
            } else {
                messageDiv.classList.add('error-message');
                messageDiv.classList.remove('success-message');
                messageDiv.innerHTML += `<p>${data.register}</p>`;
            }
            if (data.email !== 'sent') {
                messageDiv.classList.add('error-message');
                messageDiv.classList.remove('success-message');
                console.log(data.email);
            }
        })
        .catch(error => {
            const messageDiv = document.getElementById('message');
            messageDiv.classList.add('error-message');
            messageDiv.innerHTML = `<p>An unexpected error occurred: ${error}</p>`;
        });
});