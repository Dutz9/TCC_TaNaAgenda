document.addEventListener('DOMContentLoaded', () => {
    // Garante que a 'ponte' de dados do PHP existe antes de continuar.
    if (typeof eventosDaPagina === 'undefined') {
        console.error("A variável 'eventosDaPagina' não foi encontrada. Verifique o script PHP.");
        return;
    }

    // --- ELEMENTOS PRINCIPAIS ---
    const container = document.querySelector('.notificacao-container');
    const modal = document.getElementById('modal-decisao-coord');
    
    if (!container || !modal) {
        console.error('Elementos essenciais (container ou modal) não foram encontrados.');
        return;
    }
    const modalLeft = document.getElementById('modal-left-coord');
    const modalRight = document.getElementById('modal-right-coord');

    // --- "ESCUTADORES" DE CLIQUES ---

    // 1. Escutador nos CARDS para abrir o modal de detalhes
    container.addEventListener('click', (e) => {
        const botao = e.target.closest('button.detalhes-btn');
        if (!botao) return;

        const eventoId = botao.dataset.id;
        const evento = eventosDaPagina.find(ev => ev.cd_evento === eventoId);
        if (evento) {
            abrirModalDecisao(evento);
        }
    });

    // 2. Escutador DENTRO DO MODAL para os botões de Aprovar/Recusar
    modal.addEventListener('click', (e) => {
        const botao = e.target.closest('button');
        // Se o clique não foi em um botão, ou se o botão não é de aprovar/recusar, ignora
        if (!botao || (!botao.classList.contains('aprovar') && !botao.classList.contains('recusar'))) {
            return;
        }
        
        const eventoId = botao.dataset.id;
        const decisao = botao.classList.contains('aprovar') ? 'Aprovado' : 'Recusado';
        
        enviarDecisaoFinal(eventoId, decisao, botao);
    });

    // 3. Escutador para fechar o modal
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.style.display = 'none';
        }
    });


    // --- FUNÇÕES ---

    /**
     * Envia a decisão final (Aprovado/Recusado) do coordenador para a API.
     */
    async function enviarDecisaoFinal(eventoId, decisao, botao) {
        const botoesContainer = botao.parentElement;
        botoesContainer.innerHTML = '<p>Processando...</p>';

        const formData = new FormData();
        formData.append('cd_evento', eventoId);
        formData.append('decisao', decisao);

        try {
            const response = await fetch('../api/decisao_final_evento.php', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();

            if (result.status === 'sucesso') {
                alert(`Evento ${decisao.toLowerCase()} com sucesso!`);
                modal.style.display = 'none';
                
                // Remove o card do evento da lista de pendências
                const cardParaRemover = document.querySelector(`.notificacao .detalhes-btn[data-id="${eventoId}"]`).closest('.notificacao');
                if (cardParaRemover) {
                    cardParaRemover.style.opacity = '0'; // Efeito de fade out
                    setTimeout(() => cardParaRemover.remove(), 500); // Remove após a animação
                }
            } else {
                alert('Erro: ' + result.mensagem);
                botoesContainer.innerHTML = `<button class="recusar" data-id="${eventoId}">Recusar Evento</button><button class="aprovar" data-id="${eventoId}">Aprovar Evento</button>`;
            }
        } catch (error) {
            alert('Ocorreu um erro de comunicação. Tente novamente.');
            console.error('Erro no fetch:', error);
            botoesContainer.innerHTML = `<button class="recusar" data-id="${eventoId}">Recusar Evento</button><button class="aprovar" data-id="${eventoId}">Aprovar Evento</button>`;
        }
    }

    /**
     * Abre e preenche o modal com os detalhes do evento para a decisão do coordenador.
     */
    function abrirModalDecisao(evento) {
        let respostas = [];
        if (evento.respostas_professores) {
            try {
                respostas = JSON.parse(evento.respostas_professores) || [];
            } catch (e) {
                console.warn("Não foi possível analisar as respostas para o evento:", evento.cd_evento);
            }
        }

        let respostasHtml = '';
        if (respostas.length > 0) {
            respostas.forEach(resposta => {
                let statusClass = 'sem-resposta';
                if (resposta.status === 'Aprovado') statusClass = 'aprovado';
                if (resposta.status === 'Recusado') statusClass = 'recusado';
                
                respostasHtml += `
                    <div class="response-item">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="#000000" d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512H418.3c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304H178.3z"/></svg>
                        <div>
                            <p>${resposta.nome}</p>
                            <span class="${statusClass}">${resposta.status}</span>
                        </div>
                    </div> 
                `;
            });
        } else {
            respostasHtml = '<p>Nenhum professor envolvido para este evento.</p>';
        }

        modalLeft.innerHTML = `
            <div class="coordinator-info">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="#000000" d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512H418.3c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304H178.3z"/></svg>
                <div>
                    <h3>${evento.nm_solicitante}</h3>
                    <p>Solicitante</p>
                </div>
            </div>
            <div class="responses-section">
                <h4>Respostas dos Professores</h4>
                <div class="respostas-vinculadas">
                ${respostasHtml}
                </div>
            </div>
        `;

        modalRight.innerHTML = `
            <h3>Detalhes do Evento</h3>
            <div class="form-group"><label>Título:</label><input type="text" readonly value="${evento.nm_evento}"></div>
            <div class="form-row">
                <div class="form-group"><label>Horário:</label><input type="text" readonly value="${evento.horario_inicio.substr(0, 5)} - ${evento.horario_fim.substr(0, 5)}"></div>
                <div class="form-group"><label>Data:</label><input type="text" readonly value="${new Date(evento.dt_evento + 'T00:00:00').toLocaleDateString('pt-BR')}"></div>
            </div>
            <div class="form-group"><label>Turmas:</label><input type="text" readonly value="${evento.turmas_envolvidas || 'N/A'}"></div>
            <label>Descrição:</label><textarea readonly>${evento.ds_descricao}</textarea>
            <div class="modal-buttons">
                <button class="recusar" data-id="${evento.cd_evento}">Recusar Evento</button>
                <button class="aprovar" data-id="${evento.cd_evento}">Aprovar Evento</button>
            </div>
        `;
        
        modal.style.display = 'flex';
    }
});