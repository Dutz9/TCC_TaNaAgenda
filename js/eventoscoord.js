
function showFeedback(message, type = 'sucesso') {
    const bar = document.getElementById('feedback-bar');
    if (!bar) return;
    bar.textContent = message;
    bar.className = `feedback-bar ${type} show`;
    setTimeout(() => { bar.classList.remove('show'); }, 3500);
}


document.addEventListener('DOMContentLoaded', () => {
    if (typeof eventosDaPagina === 'undefined' || typeof usuario_logado === 'undefined') {
        console.error("Variáveis de dados ('eventosDaPagina' ou 'usuario_logado') não foram encontradas.");
        return;
    }


    const container = document.querySelector('.notificacao-container');
    const modalDetalhes = document.getElementById('modal-decisao-coord');
    const modalConfirmar = document.getElementById('modal-confirm-excluir');

    const modalVisualizar = document.getElementById('modal-visualizar-motivo');
    const btnFecharVisualizacao = document.getElementById('btn-fechar-visualizacao');

    if (!container || !modalDetalhes || !modalConfirmar) {
        console.error('Elementos essenciais (container ou modais) não foram encontrados.');
        return;
    }

    const modalLeft = modalDetalhes.querySelector('.modal-left');
    const modalRight = modalDetalhes.querySelector('.modal-right');
    const btnConfirmarSim = document.getElementById('btn-excluir-sim');
    const btnConfirmarNao = document.getElementById('btn-excluir-nao');
    
    let eventoParaExcluir = null;


    const formFiltros = document.getElementById('form-filtros');
    if (formFiltros) {
        formFiltros.addEventListener('change', () => {
            formFiltros.submit();
        });
    }


    
    container.addEventListener('click', (e) => {
        const botao = e.target.closest('button.detalhes-btn');
        if (botao) {
            const eventoId = botao.dataset.id;
            const evento = eventosDaPagina.find(ev => ev.cd_evento === eventoId);
            if (evento) abrirModalDecisao(evento);
        }
    });

    modalDetalhes.addEventListener('click', (e) => {
  
        if (e.target === modalDetalhes) {
            modalDetalhes.style.display = 'none';
            return;
        }

        const botaoClicado = e.target.closest('button, a');


        const itemResposta = e.target.closest('.response-item');
        if (itemResposta && !botaoClicado) { 
            const motivoTexto = itemResposta.dataset.motivo;
            

            if (motivoTexto && motivoTexto !== 'null' && motivoTexto.trim() !== '') {
                const displayTexto = document.getElementById('conteudo-motivo-leitura');
                if(displayTexto) {
                    displayTexto.textContent = motivoTexto;
                    modalVisualizar.style.display = 'flex';
                }
            }
        }


        if (!botaoClicado) return;

        if (botaoClicado.classList.contains('aprovar') || botaoClicado.classList.contains('recusar')) {
            e.preventDefault();
            const eventoId = botaoClicado.dataset.id;
            const decisao = botaoClicado.classList.contains('aprovar') ? 'Aprovado' : 'Recusado';
            enviarDecisaoFinal(eventoId, decisao, botaoClicado);
        }
        
        if (botaoClicado.classList.contains('btn-excluir-evento')) {
            e.preventDefault();
            eventoParaExcluir = botaoClicado.dataset.id;
            modalConfirmar.style.display = 'flex';
        }
    });

    if (btnFecharVisualizacao) {
        btnFecharVisualizacao.addEventListener('click', () => {
            modalVisualizar.style.display = 'none';
        });
    }
    if (modalVisualizar) {
        modalVisualizar.addEventListener('click', (e) => {
            if (e.target === modalVisualizar) {
                modalVisualizar.style.display = 'none';
            }
        });
    }

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


    async function enviarExclusao(eventoId) {
        btnConfirmarSim.disabled = true;
        btnConfirmarSim.textContent = 'Excluindo...';
        
        const formData = new FormData();
        formData.append('cd_evento', eventoId);

        try {
            const response = await fetch('../api/excluir_evento.php', { method: 'POST', body: formData });
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
                showFeedback(result.mensagem || 'Não foi possível excluir.', 'erro');
            }
        } catch (error) {
            showFeedback('Erro de comunicação.', 'erro');
            console.error('Erro no fetch:', error);
        }
        
        btnConfirmarSim.disabled = false;
        btnConfirmarSim.textContent = 'Sim, Excluir';
        eventoParaExcluir = null;
    }

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

    function formatarData(dateString) {
        const [ano, mes, dia] = dateString.split('-');
        return `${dia}/${mes}/${ano}`;
    }

    function abrirModalDecisao(evento) {
        let respostas = [];
        if (evento.respostas_professores) {
            try {
                respostas = JSON.parse(evento.respostas_professores) || [];
            } catch (e) { console.warn("JSON das respostas inválido."); }
        }
        let tituloRespostas = 'Respostas dos Professores'; 
        let respostasHtml = '';
        
        if (evento.tipo_solicitante === 'Coordenador' || evento.tipo_solicitante === 'Administrador') {
            tituloRespostas = 'Professores Envolvidos';
            
            if (respostas.length > 0) {
                respostas.forEach(r => {
                    respostasHtml += `<div class="response-item">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="#000000" d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512H418.3c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304H178.3z"/></svg>
                                        <div><p>${r.nome}</p></div>
                                      </div>`;
                });
            } else {
                 respostasHtml = '<p>Nenhum professor diretamente envolvido.</p>';
            }
    
        } else if (respostas.length > 0) {
            tituloRespostas = 'Respostas dos Professores';
            respostas.forEach(r => {
                let statusText = r.status || 'Pendente'; 
                let statusClass = r.status === 'Aprovado' ? 'aprovado' : (r.status === 'Recusado' ? 'recusado' : 'sem-resposta');
                

                let dataMotivo = r.motivo ? `data-motivo="${r.motivo.replace(/"/g, '&quot;')}"` : '';
                let tooltipHint = r.motivo ? ' title="Clique para ver o motivo"' : '';
                
                respostasHtml += `<div class="response-item" ${dataMotivo} ${tooltipHint}>
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="#000000" d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512H418.3c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304H178.3z"/></svg>
                                    <div><p>${r.nome}</p><span class="${statusClass}">${statusText}</span></div>
                                  </div>`;
            });
        } else {
            respostasHtml = '<p>Nenhum professor para aprovação neste evento.</p>';
        }
    
        modalLeft.innerHTML = `<div class="coordinator-info"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="#000000" d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512H418.3c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304H178.3z"/></svg><div><h3>${evento.nm_solicitante}</h3><p>${evento.tipo_solicitante}</p></div></div><div class="responses-section"><h4>${tituloRespostas}</h4><div class="respostas-vinculadas">${respostasHtml}</div></div>`;
                
        let botoesHtml = '';
        
        if (evento.status === 'Solicitado') {
            botoesHtml = `<div class="modal-buttons"><button class="recusar" data-id="${evento.cd_evento}">Recusar Evento</button><button class="aprovar" data-id="${evento.cd_evento}">Aprovar Evento</button></div>`;
        }
        else if (evento.status === 'Aprovado') {
             botoesHtml = `<div class="modal-buttons">
                            <button class="btn-excluir-evento recusar" data-id="${evento.cd_evento}">Excluir</button>
                            <button class="recusar" data-id="${evento.cd_evento}">Reverter p/ Recusado</button>
                            <a href="criareventocoord.php?edit=${evento.cd_evento}" class="btn-editar-evento">Editar</a>
                          </div>`;
        }
        else if (evento.status === 'Recusado') {
             botoesHtml = `<div class="modal-buttons">
                            <button class="btn-excluir-evento recusar" data-id="${evento.cd_evento}">Excluir</button>
                            <button class="aprovar" data-id="${evento.cd_evento}">Reverter p/ Aprovado</button>
                          </div>`;
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