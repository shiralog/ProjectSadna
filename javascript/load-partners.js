document.addEventListener("DOMContentLoaded", function () {
    fetch('get-partners.php')
        .then(response => response.json())
        .then(data => {
            try {
                console.log(data);
                const partnersDiv = document.getElementById('partnersContainer');
                if (data.error) {
                    partnersDiv.innerHTML = `<p>Error: ${data.error}</p>`;
                } else if (data.length > 0) {
                    const partnersList = data.map(partner => `
                        <div class="partnerCard" data-partnerID="${partner.ID}">
                        <p>${partner.firstName} ${partner.lastName}</p>
                        <p id="partnerID">${partner.ID}</p>
                        <p>${partner.profileImagePath}</p>
                        </div>
                        `).join('');
                    partnersDiv.innerHTML = partnersList;
                    // Add event listeners to each card
                    document.querySelectorAll('.partnerCard').forEach(card => {
                        card.addEventListener('click', function () {
                            console.log(this.getAttribute('data-partnerID'));
                        });
                    });
                } else {
                    partnersDiv.innerHTML = '<p>No results</p>';
                }
            } catch (error) {
                console.error('Error parsing JSON:', error);
                const partnersDiv = document.getElementById('partnersContainer');
                partnersDiv.innerHTML = '<p>An error occurred while parsing the data.</p>';
            }
        })
        .catch(error => {
            console.error('Error fetching partners:', error);
            const partnersDiv = document.getElementById('partnersContainer');
            partnersDiv.innerHTML = '<p>An error occurred while fetching data.</p>';
        });
});