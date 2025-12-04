
function showFeedback(message, type = 'sucesso') {
    const bar = document.getElementById('feedback-bar');
    if (!bar) return;
    bar.textContent = message;
    bar.className = `feedback-bar ${type} show`;
    setTimeout(() => { bar.classList.remove('show'); }, 3500);
}


document.addEventListener('DOMContentLoaded', () => {

    if (typeof professoresDaPagina === 'undefined' || typeof todasAsTurmas === 'undefined') {
        console.error("Variáveis de dados ('professoresDaPagina' ou 'todasAsTurmas') não foram encontradas.");
        return;
    }

    const searchInput = document.getElementById('search-prof');
    const cardContainer = document.getElementById('admin-card-container');
    const modalOverlay = document.getElementById('modal-overlay');
    

    const salvarBtn = document.querySelector('.salvar');
    const excluirModalBtn = document.querySelector('.excluir');
    const confirmationModal = document.getElementById('confirmation-modal');
    const cancelarBtn = document.querySelector('.cancelar');
    const excluirConfirmBtn = document.querySelector('.excluir-confirm');


    const modalProfId = document.getElementById('modal-prof-id');
    const modalProfNome = document.getElementById('modal-prof-nome');
    const modalProfRm = document.getElementById('modal-prof-rm');
    const modalProfEmail = document.getElementById('modal-prof-email');
    const modalProfTelefone = document.getElementById('modal-prof-telefone');
    const modalTurmasSelect = document.getElementById('modal-prof-turmas');

    let professorEmEdicao = null;
    let choicesTurmas = null;


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


            if (choicesTurmas) {
                choicesTurmas.destroy();
                choicesTurmas = null;
            }
            

            modalTurmasSelect.innerHTML = ''; 
            

            const turmasAtuaisMap = {};
            if (professorEmEdicao.turmas_associadas) {
                professorEmEdicao.turmas_associadas.split(', ').forEach(nomeTurma => {
                    turmasAtuaisMap[nomeTurma] = true;
                });
            }
            

            const turmasOptions = todasAsTurmas.map(turma => {
                const estaSelecionada = turmasAtuaisMap[turma.nm_turma] === true;
                
                return {
                    value: String(turma.cd_turma), 
                    label: turma.nm_turma,        
                    selected: estaSelecionada     
                };
            });
            

            choicesTurmas = new Choices(modalTurmasSelect, {
                removeItemButton: true,
                placeholder: true,
                placeholderValue: 'Selecione as turmas...',
                choices: turmasOptions,
                searchEnabled: true
            });

            
            modalOverlay.style.display = 'flex';
        }
    });

    salvarBtn.addEventListener('click', async () => {
        const id = modalProfId.value;
        const nome = modalProfNome.value;
        const email = modalProfEmail.value;
        const telefone = modalProfTelefone.value;
        const turmas = choicesTurmas.getValue(true);

        const formData = new FormData();
        formData.append('cd_usuario', id);
        formData.append('nome', nome);
        formData.append('email', email);
        formData.append('telefone', telefone);
        turmas.forEach(turmaId => {
            formData.append('turmas[]', turmaId);
        });

        salvarBtn.textContent = "Salvando...";
        salvarBtn.disabled = true;
        
        try {
            const response = await fetch('../api/atualizar_professor.php', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();

            if (response.ok && result.status === 'sucesso') {

                professorEmEdicao.nm_usuario = nome;
                professorEmEdicao.nm_email = email;
                professorEmEdicao.cd_telefone = telefone;
                professorEmEdicao.turmas_associadas = choicesTurmas.getValue(false).map(item => item.label).join(', ');


                const card = document.getElementById(`prof-card-${id}`);
                if(card) {
                    card.querySelector('h3').textContent = `${id} - ${nome}`;
                    card.querySelector('p b').nextSibling.textContent = ` ${professorEmEdicao.turmas_associadas || 'Nenhuma turma'}`;
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

    excluirModalBtn.addEventListener('click', () => { confirmationModal.style.display = 'flex'; });
    cancelarBtn.addEventListener('click', () => { confirmationModal.style.display = 'none'; });

    excluirConfirmBtn.addEventListener('click', async () => {
        const id = modalProfId.value;
        if (!id) return;
        const formData = new FormData();
        formData.append('cd_usuario', id);
        excluirConfirmBtn.textContent = "Excluindo...";
        excluirConfirmBtn.disabled = true;
        try {
            const response = await fetch('../api/excluir_professor.php', { method: 'POST', body: formData });
            const result = await response.json();
            if (response.ok && result.status === 'sucesso') {
                const index = professoresDaPagina.findIndex(p => p.cd_usuario === id);
                if (index > -1) { professoresDaPagina.splice(index, 1); }
                document.getElementById(`prof-card-${id}`)?.remove();
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

    confirmationModal.addEventListener('click', (e) => { if (e.target === confirmationModal) fecharModais(); });
    modalOverlay.addEventListener('click', (e) => { if (e.target === modalOverlay) fecharModais(); });


    searchInput.addEventListener('input', filtrarProfessores);
    renderizarProfessores(professoresDaPagina);
});