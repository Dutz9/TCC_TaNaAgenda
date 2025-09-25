document.addEventListener('DOMContentLoaded', () => {

    // --- 1. VERIFICAÇÃO E ELEMENTOS PRINCIPAIS ---
    if (typeof eventosDaPagina === 'undefined') {
        console.error("A variável 'eventosDaPagina' não foi encontrada. Verifique o script no arquivo .php");
        return;
    }

    const container = document.querySelector('.notificacao-container');
    const modal = document.getElementById('modal-detalhes-evento');
    
    // Verifica se os elementos essenciais existem antes de prosseguir
    if (!container || !modal) {
        console.error('Elementos essenciais (container ou modal) não encontrados.');
        return;
    }
    const modalLeft = modal.querySelector('.modal-left');
    const modalRight = modal.querySelector('.modal-right');


    // --- 2. "ESCUTADOR" DE CLIQUES PRINCIPAL (DELEGAÇÃO DE EVENTOS) ---
    container.addEventListener('click', (e) => {
        const botaoClicado = e.target.closest('button');
        if (!botaoClicado) return; // Se o clique não foi em um botão, ignora

        const eventoId = botaoClicado.dataset.id;
        if (!eventoId) return; // Se o botão não tem um ID de evento, ignora

        const evento = eventosDaPagina.find(ev => ev.cd_evento === eventoId);
        if (!evento) return; // Se não encontrar os dados do evento, ignora

        // Decide o que fazer com base na classe do botão
        if (botaoClicado.classList.contains('detalhes-btn')) {
            abrirModalDetalhes(evento);
        } else if (botaoClicado.classList.contains('btn-aprovar')) {
            enviarResposta(eventoId, 'Aprovado', botaoClicado);
        } else if (botaoClicado.classList.contains('btn-recusar')) {
            enviarResposta(eventoId, 'Recusado', botaoClicado);
        }
    });


    // --- 3. FUNÇÃO AJAX PARA ENVIAR A RESPOSTA (APROVAR/RECUSAR) ---
    async function enviarResposta(eventoId, resposta, botao) {
        const containerRespostas = botao.closest('.opcoes-resposta');
        containerRespostas.innerHTML = '<p>Processando...</p>';

        const formData = new FormData();
        formData.append('cd_evento', eventoId);
        formData.append('resposta', resposta);

        try {
            const response = await fetch('../api/responder_evento.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.status === 'sucesso') {
                const statusTexto = resposta === 'Aprovado' ? 'Você Aprovou' : 'Você Recusou';
                const statusClasse = resposta === 'Aprovado' ? 'status-aprovado' : 'status-recusado';
                containerRespostas.innerHTML = `<p class="${statusClasse}">${statusTexto}</p>`;
            } else {
                alert('Erro: ' + result.mensagem);
                // Em caso de erro, restaura os botões originais
                containerBotoes.innerHTML = `<button class="btn-recusar" data-id="${eventoId}">Recusar</button><button class="btn-aprovar" data-id="${eventoId}">Aprovar</button>`;
            }
        } catch (error) {
            alert('Ocorreu um erro de comunicação. Tente novamente.');
            console.error('Erro no fetch:', error);
            containerBotoes.innerHTML = `<button class="btn-recusar" data-id="${eventoId}">Recusar</button><button class="btn-aprovar" data-id="${eventoId}">Aprovar</button>`;
        }
    }


    // --- 4. FUNÇÃO PARA ABRIR O MODAL DE DETALHES ---
    function abrirModalDetalhes(evento) {
        let respostas = [];
        if (evento.respostas_professores) {
            try {
                respostas = JSON.parse(evento.respostas_professores) || [];
            } catch (e) {
                console.warn("Não foi possível analisar as respostas para o evento:", evento.cd_evento);
            }
        }

        let respostasHtml = '';
        let tituloRespostas = 'Respostas'; // Título padrão

        // Se o evento foi criado por um Coordenador
        if (evento.tipo_solicitante === 'Coordenador') {
            tituloRespostas = 'Professores Envolvidos'; // Muda o título
            if (respostas.length > 0) {
                respostas.forEach(resposta => {
                    // Monta o HTML SEM status de aprovação
                    respostasHtml += `
                        <div class="response-item">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="#000000" d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512H418.3c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304H178.3z"/></svg>
                            <div><p>${resposta.nome}</p></div>
                        </div>
                    `;
                });
            } else {
                respostasHtml = '<p>Nenhum professor diretamente envolvido nas turmas.</p>';
            }
        } 
        // Se o evento foi criado por um Professor
        else if (respostas.length > 0) {
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
            respostasHtml = '<p>Nenhum outro professor envolvido.</p>';
        }
        
        // Preenche a parte esquerda do modal
        modalLeft.innerHTML = `
            <div class="coordinator-info">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="#000000" d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512H418.3c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304H178.3z"/></svg>
                <div>
                    <h3>${evento.nm_solicitante}</h3>
                    <p>${evento.tipo_solicitante}</p>
                </div>
            </div>
            <div class="responses-section">
                <h4>${tituloRespostas}</h4>
                ${respostasHtml}
            </div>
        `;

        // Preenche a parte direita do modal
        modalRight.innerHTML = `
            <h3>Detalhes do Evento</h3>
            <div class="form-group"><label>Título do Evento:</label><input type="text" readonly value="${evento.nm_evento}"></div>
            <div class="form-row">
                <div class="form-group"><label>Horário:</label><input type="text" readonly value="${evento.horario_inicio.substr(0, 5)} - ${evento.horario_fim.substr(0, 5)}"></div>
                <div class="form-group"><label>Data do Evento:</label><input type="text" readonly value="${formatarData(evento.dt_evento)}"></div>
            </div>
            <div class="form-group"><label>Turmas Envolvidas:</label><input type="text" readonly value="${evento.turmas_envolvidas || 'N/A'}"></div>
            <label>Descrição:</label><textarea readonly>${evento.ds_descricao}</textarea>
        `;

        modal.style.display = 'flex';
    }


    // --- 5. FUNÇÕES AUXILIARES E FECHAMENTO DO MODAL ---
    function formatarData(dateString) {
        const [ano, mes, dia] = dateString.split('-');
        return `${dia}/${mes}/${ano}`;
    }
    
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.style.display = 'none';
        }
    });
});