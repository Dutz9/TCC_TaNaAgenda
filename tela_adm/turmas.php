<?php 
    require_once '../api/config.php'; 
    require_once '../api/verifica_sessao.php'; 

    if ($usuario_logado['tipo_usuario_ic_usuario'] !== 'Administrador') {
        header('Location: ../tela_prof/agendaprof.php');
        exit();
    }

    // --- LÓGICA DE FEEDBACK (TOAST) ---
    if (isset($_SESSION['mensagem_sucesso'])) {
        $mensagem_toast = $_SESSION['mensagem_sucesso'];
        unset($_SESSION['mensagem_sucesso']);
    }

    // 1. BUSCA OS DADOS DAS TURMAS
    $turmaController = new TurmaController();
    $lista_turmas = $turmaController->listarComContagem(); 

    // 2. BUSCA A LISTA DE TODAS AS TURMAS (PARA O MODAL)
    $lista_todas_turmas_para_modal = $turmaController->listar();
?>

<script>
    // 3. CRIA A "PONTE DE DADOS" PARA O JAVASCRIPT
    const turmasDaPagina = <?php echo json_encode($lista_turmas); ?>;
    const todasAsTurmas = <?php echo json_encode($lista_todas_turmas_para_modal); ?>;
</script>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Turmas - TáNaAgenda</title>
    <link id="favicon" rel="shortcut icon" href="../image/Favicon-light.png">
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/coordenador.css"> 
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css"/>
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js" defer></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
</head>
<body>
<script src="../js/favicon.js"></script>
    <header class="header">
        <button class="menu-toggle" id="menu-toggle">☰</button>
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
                
                <div class="menu-cursos">
                <img src="../image/icones/cursos.png" alt="">
                    <a href="cursos.php"><p>Cursos</p></a>
                </div> 
                <div class="menu-turmas ativo">
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

    <section class="area-notificacoes"> 
        <div id="feedback-bar" class="feedback-bar"></div>
        
        <h2>Turmas</h2>
        
        <div class="barra-de-pesquisa">
            <div class="barra">
                <label for="search-turma">Pesquisar:</label>
                <input type="text" id="search-turma" placeholder="Nome da Turma, Curso ou Período">
            </div>
        </div>
    
        <div id="admin-card-container" class="admin-card-container">
            <a href="addturma.php" class="admin-card card-adicionar">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="#ffffff" d="M256 512a256 256 0 1 0 0-512 256 256 0 1 0 0 512zM232 344l0-64-64 0c-13.3 0-24-10.7-24-24s10.7-24 24-24l64 0 0-64c0-13.3 10.7-24 24-24s24 10.7 24 24l0 64 64 0c13.3 0 24 10.7 24 24s-10.7 24-24 24l-64 0 0 64c0 13.3-10.7 24-24 24s-24-10.7-24-24z"/></svg>
            </a>
            <?php if (empty($lista_turmas)): ?>
                <p class="sem-eventos" style="grid-column: 1 / -1; text-align: center;">Nenhuma turma cadastrada.</p>
            <?php endif; ?>
            </div>
    </section>

    <div id="modal-overlay" class="modal-overlay" style="display: none;">
        <div class="modal-content">
            <div class="modal-left">
                <div class="coordinator-info">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="#000000" d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512H418.3c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304H178.3z"/></svg>
                    <div>
                        <h3 id="modal-coord-nome-turma"><?php echo htmlspecialchars($usuario_logado['nm_usuario']); ?></h3>
                        <p>Coordenador</p>
                    </div>
                </div>
                <div class="responses-section">
                    <h4>Professores Vinculados</h4>
                    <div id="professores-vinculados-lista" class="professores-vinculados"> 
                        <p>Carregando...</p>
                    </div>
                </div>
            </div>
            <div class="modal-right">
                <h3>Editar Turma</h3>
                <input type="hidden" id="modal-turma-id" value="">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="modal-turma-nome">Nome da Turma:</label>
                        <input type="text" id="modal-turma-nome" value="">
                    </div>
                    <div class="form-group">
                        <label for="modal-turma-curso">Curso:</label>
                        <input type="text" id="modal-turma-curso" value="" readonly>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="modal-turma-sala">Sala:</label>
                        <input type="text" id="modal-turma-sala" value="">
                    </div>
                    <div class="form-group">
                        <label for="modal-turma-alunos">Qtd. Alunos:</label>
                        <input type="number" id="modal-turma-alunos" value="">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="modal-turma-serie">Série/Módulo:</label>
                        <select id="modal-turma-serie">
                            <option value="1">1º</option>
                            <option value="2">2º</option>
                            <option value="3">3º</option>
                        </select>
                    </div> 
                    <div class="form-group">
                        <label for="modal-turma-periodo">Período:</label>
                        <input type="text" id="modal-turma-periodo" value="" readonly>
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
    
    <?php if (isset($mensagem_toast)): ?>
    <script>
        setTimeout(() => { showFeedback("<?php echo addslashes($mensagem_toast); ?>", 'sucesso'); }, 100);
    </script>
    <?php endif; ?>
</body> 
</html>