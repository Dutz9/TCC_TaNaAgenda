document.addEventListener('DOMContentLoaded', function() {
    // Garante que a 'ponte' de dados do PHP existe
    if (typeof relacaoTurmaProfessor === 'undefined') {
        console.error("A variável 'relacaoTurmaProfessor' não foi encontrada.");
        return;
    }

    // --- Elementos do Formulário ---
    const turmasElement = document.getElementById('selecao-turmas');
    const displayProfessores = document.getElementById('display-professores');
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
    let professorParaRemover = null; // Variável para guardar o item a ser removido

    // --- 1. LÓGICA DO SELETOR DE TURMAS (Choices.js) ---
    if (turmasElement) {
        const selectTurmas = new Choices(turmasElement, {
            removeItemButton: true,
            placeholder: true,
            placeholderValue: 'Clique para selecionar ou digite para buscar...',
            fuseOptions: {
                keys: ['label'],
                threshold: 0.3
            }
        });

        // "Escuta" mudanças nas turmas para atualizar a lista de professores
        selectTurmas.passedElement.element.addEventListener('change', function() {
            const turmasSelecionadasIds = Array.from(this.selectedOptions).map(option => option.value);
            const professoresParaExibir = {};

            turmasSelecionadasIds.forEach(turmaId => {
                if (relacaoTurmaProfessor[turmaId]) {
                    relacaoTurmaProfessor[turmaId].forEach(prof => {
                        professoresParaExibir[prof.id] = prof; // Salva o objeto {id, nome}
                    });
                }
            });
            
            displayProfessores.innerHTML = ''; // Limpa a lista visual
            // Remove inputs hidden antigos
            form.querySelectorAll('input[name="professores_notificar[]"]').forEach(input => input.remove());

            const professores = Object.values(professoresParaExibir);

            if (professores.length > 0) {
                professores.forEach(prof => {
                    // Cria o item visual na lista
                    const profItem = document.createElement('div');
                    profItem.className = 'professor-item';
                    profItem.dataset.id = prof.id;
                    profItem.innerHTML = `<p>${prof.nome}</p><span class="remove-prof-btn" data-nome="${prof.nome}" title="Remover professor da lista">&times;</span>`;
                    displayProfessores.appendChild(profItem);

                    // Cria um input escondido para cada professor
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'professores_notificar[]';
                    hiddenInput.value = prof.id;
                    hiddenInput.id = `hidden-prof-${prof.id}`;
                    form.appendChild(hiddenInput); // Adiciona o input ao formulário
                });
            } else {
                displayProfessores.innerHTML = '<p>Selecione uma ou mais turmas...</p>';
            }
        });
    }

    // --- 2. LÓGICA DE REMOÇÃO DE PROFESSOR (COM CONFIRMAÇÃO) ---
    if (displayProfessores) {
        // "Escutador" de cliques na área dos professores
        displayProfessores.addEventListener('click', (e) => {
            // Se o clique foi no botão "x"
            if (e.target.classList.contains('remove-prof-btn')) {
                professorParaRemover = e.target.closest('.professor-item'); // Guarda o elemento a ser removido
                modalConfirmText.textContent = e.target.dataset.nome; // Coloca o nome do professor no modal
                modalConfirm.style.display = 'flex'; // Mostra o modal
            }
        });
    }

    // Ação do botão "NÃO" no modal
    if (btnConfirmNo) {
        btnConfirmNo.addEventListener('click', () => {
            modalConfirm.style.display = 'none';
            professorParaRemover = null; // Limpa a variável
        });
    }

    // Ação do botão "SIM" no modal
    if (btnConfirmYes) {
        btnConfirmYes.addEventListener('click', () => {
            if (professorParaRemover) {
                const profId = professorParaRemover.dataset.id;
                // Remove o input escondido correspondente
                document.getElementById(`hidden-prof-${profId}`)?.remove();
                // Remove o item visual da lista
                professorParaRemover.remove();
            }
            modalConfirm.style.display = 'none';
            professorParaRemover = null;
        });
    }
    
    // Fecha o modal se clicar fora
    if(modalConfirm) {
        modalConfirm.addEventListener('click', (e) => {
            if(e.target === modalConfirm) {
                modalConfirm.style.display = 'none';
                professorParaRemover = null;
            }
        });
    }

    // --- 3. VALIDAÇÃO DE DATA MÍNIMA ---
    if (inputData) {
        const hoje = new Date();
        hoje.setMinutes(hoje.getMinutes() - hoje.getTimezoneOffset());
        inputData.setAttribute('min', hoje.toISOString().split('T')[0]);
    }

    // --- 4. VALIDAÇÃO DINÂMICA DE HORA DE FIM ---
    if (horarioInicioElem && horarioFimElem) {
        const ajustarHorarioFim = () => {
            const horaInicioSelecionada = horarioInicioElem.value;
            if (!horaInicioSelecionada) {
                for (const option of horarioFimElem.options) {
                    if (option.value) option.disabled = true;
                }
                return;
            }
            
            for (const option of horarioFimElem.options) {
                if (option.value) {
                    option.disabled = (option.value <= horaInicioSelecionada);
                }
            }

            if (horarioFimElem.options[horarioFimElem.selectedIndex]?.disabled) {
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