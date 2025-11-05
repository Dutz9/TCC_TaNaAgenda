/**
 * Mostra uma barra de feedback flutuante no topo da tela.
 * Fica fora do DOMContentLoaded para ser acessível pelo PHP.
 * @param {string} message - A mensagem a ser exibida.
 * @param {string} type - 'sucesso' ou 'erro'.
 */
function showFeedback(message, type = 'sucesso') {
    const bar = document.getElementById('feedback-bar');
    if (!bar) return;
    bar.textContent = message;
    bar.className = `feedback-bar ${type} show`;
    setTimeout(() => { bar.classList.remove('show'); }, 3500);
}

// --- LÓGICA PRINCIPAL DA PÁGINA ---
document.addEventListener('DOMContentLoaded', () => {
    if (typeof professoresDaPagina === 'undefined') {
        console.error("A variável 'professoresDaPagina' não foi encontrada.");
        return;
    }

    // --- Elementos Principais ---
    const searchInput = document.getElementById('search-prof');
    const cardContainer = document.getElementById('admin-card-container');
    const modalOverlay = document.getElementById('modal-overlay');
    
    // --- Elementos dos Modais ---
    const salvarBtn = document.querySelector('.salvar');
    const excluirModalBtn = document.querySelector('.excluir');
    const confirmationModal = document.getElementById('confirmation-modal');
    const cancelarBtn = document.querySelector('.cancelar');
    const excluirConfirmBtn = document.querySelector('.excluir-confirm');

    // --- Campos do Modal de Edição ---
    const modalProfId = document.getElementById('modal-prof-id');
    const modalProfNome = document.getElementById('modal-prof-nome');
    const modalProfRm = document.getElementById('modal-prof-rm');
    const modalProfEmail = document.getElementById('modal-prof-email');
    const modalProfTelefone = document.getElementById('modal-prof-telefone');
    const modalProfTurmas = document.getElementById('modal-prof-turmas');

    let professorEmEdicao = null;

    /**
     * Função para "desenhar" os cards na tela
     */
    function renderizarProfessores(listaProfessores) {
        cardContainer.querySelectorAll('.admin-card:not(.card-adicionar)').forEach(card => card.remove());
        cardContainer.querySelector('.sem-eventos')?.remove();

        if (listaProfessores.length === 0) {
            if (!cardContainer.querySelector('.sem-eventos')) {
                const p = document.createElement('p');
                p.className = 'sem-eventos';
                p.textContent = 'Nenhum professor encontrado com esse filtro.';
                cardContainer.appendChild(p);
            }
        }

        for (const professor of listaProfessores) {
            const card = document.createElement('div');
            card.className = 'admin-card';
            card.id = `prof-card-${professor.cd_usuario}`;
            card.innerHTML = `
                <div class="prof-infos">
                    <h3>${professor.cd_usuario} - ${professor.nm_usuario}</h3>
                    <p><b>Turmas:</b> ${professor.turmas_associadas || 'Nenhuma turma'}</p>
                </div>
                <button class="admin-btn btn-editar" data-id="${professor.cd_usuario}">Editar</button>
            `;
            cardContainer.appendChild(card);
        }
    }

    /**
     * Função para filtrar e re-renderizar a lista
     */
    function filtrarProfessores() {
        const termoBusca = searchInput.value.toLowerCase();
        
        const professoresFiltrados = professoresDaPagina.filter(prof => {
            const nome = prof.nm_usuario.toLowerCase();
            const turmas = (prof.turmas_associadas || '').toLowerCase();
            const rm = prof.cd_usuario.toLowerCase();
            return nome.includes(termoBusca) || turmas.includes(termoBusca) || rm.includes(termoBusca);
        });
        
        renderizarProfessores(professoresFiltrados);
    }
    
    function fecharModais() {
        modalOverlay.style.display = 'none';
        confirmationModal.style.display = 'none';
        professorEmEdicao = null;
    }

    // --- LÓGICA DOS EVENT LISTENERS ---

    // Abre o Modal de Edição
    cardContainer.addEventListener('click', (e) => {
        const botaoEditar = e.target.closest('button.btn-editar'); 
        if (!botaoEditar) return;
        const profId = botaoEditar.dataset.id;
        
        professorEmEdicao = professoresDaPagina.find(p => p.cd_usuario === profId);
        
        if (professorEmEdicao) {
            modalProfId.value = professorEmEdicao.cd_usuario;
            modalProfNome.value = professorEmEdicao.nm_usuario;
            modalProfRm.value = professorEmEdicao.cd_usuario;
            modalProfEmail.value = professorEmEdicao.nm_email;
            modalProfTelefone.value = professorEmEdicao.cd_telefone || ''; 
            modalProfTurmas.value = professorEmEdicao.turmas_associadas || 'Nenhuma turma';
            modalOverlay.style.display = 'flex';
        }
    });

    // --- LÓGICA DOS BOTÕES DO MODAL ---

    // Botão "Salvar Alterações"
    salvarBtn.addEventListener('click', async () => {
        const id = modalProfId.value;
        const nome = modalProfNome.value;
        const email = modalProfEmail.value;
        const telefone = modalProfTelefone.value;

        const formData = new FormData();
        formData.append('cd_usuario', id);
        formData.append('nome', nome);
        formData.append('email', email);
        formData.append('telefone', telefone);

        salvarBtn.textContent = "Salvando...";
        salvarBtn.disabled = true;
        
        try {
            const response = await fetch('../api/atualizar_professor.php', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();

            if (response.ok && result.status === 'sucesso') {
                // Atualiza a "memória" do JS
                professorEmEdicao.nm_usuario = nome;
                professorEmEdicao.nm_email = email;
                professorEmEdicao.cd_telefone = telefone;

                // Atualiza o card na tela
                const card = document.getElementById(`prof-card-${id}`);
                if(card) {
                    card.querySelector('h3').textContent = `${id} - ${nome}`;
                }
                
                showFeedback(result.mensagem, 'sucesso'); // <-- CORREÇÃO AQUI
                fecharModais();
            } else {
                showFeedback(result.mensagem || 'Não foi possível salvar.', 'erro'); // <-- CORREÇÃO AQUI
            }
        } catch (error) {
            showFeedback('Erro de comunicação. Tente novamente.', 'erro'); // <-- CORREÇÃO AQUI
            console.error('Erro no fetch:', error);
        }
        salvarBtn.textContent = "Salvar Alterações";
        salvarBtn.disabled = false;
    });

    // Botão "Excluir Professor" (abre a confirmação)
    excluirModalBtn.addEventListener('click', () => {
        confirmationModal.style.display = 'flex';
    });

    // Botão "Cancelar" (na confirmação)
    cancelarBtn.addEventListener('click', () => {
        confirmationModal.style.display = 'none';
    });

    // Botão "Excluir" (final, na confirmação)
    excluirConfirmBtn.addEventListener('click', async () => {
        const id = modalProfId.value;
        if (!id) return;

        const formData = new FormData();
        formData.append('cd_usuario', id);
        
        excluirConfirmBtn.textContent = "Excluindo...";
        excluirConfirmBtn.disabled = true;

        try {
            const response = await fetch('../api/excluir_professor.php', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();

            if (response.ok && result.status === 'sucesso') {
                // Remove o professor da "memória" do JS
                const index = professoresDaPagina.findIndex(p => p.cd_usuario === id);
                if (index > -1) {
                    professoresDaPagina.splice(index, 1);
                }

                // Remove o card da tela
                document.getElementById(`prof-card-${id}`)?.remove();
                
                showFeedback(result.mensagem, 'sucesso'); // <-- CORREÇÃO AQUI
                fecharModais();
            } else {
                showFeedback(result.mensagem || 'Não foi possível excluir.', 'erro'); // <-- CORREÇÃO AQUI
            }
        } catch (error) {
            showFeedback('Erro de comunicação. Tente novamente.', 'erro'); // <-- CORREÇÃO AQUI
            console.error('Erro no fetch:', error);
        }
        excluirConfirmBtn.textContent = "Excluir";
        excluirConfirmBtn.disabled = false;
    });

    // Fechar modais ao clicar fora
    confirmationModal.addEventListener('click', (e) => { if (e.target === confirmationModal) fecharModais(); });
    modalOverlay.addEventListener('click', (e) => { if (e.target === modalOverlay) fecharModais(); });

    // --- INICIALIZAÇÃO ---
    searchInput.addEventListener('input', filtrarProfessores);
    renderizarProfessores(professoresDaPagina);
});