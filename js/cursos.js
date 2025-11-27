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
    if (typeof cursosDaPagina === 'undefined') {
        console.error("A variável 'cursosDaPagina' não foi encontrada.");
        return;
    }

    // --- Elementos Principais ---
    const searchInput = document.getElementById('search-curso');
    const cardContainer = document.getElementById('admin-card-container');
    const modalOverlay = document.getElementById('modal-overlay');
    
    // --- Elementos dos Modais ---
    const salvarBtn = document.querySelector('.salvar');
    const excluirModalBtn = document.querySelector('.excluir');
    const confirmationModal = document.getElementById('confirmation-modal');
    const cancelarBtn = document.querySelector('.cancelar');
    const excluirConfirmBtn = document.querySelector('.excluir-confirm');

    // --- Campos do Modal de Edição ---
    const modalCursoId = document.getElementById('modal-curso-id');
    const modalCursoNome = document.getElementById('modal-curso-nome');
    const modalCursoTurmas = document.getElementById('modal-curso-turmas');
    const modalCursoPeriodo = document.getElementById('modal-curso-periodo');
    const modalCursoDuracao = document.getElementById('modal-curso-duracao'); // Deve existir no HTML
    const modalListaCoords = document.getElementById('coordenadores-vinculados-lista');
    
    let cursoEmEdicao = null;

    /**
     * Função para "desenhar" os cards na tela (ADAPTADA para o novo HTML/CSS)
     */
    function renderizarCursos(listaCursos) {
        // Remove todos os cards, exceto o de adicionar
        cardContainer.querySelectorAll('.admin-card:not(.card-adicionar)').forEach(card => card.remove());
        cardContainer.querySelector('.sem-eventos')?.remove();

        if (listaCursos.length === 0) {
            if (!cardContainer.querySelector('.sem-eventos')) {
                const p = document.createElement('p');
                p.className = 'sem-eventos';
                p.textContent = 'Nenhum curso encontrado com esse filtro.';
                // Garante que a mensagem ocupe a largura total da grid de 2 colunas
                p.style.gridColumn = '1 / -1'; 
                cardContainer.appendChild(p);
            }
        }

        for (const curso of listaCursos) {
            const card = document.createElement('div');
            // CHAVE: Usar a classe padrão 'admin-card'
            card.className = 'admin-card'; 
            card.id = `curso-card-${curso.cd_curso}`;
            
            // Renderiza as informações do curso
            card.innerHTML = `
                <div class="curso-infos">
                    <!-- Usa h3 e p para herdar o estilo de cor/fonte base de coordenador.css -->
                    <h3>${curso.nm_curso} (${curso.ic_periodo})</h3>
                    <p><b>Turmas:</b> ${curso.contagem_turmas || '0'}</p>
                    <p><b>Coordenadores:</b> ${curso.coordenadores_associados || 'N/A'}</p>
                </div>
                <!-- CHAVE: Usa a classe padrão 'admin-btn btn-editar' -->
                <button class="admin-btn btn-editar" data-id="${curso.cd_curso}">Editar</button>
            `;
            cardContainer.appendChild(card);
        }
    }

    /**
     * Função para filtrar e re-renderizar a lista
     */
    function filtrarCursos() {
        const termoBusca = searchInput.value.toLowerCase();
        
        const cursosFiltrados = cursosDaPagina.filter(curso => {
            const nome = curso.nm_curso.toLowerCase();
            const periodo = (curso.ic_periodo || '').toLowerCase();
            const coordenadores = (curso.coordenadores_associados || '').toLowerCase();
            
            return nome.includes(termoBusca) || periodo.includes(termoBusca) || coordenadores.includes(termoBusca);
        });
        
        renderizarCursos(cursosFiltrados);
    }
    
    function fecharModais() {
        modalOverlay.style.display = 'none';
        confirmationModal.style.display = 'none';
        cursoEmEdicao = null;
    }

    // --- FUNÇÃO AJAX (para buscar Coordenadores) ---
    async function buscarCoordenadores(cursoId) {
        modalListaCoords.innerHTML = '<p>Carregando...</p>';
        try {
            const response = await fetch(`../api/get_coordenadores_curso.php?curso_id=${cursoId}`);
            const coordenadores = await response.json(); 

            if (response.ok) {
                modalListaCoords.innerHTML = '';
                if (coordenadores.length > 0) {
                    coordenadores.forEach(coord => {
                        const item = document.createElement('div');
                        item.className = 'response-item';
                        item.innerHTML = `
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                                <path fill="#000000" d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512H418.3c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304H178.3z"/>
                            </svg>
                            <div><p>${coord.nm_usuario}</p></div>
                        `;
                        modalListaCoords.appendChild(item);
                    });
                } else {
                    modalListaCoords.innerHTML = '<p>Nenhum coordenador vinculado a este curso.</p>';
                }
            } else {
                modalListaCoords.innerHTML = `<p style="color: red;">Erro: ${coordenadores.mensagem || 'Não foi possível buscar.'}</p>`;
            }
        } catch (error) {
            console.error('Erro ao buscar coordenadores:', error);
            modalListaCoords.innerHTML = '<p style="color: red;">Erro de conexão.</p>';
        }
    }


    // --- LÓGICA DOS EVENT LISTENERS ---

    // Abre o Modal de Edição
    cardContainer.addEventListener('click', (e) => {
        const botaoEditar = e.target.closest('button.btn-editar'); 
        if (!botaoEditar) return;
        const cursoId = botaoEditar.dataset.id;
        
        cursoEmEdicao = cursosDaPagina.find(c => String(c.cd_curso) === cursoId);
        
        if (cursoEmEdicao) {
            // Preenche os campos do modal
            modalCursoId.value = cursoEmEdicao.cd_curso;
            modalCursoNome.value = cursoEmEdicao.nm_curso;
            modalCursoTurmas.value = cursoEmEdicao.contagem_turmas;
            modalCursoPeriodo.value = cursoEmEdicao.ic_periodo;
            
            // Simulação da Duração
            if (modalCursoDuracao) modalCursoDuracao.value = '3 Módulos/Anos'; 
            
            // Busca os coordenadores associados (com a função AJAX adaptada)
            buscarCoordenadores(cursoEmEdicao.cd_curso); 
            
            modalOverlay.style.display = 'flex';
        }
    });

    // Botão "Salvar Alterações" (com AJAX)
    salvarBtn.addEventListener('click', async () => {
        const formData = new FormData();
        formData.append('cd_curso', modalCursoId.value);
        formData.append('nm_curso', modalCursoNome.value);
        formData.append('ic_periodo', modalCursoPeriodo.value);

        salvarBtn.textContent = "Salvando...";
        salvarBtn.disabled = true;

        try {
            const response = await fetch('../api/atualizar_curso.php', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();

            if (response.ok && result.status === 'sucesso') {
                // Atualiza a "memória" do JS
                cursoEmEdicao.nm_curso = modalCursoNome.value;
                cursoEmEdicao.ic_periodo = modalCursoPeriodo.value;
                
                // Atualiza a lista na tela (re-renderiza para ser mais simples)
                const index = cursosDaPagina.findIndex(c => String(c.cd_curso) === cursoEmEdicao.cd_curso);
                if (index > -1) {
                     // Atualiza o objeto no array
                     cursosDaPagina[index] = { ...cursosDaPagina[index], ...cursoEmEdicao };
                }
                renderizarCursos(cursosDaPagina);
                
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

    // Botão "Excluir Curso" (abre a confirmação)
    excluirModalBtn.addEventListener('click', () => {
        confirmationModal.style.display = 'flex';
    });

    // Botão "Cancelar" (na confirmação)
    cancelarBtn.addEventListener('click', () => {
        confirmationModal.style.display = 'none';
    });

    // Botão "Excluir" (final, na confirmação)
    excluirConfirmBtn.addEventListener('click', async () => {
        const id = modalCursoId.value;
        if (!id) return;

        const formData = new FormData();
        formData.append('cd_curso', id);
        
        excluirConfirmBtn.textContent = "Excluindo...";
        excluirConfirmBtn.disabled = true;

        try {
            const response = await fetch('../api/excluir_curso.php', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();

            if (response.ok && result.status === 'sucesso') {
                const index = cursosDaPagina.findIndex(c => String(c.cd_curso) === id);
                if (index > -1) {
                    cursosDaPagina.splice(index, 1);
                }
                document.getElementById(`curso-card-${id}`)?.remove();
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
    searchInput.addEventListener('input', filtrarCursos);
    renderizarCursos(cursosDaPagina);
});