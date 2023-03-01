if (document.getElementById('requestForm')) {
    const form = document.getElementById('requestForm');
    const startDateInput = document.getElementById('request_start_date');
    const endDateInput = document.getElementById('request_end_date');

    form.addEventListener('submit', (event) => {
    const startDate = new Date(startDateInput.value);
    const endDate = new Date(endDateInput.value);

    if (endDate < startDate) {
        event.preventDefault();
        alert('La date de fin doit être supérieure à la date de début.');
    }
    });
}
