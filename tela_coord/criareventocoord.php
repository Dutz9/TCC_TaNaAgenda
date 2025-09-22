<?php 

// 1. Gire a chave: Carrega o autoloader para que o PHP encontre as classes.
require_once '../config_local.php'; 

// 2. Chame o guardião: Ele verifica a sessão E cria a variável $usuario_logado para nós.
require_once '../api/verifica_sessao.php'; 

?>

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
        <a href="perfilcoord.php">
            <p> <?php echo htmlspecialchars($usuario_logado['nm_usuario']); ?> </p>
        </a>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path fill="#ffffff" d="M320 312C386.3 312 440 258.3 440 192C440 125.7 386.3 72 320 72C253.7 72 200 125.7 200 192C200 258.3 253.7 312 320 312zM290.3 368C191.8 368 112 447.8 112 546.3C112 562.7 125.3 576 141.7 576L498.3 576C514.7 576 528 562.7 528 546.3C528 447.8 448.2 368 349.7 368L290.3 368z"/></svg>
    </header>

    <main>
        
    <section class="area-lado">
            <a href="agendacoord.php"><img src="../image/logotipo fundo azul.png" alt=""></a>
            <div class="area-menu">
                <div class="menu-agenda">
                <img src="../image/icones/agenda.png" alt="">
                    <a href="agendacoord.php"><p>Agenda</p></a>
                </div>
                <div class="menu-meus-eventos ativo">
                <img src="../image/icones/eventos.png" alt="">
                    <a href="eventoscoord.php"><p>Eventos</p></a>
                </div>
                <div class="menu-professores">
                <img src="../image/icones/professores.png" alt="">
                    <a href="professores.php"><p>Professores</p></a>
                </div> 
                <div class="menu-turmas">
                <img src="../image/icones/turmas.png" alt="">
                    <a href="turmas.php"><p>Turmas</p></a>
                </div> 
                <div class="menu-perfil">
                <img src="../image/icones/perfil.png" alt="">
                    <a href="perfilcoord.php"><p>Perfil</p></a>
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
                    <a href="eventoscoord.php"><button type="button" class="botao-cancelar">Cancelar</button></a>
                    <a href="eventoscoord.php"><button type="submit" class="botao-enviar">Criar e Notificar</button></a>
                </div>
        </section>
</body>
</html>