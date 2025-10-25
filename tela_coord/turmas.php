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
    <title>Turmas - TáNaAgenda</title>
    <link id="favicon" rel="shortcut icon" href="../image/Favicon-light.png">
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/turmas.css">
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

    <main>
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
 
        <section class="areaturma">
        <h2>Turmas</h2>
      <div class="barra-de-pesquisa">
        <div class="barra">
            <label for="search">Pesquisar:</label>
            <input type="text" id="search" name="search" placeholder="Nome da Turma">
        </div>
        </div>
    
        <div class="gridturma">
            <div class="cardturma">
                <div class="turma-infos">
                    <p style="font-size: 16px; font-weight: 600;">Sigla da turma</p>
                    <p>Professores:</p>
                </div>
                <p></p>
                <button class="adicionar-btn">Adicionar
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="#ffffff" d="M256 512a256 256 0 1 0 0-512 256 256 0 1 0 0 512zM232 344l0-64-64 0c-13.3 0-24-10.7-24-24s10.7-24 24-24l64 0 0-64c0-13.3 10.7-24 24-24s24 10.7 24 24l0 64 64 0c13.3 0 24 10.7 24 24s-10.7 24-24 24l-64 0 0 64c0 13.3-10.7 24-24 24s-24-10.7-24-24z"/></svg>
                </button>
            </div>
            <div class="cardturma">
                <div class="turma-infos">
                <p style="font-size: 16px; font-weight: 600;">SIG2</p>
                <p>Professores: 6</p>
                </div>
                <p></p>
                <button class="Editar-btn">Editar</button>
            </div>
            <div class="cardturma">
                <div class="turma-infos">
                <p style="font-size: 16px; font-weight: 600;">SIG2</p>
                <p>Professores: 6</p>
                </div>
                <p></p>
                <button class="Editar-btn">Editar</button>
            </div>
            <div class="cardturma">
                <div class="turma-infos">
                <p style="font-size: 16px; font-weight: 600;">SIG2</p>
                <p>Professores: 6</p>
                </div>
                <p></p>
                <button class="Editar-btn">Editar</button>
            </div>
            <div class="cardturma">
                <div class="turma-infos">
                <p style="font-size: 16px; font-weight: 600;">SIG2</p>
                <p>Professores: 6</p>
                </div>
                <p></p>
                <button class="Editar-btn">Editar</button>
            </div>
            <div class="cardturma">
                <div class="turma-infos">
                <p style="font-size: 16px; font-weight: 600;">SIG2</p>
                <p>Professores: 6</p>
                </div>
                <p></p>
                <button class="Editar-btn">Editar</button>
            </div>
            <div class="cardturma">
                <div class="turma-infos">
                <p style="font-size: 16px; font-weight: 600;">SIG2</p>
                <p>Professores: 6</p>
                </div>
                <p></p>
                <button class="Editar-btn">Editar</button>
            </div>
            <div class="cardturma">
                <div class="turma-infos">
                <p style="font-size: 16px; font-weight: 600;">SIG2</p>
                <p>Professores: 6</p>
                </div>
                <p></p>
                <button class="Editar-btn">Editar</button>
            </div>
            <div class="cardturma">
                <div class="turma-infos">
                <p style="font-size: 16px; font-weight: 600;">SIG2</p>
                <p>Professores: 6</p>
                </div>
                <p></p>
                <button class="Editar-btn">Editar</button>
            </div>
            <div class="cardturma">
                <div class="turma-infos">
                <p style="font-size: 16px; font-weight: 600;">SIG2</p>
                <p>Professores: 6</p>
                </div>
                <p></p>
                <button class="Editar-btn">Editar</button>
            </div>
            <div class="cardturma">
                <div class="turma-infos">
                <p style="font-size: 16px; font-weight: 600;">SIG2</p>
                <p>Professores: 6</p>
                </div>
                <p></p>
                <button class="Editar-btn">Editar</button>
            </div>            <div class="cardturma">
                <div class="turma-infos">
                <p style="font-size: 16px; font-weight: 600;">SIG2</p>
                <p>Professores: 6</p>
                </div>
                <p></p>
                <button class="Editar-btn">Editar</button>
            </div>
            <div class="cardturma">
                <div class="turma-infos">
                <p style="font-size: 16px; font-weight: 600;">SIG2</p>
                <p>Professores: 6</p>
                </div>
                <p></p>
                <button class="Editar-btn">Editar</button>
            </div>
            <div class="cardturma">
                <div class="turma-infos">
                <p style="font-size: 16px; font-weight: 600;">SIG2</p>
                <p>Professores: 6</p>
                </div>
                <p></p>
                <button class="Editar-btn">Editar</button>
            </div>
            <div class="cardturma">
                <div class="turma-infos">
                <p style="font-size: 16px; font-weight: 600;">SIG2</p>
                <p>Professores: 6</p>
                </div>
                <p></p>
                <button class="Editar-btn">Editar</button>
            </div>
            <div class="cardturma">
                <div class="turma-infos">
                <p style="font-size: 16px; font-weight: 600;">SIG2</p>
                <p>Professores: 6</p>
                </div>
                <p></p>
                <button class="Editar-btn">Editar</button>
            </div>
            <div class="cardturma">
                <div class="turma-infos">
                <p style="font-size: 16px; font-weight: 600;">SIG2</p>
                <p>Professores: 6</p>
                </div>
                <p></p>
                <button class="Editar-btn">Editar</button>
            </div>
            <div class="cardturma">
                <div class="turma-infos">
                <p style="font-size: 16px; font-weight: 600;">SIG2</p>
                <p>Professores: 6</p>
                </div>
                <p></p>
                <button class="Editar-btn">Editar</button>
            </div>
            <div class="cardturma">
                <div class="turma-infos">
                <p style="font-size: 16px; font-weight: 600;">SIG2</p>
                <p>Professores: 6</p>
                </div>
                <p></p>
                <button class="Editar-btn">Editar</button>
            </div>
            <div class="cardturma">
                <div class="turma-infos">
                <p style="font-size: 16px; font-weight: 600;">SIG2</p>
                <p>Professores: 6</p>
                </div>
                <p></p>
                <button class="Editar-btn">Editar</button>
            </div>
        </div>
</section>




<div id="modal-overlay" class="modal-overlay" style="display: none;">
    <div class="modal-content">
        <div class="modal-left">
            <div class="coordinator-info">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path fill="#000000" d="M320 312C386.3 312 440 258.3 440 192C440 125.7 386.3 72 320 72C253.7 72 200 125.7 200 192C200 258.3 253.7 312 320 312zM290.3 368C191.8 368 112 447.8 112 546.3C112 562.7 125.3 576 141.7 576L498.3 576C514.7 576 528 562.7 528 546.3C528 447.8 448.2 368 349.7 368L290.3 368z"/></svg>
                <div>
                    <h3>Nome Coordenador</h3>
                    <p>Coordenador EM</p>
                </div>
            </div>
            <div class="responses-section">
                <h4>Professores Vinculados</h4>
                <div class="professores-vinculados">
                <div class="response-item">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path fill="#000000" d="M320 312C386.3 312 440 258.3 440 192C440 125.7 386.3 72 320 72C253.7 72 200 125.7 200 192C200 258.3 253.7 312 320 312zM290.3 368C191.8 368 112 447.8 112 546.3C112 562.7 125.3 576 141.7 576L498.3 576C514.7 576 528 562.7 528 546.3C528 447.8 448.2 368 349.7 368L290.3 368z"/></svg>
                    <div>
                        <p>Professor 1</p>
                    </div>
                </div>
                <div class="response-item">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path fill="#000000" d="M320 312C386.3 312 440 258.3 440 192C440 125.7 386.3 72 320 72C253.7 72 200 125.7 200 192C200 258.3 253.7 312 320 312zM290.3 368C191.8 368 112 447.8 112 546.3C112 562.7 125.3 576 141.7 576L498.3 576C514.7 576 528 562.7 528 546.3C528 447.8 448.2 368 349.7 368L290.3 368z"/></svg>
                    <div>
                        <p>Professor 2</p>
                    </div>
                </div>
                <div class="response-item">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path fill="#000000" d="M320 312C386.3 312 440 258.3 440 192C440 125.7 386.3 72 320 72C253.7 72 200 125.7 200 192C200 258.3 253.7 312 320 312zM290.3 368C191.8 368 112 447.8 112 546.3C112 562.7 125.3 576 141.7 576L498.3 576C514.7 576 528 562.7 528 546.3C528 447.8 448.2 368 349.7 368L290.3 368z"/></svg>
                    <div>
                        <p>Professor 3</p>
                    </div>
                </div>
                <div class="response-item">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path fill="#000000" d="M320 312C386.3 312 440 258.3 440 192C440 125.7 386.3 72 320 72C253.7 72 200 125.7 200 192C200 258.3 253.7 312 320 312zM290.3 368C191.8 368 112 447.8 112 546.3C112 562.7 125.3 576 141.7 576L498.3 576C514.7 576 528 562.7 528 546.3C528 447.8 448.2 368 349.7 368L290.3 368z"/></svg>
                    <div>
                        <p>Professor 4</p>
                    </div>
                </div>
                <div class="response-item">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path fill="#000000" d="M320 312C386.3 312 440 258.3 440 192C440 125.7 386.3 72 320 72C253.7 72 200 125.7 200 192C200 258.3 253.7 312 320 312zM290.3 368C191.8 368 112 447.8 112 546.3C112 562.7 125.3 576 141.7 576L498.3 576C514.7 576 528 562.7 528 546.3C528 447.8 448.2 368 349.7 368L290.3 368z"/></svg>
                    <div>
                        <p>Professor 5</p>
                    </div>
                </div>
                <div class="response-item">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path fill="#000000" d="M320 312C386.3 312 440 258.3 440 192C440 125.7 386.3 72 320 72C253.7 72 200 125.7 200 192C200 258.3 253.7 312 320 312zM290.3 368C191.8 368 112 447.8 112 546.3C112 562.7 125.3 576 141.7 576L498.3 576C514.7 576 528 562.7 528 546.3C528 447.8 448.2 368 349.7 368L290.3 368z"/></svg>
                    <div>
                        <p>Professor 6</p>
                    </div>
                </div>
                    <div class="response-item">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path fill="#000000" d="M320 312C386.3 312 440 258.3 440 192C440 125.7 386.3 72 320 72C253.7 72 200 125.7 200 192C200 258.3 253.7 312 320 312zM290.3 368C191.8 368 112 447.8 112 546.3C112 562.7 125.3 576 141.7 576L498.3 576C514.7 576 528 562.7 528 546.3C528 447.8 448.2 368 349.7 368L290.3 368z"/></svg>
                    <div>
                        <p>Professor 7</p>
                    </div>
                </div>
                </div>
            </div>

        </div>
        <div class="modal-right">
            <h3>Editar Turma</h3>

            <div class="form-row">
            <div class="form-group">
                <label for="nome-turma">Nome da Turma</label>
                <input type="text" id="nome-turma" value="Nome Atual da Turma">
            </div>
            <div class="form-group">
                <label for="sigla-turma">Sigla</label>
                <input type="text" id="sigla-turma" value="1N1">
            </div>
            </div>
            <div class="form-row">
            <div class="form-group">
                <label for="sala-turma">Sala</label>
                <input type="text" id="sala-turma" value="Sala 7">
            </div>
            <div class="form-group">
                <label for="qtd-alunos">Quantidade de Alunos</label>
                <input type="number" id="qtd-alunos" value="30">
            </div>
            </div>
            <div class="form-row">
            <div class="form-group">
                <label for="ano-turma">Ano Escolar</label>
                <select id="ano-turma">
                    <option value="1ºAno">1º</option>
                    <option value="2ºAno">2º</option>
                    <option value="3ºAno">3º</option>
                </select>
            </div> 
            </div>
            <div class="modal-buttons">
                <button class="excluir">Excluir Turma</button>
                <button class="salvar">Salvar Alterações</button>
            </div>
        </div>
    </div>
</div>

<div id="confirmation-modal" class="confirmation-modal" style="display: none;">
    <div class="confirmation-content">
        <h3>Realmente deseja excluir a turma?</h3>
        <div class="confirmation-buttons">
            <button class="cancelar">Cancelar</button>
            <button class="excluir-confirm">Excluir</button>
        </div>
    </div>
</div>


    <script src="../js/turmas.js"></script>

</body> 

</html>