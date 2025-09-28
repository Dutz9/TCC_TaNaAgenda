document.addEventListener('DOMContentLoaded', () => {
    // Garante que as 'pontes' de dados do PHP existem
    if (typeof eventosDaPagina === 'undefined' || typeof nomeUsuarioLogado === 'undefined') {
        console.error("Variáveis de dados ('eventosDaPagina' ou 'nomeUsuarioLogado') não foram encontradas. Verifique o script PHP.");
        return;
    }

    const container = document.querySelector('.notificacao-container');
    const modal = document.getElementById('modal-detalhes-evento');
    
    if (!container || !modal) {
        console.error('Elementos essenciais (container ou modal) não foram encontrados.');
        return;
    }
    const modalLeft = modal.querySelector('.modal-left');
    const modalRight = modal.querySelector('.modal-right');

    // --- "ESCUTADOR" DE CLIQUES PRINCIPAL ---
    container.addEventListener('click', (e) => {
        const botaoClicado = e.target.closest('button');
        if (!botaoClicado) return;
        
        const eventoId = botaoClicado.dataset.id;
        if (!eventoId) return;

        const evento = eventosDaPagina.find(ev => ev.cd_evento === eventoId);
        if (!evento) return;

        if (botaoClicado.classList.contains('detalhes-btn')) {
            abrirModalDetalhes(evento);
        } else if (botaoClicado.classList.contains('btn-aprovar')) {
            enviarResposta(eventoId, 'Aprovado', botaoClicado);
        } else if (botaoClicado.classList.contains('btn-recusar')) {
            enviarResposta(eventoId, 'Recusado', botaoClicado);
        }
    });

    // --- FUNÇÕES ---

    /**
     * Envia a resposta (Aprovado/Recusado) do professor para a API.
     */
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

            if (response.ok && result.status === 'sucesso') {
                // --- SINCRONIZAÇÃO DA MEMÓRIA INTERNA (A CORREÇÃO) ---
                const indiceEvento = eventosDaPagina.findIndex(ev => ev.cd_evento === eventoId);
                if (indiceEvento > -1) {
                    // 1. Atualiza a 'minha_resposta' para a lógica de recarregar a página.
                    eventosDaPagina[indiceEvento].minha_resposta = resposta;

                    // 2. Atualiza a lista de respostas que o pop-up usa.
                    let respostas = JSON.parse(eventosDaPagina[indiceEvento].respostas_professores || '[]');
                    let minhaRespostaNaLista = respostas.find(r => r.nome === nomeUsuarioLogado);
                    if (minhaRespostaNaLista) {
                        minhaRespostaNaLista.status = resposta; // Atualiza o status
                    }
                    // Converte a lista de volta para uma string JSON e salva na memória
                    eventosDaPagina[indiceEvento].respostas_professores = JSON.stringify(respostas);
                }
                // --- FIM DA SINCRONIZAÇÃO ---

                // Atualiza a interface do card
                const statusTexto = `Sua resposta: ${resposta}`;
                const statusClasse = resposta === 'Aprovado' ? 'status-aprovado' : 'status-recusado';
                containerRespostas.innerHTML = `<p class="${statusClasse}">${statusTexto}</p>`;

            } else {
                alert('Erro: ' + (result.mensagem || 'Não foi possível registrar a resposta.'));
                containerRespostas.innerHTML = `<button class="btn-recusar" data-id="${eventoId}">Recusar</button><button class="btn-aprovar" data-id="${eventoId}">Aprovar</button>`;
            }
        } catch (error) {
            alert('Ocorreu um erro de comunicação.');
            console.error('Erro no fetch:', error);
            containerRespostas.innerHTML = `<button class="btn-recusar" data-id="${eventoId}">Recusar</button><button class="btn-aprovar" data-id="${eventoId}">Aprovar</button>`;
        }
    }
    
    // Função para formatar data (DD/MM/YYYY)
    function formatarData(dateString) {
        const [ano, mes, dia] = dateString.split('-');
        return `${dia}/${mes}/${ano}`;
    }

    /**
     * Abre e preenche o modal com os detalhes do evento.
     */
    function abrirModalDetalhes(evento) {
        // (A função abrirModalDetalhes continua a mesma da mensagem anterior, pois ela já lê
        // os dados de 'respostas_professores' que agora estamos atualizando corretamente)
        let respostas = [];
        if (evento.respostas_professores) {
            try {
                respostas = JSON.parse(evento.respostas_professores) || [];
            } catch (e) { console.warn("JSON das respostas inválido:", evento.respostas_professores); }
        }

        let tituloRespostas = (evento.tipo_solicitante === 'Coordenador') ? 'Professores Envolvidos' : 'Respostas dos Professores';
        let respostasHtml = '';
        
        if (respostas.length > 0) {
             respostas.forEach(r => {
                let statusHtml = '';
                // Mostra o status apenas se o solicitante for um Professor
                if(evento.tipo_solicitante === 'Professor') {
                    let statusClass = r.status === 'Aprovado' ? 'aprovado' : (r.status === 'Recusado' ? 'recusado' : 'sem-resposta');
                    statusHtml = `<span class="${statusClass}">${r.status}</span>`;
                }
                 respostasHtml += `<div class="response-item"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="#000000" d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512H418.3c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304H178.3z"/></svg><div><p>${r.nome}</p>${statusHtml}</div></div>`;
             });
        } else {
            respostasHtml = (evento.tipo_solicitante === 'Coordenador') ? '<p>Nenhum professor diretamente envolvido.</p>' : '<p>Nenhum outro professor envolvido.</p>';
        }

        modalLeft.innerHTML = `<div class="coordinator-info"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="#000000" d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512H418.3c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304H178.3z"/></svg><div><h3>${evento.nm_solicitante}</h3><p>${evento.tipo_solicitante}</p></div></div><div class="responses-section"><h4>${tituloRespostas}</h4>${respostasHtml}</div>`;
        modalRight.innerHTML = `<h3>Detalhes do Evento</h3><div class="form-group"><label>Título:</label><input type="text" readonly value="${evento.nm_evento}"></div><div class="form-row"><div class="form-group"><label>Horário:</label><input type="text" readonly value="${evento.horario_inicio.substr(0, 5)} - ${evento.horario_fim.substr(0, 5)}"></div><div class="form-group"><label>Data:</label><input type="text" readonly value="${formatarData(evento.dt_evento)}"></div></div><div class="form-group"><label>Turmas:</label><input type="text" readonly value="${evento.turmas_envolvidas || 'N/A'}"></div><label>Descrição:</label><textarea readonly>${evento.ds_descricao}</textarea>`;
        modal.style.display = 'flex';
    }

    modal.addEventListener('click', (e) => {
        if (e.target === modal) modal.style.display = 'none';
    });
});