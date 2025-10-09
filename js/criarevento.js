document.addEventListener('DOMContentLoaded', function() {
    // Verifica se a variável com os dados do PHP existe
    if (typeof relacaoTurmaProfessor === 'undefined') {
        console.error("A variável 'relacaoTurmaProfessor' não foi encontrada. Verifique o script no arquivo .php");
        return;
    }

    const turmasElement = document.getElementById('selecao-turmas');
    
    // Se o <select> de turmas existir na página, inicializa o Choices.js
    if (turmasElement) {
        const selectTurmas = new Choices(turmasElement, {
            removeItemButton: true,
            placeholder: true,
            placeholderValue: 'Clique para selecionar ou digite para buscar...',
            allowHTML: false,
            // Configuração de busca que faz a mágica acontecer
            fuseOptions: {
                keys: ['label'], // Busca apenas no texto visível da opção
                threshold: 0.3   // Define a busca como "flexível"
            }
        });


        const inputData = document.getElementById('data');
    const errorMessage = document.getElementById('error-message');
    
    // Define a data mínima para hoje
    inputData.setAttribute('min', new Date().toISOString().split('T')[0]);
    
    inputData.addEventListener('input', () => {
      const selectedDateStr = inputData.value; // string no formato 'YYYY-MM-DD'
      const todayStr = new Date().toISOString().split('T')[0];
    
      if (selectedDateStr < todayStr) {
        inputData.setCustomValidity('A data selecionada não pode ser anterior à data de hoje.');
      } else {
        inputData.setCustomValidity('');
        errorMessage.textContent = '';
      }
    });


        const displayProfessores = document.getElementById('display-professores');

        // Lógica para atualizar a lista de professores (exatamente como antes)
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

            displayProfessores.innerHTML = '';
            const nomesProfessores = Object.values(professoresParaExibir);

            if (nomesProfessores.length > 0) {
                nomesProfessores.forEach(nome => {
                    const p = document.createElement('p');
                    p.textContent = nome;
                    displayProfessores.appendChild(p);
                });
            } else if (turmasSelecionadasIds.length > 0) {
                displayProfessores.innerHTML = '<p>Nenhum professor encontrado para esta(s) turma(s).</p>';
            } else {
                displayProfessores.innerHTML = '<p>Selecione uma ou mais turmas...</p>';
            }
        });
    }

    const inputTitulo = document.getElementById('titulo');
    const tituloContador = document.getElementById('titulo-contador');
    const maxLength = inputTitulo.getAttribute('maxlength');

    // Função para atualizar o contador de caracteres
    function updateCharCounter() {
        const currentLength = inputTitulo.value.length;
        const remaining = maxLength - currentLength;
        tituloContador.textContent = `Caracteres restantes: ${remaining}`;

        if (remaining < 1) {
            tituloContador.style.color = 'red';
        } else if (remaining <= 5) {
            tituloContador.style.color = 'orange';
        } else {
            tituloContador.style.color = '#888';
        }
    }

    // Adiciona o listener para o evento 'input' (a cada caractere digitado)
    if (inputTitulo && tituloContador) {
        inputTitulo.addEventListener('input', updateCharCounter);
        // Chama a função uma vez ao carregar a página caso já haja texto
        updateCharCounter();
    }

        // --- Lógica para a Hora de Início e Hora de Fim ---
        const horarioInicioElement = document.getElementById('horario_inicio');
        const horarioFimElement = document.getElementById('horario_fim');
        const errorMessageFim = document.getElementById('error-message-fim'); // Novo span para o erro do horário de fim
    
        // Função para desabilitar opções de hora de fim menores ou iguais à hora de início
        function ajustarHorarioFim() {
            const horaInicio = horarioInicioElement.value;
            const options = horarioFimElement.options;
    
            // Converte hora de início para minutos
            const [hInicio, mInicio] = horaInicio.split(":").map(Number);
            const minutosInicio = hInicio * 60 + mInicio;
    
            // Atualiza as opções da hora de fim
            for (let i = 0; i < options.length; i++) {
                const option = options[i];
                const [h, m] = option.value.split(":").map(Number);
                const minutosOption = h * 60 + m;
    
                // Desabilita as opções de hora de fim menores ou iguais à hora de início
                option.disabled = minutosOption <= minutosInicio;
            }
        }
    
        // Se o elemento da hora de início existir, adicionar o evento
        if (horarioInicioElement) {
            horarioInicioElement.addEventListener('change', ajustarHorarioFim);
            ajustarHorarioFim(); // Chama uma vez ao carregar a página para garantir que as opções da hora de fim estão corretas
        }
        

    // --- Lógica para a validação do horário de fim ---
    const form = document.querySelector('form');
    form.addEventListener('submit', function (e) {
        const horarioInicio = horarioInicioElement.value;
        const horarioFim = horarioFimElement.value;
        
        // Validar se o horário de fim é posterior ao horário de início
        if (horarioInicio && horarioFim) {
            const [hInicio, mInicio] = horarioInicio.split(":").map(Number);
            const [hFim, mFim] = horarioFim.split(":").map(Number);
            
            const minutosInicio = hInicio * 60 + mInicio;
            const minutosFim = hFim * 60 + mFim;

            if (minutosFim <= minutosInicio) {
                e.preventDefault(); // Impede o envio do formulário
                errorMessageFim.textContent = 'O horário de encerramento não pode ser anterior ao horário de início.';
                errorMessageFim.style.color = 'red'; // Exibe a mensagem no lugar correto
            }
        }
    });

});