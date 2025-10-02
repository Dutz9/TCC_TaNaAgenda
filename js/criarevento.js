document.addEventListener('DOMContentLoaded', function() {
    // Inicialização para a seleção de TURMAS (já existente)
    const turmasElement = document.getElementById('selecao-turmas');
    if (turmasElement) {
        new Choices(turmasElement, {
            removeItemButton: true,
            placeholder: true,
            placeholderValue: 'Selecione as turmas envolvidas...',
            allowHTML: false,
            fuseOptions: {
                keys: ['label'],
                threshold: 0.3
            }
        });
    }

    // NOVO: Inicialização para a seleção de PROFESSORES
    const professoresElement = document.getElementById('selecao-professores');
    if (professoresElement) {
        new Choices(professoresElement, {
            removeItemButton: true,
            placeholder: true,
            placeholderValue: 'Selecione os professores a notificar...',
            allowHTML: false,
            fuseOptions: {
                keys: ['label'],
                threshold: 0.3
            }
        });
    }

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

    // Lógica do contador de caracteres (já existente)
    const inputTitulo = document.getElementById('titulo');
    const tituloContador = document.getElementById('titulo-contador');
    const maxLength = inputTitulo ? parseInt(inputTitulo.getAttribute('maxlength'), 10) : 0; // Garante que maxLength é um número

    function updateCharCounter() {
        if (inputTitulo && tituloContador) { // Verifica se os elementos existem
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
    }

    if (inputTitulo && tituloContador) { // Adiciona listeners apenas se os elementos existirem
        inputTitulo.addEventListener('input', updateCharCounter);
        updateCharCounter(); // Chama uma vez ao carregar a página
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

    // REMOVENDO a lógica antiga de display automático de professores
    // Não precisamos mais do `relacaoTurmaProfessor` nem do `displayProfessores`
    // já que a seleção é manual agora.
});
