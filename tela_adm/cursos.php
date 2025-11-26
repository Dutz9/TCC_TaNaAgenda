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
    <title>Cursos - TáNaAgenda</title>
    <link id="favicon" rel="shortcut icon" href="../image/Favicon-light.png">
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/cursos.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
</head>
<body>
<script src="../js/favicon.js"></script>
    <header class="header">
        <a href="perfiladm.php">
            <p> <?php echo htmlspecialchars($usuario_logado['nm_usuario']); ?> </p>
        </a>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path fill="#ffffff" d="M320 312C386.3 312 440 258.3 440 192C440 125.7 386.3 72 320 72C253.7 72 200 125.7 200 192C200 258.3 253.7 312 320 312zM290.3 368C191.8 368 112 447.8 112 546.3C112 562.7 125.3 576 141.7 576L498.3 576C514.7 576 528 562.7 528 546.3C528 447.8 448.2 368 349.7 368L290.3 368z"/></svg>
    </header>

    <main>
    <section class="area-lado">
            <a class="area-lado-logo" href="agendaadm.php"><img src="../image/logotipo fundo azul.png" alt=""></a>
            <div class="area-menu">
                <div class="menu-agenda">
                <img src="../image/icones/agenda.png" alt="">
                    <a href="agendaadm.php"><p>Agenda</p></a>
                </div>
                <div class="menu-meus-eventos">
                <img src="../image/icones/eventos.png" alt="">
                    <a href="eventosadm.php"><p>Eventos</p></a>
                </div>
                <div class="menu-professores">
                <img src="../image/icones/professores.png" alt="">
                    <a href="professoresadm.php"><p>Professores e Coordenadores</p></a>
                </div> 
                
                <div class="menu-cursos ativo">
                <img src="../image/icones/cursos.png" alt="">
                    <a href="cursos.php"><p>Cursos</p></a>
                </div> 
                <div class="menu-turmas">
                <img src="../image/icones/turmas.png" alt="">
                    <a href="turmas.php"><p>Turmas</p></a>
                </div>
                <div class="menu-perfil">
                <img src="../image/icones/perfil.png" alt="">
                    <a href="perfiladm.php"><p>Perfil</p></a>
                </div>  
                <a href="../login.php"><div class="menu-sair"><p>SAIR</p></div></a> 
            </div>
        </section>
 
        <section class="areacurso">
        <h2>Cursos</h2>
      <div class="barra-de-pesquisa">
        <div class="barra">
            <label for="search">Pesquisar:</label>
            <input type="text" id="search" name="search" placeholder="Nome do Curso">
        </div>
        </div>
    
        <div class="gridcurso">
            <div class="cardcurso">
                <div class="curso-infos">
                    <p style="font-size: 16px; font-weight: 600;">Nome do Curso</p>
                    <p>Coordenadores:</p>
                </div>
                <p></p>
                <button class="adicionar-btn">Adicionar
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="#ffffff" d="M256 512a256 256 0 1 0 0-512 256 256 0 1 0 0 512zM232 344l0-64-64 0c-13.3 0-24-10.7-24-24s10.7-24 24-24l64 0 0-64c0-13.3 10.7-24 24-24s24 10.7 24 24l0 64 64 0c13.3 0 24 10.7 24 24s-10.7 24-24 24l-64 0 0 64c0 13.3-10.7 24-24 24s-24-10.7-24-24z"/></svg>
                </button>
            </div>
            <div class="cardcurso">
                <div class="curso-infos">
                <p style="font-size: 16px; font-weight: 600;">Informática para Internet</p>
                <p>Coordenadores: 2</p>
                </div>
                <p></p>
                <button class="Editar-btn">Editar</button>
            </div>
            <div class="cardcurso">
                <div class="curso-infos">
                <p style="font-size: 16px; font-weight: 600;">Programação de Jogos Digitais</p>
                <p>Coordenadores: 2</p>
                </div>
                <p></p>
                <button class="Editar-btn">Editar</button>
            </div>
            <div class="cardcurso">
                <div class="curso-infos">
                <p style="font-size: 16px; font-weight: 600;">Eletrônica</p>
                <p>Coordenadores: 2</p>
                </div>
                <p></p>
                <button class="Editar-btn">Editar</button>
            </div>
            <div class="cardcurso">
                <div class="curso-infos">
                <p style="font-size: 16px; font-weight: 600;">Automação Industrial</p>
                <p>Coordenadores: 1</p>
                </div>
                <p></p>
                <button class="Editar-btn">Editar</button>
            </div>
            <div class="cardcurso">
                <div class="curso-infos">
                <p style="font-size: 16px; font-weight: 600;">Desenvolvimento de Sistemas</p>
                <p>Coordenadores: 2</p>
                </div>
                <p></p>
                <button class="Editar-btn">Editar</button>
            </div>
            <div class="cardcurso">
                <div class="curso-infos">
                <p style="font-size: 16px; font-weight: 600;">Edificações</p>
                <p>Coordenadores: 3</p>
                </div>
                <p></p>
                <button class="Editar-btn">Editar</button>
            </div>
        </div>
</section>
<div id="modal-overlay" class="modal-overlay" style="display: none;">
    <div class="modal-content">
        <div class="modal-left">
            <div class="adm-info">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path fill="#000000" d="M320 312C386.3 312 440 258.3 440 192C440 125.7 386.3 72 320 72C253.7 72 200 125.7 200 192C200 258.3 253.7 312 320 312zM290.3 368C191.8 368 112 447.8 112 546.3C112 562.7 125.3 576 141.7 576L498.3 576C514.7 576 528 562.7 528 546.3C528 447.8 448.2 368 349.7 368L290.3 368z"/></svg>
                <div>
                    <h3><?php echo htmlspecialchars($usuario_logado['nm_usuario']); ?></h3>
                    <p>Administrador</p>
                </div>
            </div>
            <div class="responses-section">
                <h4>Coordenadores vinculados</h4>
                <div class="coordenadores-vinculados">
                <div class="response-item">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path fill="#000000" d="M320 312C386.3 312 440 258.3 440 192C440 125.7 386.3 72 320 72C253.7 72 200 125.7 200 192C200 258.3 253.7 312 320 312zM290.3 368C191.8 368 112 447.8 112 546.3C112 562.7 125.3 576 141.7 576L498.3 576C514.7 576 528 562.7 528 546.3C528 447.8 448.2 368 349.7 368L290.3 368z"/></svg>
                    <div>
                        <p>Coordenador 1</p>
                    </div>
                </div>
                <div class="response-item">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path fill="#000000" d="M320 312C386.3 312 440 258.3 440 192C440 125.7 386.3 72 320 72C253.7 72 200 125.7 200 192C200 258.3 253.7 312 320 312zM290.3 368C191.8 368 112 447.8 112 546.3C112 562.7 125.3 576 141.7 576L498.3 576C514.7 576 528 562.7 528 546.3C528 447.8 448.2 368 349.7 368L290.3 368z"/></svg>
                    <div>
                        <p>Coordenador 2</p>
                    </div>
                </div>
                <div class="response-item">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path fill="#000000" d="M320 312C386.3 312 440 258.3 440 192C440 125.7 386.3 72 320 72C253.7 72 200 125.7 200 192C200 258.3 253.7 312 320 312zM290.3 368C191.8 368 112 447.8 112 546.3C112 562.7 125.3 576 141.7 576L498.3 576C514.7 576 528 562.7 528 546.3C528 447.8 448.2 368 349.7 368L290.3 368z"/></svg>
                    <div>
                        <p>Coordenador 3</p>
                    </div>
                </div>
                <div class="response-item">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path fill="#000000" d="M320 312C386.3 312 440 258.3 440 192C440 125.7 386.3 72 320 72C253.7 72 200 125.7 200 192C200 258.3 253.7 312 320 312zM290.3 368C191.8 368 112 447.8 112 546.3C112 562.7 125.3 576 141.7 576L498.3 576C514.7 576 528 562.7 528 546.3C528 447.8 448.2 368 349.7 368L290.3 368z"/></svg>
                    <div>
                        <p>Coordenador 4</p>
                    </div>
                </div>
                <div class="response-item">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path fill="#000000" d="M320 312C386.3 312 440 258.3 440 192C440 125.7 386.3 72 320 72C253.7 72 200 125.7 200 192C200 258.3 253.7 312 320 312zM290.3 368C191.8 368 112 447.8 112 546.3C112 562.7 125.3 576 141.7 576L498.3 576C514.7 576 528 562.7 528 546.3C528 447.8 448.2 368 349.7 368L290.3 368z"/></svg>
                    <div>
                        <p>Coordenador 5</p>
                    </div>
                </div>
                <div class="response-item">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path fill="#000000" d="M320 312C386.3 312 440 258.3 440 192C440 125.7 386.3 72 320 72C253.7 72 200 125.7 200 192C200 258.3 253.7 312 320 312zM290.3 368C191.8 368 112 447.8 112 546.3C112 562.7 125.3 576 141.7 576L498.3 576C514.7 576 528 562.7 528 546.3C528 447.8 448.2 368 349.7 368L290.3 368z"/></svg>
                    <div>
                        <p>Coordenador 6</p>
                    </div>
                </div>
                    <div class="response-item">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path fill="#000000" d="M320 312C386.3 312 440 258.3 440 192C440 125.7 386.3 72 320 72C253.7 72 200 125.7 200 192C200 258.3 253.7 312 320 312zM290.3 368C191.8 368 112 447.8 112 546.3C112 562.7 125.3 576 141.7 576L498.3 576C514.7 576 528 562.7 528 546.3C528 447.8 448.2 368 349.7 368L290.3 368z"/></svg>
                    <div>
                        <p>Coordenador 7</p>
                    </div>
                </div>
                </div>
            </div>

        </div>
        <div class="modal-right">
            <h3>Editar Curso</h3>

            <div class="form-row">
            <div class="form-group">
                <label for="nome-curso">Nome do Curso</label>
                <input type="text" id="nome-curso" value="Nome Atual do Curso">
            </div>
            </div>
            <div class="form-row">
            <div class="form-group">
                <label for="duracao-curso">Duração do curso</label>
                <select id="duracao-curso">
                    <option value="1-ano">1 ano</option>
                    <option value="2-anos">2 anos</option>
                    <option value="3-anos">3 anos</option>
                    <option value="3-anos">4 anos</option>
                    <option value="3-anos">5 anos</option>
                    <option value="3-anos">6 anos</option>
                </select>
            </div> 
            </div>
            <div class="form-row">
            <div class="form-group">
                <label for="periodo">Período</label>
                <select id="periodo">
                    <option value="manha">Manhã</option>
                    <option value="tarde">Tarde</option>
                    <option value="noite">Noite</option>
                </select>
            </div>
            </div>
            <div class="modal-buttons">
                <button class="excluir">Excluir Curso</button>
                <button class="salvar">Salvar Alterações</button>
            </div>
        </div>
    </div>
</div>

<div id="confirmation-modal" class="confirmation-modal" style="display: none;">
    <div class="confirmation-content">
        <h3>Realmente deseja excluir o curso?</h3>
        <div class="confirmation-buttons">
            <button class="cancelar">Cancelar</button>
            <button class="excluir-confirm">Excluir</button>
        </div>
    </div>
</div>
    <script src="../js/cursos.js"></script>
</body> 
</html>