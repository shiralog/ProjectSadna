document.getElementById('registerForm').addEventListener('submit', function (event) {
    event.preventDefault();

    var formData = new FormData(this);

    fetch('register.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.text())
        .then(data => {
            console.log(data);
            console.log(data.length);

            var messageElement = document.getElementById('message');
            if (data === 'Registered successfully') {
                messageElement.textContent = 'Registration successful!';
                messageElement.style.color = 'green';
            } else {
                messageElement.textContent = data;
                messageElement.style.color = 'red';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('message').textContent = 'An error occurred. Please try again.';
        });
});