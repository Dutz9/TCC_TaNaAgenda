
function showFeedback(message, type = 'sucesso') {
    const bar = document.getElementById('feedback-bar');
    if (!bar) return;
    bar.textContent = message;
    bar.className = `feedback-bar ${type} show`;
    setTimeout(() => { bar.classList.remove('show'); }, 3500);
}


document.addEventListener('DOMContentLoaded', () => {

    if (typeof cursosDaPagina === 'undefined') {
        console.error("A variável 'cursosDaPagina' não foi encontrada.");
        return;
    }


    const searchInput = document.getElementById('search-curso');
    const cardContainer = document.getElementById('admin-card-container');
    const modalOverlay = document.getElementById('modal-overlay');
    

    const salvarBtn = document.querySelector('.salvar');
    const excluirModalBtn = document.querySelector('.excluir');
    const confirmationModal = document.getElementById('confirmation-modal');
    const cancelarBtn = document.querySelector('.cancelar');
    const excluirConfirmBtn = document.querySelector('.excluir-confirm');


    const modalCursoId = document.getElementById('modal-curso-id');
    const modalCursoNome = document.getElementById('modal-curso-nome');
    const modalCursoTurmas = document.getElementById('modal-curso-turmas');
    const modalCursoPeriodo = document.getElementById('modal-curso-periodo');
    const modalCursoDuracao = document.getElementById('modal-curso-duracao'); 
    const modalListaCoords = document.getElementById('coordenadores-vinculados-lista');
    
    let cursoEmEdicao = null;


    function renderizarCursos(listaCursos) {

        cardContainer.querySelectorAll('.admin-card:not(.card-adicionar)').forEach(card => card.remove());
        cardContainer.querySelector('.sem-eventos')?.remove();

        if (listaCursos.length === 0) {
            if (!cardContainer.querySelector('.sem-eventos')) {
                const p = document.createElement('p');
                p.className = 'sem-eventos';
                p.textContent = 'Nenhum curso encontrado com esse filtro.';
                p.style.gridColumn = '1 / -1'; 
                cardContainer.appendChild(p);
            }
        }

        for (const curso of listaCursos) {
            const card = document.createElement('div');
            card.className = 'admin-card'; 
            card.id = `curso-card-${curso.cd_curso}`;

            const numCoords = parseInt(curso.contagem_coordenadores, 10);
            const coordDisplay = `<b>Coordenadores:</b> ${numCoords === 0 || isNaN(numCoords) ? 'N/A' : numCoords}`;
            

            card.innerHTML = `
                <div class="curso-infos">
                    <h3>${curso.nm_curso} (${curso.ic_periodo})</h3>
                    <p><b>Turmas:</b> ${curso.contagem_turmas || '0'}</p>
                    <p>${coordDisplay}</p> <!-- CHAVE: AGORA EXIBE A CONTAGEM -->
                </div>
                <button class="admin-btn btn-editar" data-id="${curso.cd_curso}">Editar</button>
            `;
            cardContainer.appendChild(card);
        }
    }


    function filtrarCursos() {
        const termoBusca = searchInput.value.toLowerCase();
        
        const cursosFiltrados = cursosDaPagina.filter(curso => {
            const nome = curso.nm_curso.toLowerCase();
            const periodo = (curso.ic_periodo || '').toLowerCase();
            const coordenadores = (curso.coordenadores_associados || '').toLowerCase(); // Filtra por nome no modal
            
            return nome.includes(termoBusca) || periodo.includes(termoBusca) || coordenadores.includes(termoBusca);
        });
        
        renderizarCursos(cursosFiltrados);
    }
    
    function fecharModais() {
        modalOverlay.style.display = 'none';
        confirmationModal.style.display = 'none';
        cursoEmEdicao = null;
    }

    function exibirCoordenadoresModal(curso) { 

        const coordenadoresNomes = curso.coordenadores_associados;
        modalListaCoords.innerHTML = '';
        

        if (!coordenadoresNomes || coordenadoresNomes.trim() === '') {
            modalListaCoords.innerHTML = '<p>Nenhum coordenador vinculado a este curso.</p>';
            return;
        }

        const listaNomes = coordenadoresNomes.split(', ');
        

        listaNomes.forEach(nome => {
            if (nome.trim() !== '') {
                const item = document.createElement('div');
                item.className = 'response-item';
                item.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                        <path fill="#000000" d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512H418.3c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304H178.3z"/>
                    </svg>
                    <div><p>${nome}</p></div>
                `;
                modalListaCoords.appendChild(item);
            }
        });
    }

    cardContainer.addEventListener('click', (e) => {
        const botaoEditar = e.target.closest('button.btn-editar'); 
        if (!botaoEditar) return;
        const cursoId = botaoEditar.dataset.id;
        
        cursoEmEdicao = cursosDaPagina.find(c => String(c.cd_curso) === cursoId);
        
        if (cursoEmEdicao) {
            modalCursoId.value = cursoEmEdicao.cd_curso;
            modalCursoNome.value = cursoEmEdicao.nm_curso;
            modalCursoTurmas.value = cursoEmEdicao.contagem_turmas;
            modalCursoPeriodo.value = cursoEmEdicao.ic_periodo;
            

            if (modalCursoDuracao) modalCursoDuracao.value = '3 Módulos/Anos'; 
            
            exibirCoordenadoresModal(cursoEmEdicao); 
            
            modalOverlay.style.display = 'flex';
        }
    });

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
                

                showFeedback(result.mensagem, 'sucesso');
                setTimeout(() => {
                    window.location.reload(); 
                }, 500); 

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

    excluirModalBtn.addEventListener('click', () => {
        confirmationModal.style.display = 'flex';
    });

    cancelarBtn.addEventListener('click', () => {
        confirmationModal.style.display = 'none';
    });

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
                
                showFeedback(result.mensagem, 'sucesso');
                 setTimeout(() => {
                    window.location.reload(); 
                }, 500); 
                
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

    confirmationModal.addEventListener('click', (e) => { if (e.target === confirmationModal) fecharModais(); });
    modalOverlay.addEventListener('click', (e) => { if (e.target === modalOverlay) fecharModais(); });

    searchInput.addEventListener('input', filtrarCursos);
    renderizarCursos(cursosDaPagina);
});