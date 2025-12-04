document.addEventListener('DOMContentLoaded', function() {

    if (typeof relacaoTurmaProfessor === 'undefined' || typeof usuario_logado === 'undefined' || typeof mapaAlunosTurma === 'undefined' || typeof modoEdicao === 'undefined' || typeof professoresSelecionados === 'undefined') {
        console.error("Variáveis de dados PHP não encontradas.");
        return;
    }


    const turmasElement = document.getElementById('selecao-turmas');
    const professoresElement = document.getElementById('selecao-professores');
    const displayTotalAlunos = document.getElementById('display-total-alunos');
    
    const inputData = document.getElementById('data');
    const horarioInicioElem = document.getElementById('horario_inicio');
    const horarioFimElem = document.getElementById('horario_fim');
    const inputTitulo = document.getElementById('titulo');

    let choicesTurmas = null;
    let choicesProfessores = null;

    if (professoresElement) {
        choicesProfessores = new Choices(professoresElement, {
            removeItemButton: true,
            placeholder: true,
            placeholderValue: 'Selecione as turmas primeiro...',
            noResultsText: 'Nenhum professor encontrado para estas turmas',
            searchEnabled: true,
            shouldSort: false
        });
    }


    if (turmasElement) {
        choicesTurmas = new Choices(turmasElement, {
            removeItemButton: true,
            placeholder: true,
            placeholderValue: 'Clique para selecionar turmas...',
            searchEnabled: true
        });


        turmasElement.addEventListener('change', function() {
            atualizarListaProfessoresEAlunos();
        });
    }


    function atualizarListaProfessoresEAlunos() {
        const turmasIds = choicesTurmas.getValue(true);
        
        let totalAlunos = 0;
        const professoresDisponiveisMap = new Map();


        turmasIds.forEach(turmaId => {

            if (mapaAlunosTurma[turmaId]) {
                totalAlunos += parseInt(mapaAlunosTurma[turmaId], 10);
            }

            if (relacaoTurmaProfessor[turmaId]) {
                relacaoTurmaProfessor[turmaId].forEach(prof => {
                    if (prof.id !== usuario_logado.cd_usuario) {
                        professoresDisponiveisMap.set(prof.id, prof.nome);
                    }
                });
            }
        });


        if (displayTotalAlunos) {
            displayTotalAlunos.value = totalAlunos;
        }


        if (choicesProfessores) {

            const selecionadosAnteriormente = choicesProfessores.getValue(true);
            

            choicesProfessores.clearStore();
            choicesProfessores.clearInput();


            const novasOpcoes = [];
            professoresDisponiveisMap.forEach((nome, id) => {

                let deveSelecionar = false;
                
                if (modoEdicao && professoresSelecionados[id]) {
                    deveSelecionar = true;
                } else if (selecionadosAnteriormente && selecionadosAnteriormente.includes(id)) {
                    deveSelecionar = true;
                }

                novasOpcoes.push({
                    value: id,
                    label: nome,
                    selected: deveSelecionar
                });
            });

            choicesProfessores.setChoices(novasOpcoes, 'value', 'label', true);

            if (novasOpcoes.length > 0) {
                professoresElement.setAttribute('placeholder', 'Clique para selecionar professores...');
            } else {
                professoresElement.setAttribute('placeholder', 'Nenhum professor disponível.');
            }
        }
    }

    if (modoEdicao) {

        atualizarListaProfessoresEAlunos();
    }

    if (inputData) {
        const hoje = new Date();
        hoje.setMinutes(hoje.getMinutes() - hoje.getTimezoneOffset());
        const dataMinima = hoje.toISOString().split('T')[0];

        if (!modoEdicao || (modoEdicao && inputData.value >= dataMinima)) {
             inputData.setAttribute('min', dataMinima);
        }
    }

    if (horarioInicioElem && horarioFimElem) {
        const ajustarHorarioFim = () => {
            const horaInicio = horarioInicioElem.value;
            if (!horaInicio) {
                horarioFimElem.value = "";
                return;
            }
            for (const option of horarioFimElem.options) {
                if (option.value) {
                    option.disabled = (option.value <= horaInicio);
                }
            }
            if (horarioFimElem.options[horarioFimElem.selectedIndex]?.disabled) {
                horarioFimElem.value = "";
            }
        };
        horarioInicioElem.addEventListener('change', ajustarHorarioFim);
        ajustarHorarioFim();
    }

    if (inputTitulo && inputTitulo.maxLength > 0) {
        const contador = document.createElement('small');
        contador.style.cssText = 'color: #888; font-size: 0.8em; display: block; margin-top: 5px;';
        inputTitulo.parentNode.appendChild(contador);
        const maxLength = inputTitulo.getAttribute('maxlength');
        
        const updateCharCounter = () => {
            const remaining = maxLength - inputTitulo.value.length;
            contador.textContent = `${remaining} caracteres restantes`;
        };
        inputTitulo.addEventListener('input', updateCharCounter);
        updateCharCounter();
    }
});