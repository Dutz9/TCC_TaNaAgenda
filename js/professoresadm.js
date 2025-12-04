
function showFeedback(message, type = 'sucesso') {
    const bar = document.getElementById('feedback-bar');
    if (!bar) return;
    bar.textContent = message;
    bar.className = `feedback-bar ${type} show`;
    setTimeout(() => { bar.classList.remove('show'); }, 3500);
}

document.addEventListener('DOMContentLoaded', () => {

    if (typeof funcionariosDaPagina === 'undefined' || typeof todasAsTurmas === 'undefined' || typeof todosOsCursos === 'undefined') {
        console.error("Variáveis de dados (funcionariosDaPagina, todasAsTurmas ou todosOsCursos) não foram encontradas.");
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


    const modalUserId = document.getElementById('modal-user-id');
    const modalUserNome = document.getElementById('modal-user-nome');
    const modalUserRm = document.getElementById('modal-user-rm');
    const modalUserEmail = document.getElementById('modal-user-email');
    const modalUserTelefone = document.getElementById('modal-user-telefone');
    
    const dropdownTurmasContainer = document.getElementById('dropdown-turmas-container');
    const modalTurmasSelect = document.getElementById('modal-user-turmas');
    
    const dropdownCursosContainer = document.getElementById('dropdown-cursos-container');
    const modalCursosSelect = document.getElementById('modal-user-cursos');

    let funcionarioEmEdicao = null;
    let choicesAssoc = null;
    const cardAdicionar = document.querySelector('.card-adicionar');



    function renderizarFuncionarios(listaFuncionarios) {
        cardContainer.querySelectorAll('.admin-card:not(.card-adicionar)').forEach(card => card.remove());
        cardContainer.querySelector('.sem-eventos')?.remove();

        if (listaFuncionarios.length === 0) {
            if (!cardContainer.querySelector('.sem-eventos') && cardAdicionar) {
                const p = document.createElement('p');
                p.className = 'sem-eventos';
                p.textContent = 'Nenhum funcionário encontrado com esse filtro.';

                p.style.gridColumn = '1 / -1'; 
                cardContainer.appendChild(p);
            }
        }

        for (const funcionario of listaFuncionarios) {
            const card = document.createElement('div');
            card.className = 'admin-card';
            card.id = `user-card-${funcionario.cd_usuario}`;
            
            let assocInfo = '';
            let assocNomes = '';
            
            if (funcionario.tipo_usuario === 'Professor') {
                assocNomes = funcionario.turmas_associadas_nomes || '';

                const numAssoc = assocNomes ? assocNomes.split(' | ').filter(n => n.trim() !== '').length : 0;
                assocInfo = `<b>Turmas:</b> ${numAssoc === 0 ? 'Nenhuma' : numAssoc}`;
            } else if (funcionario.tipo_usuario === 'Coordenador') {
                assocNomes = funcionario.cursos_associados_nomes || '';

                const numAssoc = assocNomes ? assocNomes.split(' | ').filter(n => n.trim() !== '').length : 0;
                assocInfo = `<b>Cursos:</b> ${numAssoc === 0 ? 'Nenhum' : numAssoc}`;
            }
            
            card.innerHTML = `
                <div class="prof-infos">
                    <h3>${funcionario.cd_usuario} - ${funcionario.nm_usuario}</h3>
                    <p><b>Cargo:</b> ${funcionario.tipo_usuario}</p>
                    <p>${assocInfo}</p>
                </div>
                <button class="admin-btn btn-editar" data-id="${funcionario.cd_usuario}" data-tipo="${funcionario.tipo_usuario}">Editar</button>
            `;
            cardContainer.appendChild(card);
        }
    }

    
    function filtrarFuncionarios() {
        const termoBusca = searchInput.value.toLowerCase();
        const funcionariosFiltrados = funcionariosDaPagina.filter(func => {
            const nome = func.nm_usuario.toLowerCase();
            const rm = func.cd_usuario.toLowerCase();
            const cargo = func.tipo_usuario.toLowerCase();

            const turmas = (func.turmas_associadas_nomes || '').toLowerCase();
            const cursos = (func.cursos_associados_nomes || '').toLowerCase();
            
            return nome.includes(termoBusca) || rm.includes(termoBusca) || cargo.includes(termoBusca) || turmas.includes(termoBusca) || cursos.includes(termoBusca);
        });
        renderizarFuncionarios(funcionariosFiltrados);
    }
    
    function fecharModais() {
        modalOverlay.style.display = 'none';
        confirmationModal.style.display = 'none';
        funcionarioEmEdicao = null;
    }


    function setupChoicesDropdown(tipoUsuario, associacoesAtuaisString) {

        if (choicesAssoc) {
            choicesAssoc.destroy();
            choicesAssoc = null;
        }

        let selectElement = (tipoUsuario === 'Professor') ? modalTurmasSelect : modalCursosSelect;
        let dataArray = (tipoUsuario === 'Professor') ? todasAsTurmas : todosOsCursos;
        let idKey = (tipoUsuario === 'Professor') ? 'cd_turma' : 'cd_curso';
        let labelKey = (tipoUsuario === 'Professor') ? 'nm_turma' : 'nm_curso';
        let placeholderText = (tipoUsuario === 'Professor') ? 'Selecione as turmas...' : 'Selecione os cursos...';
        

        selectElement.innerHTML = ''; 


        const assocMap = {};

        if (associacoesAtuaisString) {
             associacoesAtuaisString.split(' | ').forEach(itemNome => {
     
                assocMap[itemNome.trim()] = true; 
            });
        }
        

        const options = dataArray.map(item => {
  
            const estaSelecionada = assocMap[item[labelKey].trim()] === true;
            
            return {
                value: String(item[idKey]), 
                label: item[labelKey],         
                selected: estaSelecionada      
            };
        });

    
        choicesAssoc = new Choices(selectElement, {
            removeItemButton: true,
            placeholder: true,
            placeholderValue: placeholderText,
            choices: options, 
            searchEnabled: true
        });
        

        dropdownTurmasContainer.style.display = (tipoUsuario === 'Professor') ? 'block' : 'none';
        dropdownCursosContainer.style.display = (tipoUsuario === 'Coordenador') ? 'block' : 'none';
    }


    cardContainer.addEventListener('click', (e) => {
        const botaoEditar = e.target.closest('button.btn-editar'); 
        if (!botaoEditar) return;
        const userId = botaoEditar.dataset.id;
        const userType = botaoEditar.dataset.tipo;
        

        funcionarioEmEdicao = funcionariosDaPagina.find(f => f.cd_usuario === userId);
        
        if (funcionarioEmEdicao) {

            modalUserId.value = funcionarioEmEdicao.cd_usuario;
            modalUserNome.value = funcionarioEmEdicao.nm_usuario;
            modalUserRm.value = funcionarioEmEdicao.cd_usuario;
            modalUserEmail.value = funcionarioEmEdicao.nm_email;
            modalUserTelefone.value = funcionarioEmEdicao.cd_telefone || ''; 
            document.getElementById('modal-titulo-edicao').textContent = `Editar ${funcionarioEmEdicao.tipo_usuario}`;

            const associacoesString = (userType === 'Professor') ? funcionarioEmEdicao.turmas_associadas_nomes : funcionarioEmEdicao.cursos_associados_nomes;
            setupChoicesDropdown(userType, associacoesString);
            
            modalOverlay.style.display = 'flex';
        }
    });


    salvarBtn.addEventListener('click', async () => {
        const id = modalUserId.value;
        const nome = modalUserNome.value;
        const email = modalUserEmail.value;
        const telefone = modalUserTelefone.value;
        const tipo = funcionarioEmEdicao.tipo_usuario;
        

        const associacoesSelecionadas = choicesAssoc ? choicesAssoc.getValue(true) : [];
        
        const formData = new FormData();
        formData.append('cd_usuario', id);
        formData.append('nome', nome);
        formData.append('email', email);
        formData.append('telefone', telefone);
        

        let endpoint = '';
        let assocParamName = '';

        if (tipo === 'Professor') {
            endpoint = '../api/atualizar_professor.php';
            assocParamName = 'turmas[]';
        } else if (tipo === 'Coordenador') {
            endpoint = '../api/atualizar_coordenador.php';
            assocParamName = 'cursos[]';
        } else {
            showFeedback('Erro: Tipo de usuário inválido para edição.', 'erro');
            return;
        }

        associacoesSelecionadas.forEach(assocId => {
            formData.append(assocParamName, assocId);
        });


        salvarBtn.textContent = "Salvando...";
        salvarBtn.disabled = true;
        
        try {
            const response = await fetch(endpoint, {
                method: 'POST',
                body: formData
            });
            const result = await response.json();

            if (response.ok && result.status === 'sucesso') {
                

                funcionarioEmEdicao.nm_usuario = nome;
                funcionarioEmEdicao.nm_email = email;
                funcionarioEmEdicao.cd_telefone = telefone;

                const nomesAssociados = choicesAssoc.getValue(false).map(item => item.label).join(' | ');
                
                if (tipo === 'Professor') {

                    funcionarioEmEdicao.turmas_associadas_nomes = nomesAssociados; 
                } else {

                    funcionarioEmEdicao.cursos_associados_nomes = nomesAssociados;
                }
                

                renderizarFuncionarios(funcionariosDaPagina);
                
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
        const id = modalUserId.value;
        const tipo = funcionarioEmEdicao.tipo_usuario;
        if (!id) return;
        

        const endpoint = '../api/excluir_professor.php'; 
        
        if(tipo === 'Administrador') {
             showFeedback('Administradores devem ser excluídos através do módulo Administradores.', 'erro');
             confirmationModal.style.display = 'none';
             return;
        }
        
        excluirConfirmBtn.textContent = "Excluindo...";
        excluirConfirmBtn.disabled = true;
        
        try {
            const formData = new FormData();
            formData.append('cd_usuario', id);
            
            const response = await fetch(endpoint, { method: 'POST', body: formData });
            const result = await response.json();
            
            if (response.ok && result.status === 'sucesso') {
                const index = funcionariosDaPagina.findIndex(f => f.cd_usuario === id);
                if (index > -1) { funcionariosDaPagina.splice(index, 1); }
                document.getElementById(`user-card-${id}`)?.remove();
                showFeedback(`Usuário (${tipo}) excluído com sucesso!`, 'sucesso');
                fecharModais();
            } else {
                showFeedback(result.mensagem || 'Não foi possível excluir. Verifique se o usuário criou eventos.', 'erro');
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


    searchInput.addEventListener('input', filtrarFuncionarios);
    renderizarFuncionarios(funcionariosDaPagina);
});