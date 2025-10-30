document.addEventListener('DOMContentLoaded', function() {
    // Garante que as 'pontes' de dados do PHP existem
    if (typeof relacaoTurmaProfessor === 'undefined' || typeof usuario_logado === 'undefined' || typeof mapaAlunosTurma === 'undefined' || typeof modoEdicao === 'undefined' || typeof professoresSelecionados === 'undefined') {
        console.error("Uma ou mais variáveis de dados (PHP) não foram encontradas. Verifique o <script> no arquivo PHP.");
        return;
    }

    // --- Elementos do Formulário ---
    const turmasElement = document.getElementById('selecao-turmas');
    const displayProfessores = document.getElementById('display-professores');
    const displayTotalAlunos = document.getElementById('display-total-alunos');
    const form = document.querySelector('.formulario-evento form');
    const inputData = document.getElementById('data');
    const horarioInicioElem = document.getElementById('horario_inicio');
    const horarioFimElem = document.getElementById('horario_fim');
    const inputTitulo = document.getElementById('titulo');

    // --- Elementos do Modal de Confirmação ---
    const modalConfirm = document.getElementById('modal-confirm-remove-prof');
    const modalConfirmText = document.getElementById('modal-prof-name');
    const btnConfirmYes = document.getElementById('btn-confirm-yes');
    const btnConfirmNo = document.getElementById('btn-confirm-no');
    let professorParaRemover = null;

    // --- 1. LÓGICA DO SELETOR DE TURMAS (Choices.js) ---
    if (turmasElement) {
        const selectTurmas = new Choices(turmasElement, {
            removeItemButton: true,
            placeholder: true,
            placeholderValue: 'Clique para selecionar ou digite para buscar...',
            fuseOptions: { keys: ['label'], threshold: 0.3 }
        });

        // "Escuta" mudanças nas turmas para atualizar professores e alunos
        selectTurmas.passedElement.element.addEventListener('change', function() {
            const turmasSelecionadasIds = Array.from(this.selectedOptions).map(option => option.value);
            const professoresParaExibir = {};
            let totalAlunos = 0;

            form.querySelectorAll('input[name="professores_notificar[]"]').forEach(input => input.remove());
            displayProfessores.innerHTML = '';

            turmasSelecionadasIds.forEach(turmaId => {
                // Soma os alunos
                if (mapaAlunosTurma[turmaId]) {
                    totalAlunos += parseInt(mapaAlunosTurma[turmaId], 10);
                }
                
                // Monta a lista de professores
                if (relacaoTurmaProfessor[turmaId]) {
                    relacaoTurmaProfessor[turmaId].forEach(prof => {
                        // Filtra o próprio usuário logado
                        if (prof.id !== usuario_logado.cd_usuario) { 
                            professoresParaExibir[prof.id] = prof;
                        }
                    });
                }
            });
            
            displayTotalAlunos.value = totalAlunos;
            const professores = Object.values(professoresParaExibir);

            if (professores.length > 0) {
                professores.forEach(prof => {
                    // --- LÓGICA DE EDIÇÃO ---
                    // Se estiver criando (não-edição), OU se estiver editando E o prof estava na lista, mostre-o.
                    if (!modoEdicao || (modoEdicao && professoresSelecionados[prof.id])) {
                        const profItem = document.createElement('div');
                        profItem.className = 'professor-item';
                        profItem.dataset.id = prof.id;
                        profItem.innerHTML = `<p>${prof.nome}</p><span class="remove-prof-btn" data-nome="${prof.nome}" title="Remover professor da lista">&times;</span>`;
                        displayProfessores.appendChild(profItem);

                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = 'professores_notificar[]';
                        hiddenInput.value = prof.id;
                        hiddenInput.id = `hidden-prof-${prof.id}`;
                        form.appendChild(hiddenInput);
                    }
                });
            } else {
                displayProfessores.innerHTML = '<p>Selecione uma ou mais turmas...</p>';
            }
        });

        // --- ATIVAÇÃO DO MODO DE EDIÇÃO ---
        if (modoEdicao) {
            // Dispara manualmente o evento 'change' para forçar o JS a ler as turmas
            // que o PHP pré-selecionou, e assim preencher a contagem de alunos e a lista de professores.
            selectTurmas.passedElement.element.dispatchEvent(new Event('change'));
        }
    }

    // --- 2. LÓGICA DE REMOÇÃO DE PROFESSOR (COM CONFIRMAÇÃO) ---
    if (displayProfessores) {
        displayProfessores.addEventListener('click', (e) => {
            if (e.target.classList.contains('remove-prof-btn')) {
                professorParaRemover = e.target.closest('.professor-item');
                modalConfirmText.textContent = e.target.dataset.nome;
                modalConfirm.style.display = 'flex';
            }
        });
    }
    
    const fecharModal = () => {
        if (modalConfirm) modalConfirm.style.display = 'none';
        professorParaRemover = null;
    };
    if (btnConfirmNo) btnConfirmNo.addEventListener('click', fecharModal);
    if (modalConfirm) modalConfirm.addEventListener('click', (e) => { if (e.target === modalConfirm) fecharModal(); });

    if (btnConfirmYes) {
        btnConfirmYes.addEventListener('click', () => {
            if (professorParaRemover) {
                const profId = professorParaRemover.dataset.id;
                document.getElementById(`hidden-prof-${profId}`)?.remove();
                professorParaRemover.remove();
            }
            fecharModal();
        });
    }

    // --- 3. VALIDAÇÃO DE DATA MÍNIMA ---
    if (inputData) {
        const hoje = new Date();
        hoje.setMinutes(hoje.getMinutes() - hoje.getTimezoneOffset());
        const dataMinima = hoje.toISOString().split('T')[0];
        
        // No modo de edição, não podemos definir uma data mínima que seja posterior à data já salva
        if (modoEdicao && inputData.value < dataMinima) {
             // Se o evento é antigo, não define data mínima
        } else {
            inputData.setAttribute('min', dataMinima);
        }
    }

    // --- 4. VALIDAÇÃO DINÂMICA DE HORA DE FIM ---
    if (horarioInicioElem && horarioFimElem) {
        const ajustarHorarioFim = () => {
            const horaInicioSelecionada = horarioInicioElem.value;
            if (!horaInicioSelecionada) {
                for (const option of horarioFimElem.options) {
                    if (option.value) option.disabled = true;
                }
                horarioFimElem.value = "";
                return;
            }
            
            for (const option of horarioFimElem.options) {
                if (option.value) {
                    option.disabled = (option.value <= horaInicioSelecionada);
                }
            }

            // Se a opção selecionada (do modo de edição) for desabilitada, não faz nada
            // Mas se o usuário MUDAR o início, e o fim se tornar inválido, ele limpa.
            if (!modoEdicao && horarioFimElem.options[horarioFimElem.selectedIndex]?.disabled) {
                horarioFimElem.value = "";
            }
        };
        horarioInicioElem.addEventListener('change', ajustarHorarioFim);
        ajustarHorarioFim();
    }
    
    // --- 5. CONTADOR DE CARACTERES DO TÍTULO ---
    if (inputTitulo && inputTitulo.maxLength > 0) {
        const contador = document.createElement('small');
        contador.id = 'titulo-contador';
        contador.style.cssText = 'color: #888; font-size: 0.8em; margin-top: 5px; display: block;';
        inputTitulo.parentNode.appendChild(contador);
        const maxLength = inputTitulo.getAttribute('maxlength');
        
        const updateCharCounter = () => {
            const currentLength = inputTitulo.value.length;
            const remaining = maxLength - currentLength;
            contador.textContent = `${remaining} caracteres restantes`;
        };
        inputTitulo.addEventListener('input', updateCharCounter);
        updateCharCounter();
    }
});