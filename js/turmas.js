const EditarBtns = document.querySelectorAll('.Editar-btn');
const adicionarBtn = document.querySelector('.adicionar-btn');
const modalOverlay = document.getElementById('modal-overlay');
const excluirBtn = document.querySelector('.excluir');
const salvarBtn = document.querySelector('.salvar');

const confirmationModal = document.getElementById('confirmation-modal');
const cancelarBtn = document.querySelector('.cancelar');
const excluirConfirmBtn = document.querySelector('.excluir-confirm');

EditarBtns.forEach(btn => {
    btn.addEventListener('click', () => {
        modalOverlay.style.display = 'flex';
    });
});

adicionarBtn.addEventListener('click', () => {
    window.location.href = 'addturma.php';
});

excluirBtn.addEventListener('click', () => {
    confirmationModal.style.display = 'flex';
});

salvarBtn.addEventListener('click', () => {
    modalOverlay.style.display = 'none';
});

cancelarBtn.addEventListener('click', () => {
        confirmationModal.style.display = 'none';
});

excluirConfirmBtn.addEventListener('click', () => {
        confirmationModal.style.display = 'none';
        modalOverlay.style.display = 'none';
 });

 confirmationModal.addEventListener('click', (e) => {
        if (e.target === confirmationModal) {
            confirmationModal.style.display = 'none';
        }
});

modalOverlay.addEventListener('click', (e) => {
    if (e.target === modalOverlay) {
        modalOverlay.style.display = 'none';
    }
});