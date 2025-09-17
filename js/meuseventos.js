const detalhesBtns = document.querySelectorAll('.detalhes-btn');
        const detalhesBtnsAprovado = document.querySelectorAll('.detalhes-btn-seu-evento-aprovado');
        const detalhesBtnsRecusado = document.querySelectorAll('.detalhes-btn-seu-evento-recusado');
        const criarBtn = document.querySelector('.criar-btn');
        const modalOverlay = document.getElementById('modal-overlay');
        const modalOverlayAprovado = document.getElementById('modal-overlay-aprovado');
        const modalOverlayRecusado = document.getElementById('modal-overlay-recusado');
        const excluirBtn = document.querySelector('.excluir');
        const excluirBtnAprovado = document.querySelector('.excluir-aprovado');
        const excluirBtnRecusado = document.querySelector('.excluir-recusado');
        const salvarBtn = document.querySelector('.salvar');

        const excluirConfirmBtn = document.querySelector('.excluir-confirm');
        const cancelarConfirmBtn = document.querySelector('.cancelar-confirm');
        const apagarConfirmBtn = document.querySelector('.apagar-confirm');
        const confirmationModal = document.getElementById('confirmation-modal');
        const confirmationModalAprovado = document.getElementById('confirmation-modal-aprovado');
        const confirmationModalRecusado = document.getElementById('confirmation-modal-recusado');
        const cancelarBtn = document.querySelector('.cancelar');
        const cancelarBtnAprovado = document.querySelector('.cancelarAprovado');
        const cancelarBtnRecusado = document.querySelector('.cancelarRecusado');

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

        detalhesBtnsAprovado.forEach(btn => {
            btn.addEventListener('click', () => {
                modalOverlayAprovado.style.display = 'flex';
            });
        });

        detalhesBtnsRecusado.forEach(btn => {
            btn.addEventListener('click', () => {
                modalOverlayRecusado.style.display = 'flex';
            });
        });

        criarBtn.addEventListener('click', () => {
            window.location.href = 'criarevento.php';
        });

        excluirBtn.addEventListener('click', () => {
            confirmationModal.style.display = 'flex';
        });

        excluirBtnAprovado.addEventListener('click', () => {
            confirmationModalAprovado.style.display = 'flex';
        });

        excluirBtnRecusado.addEventListener('click', () => {
            confirmationModalRecusado.style.display = 'flex';
        });

        cancelarBtn.addEventListener('click', () => {
            confirmationModal.style.display = 'none';
            confirmationModalRecusado.style.display = 'none';
        });

        cancelarBtnAprovado.addEventListener('click', () => {
            confirmationModalAprovado.style.display = 'none';
        });

        cancelarBtnRecusado.addEventListener('click', () => {
            confirmationModalRecusado.style.display = 'none';
        });

        excluirConfirmBtn.addEventListener('click', () => {
            confirmationModal.style.display = 'none';
            modalOverlay.style.display = 'none';
        });

        cancelarConfirmBtn.addEventListener('click', () => {
            confirmationModalAprovado.style.display = 'none';
            modalOverlayAprovado.style.display = 'none';
        });

        apagarConfirmBtn.addEventListener('click', () => {
            confirmationModalRecusado.style.display = 'none';
            modalOverlayRecusado.style.display = 'none';
        });

        confirmationModal.addEventListener('click', (e) => {
            if (e.target === confirmationModal) {
                confirmationModal.style.display = 'none';
            }
        });

        confirmationModalAprovado.addEventListener('click', (e) => {
            if (e.target === confirmationModalAprovado) {
                confirmationModalAprovado.style.display = 'none';
            }
        });

        confirmationModalRecusado.addEventListener('click', (e) => {
            if (e.target === confirmationModalRecusado) {
                confirmationModalRecusado.style.display = 'none';
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

        modalOverlayAprovado.addEventListener('click', (e) => {
            if (e.target === modalOverlayAprovado) {
                modalOverlayAprovado.style.display = 'none';
            }
        });

        modalOverlayRecusado.addEventListener('click', (e) => {
            if (e.target === modalOverlayRecusado) {
                modalOverlayRecusado.style.display = 'none';
            }
        });