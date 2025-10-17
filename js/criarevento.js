document.addEventListener('DOMContentLoaded', function() {
    // Garante que a 'ponte' de dados do PHP existe
    if (typeof relacaoTurmaProfessor === 'undefined') {
        console.error("A variável 'relacaoTurmaProfessor' não foi encontrada.");
        return;
    }

    // --- 1. LÓGICA DO SELETOR DE TURMAS (Choices.js) ---
    const turmasElement = document.getElementById('selecao-turmas');
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

        const displayProfessores = document.getElementById('display-professores');
        selectTurmas.passedElement.element.addEventListener('change', function() {
            const turmasSelecionadasIds = Array.from(this.selectedOptions).map(option => option.value);
            const professoresParaExibir = {};
            turmasSelecionadasIds.forEach(turmaId => {
                if (relacaoTurmaProfessor[turmaId]) {
                    relacaoTurmaProfessor[turmaId].forEach(prof => {
                        professoresParaExibir[prof.id] = prof.nome;
                    });
                }
            });
            const nomesProfessores = Object.values(professoresParaExibir);
            displayProfessores.innerHTML = '';
            if (nomesProfessores.length > 0) {
                nomesProfessores.forEach(nome => {
                    const p = document.createElement('p');
                    p.textContent = nome;
                    displayProfessores.appendChild(p);
                });
            } else {
                displayProfessores.innerHTML = '<p>Selecione uma ou mais turmas...</p>';
            }
        });
    }

    // --- 2. VALIDAÇÃO DE DATA MÍNIMA ---
    const inputData = document.getElementById('data');
    if (inputData) {
        const hoje = new Date();
        hoje.setMinutes(hoje.getMinutes() - hoje.getTimezoneOffset()); // Ajusta para o fuso horário local
        inputData.setAttribute('min', hoje.toISOString().split('T')[0]);
    }

    // --- 3. VALIDAÇÃO DINÂMICA DE HORA DE FIM ---
    const horarioInicioElem = document.getElementById('horario_inicio');
    const horarioFimElem = document.getElementById('horario_fim');

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
        ajustarHorarioFim(); // Roda uma vez no início
    }
    
    // --- 4. CONTADOR DE CARACTERES DO TÍTULO ---
    const inputTitulo = document.getElementById('titulo');
    if (inputTitulo && inputTitulo.maxLength > 0) {
        // Cria o elemento do contador dinamicamente
        const contador = document.createElement('small');
        contador.id = 'titulo-contador';
        inputTitulo.parentNode.appendChild(contador); // Adiciona o contador logo após o input

        const maxLength = inputTitulo.getAttribute('maxlength');
        const updateCharCounter = () => {
            const currentLength = inputTitulo.value.length;
            const remaining = maxLength - currentLength;
            contador.textContent = `${remaining} caracteres restantes`;
        };
        inputTitulo.addEventListener('input', updateCharCounter);
        updateCharCounter(); // Chama uma vez para inicializar
    }
});