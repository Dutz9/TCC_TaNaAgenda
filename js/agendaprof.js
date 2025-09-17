document.addEventListener('DOMContentLoaded', () => {
    const events = document.querySelectorAll('.event');
    const modalOverlay = document.getElementById('modal-overlay');

    events.forEach(event => {
        event.addEventListener('click', () => {
            modalOverlay.style.display = 'flex';
        });
    });

    modalOverlay.addEventListener('click', (e) => {
        if (e.target === modalOverlay) {
            modalOverlay.style.display = 'none';
        }
    });
});