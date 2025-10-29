/**
 * Mostra uma barra de feedback flutuante no topo da tela.
 * Fica fora do DOMContentLoaded para ser acessível pelo PHP.
 * @param {string} message - A mensagem a ser exibida.
 * @param {string} type - 'sucesso' ou 'erro'.
 */
function showFeedback(message, type = 'sucesso') {
    const bar = document.getElementById('feedback-bar');
    if (!bar) return;
    bar.textContent = message;
    bar.className = `feedback-bar ${type} show`;
    setTimeout(() => { bar.classList.remove('show'); }, 3500);
}

// --- LÓGICA PRINCIPAL DA PÁGINA ---
document.addEventListener('DOMContentLoaded', () => {
    // Garante que as 'pontes' de dados do PHP existem
    if (typeof eventosDaPagina === 'undefined' || typeof usuario_logado === 'undefined') {
        console.error("Variáveis de dados ('eventosDaPagina' ou 'usuario_logado') não foram encontradas.");
        return;
    }

    // --- Elementos Principais ---
    const container = document.querySelector('.notificacao-container');
    const modalDetalhes = document.getElementById('modal-decisao-coord');
    const modalConfirmar = document.getElementById('modal-confirm-excluir'); // O modal de confirmação

    if (!container || !modalDetalhes || !modalConfirmar) {
        console.error('Elementos essenciais (container ou modais) não foram encontrados.');
        return;
    }

    const modalLeft = modalDetalhes.querySelector('.modal-left');
    const modalRight = modalDetalhes.querySelector('.modal-right');
    const btnConfirmarSim = document.getElementById('btn-excluir-sim');
    const btnConfirmarNao = document.getElementById('btn-excluir-nao');
    
    let eventoParaExcluir = null; // Guarda o ID do evento a ser excluído

    // --- "ESCUTADORES" DE CLIQUES ---
    
    // 1. Escutador nos CARDS para abrir o modal de detalhes
    container.addEventListener('click', (e) => {
        const botao = e.target.closest('button.detalhes-btn');
        if (botao) {
            const eventoId = botao.dataset.id;
            const evento = eventosDaPagina.find(ev => ev.cd_evento === eventoId);
            if (evento) abrirModalDecisao(evento);
        }
    });

    // 2. Escutador DENTRO DO MODAL DE DETALHES
    modalDetalhes.addEventListener('click', (e) => {
        if (e.target === modalDetalhes) modalDetalhes.style.display = 'none';

        const botao = e.target.closest('button');
        if (!botao) return;

        // Ação de Aprovar/Recusar
        if (botao.classList.contains('aprovar') || botao.classList.contains('recusar')) {
            const eventoId = botao.dataset.id;
            const decisao = botao.classList.contains('aprovar') ? 'Aprovado' : 'Recusado';
            enviarDecisaoFinal(eventoId, decisao, botao);
        }
        
        // Ação de Excluir Evento
        if (botao.classList.contains('btn-excluir-evento')) {
            eventoParaExcluir = botao.dataset.id; // Guarda o ID do evento
            modalConfirmar.style.display = 'flex'; // Abre o modal de confirmação
        }
    });

    // 3. Escutadores no MODAL DE CONFIRMAÇÃO DE EXCLUSÃO
    btnConfirmarNao.addEventListener('click', () => {
        modalConfirmar.style.display = 'none';
        eventoParaExcluir = null;
    });

    btnConfirmarSim.addEventListener('click', () => {
        if (eventoParaExcluir) {
            enviarExclusao(eventoParaExcluir);
        }
    });

    modalConfirmar.addEventListener('click', (e) => {
        if (e.target === modalConfirmar) {
            modalConfirmar.style.display = 'none';
            eventoParaExcluir = null;
        }
    });

    // --- FUNÇÕES AJAX ---

    /**
     * Envia o pedido de EXCLUSÃO para a API.
     */
    async function enviarExclusao(eventoId) {
        btnConfirmarSim.disabled = true;
        btnConfirmarSim.textContent = 'Excluindo...';
        
        const formData = new FormData();
        formData.append('cd_evento', eventoId);

        try {
            const response = await fetch('../api/excluir_evento.php', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();

            if (response.ok && result.status === 'sucesso') {
                modalConfirmar.style.display = 'none';
                modalDetalhes.style.display = 'none';
                showFeedback(result.mensagem, 'sucesso');
                
                const card = document.querySelector(`.notificacao .detalhes-btn[data-id="${eventoId}"]`).closest('.notificacao');
                if (card) {
                    card.style.opacity = '0';
                    setTimeout(() => card.remove(), 500);
                }
            } else {
                alert('Erro: ' + (result.mensagem || 'Não foi possível excluir.'));
            }
        } catch (error) {
            alert('Erro de comunicação.');
            console.error('Erro no fetch:', error);
        }
        
        btnConfirmarSim.disabled = false;
        btnConfirmarSim.textContent = 'Sim, Excluir';
        eventoParaExcluir = null;
    }

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
            const response = await fetch('../api/decisao_final_evento.php', { method: 'POST', body: formData });
            const result = await response.json();

            if (response.ok && result.status === 'sucesso') {
                showFeedback(`Evento ${decisao.toLowerCase()} com sucesso!`, 'sucesso');
                modalDetalhes.style.display = 'none';

                const card = document.querySelector(`.notificacao .detalhes-btn[data-id="${eventoId}"]`).closest('.notificacao');
                if (card) {
                    const pStatus = card.querySelector('p[class*="status-"]');
                    const statusClasse = `status-${decisao.toLowerCase()}`;
                    if (pStatus) {
                        pStatus.innerHTML = `<b>Status:</b> ${decisao}`;
                        pStatus.className = statusClasse;
                    }
                    card.querySelector('.detalhes-btn').textContent = 'Ver Detalhes';
                    card.classList.add('card-respondido'); 
                }

                const indiceEvento = eventosDaPagina.findIndex(ev => ev.cd_evento === eventoId);
                if (indiceEvento > -1) {
                    eventosDaPagina[indiceEvento].status = decisao;
                }
            } else {
                showFeedback(result.mensagem || 'Ocorreu um erro.', 'erro');
                botoesContainer.innerHTML = `<button class="recusar" data-id="${eventoId}">Recusar Evento</button><button class="aprovar" data-id="${eventoId}">Aprovar Evento</button>`;
            }
        } catch (error) {
            showFeedback('Ocorreu um erro de comunicação.', 'erro');
            console.error('Erro no fetch:', error);
            botoesContainer.innerHTML = `<button class="recusar" data-id="${eventoId}">Recusar Evento</button><button class="aprovar" data-id="${eventoId}">Aprovar Evento</button>`;
        }
    }

    /**
     * Formata a data para DD/MM/YYYY
     */
    function formatarData(dateString) {
        const [ano, mes, dia] = dateString.split('-');
        return `${dia}/${mes}/${ano}`;
    }

    /**
     * Abre e preenche o modal de DETALHES com os botões corretos.
     */
    function abrirModalDecisao(evento) {
        let respostas = [];
        if (evento.respostas_professores) {
            try {
                respostas = JSON.parse(evento.respostas_professores) || [];
            } catch (e) { console.warn("JSON das respostas inválido."); }
        }

        let tituloRespostas = 'Respostas dos Professores';
        let respostasHtml = '';

        if (evento.tipo_solicitante === 'Coordenador') {
            tituloRespostas = 'Professores Envolvidos';
            if (respostas.length > 0) {
                respostas.forEach(r => {
                    respostasHtml += `<div class="response-item"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="#000000" d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512H418.3c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304H178.3z"/></svg><div><p>${r.nome}</p></div></div>`;
                });
            } else {
                respostasHtml = '<p>Nenhum professor diretamente envolvido.</p>';
            }
        } else if (respostas.length > 0) {
            respostas.forEach(r => {
                let statusClass = r.status === 'Aprovado' ? 'aprovado' : (r.status === 'Recusado' ? 'recusado' : 'sem-resposta');
                respostasHtml += `<div class="response-item"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="#000000" d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512H418.3c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304H178.3z"/></svg><div><p>${r.nome}</p><span class="${statusClass}">${r.status}</span></div></div>`;
            });
        } else {
            respostasHtml = '<p>Nenhum professor para aprovação neste evento.</p>';
        }

        modalLeft.innerHTML = `<div class="coordinator-info"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="#000000" d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512H418.3c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304H178.3z"/></svg><div><h3>${evento.nm_solicitante}</h3><p>${evento.tipo_solicitante}</p></div></div><div class="responses-section"><h4>${tituloRespostas}</h4>${respostasHtml}</div>`;
        
        let botoesHtml = '';
        if (evento.status === 'Solicitado') {
            botoesHtml = `<div class="modal-buttons"><button class="recusar" data-id="${evento.cd_evento}">Recusar Evento</button><button class="aprovar" data-id="${evento.cd_evento}">Aprovar Evento</button></div>`;
        }
        else {
             botoesHtml = `<div class="modal-buttons"><button class="btn-excluir-evento recusar" data-id="${evento.cd_evento}">Excluir Evento</button></div>`;
        }

        modalRight.innerHTML = `
            <h3>Detalhes do Evento</h3>
            <div class="form-group"><label>Título:</label><input type="text" readonly value="${evento.nm_evento}"></div>
            <div class="form-row">
                <div class="form-group"><label>Horário:</label><input type="text" readonly value="${evento.horario_inicio.substr(0, 5)} - ${evento.horario_fim.substr(0, 5)}"></div>
                <div class="form-group"><label>Data:</label><input type="text" readonly value="${formatarData(evento.dt_evento)}"></div>
            </div>
            <div class="form-row">
                <div class="form-group"><label>Turmas:</label><input type="text" readonly value="${evento.turmas_envolvidas || 'N/A'}"></div>
                <div class="form-group"><label>Total de Alunos:</label><input type="text" readonly value="${evento.total_alunos || '0'}" style="font-weight: bold;"></div>
            </div>
            <label>Descrição:</label><textarea readonly>${evento.ds_descricao}</textarea>
            ${botoesHtml}
        `;
        
        modalDetalhes.style.display = 'flex';
    }
});