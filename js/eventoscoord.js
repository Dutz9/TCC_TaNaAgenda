const detalhesBtns = document.querySelectorAll('.detalhes-btn');
const criarBtn = document.querySelector('.criar-btn');
const modalOverlay = document.getElementById('modal-overlay');
const excluirBtn = document.querySelector('.excluir');
const salvarBtn = document.querySelector('.salvar');
const excluirConfirmBtn = document.querySelector('.excluir-confirm');
const confirmationModal = document.getElementById('confirmation-modal');
const cancelarBtn = document.querySelector('.cancelar');

const detalhesBtnsResponder = document.querySelectorAll('.detalhes-btn-responder-solicitacao');
const modalOverlaySolicitacao = document.getElementById('modal-overlay-solicitacao');
const recusarBtn = document.querySelector('.recusar');
const aprovarBtn = document.querySelector('.aprovar');
const confirmationModalSolicitacao = document.getElementById('confirmation-modal-responder-solicitacao');
const cancelarBtnSolicitacao = document.querySelector('.cancelar-solicitacao');
const recusarConfirmBtn = document.querySelector('.recusar-confirm');

detalhesBtnsResponder.forEach(btn => {
    btn.addEventListener('click', () => {
        modalOverlaySolicitacao.style.display = 'flex';
    });
});

recusarBtn.addEventListener('click', () => {
    confirmationModalSolicitacao.style.display = 'flex';
});

aprovarBtn.addEventListener('click', () => {
    modalOverlaySolicitacao.style.display = 'none';
});

cancelarBtnSolicitacao.addEventListener('click', () => {
    confirmationModalSolicitacao.style.display = 'none';
});

recusarConfirmBtn.addEventListener('click', () => {
    confirmationModalSolicitacao.style.display = 'none';
    modalOverlaySolicitacao.style.display = 'none';
});

modalOverlaySolicitacao.addEventListener('click', (e) => {
    if (e.target === modalOverlay) {
        modalOverlaySolicitacao.style.display = 'none';
    }
});

confirmationModalSolicitacao.addEventListener('click', (e) => {
    if (e.target === confirmationModalSolicitacao) {
        confirmationModalSolicitacao.style.display = 'none';
    }
});

detalhesBtns.forEach(btn => {
    btn.addEventListener('click', () => {
        modalOverlay.style.display = 'flex';
    });
});

criarBtn.addEventListener('click', () => {
    window.location.href = 'criareventocoord.php';
});

excluirBtn.addEventListener('click', () => {
    confirmationModal.style.display = 'flex';
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

salvarBtn.addEventListener('click', () => {
    modalOverlay.style.display = 'none';
});

modalOverlay.addEventListener('click', (e) => {
    if (e.target === modalOverlay) {
        modalOverlay.style.display = 'none';
    }
});