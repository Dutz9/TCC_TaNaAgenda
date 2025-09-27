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
    <title>Adicionar Turma - TáNaAgenda</title>
    <link id="favicon" rel="shortcut icon" href="../image/Favicon-light.png">
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/addturma.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
</head>
<body>
<script src="../js/favicon.js"></script>
    <header class="header">
        <a href="perfilcoord.php">
            <p> <?php echo htmlspecialchars($usuario_logado['nm_usuario']); ?> </p>
        </a>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path fill="#ffffff" d="M320 312C386.3 312 440 258.3 440 192C440 125.7 386.3 72 320 72C253.7 72 200 125.7 200 192C200 258.3 253.7 312 320 312zM290.3 368C191.8 368 112 447.8 112 546.3C112 562.7 125.3 576 141.7 576L498.3 576C514.7 576 528 562.7 528 546.3C528 447.8 448.2 368 349.7 368L290.3 368z"/></svg>
    </header>

    <main></main>
        
        <section class="area-lado">
            <a class="area-lado-logo" href="agendacoord.php"><img src="../image/logotipo fundo azul.png" alt=""></a>
            <div class="area-menu">
                <div class="menu-agenda">
                <img src="../image/icones/agenda.png" alt="">
                    <a href="agendacoord.php"><p>Agenda</p></a>
                </div>
                <div class="menu-meus-eventos">
                <img src="../image/icones/eventos.png" alt="">
                    <a href="eventoscoord.php"><p>Eventos</p></a>
                </div>
                <div class="menu-professores">
                <img src="../image/icones/professores.png" alt="">
                    <a href="professores.php"><p>Professores</p></a>
                </div> 
                <div class="menu-turmas ativo">
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

        <section class="formulario-turma" >
            <h2 >Adicionar Turma</h2>
            <div class="linha-form">
                <div  class="campo">
                    <label  for="titulo">Nome da Turma</label>
                    <input  type="text" id="titulo" name="titulo" placeholder="Turma">
                </div>
    
                <div class="campo">
                    <label  placeholder="3I,2I,1I" for="turmas"> Sigla da Turma</label >
                        <input  type="text" id="titulo" name="titulo" placeholder="Sigla">
                </div>
            </div>
            <div class="linha-form">
                <div class="campo">
                    <label> Sala</label >
                        <input  type="text" id="titulo" name="titulo" placeholder="Sala">
                </div>
               
                <div class="campo">
                    <label> Ano Escolar</label >
                        <select id="tipo" name="tipo">
                            <option>1º</option>
                            <option>2º</option>
                            <option>3º</option>
                        </select>
                </div>
            </div>
            <div class="linha-form">
            <div class="campo">
                    <label> Quantidade de Alunos</label >
                        <input  type="number" id="titulo" name="titulo" placeholder="Quantidade">
                </div> 

                   <div class="botoes">
                    <a href="turmas.php"><button type="button" class="botao-cancelar">Cancelar</button></a>
                    <a href="turmas.php"><button type="submit" class="botao-enviar">Adicionar Turma</button></a>
                </div>
            </div>

            

        </section>
</body>
</html>