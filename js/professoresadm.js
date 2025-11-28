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

document.addEventListener('DOMContentLoaded', () => {
    // Garante que as pontes de dados existem
    if (typeof funcionariosDaPagina === 'undefined' || typeof todasAsTurmas === 'undefined' || typeof todosOsCursos === 'undefined') {
        console.error("Variáveis de dados (funcionariosDaPagina, todasAsTurmas ou todosOsCursos) não foram encontradas.");
        return;
    }

    // --- Elementos Comuns ---
    const searchInput = document.getElementById('search-prof');
    const cardContainer = document.getElementById('admin-card-container');
    const modalOverlay = document.getElementById('modal-overlay');
    const salvarBtn = document.querySelector('.salvar');
    const excluirModalBtn = document.querySelector('.excluir');
    const confirmationModal = document.getElementById('confirmation-modal');
    const cancelarBtn = document.querySelector('.cancelar');
    const excluirConfirmBtn = document.querySelector('.excluir-confirm');

    // --- Campos do Modal ---
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
    let choicesAssoc = null; // Instância ativa do Choices.js (Turma ou Curso)
    const cardAdicionar = document.querySelector('.card-adicionar');


    /**
     * Função para "desenhar" os cards (CORRIGIDO PARA USAR CONTAGEM)
     */
    function renderizarFuncionarios(listaFuncionarios) {
        cardContainer.querySelectorAll('.admin-card:not(.card-adicionar)').forEach(card => card.remove());
        cardContainer.querySelector('.sem-eventos')?.remove();

        if (listaFuncionarios.length === 0) {
            if (!cardContainer.querySelector('.sem-eventos') && cardAdicionar) {
                const p = document.createElement('p');
                p.className = 'sem-eventos';
                p.textContent = 'Nenhum funcionário encontrado com esse filtro.';
                // Garante que a mensagem ocupe a largura total do grid
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
                // CHAVE: Calcula a contagem usando o novo separador ' | '
                const numAssoc = assocNomes ? assocNomes.split(' | ').filter(n => n.trim() !== '').length : 0;
                assocInfo = `<b>Turmas:</b> ${numAssoc === 0 ? 'Nenhuma' : numAssoc}`;
            } else if (funcionario.tipo_usuario === 'Coordenador') {
                assocNomes = funcionario.cursos_associados_nomes || '';
                 // CHAVE: Calcula a contagem usando o novo separador ' | '
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

    /**
     * Função para filtrar
     */
    function filtrarFuncionarios() {
        const termoBusca = searchInput.value.toLowerCase();
        const funcionariosFiltrados = funcionariosDaPagina.filter(func => {
            const nome = func.nm_usuario.toLowerCase();
            const rm = func.cd_usuario.toLowerCase();
            const cargo = func.tipo_usuario.toLowerCase();
            // CHAVE: Filtra usando a string de nomes completa (separada por ' | ')
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

    /**
     * Prepara o dropdown (Choices.js) baseado no tipo de usuário (Prof/Coord)
     * CORRIGIDO: Limpa o SELECT antes de criar a nova instância.
     */
    function setupChoicesDropdown(tipoUsuario, associacoesAtuaisString) {
        // 1. Destrói a instância anterior se existir
        if (choicesAssoc) {
            choicesAssoc.destroy();
            choicesAssoc = null;
        }

        // 2. Define o <select> a ser usado, o array de dados e a chave de associação
        let selectElement = (tipoUsuario === 'Professor') ? modalTurmasSelect : modalCursosSelect;
        let dataArray = (tipoUsuario === 'Professor') ? todasAsTurmas : todosOsCursos;
        let idKey = (tipoUsuario === 'Professor') ? 'cd_turma' : 'cd_curso';
        let labelKey = (tipoUsuario === 'Professor') ? 'nm_turma' : 'nm_curso';
        let placeholderText = (tipoUsuario === 'Professor') ? 'Selecione as turmas...' : 'Selecione os cursos...';
        
        // CHAVE DE CORREÇÃO DE DUPLICAÇÃO/VAZAMENTO: Limpa o conteúdo do SELECT (se não tiver Choices.js)
        selectElement.innerHTML = ''; 

        // 3. Monta o mapa de associações atuais (string para array)
        const assocMap = {};
        // CHAVE: Usa o novo separador ' | '
        if (associacoesAtuaisString) {
             associacoesAtuaisString.split(' | ').forEach(itemNome => {
                // Remove espaços em branco antes/depois do nome
                assocMap[itemNome.trim()] = true; 
            });
        }
        
        // 4. Prepara as opções para o Choices.js
        const options = dataArray.map(item => {
            // Verifica se o NOME do item está no mapa de associações atuais
            const estaSelecionada = assocMap[item[labelKey].trim()] === true;
            
            return {
                value: String(item[idKey]), 
                label: item[labelKey],         
                selected: estaSelecionada      
            };
        });

        // 5. Inicializa o Choices.js
        choicesAssoc = new Choices(selectElement, {
            removeItemButton: true,
            placeholder: true,
            placeholderValue: placeholderText,
            choices: options, 
            searchEnabled: true
        });
        
        // 6. Mostra/Esconde o container correto
        dropdownTurmasContainer.style.display = (tipoUsuario === 'Professor') ? 'block' : 'none';
        dropdownCursosContainer.style.display = (tipoUsuario === 'Coordenador') ? 'block' : 'none';
    }


    // --- LÓGICA DOS EVENT LISTENERS ---

    // Abre o Modal de Edição
    cardContainer.addEventListener('click', (e) => {
        const botaoEditar = e.target.closest('button.btn-editar'); 
        if (!botaoEditar) return;
        const userId = botaoEditar.dataset.id;
        const userType = botaoEditar.dataset.tipo;
        
        // CORREÇÃO: Encontra o objeto mais recente na lista
        funcionarioEmEdicao = funcionariosDaPagina.find(f => f.cd_usuario === userId);
        
        if (funcionarioEmEdicao) {
            // Preenche campos de texto
            modalUserId.value = funcionarioEmEdicao.cd_usuario;
            modalUserNome.value = funcionarioEmEdicao.nm_usuario;
            modalUserRm.value = funcionarioEmEdicao.cd_usuario;
            modalUserEmail.value = funcionarioEmEdicao.nm_email;
            modalUserTelefone.value = funcionarioEmEdicao.cd_telefone || ''; 
            document.getElementById('modal-titulo-edicao').textContent = `Editar ${funcionarioEmEdicao.tipo_usuario}`;

            // Configura o dropdown dinâmico
            // CHAVE: Pega a string de nomes
            const associacoesString = (userType === 'Professor') ? funcionarioEmEdicao.turmas_associadas_nomes : funcionarioEmEdicao.cursos_associados_nomes;
            setupChoicesDropdown(userType, associacoesString);
            
            modalOverlay.style.display = 'flex';
        }
    });

    // Botão "Salvar Alterações"
    salvarBtn.addEventListener('click', async () => {
        const id = modalUserId.value;
        const nome = modalUserNome.value;
        const email = modalUserEmail.value;
        const telefone = modalUserTelefone.value;
        const tipo = funcionarioEmEdicao.tipo_usuario;
        
        // Captura as associações corretas
        const associacoesSelecionadas = choicesAssoc ? choicesAssoc.getValue(true) : [];
        
        const formData = new FormData();
        formData.append('cd_usuario', id);
        formData.append('nome', nome);
        formData.append('email', email);
        formData.append('telefone', telefone);
        
        // Define o endpoint e o parâmetro de associações
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
                
                // Atualiza o objeto na memória do JS
                funcionarioEmEdicao.nm_usuario = nome;
                funcionarioEmEdicao.nm_email = email;
                funcionarioEmEdicao.cd_telefone = telefone;
                
                // CHAVE: Atualiza o campo correto com o novo separador ' | ' para refletir as alterações
                const nomesAssociados = choicesAssoc.getValue(false).map(item => item.label).join(' | ');
                
                if (tipo === 'Professor') {
                    // ATUALIZA O NOME DO CAMPO NA MEMÓRIA
                    funcionarioEmEdicao.turmas_associadas_nomes = nomesAssociados; 
                } else {
                    // ATUALIZA O NOME DO CAMPO NA MEMÓRIA
                    funcionarioEmEdicao.cursos_associados_nomes = nomesAssociados;
                }
                
                // Re-renderiza a lista para refletir as mudanças no card
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

    // --- Lógica de Exclusão ---
    excluirModalBtn.addEventListener('click', () => { confirmationModal.style.display = 'flex'; });
    cancelarBtn.addEventListener('click', () => { confirmationModal.style.display = 'none'; });

    excluirConfirmBtn.addEventListener('click', async () => {
        const id = modalUserId.value;
        const tipo = funcionarioEmEdicao.tipo_usuario;
        if (!id) return;
        
        // Coordenadores/Professores usam o mesmo endpoint de exclusão de professor
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

    // Fechar modais
    confirmationModal.addEventListener('click', (e) => { if (e.target === confirmationModal) fecharModais(); });
    modalOverlay.addEventListener('click', (e) => { if (e.target === modalOverlay) fecharModais(); });

    // --- INICIALIZAÇÃO ---
    searchInput.addEventListener('input', filtrarFuncionarios);
    renderizarFuncionarios(funcionariosDaPagina);
});