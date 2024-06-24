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
                        <div class="partnerCard" data-partnerID="${partner.ID}" data-partnerFullName="${partner.firstName} ${partner.lastName}">
                        <p>${partner.firstName} ${partner.lastName} - ${partner.ID}</p>
                        </div>
                        `).join('');
                    partnersDiv.innerHTML = partnersList;
                    // Add event listeners to each card
                    document.querySelectorAll('.partnerCard').forEach(card => {
                        card.addEventListener('click', function () {
                            document.getElementById('afterClick').hidden = false;
                            document.getElementById('chatContainer').hidden = false;
                            document.getElementById('beforeClick').hidden = true;
                            const chatTitle = document.getElementById('chatTitle');
                            chatTitle.textContent = this.getAttribute('data-partnerFullName');
                            openChat(this.getAttribute('data-partnerID'), this.getAttribute('data-partnerFullName'));
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