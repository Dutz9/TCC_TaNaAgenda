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

    // REMOVENDO a lógica antiga de display automático de professores
    // Não precisamos mais do `relacaoTurmaProfessor` nem do `displayProfessores`
    // já que a seleção é manual agora.
});
