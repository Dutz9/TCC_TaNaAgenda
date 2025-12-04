/**
 * Mostra uma barra de feedback flutuante no topo da tela.
 */
function showToast(message, type = 'sucesso') {
    const bar = document.getElementById('toast-notification');
    if (!bar) return;
    bar.textContent = message;
    bar.className = `feedback-bar ${type} show`;
    setTimeout(() => { bar.classList.remove('show'); }, 3500);
}

document.addEventListener('DOMContentLoaded', () => {
    if (typeof eventosDaPagina === 'undefined' || typeof usuario_logado === 'undefined') {
        console.error("Variáveis de dados não encontradas.");
        return;
    }

    const container = document.querySelector('.notificacao-container');
    const modalDetalhes = document.getElementById('modal-detalhes-evento');
    const modalConfirmar = document.getElementById('modal-confirm-cancelar');
    
    // --- ELEMENTOS DO MODAL DE MOTIVO (RECUSA) ---
    const modalMotivo = document.getElementById('modal-motivo-recusa');
    const btnCancelarRecusa = document.getElementById('btn-cancelar-recusa');
    const formRecusa = document.getElementById('form-recusa');
    const textoMotivo = document.getElementById('texto-motivo');
    
    // --- ELEMENTOS DO MODAL DE VISUALIZAÇÃO ---
    const modalVisualizar = document.getElementById('modal-visualizar-motivo');
    const btnFecharVisualizacao = document.getElementById('btn-fechar-visualizacao');

    let eventoParaCancelar = null;
    let eventoIdParaRecusar = null;
    let btnRecusarClicado = null; // Guarda referência do botão para atualizar UI

    const btnConfirmarSim = document.getElementById('btn-cancelar-sim');
    const btnConfirmarNao = document.getElementById('btn-cancelar-nao');

    // --- Lógica de Filtros ---
    const formFiltros = document.getElementById('form-filtros');
    if (formFiltros) {
        formFiltros.addEventListener('change', () => formFiltros.submit());
    }

    // --- Abrir Detalhes ---
    container.addEventListener('click', (e) => {
        const botaoClicado = e.target.closest('button.detalhes-btn');
        if (botaoClicado) {
            const eventoId = botaoClicado.dataset.id;
            const evento = eventosDaPagina.find(ev => ev.cd_evento === eventoId);
            if (evento) abrirModalDetalhes(evento);
        }
    });

    // --- Ações dentro do Modal de Detalhes ---
    modalDetalhes.addEventListener('click', (e) => {
        if (e.target === modalDetalhes) {
            modalDetalhes.style.display = 'none';
            return;
        }

        const botaoClicado = e.target.closest('button, a');
        
        // Clicou num item de resposta (Ver Motivo)
        const itemResposta = e.target.closest('.response-item');
        if (itemResposta && !botaoClicado) { 
            const motivoTexto = itemResposta.dataset.motivo;
            if (motivoTexto && motivoTexto !== 'null' && motivoTexto.trim() !== '') {
                document.getElementById('conteudo-motivo-leitura').textContent = motivoTexto;
                modalVisualizar.style.display = 'flex';
            }
        }

        if (!botaoClicado) return;

        // Botão Aprovar (Direto)
        if (botaoClicado.classList.contains('btn-aprovar')) {
            e.preventDefault();
            const eventoId = botaoClicado.dataset.id;
            enviarResposta(eventoId, 'Aprovado', null, botaoClicado);
        }
        
        // Botão Recusar (Abre modal de motivo)
        if (botaoClicado.classList.contains('btn-recusar')) {
            e.preventDefault();
            eventoIdParaRecusar = botaoClicado.dataset.id;
            btnRecusarClicado = botaoClicado;
            textoMotivo.value = ''; // Limpa textarea
            modalMotivo.style.display = 'flex';
        }
        
        // Botão Cancelar Solicitação
        if (botaoClicado.classList.contains('btn-cancelar-solicitacao')) {
            e.preventDefault();
            eventoParaCancelar = botaoClicado.dataset.id;
            modalConfirmar.style.display = 'flex';
        }
    });

    // --- Lógica Modal Motivo (Recusa) ---
    if (btnCancelarRecusa) {
        btnCancelarRecusa.addEventListener('click', () => {
            modalMotivo.style.display = 'none';
            eventoIdParaRecusar = null;
        });
    }

    if (formRecusa) {
        formRecusa.addEventListener('submit', (e) => {
            e.preventDefault();
            const motivo = textoMotivo.value.trim();
            if (motivo === "") {
                alert("Por favor, digite um motivo.");
                return;
            }
            if (eventoIdParaRecusar) {
                enviarResposta(eventoIdParaRecusar, 'Recusado', motivo, btnRecusarClicado);
                modalMotivo.style.display = 'none';
            }
        });
    }

    if (modalMotivo) {
        modalMotivo.addEventListener('click', (e) => {
            if (e.target === modalMotivo) modalMotivo.style.display = 'none';
        });
    }

    // --- Lógica Modal Visualizar Motivo ---
    if (btnFecharVisualizacao) {
        btnFecharVisualizacao.addEventListener('click', () => modalVisualizar.style.display = 'none');
    }
    if (modalVisualizar) {
        modalVisualizar.addEventListener('click', (e) => {
            if (e.target === modalVisualizar) modalVisualizar.style.display = 'none';
        });
    }

    // --- Lógica Modal Cancelar ---
    btnConfirmarNao.addEventListener('click', () => modalConfirmar.style.display = 'none');
    
    btnConfirmarSim.addEventListener('click', () => {
        if (eventoParaCancelar) enviarCancelamento(eventoParaCancelar);
    });

    modalConfirmar.addEventListener('click', (e) => {
        if (e.target === modalConfirmar) modalConfirmar.style.display = 'none';
    });

    // --- FUNÇÕES AJAX ---

    async function enviarCancelamento(eventoId) {
        btnConfirmarSim.disabled = true;
        btnConfirmarSim.textContent = 'Cancelando...';
        const formData = new FormData();
        formData.append('cd_evento', eventoId);

        try {
            const response = await fetch('../api/cancelar_evento.php', { method: 'POST', body: formData });
            const result = await response.json();

            if (response.ok && result.status === 'sucesso') {
                modalConfirmar.style.display = 'none';
                modalDetalhes.style.display = 'none';
                showToast(result.mensagem, 'sucesso');
                const card = document.querySelector(`.notificacao .detalhes-btn[data-id="${eventoId}"]`).closest('.notificacao');
                if (card) {
                    card.style.opacity = '0';
                    setTimeout(() => card.remove(), 500);
                }
            } else {
                showToast(result.mensagem || 'Erro ao cancelar.', 'erro');
            }
        } catch (error) {
            showToast('Erro de comunicação.', 'erro');
        }
        btnConfirmarSim.disabled = false;
        btnConfirmarSim.textContent = 'Sim, Cancelar';
        eventoParaCancelar = null;
    }

    async function enviarResposta(eventoId, resposta, motivo, botao) {
        let containerBotoes = null;
        if (botao) {
            containerBotoes = botao.parentElement;
            containerBotoes.innerHTML = '<p>Processando...</p>';
        }

        const formData = new FormData();
        formData.append('cd_evento', eventoId);
        formData.append('resposta', resposta);
        if (motivo) formData.append('motivo', motivo);

        try {
            const response = await fetch('../api/responder_evento.php', { method: 'POST', body: formData });
            const result = await response.json();

            if (response.ok && result.status === 'sucesso') {
                const indice = eventosDaPagina.findIndex(ev => ev.cd_evento === eventoId);
                if (indice > -1) {
                    eventosDaPagina[indice].minha_resposta = resposta;
                    let respostas = JSON.parse(eventosDaPagina[indice].respostas_professores || '[]');
                    let eu = respostas.find(r => r.nome === usuario_logado.nm_usuario);
                    if (eu) {
                        eu.status = resposta;
                        if(motivo) eu.motivo = motivo;
                    }
                    eventosDaPagina[indice].respostas_professores = JSON.stringify(respostas);
                }
                
                const card = document.querySelector(`.notificacao .detalhes-btn[data-id="${eventoId}"]`).closest('.notificacao');
                const divResp = card.querySelector('.opcoes-resposta');
                if (divResp) {
                    const cls = resposta === 'Aprovado' ? 'status-aprovado' : 'status-recusado';
                    divResp.innerHTML = `<p class="${cls}">Sua resposta: ${resposta}</p>`;
                }
                if (card) card.classList.add('card-respondido');
                modalDetalhes.style.display = 'none';
                
                if (typeof atualizarNotificacoes === 'function') atualizarNotificacoes();
                showToast('Resposta enviada!', 'sucesso');
            } else {
                showToast(result.mensagem, 'erro');
                if (containerBotoes) {
                    // Restaura botões
                    containerBotoes.innerHTML = `<button class="btn-recusar" data-id="${eventoId}">Recusar</button><button class="btn-aprovar" data-id="${eventoId}">Aprovar</button>`;
                }
            }
        } catch (error) {
            showToast('Erro de comunicação.', 'erro');
            console.error(error);
        }
    }
    
    function formatarData(ds) {
        const [a, m, d] = ds.split('-');
        return `${d}/${m}/${a}`;
    }

    function abrirModalDetalhes(evento) {
        let respostas = [];
        try { respostas = JSON.parse(evento.respostas_professores) || []; } catch(e){}

        let titulo = (evento.tipo_solicitante === 'Coordenador') ? 'Professores Envolvidos' : 'Respostas dos Professores';
        let htmlResp = '';
        
        if (respostas.length > 0) {
             respostas.forEach(r => {
                let nome = (r.nome === usuario_logado.nm_usuario) ? 'Você' : r.nome;
                let dataMotivo = r.motivo ? `data-motivo="${r.motivo.replace(/"/g, '&quot;')}"` : '';
                let hint = r.motivo ? ' title="Clique para ver o motivo"' : '';
                let statusHtml = '';
                
                if(evento.tipo_solicitante === 'Professor') {
                    let cls = r.status === 'Aprovado' ? 'aprovado' : (r.status === 'Recusado' ? 'recusado' : 'sem-resposta');
                    statusHtml = `<span class="${cls}">${r.status}</span>`;
                }
                htmlResp += `<div class="response-item" ${dataMotivo} ${hint}><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="#000000" d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512H418.3c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304H178.3z"/></svg><div><p>${nome}</p>${statusHtml}</div></div>`;
             });
        } else {
            htmlResp = '<p>Nenhum professor envolvido.</p>';
        }

        const mLeft = document.querySelector('#modal-detalhes-evento .modal-left');
        const mRight = document.querySelector('#modal-detalhes-evento .modal-right');

        mLeft.innerHTML = `<div class="coordinator-info"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="#000000" d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512H418.3c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304H178.3z"/></svg><div><h3>${evento.nm_solicitante}</h3><p>${evento.tipo_solicitante}</p></div></div><div class="responses-section"><h4>${titulo}</h4><div class="respostas-vinculadas">${htmlResp}</div></div>`;
        
        let botoes = '';
        if (evento.status === 'Solicitado' && evento.cd_usuario_solicitante !== usuario_logado.cd_usuario && evento.minha_resposta === 'Pendente') {
            botoes = `<div class="modal-buttons"><button class="btn-recusar" data-id="${evento.cd_evento}">Recusar</button><button class="btn-aprovar" data-id="${evento.cd_evento}">Aprovar</button></div>`;
        } else if (evento.status === 'Solicitado' && evento.cd_usuario_solicitante === usuario_logado.cd_usuario) {
            botoes = `<div class="modal-buttons"><button class="btn-cancelar-solicitacao recusar" data-id="${evento.cd_evento}">Cancelar Solicitação</button><a href="criarevento.php?edit=${evento.cd_evento}" class="btn-editar-evento">Editar</a></div>`;
        }

        mRight.innerHTML = `<h3>Detalhes do Evento</h3>
            <div class="form-group"><label>Título:</label><input type="text" readonly value="${evento.nm_evento}"></div>
            <div class="form-row"><div class="form-group"><label>Horário:</label><input type="text" readonly value="${evento.horario_inicio.substr(0,5)} - ${evento.horario_fim.substr(0,5)}"></div><div class="form-group"><label>Data:</label><input type="text" readonly value="${formatarData(evento.dt_evento)}"></div></div>
            <div class="form-row"><div class="form-group"><label>Turmas:</label><input type="text" readonly value="${evento.turmas_envolvidas||'N/A'}"></div><div class="form-group"><label>Total de Alunos:</label><input type="text" readonly value="${evento.total_alunos||'0'}"></div></div>
            <label>Descrição:</label><textarea readonly>${evento.ds_descricao}</textarea>
            ${botoes}`;
        
        document.getElementById('modal-detalhes-evento').style.display = 'flex';
    }
});