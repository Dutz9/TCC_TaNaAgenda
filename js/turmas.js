/**
 * Mostra uma barra de feedback flutuante no topo da tela.
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
    // Garante que a ponte de dados existe
    if (typeof turmasDaPagina === 'undefined') {
        console.error("A variável 'turmasDaPagina' não foi encontrada.");
        return;
    }

    // --- Elementos Principais ---
    const searchInput = document.getElementById('search-turma');
    const cardContainer = document.getElementById('admin-card-container');
    const modalOverlay = document.getElementById('modal-overlay');
    
    // --- Elementos dos Modais ---
    const salvarBtn = document.querySelector('.salvar');
    const excluirModalBtn = document.querySelector('.excluir');
    const confirmationModal = document.getElementById('confirmation-modal');
    const cancelarBtn = document.querySelector('.cancelar');
    const excluirConfirmBtn = document.querySelector('.excluir-confirm');

    // --- Campos do Modal de Edição ---
    const modalTurmaId = document.getElementById('modal-turma-id');
    const modalTurmaNome = document.getElementById('modal-turma-nome');
    const modalTurmaCurso = document.getElementById('modal-turma-curso');
    const modalTurmaSala = document.getElementById('modal-turma-sala');
    const modalTurmaAlunos = document.getElementById('modal-turma-alunos');
    const modalTurmaSerie = document.getElementById('modal-turma-serie');
    const modalTurmaPeriodo = document.getElementById('modal-turma-periodo');
    const modalListaProfs = document.getElementById('professores-vinculados-lista');
    
    let turmaEmEdicao = null;

    /**
     * Função para "desenhar" os cards na tela
     */
    function renderizarTurmas(listaTurmas) {
        cardContainer.querySelectorAll('.admin-card:not(.card-adicionar)').forEach(card => card.remove());
        cardContainer.querySelector('.sem-eventos')?.remove();

        if (listaTurmas.length === 0) {
            if (!cardContainer.querySelector('.sem-eventos')) {
                const p = document.createElement('p');
                p.className = 'sem-eventos';
                p.textContent = 'Nenhuma turma encontrada com esse filtro.';
                cardContainer.appendChild(p);
            }
        }

        for (const turma of listaTurmas) {
            const card = document.createElement('div');
            card.className = 'admin-card';
            card.id = `turma-card-${turma.cd_turma}`;
            card.innerHTML = `
                <div class="prof-infos">
                    <h3>${turma.nm_turma}</h3>
                    <p><b>Professores:</b> ${turma.contagem_professores || '0'}</p>
                </div>
                <button class="admin-btn btn-editar" data-id="${turma.cd_turma}">Editar</button>
            `;
            cardContainer.appendChild(card);
        }
    }

    /**
     * Função para filtrar e re-renderizar a lista
     */
    function filtrarTurmas() {
        const termoBusca = searchInput.value.toLowerCase();
        
        const turmasFiltradas = turmasDaPagina.filter(turma => {
            const nome = turma.nm_turma.toLowerCase();
            const curso = (turma.nm_curso || '').toLowerCase();
            const periodo = (turma.ic_periodo || '').toLowerCase();
            
            return nome.includes(termoBusca) || curso.includes(termoBusca) || periodo.includes(termoBusca);
        });
        
        renderizarTurmas(turmasFiltradas);
    }
    
    function fecharModais() {
        modalOverlay.style.display = 'none';
        confirmationModal.style.display = 'none';
        turmaEmEdicao = null;
    }

    // --- FUNÇÃO AJAX (com SVG corrigido) ---
    async function buscarProfessores(turmaId) {
        modalListaProfs.innerHTML = '<p>Carregando...</p>';
        try {
            const response = await fetch(`../api/get_professores_turma.php?turma_id=${turmaId}`);
            const professores = await response.json(); 

            if (response.ok) {
                modalListaProfs.innerHTML = '';
                if (professores.length > 0) {
                    professores.forEach(prof => {
                        const item = document.createElement('div');
                        item.className = 'response-item';
                        // --- CORREÇÃO AQUI: SVG COMPLETO ---
                        item.innerHTML = `
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                                <path fill="#000000" d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512H418.3c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304H178.3z"/>
                            </svg>
                            <div><p>${prof.nm_usuario}</p></div>
                        `;
                        modalListaProfs.appendChild(item);
                    });
                } else {
                    modalListaProfs.innerHTML = '<p>Nenhum professor vinculado a esta turma.</p>';
                }
            } else {
                modalListaProfs.innerHTML = `<p style="color: red;">Erro: ${professores.mensagem || 'Não foi possível buscar.'}</p>`;
            }
        } catch (error) {
            console.error('Erro ao buscar professores:', error);
            modalListaProfs.innerHTML = '<p style="color: red;">Erro de conexão.</p>';
        }
    }

    // --- LÓGICA DOS EVENT LISTENERS ---

    // Abre o Modal de Edição
    cardContainer.addEventListener('click', (e) => {
        const botaoEditar = e.target.closest('button.btn-editar'); 
        if (!botaoEditar) return;
        const turmaId = botaoEditar.dataset.id;
        
        turmaEmEdicao = turmasDaPagina.find(t => String(t.cd_turma) === turmaId);
        
        if (turmaEmEdicao) {
            modalTurmaId.value = turmaEmEdicao.cd_turma;
            modalTurmaNome.value = turmaEmEdicao.nm_turma;
            modalTurmaCurso.value = turmaEmEdicao.nm_curso;
            modalTurmaSala.value = turmaEmEdicao.cd_sala;
            modalTurmaAlunos.value = turmaEmEdicao.qt_alunos;
            modalTurmaSerie.value = turmaEmEdicao.ic_serie;
            modalTurmaPeriodo.value = turmaEmEdicao.ic_periodo;
            
            buscarProfessores(turmaEmEdicao.cd_turma); 
            modalOverlay.style.display = 'flex';
        }
    });

    // --- LÓGICA DOS BOTÕES DO MODAL (com AJAX) ---

    // Botão "Salvar Alterações"
    salvarBtn.addEventListener('click', async () => {
        const formData = new FormData();
        formData.append('cd_turma', modalTurmaId.value);
        formData.append('nm_turma', modalTurmaNome.value);
        formData.append('cd_sala', modalTurmaSala.value);
        formData.append('qt_alunos', modalTurmaAlunos.value);
        formData.append('ic_serie', modalTurmaSerie.value);

        salvarBtn.textContent = "Salvando...";
        salvarBtn.disabled = true;

        try {
            const response = await fetch('../api/atualizar_turma.php', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();

            if (response.ok && result.status === 'sucesso') {
                // Atualiza a "memória" do JS
                turmaEmEdicao.nm_turma = modalTurmaNome.value;
                turmaEmEdicao.cd_sala = modalTurmaSala.value;
                turmaEmEdicao.qt_alunos = modalTurmaAlunos.value;
                turmaEmEdicao.ic_serie = modalTurmaSerie.value;

                // Atualiza o card na tela
                const card = document.getElementById(`turma-card-${turmaEmEdicao.cd_turma}`);
                if (card) {
                    card.querySelector('h3').textContent = modalTurmaNome.value;
                }
                
                showFeedback(result.mensagem, 'sucesso');
                fecharModais();
            } else {
                showFeedback(result.mensagem || 'Não foi possível salvar.', 'erro');
            }
        } catch (error) {
            showFeedback('Erro de comunicação. Tente novamente.', 'erro');
            console.error('Erro no fetch:', error);
        }
        salvarBtn.textContent = "Salvar Alterações";
        salvarBtn.disabled = false;
    });

    // Botão "Excluir Turma" (abre a confirmação)
    excluirModalBtn.addEventListener('click', () => {
        confirmationModal.style.display = 'flex';
    });

    // Botão "Cancelar" (na confirmação)
    cancelarBtn.addEventListener('click', () => {
        confirmationModal.style.display = 'none';
    });

    // Botão "Excluir" (final, na confirmação)
    excluirConfirmBtn.addEventListener('click', async () => {
        const id = modalTurmaId.value;
        if (!id) return;

        const formData = new FormData();
        formData.append('cd_turma', id);
        
        excluirConfirmBtn.textContent = "Excluindo...";
        excluirConfirmBtn.disabled = true;

        try {
            const response = await fetch('../api/excluir_turma.php', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();

            if (response.ok && result.status === 'sucesso') {
                const index = turmasDaPagina.findIndex(t => String(t.cd_turma) === id);
                if (index > -1) {
                    turmasDaPagina.splice(index, 1);
                }
                document.getElementById(`turma-card-${id}`)?.remove();
                showFeedback(result.mensagem, 'sucesso');
                fecharModais();
            } else {
                showFeedback(result.mensagem || 'Não foi possível excluir.', 'erro');
            }
        } catch (error) {
            showFeedback('Erro de comunicação. Tente novamente.', 'erro');
            console.error('Erro no fetch:', error);
        }
        excluirConfirmBtn.textContent = "Excluir";
        excluirConfirmBtn.disabled = false;
    });

    // Fechar modais ao clicar fora
    confirmationModal.addEventListener('click', (e) => { if (e.target === confirmationModal) fecharModais(); });
    modalOverlay.addEventListener('click', (e) => { if (e.target === modalOverlay) fecharModais(); });

    // --- INICIALIZAÇÃO ---
    searchInput.addEventListener('input', filtrarTurmas);
    renderizarTurmas(turmasDaPagina);
});