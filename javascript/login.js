document.getElementById('loginForm').addEventListener('submit', function (event) {
    event.preventDefault();

    const formData = new FormData(this);

    fetch('/pages/login.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.text())
        .then(data => {
            console.log(data);
            if (data === 'OK') {
                window.location.href = '/pages/dashboard.php';
            } else {
                document.getElementById('message').textContent = 'Incorrect email/password';
            }
        })
        .catch(error => console.error('Error:', error));
});