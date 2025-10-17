/**
 * Mostra uma barra de feedback flutuante no topo da tela.
 * Fica fora do DOMContentLoaded para ser acessível pelo PHP.
 * @param {string} message - A mensagem a ser exibida.
 * @param {string} type - 'sucesso' ou 'erro'.
 */
function showToast(message, type = 'sucesso') {
    const bar = document.getElementById('toast-notification');
    if (!bar) return;

    bar.textContent = message;
    bar.className = `feedback-bar ${type} show`;

    // Esconde a barra após 3.5 segundos
    setTimeout(() => {
        bar.classList.remove('show');
    }, 3500);
}


// --- LÓGICA PRINCIPAL DA PÁGINA ---
document.addEventListener('DOMContentLoaded', () => {
    // Garante que as 'pontes' de dados do PHP existem
    if (typeof eventosDaPagina === 'undefined' || typeof usuario_logado === 'undefined') {
        console.error("Variáveis de dados ('eventosDaPagina' ou 'usuario_logado') não foram encontradas.");
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

    // --- "ESCUTADORES" DE CLIQUES ---
    
    // 1. Escutador nos CARDS para abrir o modal de detalhes
    container.addEventListener('click', (e) => {
        const botaoClicado = e.target.closest('button.detalhes-btn');
        if (!botaoClicado) return;
        
        const eventoId = botaoClicado.dataset.id;
        if (!eventoId) return;

        const evento = eventosDaPagina.find(ev => ev.cd_evento === eventoId);
        if (evento) {
            abrirModalDetalhes(evento);
        }
    });

    // 2. Escutador DENTRO DO MODAL para os botões de Aprovar/Recusar e para fechar
    modal.addEventListener('click', (e) => {
        // Se o clique foi no fundo do modal, fecha o modal
        if (e.target === modal) {
            modal.style.display = 'none';
            return;
        }

        const botaoClicado = e.target.closest('button');
        // Se o clique foi em um botão de ação
        if (botaoClicado && (botaoClicado.classList.contains('btn-aprovar') || botaoClicado.classList.contains('btn-recusar'))) {
            const eventoId = botaoClicado.dataset.id;
            const decisao = botaoClicado.classList.contains('btn-aprovar') ? 'Aprovado' : 'Recusado';
            enviarResposta(eventoId, decisao, botaoClicado);
        }
    });


    // --- FUNÇÕES ---

    /**
     * Envia a resposta (Aprovado/Recusado) do professor para a API.
     */
    async function enviarResposta(eventoId, resposta, botao) {
        const containerBotoes = botao.parentElement;
        containerBotoes.innerHTML = '<p>Processando...</p>';

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
                // SINCRONIZAÇÃO DA MEMÓRIA INTERNA
                const indiceEvento = eventosDaPagina.findIndex(ev => ev.cd_evento === eventoId);
                if (indiceEvento > -1) {
                    eventosDaPagina[indiceEvento].minha_resposta = resposta;
                    let respostas = JSON.parse(eventosDaPagina[indiceEvento].respostas_professores || '[]');
                    let minhaRespostaNaLista = respostas.find(r => r.nome === usuario_logado.nm_usuario);
                    if (minhaRespostaNaLista) {
                        minhaRespostaNaLista.status = resposta;
                    }
                    eventosDaPagina[indiceEvento].respostas_professores = JSON.stringify(respostas);
                }
                
                // Atualiza a interface do card
                const card = document.querySelector(`.notificacao .detalhes-btn[data-id="${eventoId}"]`).closest('.notificacao');
                const opcoesRespostaDiv = card.querySelector('.opcoes-resposta');
                if (opcoesRespostaDiv) {
                    const statusTexto = `Sua resposta: ${resposta}`;
                    const statusClasse = resposta === 'Aprovado' ? 'status-aprovado' : 'status-recusado';
                    opcoesRespostaDiv.innerHTML = `<p class="${statusClasse}">${statusTexto}</p>`;
                }
                
                // Fecha o modal após a ação
                modal.style.display = 'none';

            } else {
                alert('Erro: ' + (result.mensagem || 'Não foi possível registrar a resposta.'));
                containerBotoes.innerHTML = `<button class="btn-recusar" data-id="${eventoId}">Recusar</button><button class="btn-aprovar" data-id="${eventoId}">Aprovar</button>`;
            }
        } catch (error) {
            alert('Ocorreu um erro de comunicação.');
            console.error('Erro no fetch:', error);
            containerBotoes.innerHTML = `<button class="btn-recusar" data-id="${eventoId}">Recusar</button><button class="btn-aprovar" data-id="${eventoId}">Aprovar</button>`;
        }
    }
    
    function formatarData(dateString) {
        const [ano, mes, dia] = dateString.split('-');
        return `${dia}/${mes}/${ano}`;
    }

    /**
     * Abre e preenche o modal com os detalhes do evento e os botões de ação.
     */
    function abrirModalDetalhes(evento) {
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
        
        let botoesHtml = '';
        if (evento.status === 'Solicitado' && evento.cd_usuario_solicitante !== usuario_logado.cd_usuario && evento.minha_resposta === null) {
            botoesHtml = `<div class="modal-buttons"><button class="btn-recusar" data-id="${evento.cd_evento}">Recusar</button><button class="btn-aprovar" data-id="${evento.cd_evento}">Aprovar</button></div>`;
        }

        modalRight.innerHTML = `<h3>Detalhes do Evento</h3><div class="form-group"><label>Título:</label><input type="text" readonly value="${evento.nm_evento}"></div><div class="form-row"><div class="form-group"><label>Horário:</label><input type="text" readonly value="${evento.horario_inicio.substr(0, 5)} - ${evento.horario_fim.substr(0, 5)}"></div><div class="form-group"><label>Data:</label><input type="text" readonly value="${formatarData(evento.dt_evento)}"></div></div><div class="form-group"><label>Turmas:</label><input type="text" readonly value="${evento.turmas_envolvidas || 'N/A'}"></div><label>Descrição:</label><textarea readonly>${evento.ds_descricao}</textarea>${botoesHtml}`;
        
        modal.style.display = 'flex';
    }
});