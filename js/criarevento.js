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
});