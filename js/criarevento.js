document.addEventListener('DOMContentLoaded', function() {
    // Garante que as variáveis do PHP existem
    if (typeof relacaoTurmaProfessor === 'undefined' || typeof usuario_logado === 'undefined' || typeof mapaAlunosTurma === 'undefined' || typeof modoEdicao === 'undefined' || typeof professoresSelecionados === 'undefined') {
        console.error("Variáveis de dados PHP não encontradas.");
        return;
    }

    // --- Elementos ---
    const turmasElement = document.getElementById('selecao-turmas');
    const professoresElement = document.getElementById('selecao-professores'); // O novo <select> de professores
    const displayTotalAlunos = document.getElementById('display-total-alunos');
    
    const inputData = document.getElementById('data');
    const horarioInicioElem = document.getElementById('horario_inicio');
    const horarioFimElem = document.getElementById('horario_fim');
    const inputTitulo = document.getElementById('titulo');

    // Variáveis para as instâncias do Choices.js
    let choicesTurmas = null;
    let choicesProfessores = null;

    // --- INICIALIZAÇÃO DO CHOICES.JS ---
    
    // 1. Configura o select de PROFESSORES (vazio inicialmente)
    if (professoresElement) {
        choicesProfessores = new Choices(professoresElement, {
            removeItemButton: true,
            placeholder: true,
            placeholderValue: 'Selecione as turmas primeiro...',
            noResultsText: 'Nenhum professor encontrado para estas turmas',
            searchEnabled: true,
            shouldSort: false // Mantém a ordem que inserirmos
        });
    }

    // 2. Configura o select de TURMAS e adiciona o Listener
    if (turmasElement) {
        choicesTurmas = new Choices(turmasElement, {
            removeItemButton: true,
            placeholder: true,
            placeholderValue: 'Clique para selecionar turmas...',
            searchEnabled: true
        });

        // Quando as turmas mudam, atualizamos a lista de professores
        turmasElement.addEventListener('change', function() {
            atualizarListaProfessoresEAlunos();
        });
    }

    /**
     * Função principal: Cruza Turmas -> Professores e Alunos
     */
    function atualizarListaProfessoresEAlunos() {
        // Pega os IDs das turmas selecionadas
        const turmasIds = choicesTurmas.getValue(true); // Retorna array de IDs (strings ou numbers)
        
        let totalAlunos = 0;
        const professoresDisponiveisMap = new Map(); // Map para evitar duplicatas (ID -> Nome)

        // Loop pelas turmas selecionadas
        turmasIds.forEach(turmaId => {
            // 1. Soma alunos
            if (mapaAlunosTurma[turmaId]) {
                totalAlunos += parseInt(mapaAlunosTurma[turmaId], 10);
            }

            // 2. Coleta professores dessa turma
            if (relacaoTurmaProfessor[turmaId]) {
                relacaoTurmaProfessor[turmaId].forEach(prof => {
                    // Não adiciona o próprio usuário logado na lista de notificação
                    if (prof.id !== usuario_logado.cd_usuario) {
                        professoresDisponiveisMap.set(prof.id, prof.nome);
                    }
                });
            }
        });

        // Atualiza input de alunos
        if (displayTotalAlunos) {
            displayTotalAlunos.value = totalAlunos;
        }

        // Atualiza o Select de Professores
        if (choicesProfessores) {
            // Salva quem já estava selecionado (para não perder a seleção se o usuário adicionar mais uma turma)
            const selecionadosAnteriormente = choicesProfessores.getValue(true);
            
            // Limpa tudo
            choicesProfessores.clearStore();
            choicesProfessores.clearInput();

            // Cria o novo array de opções para o Choices
            const novasOpcoes = [];
            professoresDisponiveisMap.forEach((nome, id) => {
                // Lógica de Pré-seleção:
                // 1. Se estivermos editando E o professor já estava salvo no banco -> Seleciona
                // 2. Se o usuário já tinha selecionado ele manualmente antes de mudar a turma -> Mantém selecionado
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

            // Atualiza o componente com as novas opções
            choicesProfessores.setChoices(novasOpcoes, 'value', 'label', true);
            
            // Ajusta o placeholder se houver ou não opções
            if (novasOpcoes.length > 0) {
                professoresElement.setAttribute('placeholder', 'Clique para selecionar professores...');
            } else {
                professoresElement.setAttribute('placeholder', 'Nenhum professor disponível.');
            }
        }
    }

    // --- EXECUÇÃO INICIAL (Para carregar dados no Modo Edição) ---
    if (modoEdicao) {
        // Força a atualização para carregar os alunos e professores salvos
        atualizarListaProfessoresEAlunos();
    }


    // --- VALIDAÇÕES (Data, Hora, Título) ---

    // Validação de Data Mínima (Hoje)
    if (inputData) {
        const hoje = new Date();
        hoje.setMinutes(hoje.getMinutes() - hoje.getTimezoneOffset());
        const dataMinima = hoje.toISOString().split('T')[0];
        
        // Se for edição de evento passado, permite a data antiga. Se for novo, bloqueia passado.
        if (!modoEdicao || (modoEdicao && inputData.value >= dataMinima)) {
             inputData.setAttribute('min', dataMinima);
        }
    }

    // Validação Dinâmica de Horário (Início < Fim)
    if (horarioInicioElem && horarioFimElem) {
        const ajustarHorarioFim = () => {
            const horaInicio = horarioInicioElem.value;
            if (!horaInicio) {
                horarioFimElem.value = "";
                return;
            }
            for (const option of horarioFimElem.options) {
                if (option.value) {
                    // Desabilita horários anteriores ao início
                    option.disabled = (option.value <= horaInicio);
                }
            }
            // Se a opção selecionada ficou inválida, limpa o campo
            if (horarioFimElem.options[horarioFimElem.selectedIndex]?.disabled) {
                horarioFimElem.value = "";
            }
        };
        horarioInicioElem.addEventListener('change', ajustarHorarioFim);
        ajustarHorarioFim(); // Roda ao carregar
    }
    
    // Contador de Caracteres do Título
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