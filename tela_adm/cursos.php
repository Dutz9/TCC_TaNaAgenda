<?php 
    require_once '../api/config.php'; 
    require_once '../api/verifica_sessao.php'; 

    if ($usuario_logado['tipo_usuario_ic_usuario'] !== 'Administrador') {
        header('Location: ../tela_prof/agendaprof.php');
        exit();
    }

    if (isset($_SESSION['mensagem_sucesso'])) {
        $mensagem_toast = $_SESSION['mensagem_sucesso'];
        unset($_SESSION['mensagem_sucesso']);
    }
    $cursoController = new CursoController();
    $lista_cursos = $cursoController->listarComContagem(); 
    $usuarioController = new UsuarioController();
    $lista_coordenadores = $usuarioController->listarCoordenadores();
?>

<script>
    const cursosDaPagina = <?php echo json_encode($lista_cursos); ?>;
    const coordenadoresDaPagina = <?php echo json_encode($lista_coordenadores); ?>;
</script>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cursos - TáNaAgenda</title>
    <link id="favicon" rel="shortcut icon" href="../image/Favicon-light.png">
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/coordenador.css"> 
    <link rel="stylesheet" href="../css/cursos.css"> 
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css"/>
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js" defer></script>
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
                <a href="../logout.php"><div class="menu-sair"><p>SAIR</p></div></a> 
            </div>
        </section>
    <section class="area-notificacoes"> 
        <div id="feedback-bar" class="feedback-bar"></div>
        
        <h2>Cursos</h2>
        
        <div class="barra-de-pesquisa">
            <div class="barra">
                <label for="search-curso">Pesquisar:</label>
                <input type="text" id="search-curso" placeholder="Nome do Curso ou Período">
            </div>
        </div>
        <div id="admin-card-container" class="admin-card-container cursos-grid-container">
            <a href="addcurso.php" class="admin-card card-adicionar">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="#ffffff" d="M256 512a256 256 0 1 0 0-512 256 256 0 1 0 0 512zM232 344l0-64-64 0c-13.3 0-24-10.7-24-24s10.7-24 24-24l64 0 0-64c0-13.3 10.7-24 24-24s24 10.7 24 24l0 64 64 0c13.3 0 24 10.7 24 24s-10.7 24-24 24l-64 0 0 64c0 13.3-10.7 24-24 24s-24-10.7-24-24z"/></svg>
            </a>
            <?php if (empty($lista_cursos)): ?>
                <p class="sem-eventos" style="grid-column: 1 / -1; text-align: center;">Nenhum curso cadastrado.</p>
            <?php endif; ?>
            
        </div>
    </section>

    <div id="modal-overlay" class="modal-overlay" style="display: none;">
        <div class="modal-content">
            <div class="modal-left">
                <div class="coordinator-info">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="#000000" d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512H418.3c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304H178.3z"/></svg>
                    <div>
                        <h3 id="modal-adm-nome"><?php echo htmlspecialchars($usuario_logado['nm_usuario']); ?></h3>
                        <p>Administrador</p>
                    </div>
                </div>
                <div class="responses-section">
                    <h4>Coordenadores Vinculados</h4>
                    <div id="coordenadores-vinculados-lista" class="professores-vinculados"> 
                        <p>Carregando...</p>
                    </div>
                </div>
            </div>
            <div class="modal-right">
                <h3>Editar Curso</h3>
                <input type="hidden" id="modal-curso-id" value="">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="modal-curso-nome">Nome do Curso:</label>
                        <input type="text" id="modal-curso-nome" value="">
                    </div>
                    <div class="form-group">
                        <label for="modal-curso-turmas">Total de Turmas:</label>
                        <input type="text" id="modal-curso-turmas" value="" readonly>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="modal-curso-periodo">Período:</label>
                        <select id="modal-curso-periodo">
                            <option value="Manha">Manhã</option>
                            <option value="Tarde">Tarde</option>
                            <option value="Noite">Noite</option>
                        </select>
                    </div> 
                    <div class="form-group">
                        <label for="modal-curso-duracao">Duração:</label>
                        <input type="text" id="modal-curso-duracao" value="3 Módulos/Anos" readonly>
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
            <p>Se este curso tiver turmas vinculadas, ele **não poderá ser excluído**.</p>
            <div class="confirmation-buttons">
                <button class="cancelar">Cancelar</button>
                <button class="excluir-confirm">Excluir</button>
            </div>
        </div>
    </div>

    <script src="../js/cursos.js"></script>
    
    <?php if (isset($mensagem_toast)): ?>
    <script>
        setTimeout(() => { showFeedback("<?php echo addslashes($mensagem_toast); ?>", 'sucesso'); }, 100);
    </script>
    <?php endif; ?>
</body> 
</html>