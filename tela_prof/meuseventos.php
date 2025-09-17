<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus Eventos - TáNaAgenda</title>
    <link rel="shortcut icon" href="../image/Favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/index.css">
    <link rel="stylesheet" href="../css/meuseventos.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
</head>
<body>
    <header class="header">
        <a href="perfil.php"><p>Professor</p></a>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path fill="#ffffff" d="M320 312C386.3 312 440 258.3 440 192C440 125.7 386.3 72 320 72C253.7 72 200 125.7 200 192C200 258.3 253.7 312 320 312zM290.3 368C191.8 368 112 447.8 112 546.3C112 562.7 125.3 576 141.7 576L498.3 576C514.7 576 528 562.7 528 546.3C528 447.8 448.2 368 349.7 368L290.3 368z"/></svg>
    </header>

    <main>
    <section class="area-lado">
            <a href="agendaprof.php"><img src="../image/logotipo fundo azul.png" alt=""></a>
            <div class="area-menu">         
                <div class="menu-agenda">
                <img src="../image/icones/agenda.png" alt="">
                    <a href="agendaprof.php"><p>Agenda</p></a>
                </div>
                <div class="menu-meus-eventos ativo">
                <img src="../image/icones/eventos.png" alt="">
                    <a href="meuseventos.php"><p>Eventos</p></a>
                </div>
                <div class="menu-perfil">
                <img src="../image/icones/perfil.png" alt="">
                    <a href="perfil.php"><p>Perfil</p></a>
                </div>  
                <a href="../login.php"><div class="menu-sair"><p>SAIR</p></div></a> 
            </div>
        </section>

        <section class="area-notificacoes">
                <h2>Eventos</h2>
            <div class="notificacao-container">
                    <button class="criar-btn"> 
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="#ffffff" d="M256 512a256 256 0 1 0 0-512 256 256 0 1 0 0 512zM232 344l0-64-64 0c-13.3 0-24-10.7-24-24s10.7-24 24-24l64 0 0-64c0-13.3 10.7-24 24-24s24 10.7 24 24l0 64 64 0c13.3 0 24 10.7 24 24s-10.7 24-24 24l-64 0 0 64c0 13.3-10.7 24-24 24s-24-10.7-24-24z"/></svg>
                    </button>




                <div class="notificacao">
                <h3>Título do Evento</h3>
                <p>Solicitado por: Professor</p>
                <p>Data de Solicitação: 06/08/2025</p>
                <p>Data do Evento: 26/08/2025</p>
                <p>Turmas Envolvidas: 1I, 2I, 3I</p>
                <button class="detalhes-btn-responder-solicitacao">Mais Detalhes</button>
                </div>   






                
                <div class="notificacao">
                    <h3>Título do Evento</h3>
                    <p style="color: #0479F9; font-weight: bolder;">Evento Solicitado</p>
                    <p>Solicitado por: Você</p>
                    <p>Data de Solicitação: 05/08/2025</p>
                    <p>Data do Evento: 26/08/2025</p>
                    <button class="detalhes-btn">Mais Detalhes</button>
                </div>
                <div class="notificacao">
                    <h3>Título do Evento</h3>
                    <p style="color: #0479F9; font-weight: bolder;">Evento Solicitado</p>
                    <p>Solicitado por: Você</p>
                    <p>Data de Solicitação: 06/08/2025</p>
                    <p>Data do Evento: 27/08/2025</p>
                    <button class="detalhes-btn">Mais Detalhes</button>
                </div>
                <div class="notificacao">
                    <h3>Título do Evento</h3>
                    <p style="color: #1DB81D; font-weight: bolder;">Evento Aprovado</p>
                    <p>Solicitado por: Você</p>
                    <p>Data de Solicitação: 07/08/2025</p>
                    <p>Data do Evento: 28/08/2025</p>
                    <button class="detalhes-btn-seu-evento-aprovado">Mais Detalhes</button>
                </div>
                <div class="notificacao">
                    <h3>Título do Evento</h3>
                    <p style="color: #FF0000; font-weight: bolder;">Evento Recusado</p>
                    <p>Solicitado por: Você</p>
                    <p>Data de Solicitação: 11/08/2025</p>
                    <p>Data do Evento: 01/09/2025</p>
                    <button class="detalhes-btn-seu-evento-recusado">Mais Detalhes</button>
                </div>
                <div class="notificacao">
                    <h3>Título do Evento</h3>
                    <p style="color: #1DB81D; font-weight: bolder;">Evento Aprovado</p>
                    <p>Solicitado por: Você</p>
                    <p>Data de Solicitação: 09/08/2025</p>
                    <p>Data do Evento: 30/08/2025</p>
                    <button class="detalhes-btn-seu-evento-aprovado">Mais Detalhes</button>
                </div>













                <div class="notificacao-lido">
                        <h3>Título do Evento</h3>
                        <p style="color: #1DB81D; font-weight: bolder;">Evento Aprovado</p>
                        <p>Solicitado por: Você</p>
                        <p>Data de Solicitação: 10/08/2025</p>
                        <p>Data do Evento: 31/08/2025</p>
                        <button class="detalhes-btn-seu-evento-aprovado">Mais Detalhes</button>
                    </div>















                <div class="notificacao-lido">
                    <h3>Título do Evento</h3>
                    <p style="color: #FF0000; font-weight: bolder;">Evento Recusado</p>
                    <p>Solicitado por: Você</p>
                    <p>Data de Solicitação: 11/08/2025</p>
                    <p>Data do Evento: 01/09/2025</p>
                    <button class="detalhes-btn-seu-evento-recusado">Mais Detalhes</button>
                </div>
                <div class="notificacao-lido">
                    <h3>Título do Evento</h3>
                    <p style="color: #1DB81D; font-weight: bolder;">Evento Aprovado</p>
                    <p>Solicitado por: Você</p>
                    <p>Data de Solicitação: 11/08/2025</p>
                    <p>Data do Evento: 01/09/2025</p>
                    <button class="detalhes-btn-seu-evento-aprovado">Mais Detalhes</button>
                </div>
                <div class="notificacao-lido">
                    <h3>Título do Evento</h3>
                    <p style="color: #1DB81D; font-weight: bolder;">Evento Aprovado</p>
                    <p>Solicitado por: Você</p>
                    <p>Data de Solicitação: 11/08/2025</p>
                    <p>Data do Evento: 01/09/2025</p>
                    <button class="detalhes-btn-seu-evento-aprovado">Mais Detalhes</button>
                </div>
                <div class="notificacao-lido">
                    <h3>Título do Evento</h3>
                    <p style="color: #1DB81D; font-weight: bolder;">Evento Aprovado</p>
                    <p>Solicitado por: Você</p>
                    <p>Data de Solicitação: 11/08/2025</p>
                    <p>Data do Evento: 01/09/2025</p>
                    <button class="detalhes-btn-seu-evento-aprovado">Mais Detalhes</button>
                </div>
            </div>
        </section> 
    </main>
    
    <div id="modal-overlay-solicitacao" class="modal-overlay" style="display: none;">
        <div class="modal-content">
            <div class="modal-left">
                <div class="coordinator-info">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path fill="#000000" d="M320 312C386.3 312 440 258.3 440 192C440 125.7 386.3 72 320 72C253.7 72 200 125.7 200 192C200 258.3 253.7 312 320 312zM290.3 368C191.8 368 112 447.8 112 546.3C112 562.7 125.3 576 141.7 576L498.3 576C514.7 576 528 562.7 528 546.3C528 447.8 448.2 368 349.7 368L290.3 368z"/></svg>
                    <div>
                        <h3>Nome Professor</h3>
                        <p>Professor EM</p>
                    </div>
                </div>
                <div class="responses-section">
                    <h4>Respostas</h4>
                    <div class="response-item">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path fill="#000000" d="M320 312C386.3 312 440 258.3 440 192C440 125.7 386.3 72 320 72C253.7 72 200 125.7 200 192C200 258.3 253.7 312 320 312zM290.3 368C191.8 368 112 447.8 112 546.3C112 562.7 125.3 576 141.7 576L498.3 576C514.7 576 528 562.7 528 546.3C528 447.8 448.2 368 349.7 368L290.3 368z"/></svg>
                        <div>
                            <p>Professor 1</p>
                            <span class="aprovado">Aprovado</span>
                        </div>
                    </div>
                    <div class="response-item">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path fill="#000000" d="M320 312C386.3 312 440 258.3 440 192C440 125.7 386.3 72 320 72C253.7 72 200 125.7 200 192C200 258.3 253.7 312 320 312zM290.3 368C191.8 368 112 447.8 112 546.3C112 562.7 125.3 576 141.7 576L498.3 576C514.7 576 528 562.7 528 546.3C528 447.8 448.2 368 349.7 368L290.3 368z"/></svg>
                        <div>
                            <p>Professor 2</p>
                            <span class="aprovado">Aprovado</span>
                        </div>
                    </div>
                    <div class="response-item">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path fill="#000000" d="M320 312C386.3 312 440 258.3 440 192C440 125.7 386.3 72 320 72C253.7 72 200 125.7 200 192C200 258.3 253.7 312 320 312zM290.3 368C191.8 368 112 447.8 112 546.3C112 562.7 125.3 576 141.7 576L498.3 576C514.7 576 528 562.7 528 546.3C528 447.8 448.2 368 349.7 368L290.3 368z"/></svg>
                        <div>
                            <p>Professor 3</p>
                            <span class="aprovado">Aprovado</span>
                        </div>
                    </div>
                    <div class="response-item">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path fill="#000000" d="M320 312C386.3 312 440 258.3 440 192C440 125.7 386.3 72 320 72C253.7 72 200 125.7 200 192C200 258.3 253.7 312 320 312zM290.3 368C191.8 368 112 447.8 112 546.3C112 562.7 125.3 576 141.7 576L498.3 576C514.7 576 528 562.7 528 546.3C528 447.8 448.2 368 349.7 368L290.3 368z"/></svg>
                        <div>
                            <p class="respostausuario">Você</p>
                            <span class="sem-resposta">Ainda sem Resposta</span>
                        </div>
                    </div>
                    <div class="response-item">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path fill="#000000" d="M320 312C386.3 312 440 258.3 440 192C440 125.7 386.3 72 320 72C253.7 72 200 125.7 200 192C200 258.3 253.7 312 320 312zM290.3 368C191.8 368 112 447.8 112 546.3C112 562.7 125.3 576 141.7 576L498.3 576C514.7 576 528 562.7 528 546.3C528 447.8 448.2 368 349.7 368L290.3 368z"/></svg>
                        <div>
                            <p>Professor 5</p>
                            <span class="sem-resposta">Ainda sem Resposta</span>
                        </div>
                    </div>
                    <div class="response-item">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path fill="#000000" d="M320 312C386.3 312 440 258.3 440 192C440 125.7 386.3 72 320 72C253.7 72 200 125.7 200 192C200 258.3 253.7 312 320 312zM290.3 368C191.8 368 112 447.8 112 546.3C112 562.7 125.3 576 141.7 576L498.3 576C514.7 576 528 562.7 528 546.3C528 447.8 448.2 368 349.7 368L290.3 368z"/></svg>
                        <div>
                            <p>Professor 6</p>
                            <span class="sem-resposta">Ainda sem Resposta</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-right">
                <h3>Nome do Evento</h3>
                <div class="form-group">
                <label for="titulo-evento">Título do Evento:</label>
                <input type="text" id="titulo-evento" readonly value="Nome atual do evento">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="hora-inicio-fim">Hora de Início e Fim:</label>
                        <input type="text" id="hora-inicio-fim" readonly value="07:00 - 12:00">
                    </div>
                    <div class="form-group">
                        <label for="data-evento">Data do Evento:</label>
                        <input type="text" id="data-evento" readonly value="12/08/2025">
                    </div>
                </div>
                <div class="form-group">
                <label for="tipo-evento">Tipo do Evento:</label>
                <input type="text" id="tipo-evento" readonly value="Palestra">
            </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="professores-envolvidos">Professores Envolvidos:</label>
                        <input type="text" id="professores-envolvidos" readonly value="Nomes dos professores">
                    </div>
                    <div class="form-group">
                        <label for="turmas-envolvidas">Turmas Envolvidas:</label>
                        <input type="text" id="turmas-envolvidas" readonly value="1I, 2N, 3R, 3N">
                    </div>
                </div>
                <label for="descricao-evento">Descrição:</label>
                <textarea id="descricao-evento" readonly>Descrição do Evento</textarea>
                <div class="modal-buttons">
                    <button class="recusar">Recusar</button>
                    <button class="aprovar">Aprovar</button>
                </div>
            </div>
        </div>
    </div>

    <div id="confirmation-modal-responder-solicitacao" class="confirmation-modal-responder-solicitacao" style="display: none;">
        <div class="confirmation-content">
            <h3>Realmente deseja recusar o evento?</h3>
            <label for="motivo">Motivo:</label>
            <textarea id="motivo" placeholder="Opcional"></textarea>
            <div class="confirmation-buttons">
                <button class="cancelar-solicitacao">Cancelar</button>
                <button class="recusar-confirm">Recusar</button>
            </div>
        </div>
    </div>

    <div id="modal-overlay" class="modal-overlay" style="display: none;">
        <div class="modal-content">
            <div class="modal-left">
                <div class="coordinator-info">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path fill="#000000" d="M320 312C386.3 312 440 258.3 440 192C440 125.7 386.3 72 320 72C253.7 72 200 125.7 200 192C200 258.3 253.7 312 320 312zM290.3 368C191.8 368 112 447.8 112 546.3C112 562.7 125.3 576 141.7 576L498.3 576C514.7 576 528 562.7 528 546.3C528 447.8 448.2 368 349.7 368L290.3 368z"/></svg>
                    <div>
                        <h3>Nome Professor</h3>
                        <p>Professor EM</p>
                    </div>
                </div>
                <div class="responses-section">
                    <h4>Respostas</h4>
                    <div class="response-item">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path fill="#000000" d="M320 312C386.3 312 440 258.3 440 192C440 125.7 386.3 72 320 72C253.7 72 200 125.7 200 192C200 258.3 253.7 312 320 312zM290.3 368C191.8 368 112 447.8 112 546.3C112 562.7 125.3 576 141.7 576L498.3 576C514.7 576 528 562.7 528 546.3C528 447.8 448.2 368 349.7 368L290.3 368z"/></svg>
                        <div>
                            <p>Professor 1</p>
                            <span class="aprovado">Aprovado</span>
                        </div>
                    </div>
                    <div class="response-item">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path fill="#000000" d="M320 312C386.3 312 440 258.3 440 192C440 125.7 386.3 72 320 72C253.7 72 200 125.7 200 192C200 258.3 253.7 312 320 312zM290.3 368C191.8 368 112 447.8 112 546.3C112 562.7 125.3 576 141.7 576L498.3 576C514.7 576 528 562.7 528 546.3C528 447.8 448.2 368 349.7 368L290.3 368z"/></svg>
                        <div>
                            <p>Professor 2</p>
                            <span class="aprovado">Aprovado</span>
                        </div>
                    </div>
                    <div class="response-item">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path fill="#000000" d="M320 312C386.3 312 440 258.3 440 192C440 125.7 386.3 72 320 72C253.7 72 200 125.7 200 192C200 258.3 253.7 312 320 312zM290.3 368C191.8 368 112 447.8 112 546.3C112 562.7 125.3 576 141.7 576L498.3 576C514.7 576 528 562.7 528 546.3C528 447.8 448.2 368 349.7 368L290.3 368z"/></svg>
                        <div>
                            <p>Professor 3</p>
                            <span class="aprovado">Aprovado</span>
                        </div>
                    </div>
                    <div class="response-item">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path fill="#000000" d="M320 312C386.3 312 440 258.3 440 192C440 125.7 386.3 72 320 72C253.7 72 200 125.7 200 192C200 258.3 253.7 312 320 312zM290.3 368C191.8 368 112 447.8 112 546.3C112 562.7 125.3 576 141.7 576L498.3 576C514.7 576 528 562.7 528 546.3C528 447.8 448.2 368 349.7 368L290.3 368z"/></svg>
                        <div>
                            <p>Professor 4</p>
                            <span class="sem-resposta">Ainda sem Resposta</span>
                        </div>
                    </div>
                    <div class="response-item">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path fill="#000000" d="M320 312C386.3 312 440 258.3 440 192C440 125.7 386.3 72 320 72C253.7 72 200 125.7 200 192C200 258.3 253.7 312 320 312zM290.3 368C191.8 368 112 447.8 112 546.3C112 562.7 125.3 576 141.7 576L498.3 576C514.7 576 528 562.7 528 546.3C528 447.8 448.2 368 349.7 368L290.3 368z"/></svg>
                        <div>
                            <p>Professor 5</p>
                            <span class="sem-resposta">Ainda sem Resposta</span>
                        </div>
                    </div>
                    <div class="response-item">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path fill="#000000" d="M320 312C386.3 312 440 258.3 440 192C440 125.7 386.3 72 320 72C253.7 72 200 125.7 200 192C200 258.3 253.7 312 320 312zM290.3 368C191.8 368 112 447.8 112 546.3C112 562.7 125.3 576 141.7 576L498.3 576C514.7 576 528 562.7 528 546.3C528 447.8 448.2 368 349.7 368L290.3 368z"/></svg>
                        <div>
                            <p>Professor 6</p>
                            <span class="sem-resposta">Ainda sem Resposta</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-right">
                <h3>Modificar Evento</h3>
                <div class="form-group">
                    <label for="nome-evento">Nome do Evento:</label>
                    <input type="text" id="nome-evento" value="Título do Evento">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="hora-inicio">Hora de Início:</label>
                        <input type="text" id="hora-inicio" value="07:10">
                    </div>
                    <div class="form-group">
                        <label for="hora-fim">Hora de Fim:</label>
                        <input type="text" id="hora-fim" value="12:30">
                    </div> 
                </div> 
                <div class="form-row">
                <div class="form-group">
                        <label for="data-evento">Data do Evento:</label>
                        <input type="date" id="data-evento" value="2025-08-26">
                    </div>
                </div>
                <div class="form-group">
                    <label for="tipo-evento">Tipo do Evento:</label>
                    <select id="tipo-evento">
                        <option value="Palestra">Palestra</option>
                        <option value="Oficina">Oficina</option>
                        <option value="Reunião">Reunião</option>
                    </select>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="professores-envolvidos">Professores Envolvidos:</label>
                        <select id="professores-envolvidos" multiple>
                            <option value="Professor 1">Professor 1</option>
                            <option value="Professor 2">Professor 2</option>
                            <option value="Professor 3">Professor 3</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="turmas-envolvidas">Turmas Envolvidas:</label>
                        <input type="text" id="turmas-envolvidas" value="1I, 2N, 3R">
                    </div>
                </div>
                <label for="descricao-evento">Descrição:</label>
                <textarea id="descricao-evento">Descrição do evento a ser modificado.</textarea>
                <div class="modal-buttons">
                    <button class="excluir">Excluir Evento</button>
                    <button class="salvar">Salvar Alterações</button>
                </div>
            </div>
        </div>
    </div>


    <div id="modal-overlay-aprovado" class="modal-overlay" style="display: none;">
        <div class="modal-content">
            <div class="modal-left">
                <div class="coordinator-info">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path fill="#000000" d="M320 312C386.3 312 440 258.3 440 192C440 125.7 386.3 72 320 72C253.7 72 200 125.7 200 192C200 258.3 253.7 312 320 312zM290.3 368C191.8 368 112 447.8 112 546.3C112 562.7 125.3 576 141.7 576L498.3 576C514.7 576 528 562.7 528 546.3C528 447.8 448.2 368 349.7 368L290.3 368z"/></svg>
                    <div>
                        <h3>Nome Professor</h3>
                        <p>Professor EM</p>
                    </div>
                </div>
                <div class="responses-section">
                    <h4>Respostas</h4>
                    <div class="response-item">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path fill="#000000" d="M320 312C386.3 312 440 258.3 440 192C440 125.7 386.3 72 320 72C253.7 72 200 125.7 200 192C200 258.3 253.7 312 320 312zM290.3 368C191.8 368 112 447.8 112 546.3C112 562.7 125.3 576 141.7 576L498.3 576C514.7 576 528 562.7 528 546.3C528 447.8 448.2 368 349.7 368L290.3 368z"/></svg>
                        <div>
                            <p>Professor 1</p>
                            <span class="aprovado">Aprovado</span>
                        </div>
                    </div>
                    <div class="response-item">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path fill="#000000" d="M320 312C386.3 312 440 258.3 440 192C440 125.7 386.3 72 320 72C253.7 72 200 125.7 200 192C200 258.3 253.7 312 320 312zM290.3 368C191.8 368 112 447.8 112 546.3C112 562.7 125.3 576 141.7 576L498.3 576C514.7 576 528 562.7 528 546.3C528 447.8 448.2 368 349.7 368L290.3 368z"/></svg>
                        <div>
                            <p>Professor 2</p>
                            <span class="aprovado">Aprovado</span>
                        </div>
                    </div>
                    <div class="response-item">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path fill="#000000" d="M320 312C386.3 312 440 258.3 440 192C440 125.7 386.3 72 320 72C253.7 72 200 125.7 200 192C200 258.3 253.7 312 320 312zM290.3 368C191.8 368 112 447.8 112 546.3C112 562.7 125.3 576 141.7 576L498.3 576C514.7 576 528 562.7 528 546.3C528 447.8 448.2 368 349.7 368L290.3 368z"/></svg>
                        <div>
                            <p>Professor 3</p>
                            <span class="aprovado">Aprovado</span>
                        </div>
                    </div>
                    <div class="response-item">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path fill="#000000" d="M320 312C386.3 312 440 258.3 440 192C440 125.7 386.3 72 320 72C253.7 72 200 125.7 200 192C200 258.3 253.7 312 320 312zM290.3 368C191.8 368 112 447.8 112 546.3C112 562.7 125.3 576 141.7 576L498.3 576C514.7 576 528 562.7 528 546.3C528 447.8 448.2 368 349.7 368L290.3 368z"/></svg>
                        <div>
                            <p>Professor 4</p>
                            <span class="aprovado">Aprovado</span>
                        </div>
                    </div>
                    <div class="response-item">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path fill="#000000" d="M320 312C386.3 312 440 258.3 440 192C440 125.7 386.3 72 320 72C253.7 72 200 125.7 200 192C200 258.3 253.7 312 320 312zM290.3 368C191.8 368 112 447.8 112 546.3C112 562.7 125.3 576 141.7 576L498.3 576C514.7 576 528 562.7 528 546.3C528 447.8 448.2 368 349.7 368L290.3 368z"/></svg>
                        <div>
                            <p>Professor 5</p>
                            <span class="aprovado">Aprovado</span>
                        </div>
                    </div>
                    <div class="response-item">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path fill="#000000" d="M320 312C386.3 312 440 258.3 440 192C440 125.7 386.3 72 320 72C253.7 72 200 125.7 200 192C200 258.3 253.7 312 320 312zM290.3 368C191.8 368 112 447.8 112 546.3C112 562.7 125.3 576 141.7 576L498.3 576C514.7 576 528 562.7 528 546.3C528 447.8 448.2 368 349.7 368L290.3 368z"/></svg>
                        <div>
                            <p>Professor 6</p>
                            <span class="aprovado">Aprovado</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-right">
                <h3>Modificar Evento</h3>
                <div class="form-group">
                    <label for="nome-evento">Nome do Evento:</label>
                    <input type="text" id="nome-evento" value="Título do Evento">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="hora-inicio">Hora de Início:</label>
                        <input type="text" id="hora-inicio" value="07:10">
                    </div>
                    <div class="form-group">
                        <label for="hora-fim">Hora de Fim:</label>
                        <input type="text" id="hora-fim" value="12:30">
                    </div>
                </div> 
                <div class="form-row">
                <div class="form-group">
                        <label for="data-evento">Data do Evento:</label>
                        <input type="date" id="data-evento" value="2025-08-26">
                    </div>
                </div>
                <div class="form-group">
                    <label for="tipo-evento">Tipo do Evento:</label>
                    <select id="tipo-evento">
                        <option value="Palestra">Palestra</option>
                        <option value="Oficina">Oficina</option>
                        <option value="Reunião">Reunião</option>
                    </select>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="professores-envolvidos">Professores Envolvidos:</label>
                        <select id="professores-envolvidos" multiple>
                            <option value="Professor 1">Professor 1</option>
                            <option value="Professor 2">Professor 2</option>
                            <option value="Professor 3">Professor 3</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="turmas-envolvidas">Turmas Envolvidas:</label>
                        <input type="text" id="turmas-envolvidas" value="1I, 2N, 3R">
                    </div>
                </div>
                <label for="descricao-evento">Descrição:</label>
                <textarea id="descricao-evento">Descrição do evento a ser modificado.</textarea>
                <div class="modal-buttons">
                    <button class="excluir-aprovado">Cancelar Evento</button>
                </div>
            </div>
        </div>
    </div>

    <div id="modal-overlay-recusado" class="modal-overlay" style="display: none;">
        <div class="modal-content">
            <div class="modal-left">
                <div class="coordinator-info">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path fill="#000000" d="M320 312C386.3 312 440 258.3 440 192C440 125.7 386.3 72 320 72C253.7 72 200 125.7 200 192C200 258.3 253.7 312 320 312zM290.3 368C191.8 368 112 447.8 112 546.3C112 562.7 125.3 576 141.7 576L498.3 576C514.7 576 528 562.7 528 546.3C528 447.8 448.2 368 349.7 368L290.3 368z"/></svg>
                    <div>
                        <h3>Nome Professor</h3>
                        <p>Professor EM</p>
                    </div>
                </div>
                <div class="responses-section">
                    <h4>Respostas</h4>
                    <div class="response-item">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path fill="#000000" d="M320 312C386.3 312 440 258.3 440 192C440 125.7 386.3 72 320 72C253.7 72 200 125.7 200 192C200 258.3 253.7 312 320 312zM290.3 368C191.8 368 112 447.8 112 546.3C112 562.7 125.3 576 141.7 576L498.3 576C514.7 576 528 562.7 528 546.3C528 447.8 448.2 368 349.7 368L290.3 368z"/></svg>
                        <div>
                            <p>Professor 1</p>
                            <span class="recusado">Recusado</span>
                        </div>
                    </div>
                    <div class="response-item">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path fill="#000000" d="M320 312C386.3 312 440 258.3 440 192C440 125.7 386.3 72 320 72C253.7 72 200 125.7 200 192C200 258.3 253.7 312 320 312zM290.3 368C191.8 368 112 447.8 112 546.3C112 562.7 125.3 576 141.7 576L498.3 576C514.7 576 528 562.7 528 546.3C528 447.8 448.2 368 349.7 368L290.3 368z"/></svg>
                        <div>
                            <p>Professor 2</p>
                            <span class="aprovado">Aprovado</span>
                        </div>
                    </div>
                    <div class="response-item">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path fill="#000000" d="M320 312C386.3 312 440 258.3 440 192C440 125.7 386.3 72 320 72C253.7 72 200 125.7 200 192C200 258.3 253.7 312 320 312zM290.3 368C191.8 368 112 447.8 112 546.3C112 562.7 125.3 576 141.7 576L498.3 576C514.7 576 528 562.7 528 546.3C528 447.8 448.2 368 349.7 368L290.3 368z"/></svg>
                        <div>
                            <p>Professor 3</p>
                            <span class="recusado">Recusado</span>
                        </div>
                    </div>
                    <div class="response-item">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path fill="#000000" d="M320 312C386.3 312 440 258.3 440 192C440 125.7 386.3 72 320 72C253.7 72 200 125.7 200 192C200 258.3 253.7 312 320 312zM290.3 368C191.8 368 112 447.8 112 546.3C112 562.7 125.3 576 141.7 576L498.3 576C514.7 576 528 562.7 528 546.3C528 447.8 448.2 368 349.7 368L290.3 368z"/></svg>
                        <div>
                            <p>Professor 4</p>
                            <span class="aprovado">Aprovado</span>
                        </div>
                    </div>
                    <div class="response-item">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path fill="#000000" d="M320 312C386.3 312 440 258.3 440 192C440 125.7 386.3 72 320 72C253.7 72 200 125.7 200 192C200 258.3 253.7 312 320 312zM290.3 368C191.8 368 112 447.8 112 546.3C112 562.7 125.3 576 141.7 576L498.3 576C514.7 576 528 562.7 528 546.3C528 447.8 448.2 368 349.7 368L290.3 368z"/></svg>
                        <div>
                            <p>Professor 5</p>
                            <span class="aprovado">Aprovado</span>
                        </div>
                    </div>
                    <div class="response-item">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path fill="#000000" d="M320 312C386.3 312 440 258.3 440 192C440 125.7 386.3 72 320 72C253.7 72 200 125.7 200 192C200 258.3 253.7 312 320 312zM290.3 368C191.8 368 112 447.8 112 546.3C112 562.7 125.3 576 141.7 576L498.3 576C514.7 576 528 562.7 528 546.3C528 447.8 448.2 368 349.7 368L290.3 368z"/></svg>
                        <div>
                            <p>Professor 6</p>
                            <span class="aprovado">Aprovado</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-right">
                <h3>Detalhes Evento</h3>
                <div class="form-group">
                    <label for="nome-evento">Nome do Evento:</label>
                    <input type="text" id="nome-evento" value="Título do Evento">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="hora-inicio">Hora de Início:</label>
                        <input type="text" id="hora-inicio" value="07:10">
                    </div>
                    <div class="form-group">
                        <label for="hora-fim">Hora de Fim:</label>
                        <input type="text" id="hora-fim" value="12:30">
                    </div>
                </div> 
                <div class="form-row">
                <div class="form-group">
                        <label for="data-evento">Data do Evento:</label>
                        <input type="date" id="data-evento" value="2025-08-26">
                    </div>
                </div>
                <div class="form-group">
                    <label for="tipo-evento">Tipo do Evento:</label>
                    <select id="tipo-evento">
                        <option value="Palestra">Palestra</option>
                        <option value="Oficina">Oficina</option>
                        <option value="Reunião">Reunião</option>
                    </select>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="professores-envolvidos">Professores Envolvidos:</label>
                        <select id="professores-envolvidos" multiple>
                            <option value="Professor 1">Professor 1</option>
                            <option value="Professor 2">Professor 2</option>
                            <option value="Professor 3">Professor 3</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="turmas-envolvidas">Turmas Envolvidas:</label>
                        <input type="text" id="turmas-envolvidas" value="1I, 2N, 3R">
                    </div>
                </div>
                <label for="descricao-evento">Descrição:</label>
                <textarea id="descricao-evento">Descrição do evento.</textarea>
                <div class="modal-buttons">
                    <button class="excluir-recusado">Apagar Evento</button>
                </div>
            </div>
        </div>
    </div>

    <div id="confirmation-modal" class="confirmation-modal" style="display: none;">
        <div class="confirmation-content">
            <h3>Realmente deseja excluir o evento?</h3>
            <div class="confirmation-buttons">
                <button class="cancelar">Cancelar</button>
                <button class="excluir-confirm">Excluir</button>
            </div>
        </div>
    </div>

    <div id="confirmation-modal-aprovado" class="confirmation-modal" style="display: none;">
        <div class="confirmation-content">
            <h3>Realmente deseja cancelar o evento?</h3>
            <div class="confirmation-buttons">
                <button class="cancelarAprovado">Cancelar</button>
                <button class="cancelar-confirm">Cancelar Evento</button>
            </div>
        </div>
    </div>

    <div id="confirmation-modal-recusado" class="confirmation-modal" style="display: none;">
        <div class="confirmation-content">
            <h3>Realmente deseja apagar o evento?</h3>
            <div class="confirmation-buttons">
                <button class="cancelarRecusado">Cancelar</button>
                <button class="apagar-confirm">Apagar</button>
            </div>
        </div>
    </div>

    <script src="../js/meuseventos.js"></script>

</body>
</html>