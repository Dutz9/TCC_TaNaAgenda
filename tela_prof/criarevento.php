<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Evento - TáNaAgenda</title>
    <link rel="shortcut icon" href="../image/Favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/index.css">
    <link rel="stylesheet" href="../css/coordenador.css">
    <link rel="stylesheet" href="../css/criarevento.css">
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
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path fill="#ffffff" d="M224 64C241.7 64 256 78.3 256 96L256 128L384 128L384 96C384 78.3 398.3 64 416 64C433.7 64 448 78.3 448 96L448 128L480 128C515.3 128 544 156.7 544 192L544 480C544 515.3 515.3 544 480 544L160 544C124.7 544 96 515.3 96 480L96 192C96 156.7 124.7 128 160 128L192 128L192 96C192 78.3 206.3 64 224 64zM160 304L160 336C160 344.8 167.2 352 176 352L208 352C216.8 352 224 344.8 224 336L224 304C224 295.2 216.8 288 208 288L176 288C167.2 288 160 295.2 160 304zM288 304L288 336C288 344.8 295.2 352 304 352L336 352C344.8 352 352 344.8 352 336L352 304C352 295.2 344.8 288 336 288L304 288C295.2 288 288 295.2 288 304zM432 288C423.2 288 416 295.2 416 304L416 336C416 344.8 423.2 352 432 352L464 352C472.8 352 480 344.8 480 336L480 304C480 295.2 472.8 288 464 288L432 288zM160 432L160 464C160 472.8 167.2 480 176 480L208 480C216.8 480 224 472.8 224 464L224 432C224 423.2 216.8 416 208 416L176 416C167.2 416 160 423.2 160 432zM304 416C295.2 416 288 423.2 288 432L288 464C288 472.8 295.2 480 304 480L336 480C344.8 480 352 472.8 352 464L352 432C352 423.2 344.8 416 336 416L304 416zM416 432L416 464C416 472.8 423.2 480 432 480L464 480C472.8 480 480 472.8 480 464L480 432C480 423.2 472.8 416 464 416L432 416C423.2 416 416 423.2 416 432z"/></svg>
                    <a href="agendaprof.php"><p>Agenda</p></a>
                </div>
                <div class="menu-meus-eventos ativo">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path fill="#ffffff" d="M224 64C241.7 64 256 78.3 256 96L256 128L384 128L384 96C384 78.3 398.3 64 416 64C433.7 64 448 78.3 448 96L448 128L480 128C515.3 128 544 156.7 544 192L544 480C544 515.3 515.3 544 480 544L160 544C124.7 544 96 515.3 96 480L96 192C96 156.7 124.7 128 160 128L192 128L192 96C192 78.3 206.3 64 224 64zM320 256C306.7 256 296 266.7 296 280L296 328L248 328C234.7 328 224 338.7 224 352C224 365.3 234.7 376 248 376L296 376L296 424C296 437.3 306.7 448 320 448C333.3 448 344 437.3 344 424L344 376L392 376C405.3 376 416 365.3 416 352C416 338.7 405.3 328 392 328L344 328L344 280C344 266.7 333.3 256 320 256z"/></svg>
                    <a href="meuseventos.php"><p>Meus Eventos</p></a>
                </div>
                <div class="menu-notificacoes">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path fill="#ffffff" d="M320 64C302.3 64 288 78.3 288 96L288 99.2C215 114 160 178.6 160 256L160 277.7C160 325.8 143.6 372.5 113.6 410.1L103.8 422.3C98.7 428.6 96 436.4 96 444.5C96 464.1 111.9 480 131.5 480L508.4 480C528 480 543.9 464.1 543.9 444.5C543.9 436.4 541.2 428.6 536.1 422.3L526.3 410.1C496.4 372.5 480 325.8 480 277.7L480 256C480 178.6 425 114 352 99.2L352 96C352 78.3 337.7 64 320 64zM258 528C265.1 555.6 290.2 576 320 576C349.8 576 374.9 555.6 382 528L258 528z"/></svg>
                    <a href="notificacoes.php"><p>Notificações</p></a>
                </div>
                <div class="menu-perfil">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path fill="#ffffff" d="M320 312C386.3 312 440 258.3 440 192C440 125.7 386.3 72 320 72C253.7 72 200 125.7 200 192C200 258.3 253.7 312 320 312zM290.3 368C191.8 368 112 447.8 112 546.3C112 562.7 125.3 576 141.7 576L498.3 576C514.7 576 528 562.7 528 546.3C528 447.8 448.2 368 349.7 368L290.3 368z"/></svg>
                    <a href="perfil.php"><p>Perfil</p></a>
                </div>  
                <a href="../login.php"><div class="menu-sair"><p>SAIR</p></div></a> 
            </div>
        </section>

        <section class="formulario-evento" >
            <h2 >Criar Evento</h2>
            <div class="linha-form">
                <div  class="campo">
                    <label  for="titulo">Título do Evento</label>
                    <input  type="text" id="titulo" name="titulo" placeholder="Título">
                </div>
    
                <div class="campo">
                    <label  placeholder="3I,2I,1I" for="turmas"> Turmas Envolvidas</label >
                    <select  id="turmas" name="turmas">
                        <option>1I</option>
                        <option>2I</option>
                        <option>3I</option>
    
                        <option>1P</option>
                        <option>2P</option>
                        <option>3P</option>
    
                        <option>1R</option>
                        <option>2R</option>
                        <option>3R</option>
    
                        <option>1N</option>
                        <option>2N</option>
                        <option>3N</option>
    
                        <option>1G</option>
                        <option>2G</option>
                        <option>3G</option>
    
                        <option>1K</option>
                        <option>2K</option>
                        <option>3K</option>
    
                        
                    </select>
                </div>
            </div>
            <div class="linha-form">
                <div class="campo">
                    <label for="horario">Hora de Início</label>
                    <select id="horario" name="horario">
                        <option>07:10</option>
                        <option>08:00</option>
                         <option>08:50</option>
                          <option>10:00</option>
                          <option>10:50</option>
                          <option>11:40</option>
                    </select>
                </div>
                <div class="campo">
                    <label for="horario">Hora de  Fim</label>
                    <select id="horario" name="horario">
                        <option>08:00</option>
                        <option>08:50 </option>
                         <option>9:40 </option>
                          <option>10:50</option>
                          <option>11:40</option>
                          <option>12:30</option>
                    </select>
                </div>
            </div>
               
            <div class="linha-form">
                <div class="campo">
                    <label for="data">Data do Evento</label>
                    <input type="text" id="data" name="data" value="26/02/2025">
                </div>
                 <div class="campo">
                    <label for="professores">Professores a Notificar</label>
                    <select id="professores" name="professores">
                        <option>Seleção de professores</option>
                    </select>
                </div>
            </div>
            <div class="linha-form">
                <div class="campo">
                    <label for="tipo">Tipo do Evento</label>
                    <select id="tipo" name="tipo">
                        <option>Palestra</option>
                    </select>
                </div> 
                  <div class="campo">
                    <label for="descricao">Descrição</label>
                    <input type="text" id="descricao" name="descricao" placeholder="Descrição do evento">
                </div>
            </div>   
                <div class="botoes">
                    <a href="meuseventos.php"><button type="button" class="botao-cancelar">Cancelar</button></a>
                    <a href="meuseventos.php"><button type="submit" class="botao-enviar">Enviar Solicitação</button></a>
                </div>
            </div>
        </section>
</body>
</html>