<?php

    // 1. CONFIGURAÇÃO E SEGURANÇA
    require_once '../api/config.php'; 
    require_once '../api/verifica_sessao.php'; 

    // 2. BUSCA DE DADOS PARA FILTROS
    // Precisamos da lista de turmas para preencher o dropdown de filtro
    $turmaController = new TurmaController();
    $lista_turmas_filtro = $turmaController->listar();
    // Lista dos tipos de evento que definimos no banco
    $tipos_evento = ['Palestra', 'Visita Técnica', 'Reunião', 'Prova', 'Conselho de Classe', 'Evento Esportivo', 'Outro'];

    // 3. LEITURA DOS FILTROS DA URL (via GET)
    $filtros = [
        'status' => $_GET['status'] ?? null,
        'solicitante' => $_GET['solicitante'] ?? null,
        'turma' => $_GET['turma'] ?? null,
        'tipo' => $_GET['tipo'] ?? null,
        'data' => $_GET['data'] ?? null
    ];
    // Limpa filtros vazios (ex: ?status="")
    foreach ($filtros as $chave => $valor) {
        if (empty($valor)) {
            $filtros[$chave] = null;
        }
    }

    // 4. BUSCA DOS DADOS PRINCIPAIS (AGORA COM FILTROS)
    $eventoController = new EventoController();
    $cd_usuario_logado = $usuario_logado['cd_usuario'];
    // Passa o array de filtros para o controller
    $lista_eventos = $eventoController->listarParaCoordenador($cd_usuario_logado, $filtros);

    // 5. LÓGICA PARA MENSAGEM DE FEEDBACK (TOAST)
    if (isset($_SESSION['mensagem_sucesso'])) {
        $mensagem_toast = $_SESSION['mensagem_sucesso'];
        unset($_SESSION['mensagem_sucesso']);
    }

?>

<script>
    // Ponte de dados para o JavaScript
    const eventosDaPagina = <?php echo json_encode($lista_eventos); ?>;
    const usuario_logado = <?php echo json_encode($usuario_logado); ?>;
</script>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eventos (Coord.) - TáNaAgenda</title>
    <link id="favicon" rel="shortcut icon" href="../image/Favicon-light.png">
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/coordenador.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
</head>
<body>
    <div id="feedback-bar" class="feedback-bar"></div>

    <script src="../js/favicon.js"></script>
    <header class="header">
        <a href="perfilcoord.php">
            <p><?php echo htmlspecialchars($usuario_logado['nm_usuario']); ?></p>
        </a>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="#ffffff" d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512H418.3c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304H178.3z"/></svg>
    </header>

    <main>
        <section class="area-lado">
            <a class="area-lado-logo" href="agendacoord.php"><img src="../image/logotipo fundo azul.png" alt=""></a>
            <div class="area-menu">
                <div class="menu-agenda"><img src="../image/icones/agenda.png" alt=""><a href="agendacoord.php"><p>Agenda</p></a></div>
                <div class="menu-meus-eventos ativo"><img src="../image/icones/eventos.png" alt=""><a href="eventoscoord.php"><p>Eventos</p></a></div>
                <div class="menu-professores"><img src="../image/icones/professores.png" alt=""><a href="professores.php"><p>Professores</p></a></div> 
                <div class="menu-turmas"><img src="../image/icones/turmas.png" alt=""><a href="turmas.php"><p>Turmas</p></a></div> 
                <div class="menu-perfil"><img src="../image/icones/perfil.png" alt=""><a href="perfilcoord.php"><p>Perfil</p></a></div> 
                <a href="../logout.php"><div class="menu-sair"><p>SAIR</p></div></a> 
            </div>
        </section>

        <section class="area-notificacoes">
            <div id="feedback-bar" class="feedback-bar"></div>
            
            <h2>Eventos</h2>

            <form id="form-filtros" action="eventoscoord.php" method="GET" class="container-filtros">
                
                <select name="data">
                    <option value="">Todas as Datas</option>
                    <option value="Proximos7Dias" <?php if($filtros['data'] == 'Proximos7Dias') echo 'selected'; ?>>Próximos 7 dias</option>
                    <option value="EsteMes" <?php if($filtros['data'] == 'EsteMes') echo 'selected'; ?>>Este Mês</option>
                    <option value="ProximoMes" <?php if($filtros['data'] == 'ProximoMes') echo 'selected'; ?>>Próximo Mês</option>
                    <option value="MesPassado" <?php if($filtros['data'] == 'MesPassado') echo 'selected'; ?>>Mês Passado</option>
                </select>
                
                <select name="status">
                    <option value="">Todos os Status</option>
                    <option value="Solicitado" <?php if($filtros['status'] == 'Solicitado') echo 'selected'; ?>>Solicitado</option>
                    <option value="Aprovado" <?php if($filtros['status'] == 'Aprovado') echo 'selected'; ?>>Aprovado</option>
                    <option value="Recusado" <?php if($filtros['status'] == 'Recusado') echo 'selected'; ?>>Recusado</option>
                </select>

                <select name="solicitante">
                    <option value="">Todos os Solicitantes</option>
                    <option value="Eu" <?php if($filtros['solicitante'] == 'Eu') echo 'selected'; ?>>Criados por mim</option>
                    <option value="Professores" <?php if($filtros['solicitante'] == 'Professores') echo 'selected'; ?>>Solicitados por Professores</option>
                </select>

                <select name="turma">
                    <option value="">Todas as Turmas</option>
                    <?php foreach($lista_turmas_filtro as $turma): ?>
                        <option value="<?php echo $turma['cd_turma']; ?>" <?php if($filtros['turma'] == $turma['cd_turma']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($turma['nm_turma']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <select name="tipo">
                    <option value="">Todos os Tipos</option>
                    <?php foreach($tipos_evento as $tipo): ?>
                        <option value="<?php echo $tipo; ?>" <?php if($filtros['tipo'] == $tipo) echo 'selected'; ?>>
                            <?php echo $tipo; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                
                <a href="eventoscoord.php" class="botao-limpar">Limpar Filtros</a>
                
            </form>
            <div class="notificacao-container">
                <a href="criareventocoord.php" class="criar-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="#ffffff" d="M256 512a256 256 0 1 0 0-512 256 256 0 1 0 0 512zM232 344l0-64-64 0c-13.3 0-24-10.7-24-24s10.7-24 24-24l64 0 0-64c0-13.3 10.7-24 24-24s24 10.7 24 24l0 64 64 0c13.3 0 24 10.7 24 24s-10.7 24-24 24l-64 0 0 64c0 13.3-10.7 24-24 24s-24-10.7-24-24z"/></svg>
                </a>

                <?php if (empty($lista_eventos)): ?>
                    <p class="sem-eventos">Nenhum evento para gerenciar no momento.</p>
                <?php else: ?>
                    <?php foreach ($lista_eventos as $evento): 
                        $dt_solicitacao = (new DateTime($evento['dt_solicitacao']))->format('d/m/Y');
                        $dt_evento = (new DateTime($evento['dt_evento']))->format('d/m/Y');
                        $cor_status = 'status-' . strtolower($evento['status']);

                        // --- LÓGICA CORRETA PARA O CARD DO COORDENADOR ---
                        $classe_card = 'notificacao'; // Classe padrão

                        // Se o status do evento NÃO é mais "Solicitado" (ou seja, já foi Aprovado ou Recusado),
                        // então ele já foi tratado e deve ser esmaecido.
                        if ($evento['status'] !== 'Solicitado') {
                            $classe_card = 'notificacao card-respondido'; 
                        }
                        // --- FIM DA LÓGICA CORRETA ---
                    ?>
                        <div class="<?php echo $classe_card; ?>">
                            <h3><?php echo htmlspecialchars($evento['nm_evento']); ?></h3>
                            <p class="<?php echo $cor_status; ?>"><b>Status:</b> <?php echo $evento['status']; ?></p>
                            <p><b>Solicitado por:</b> <?php echo htmlspecialchars($evento['nm_solicitante']); ?></p>
                            <p><b>Data do Evento:</b> <?php echo (new DateTime($evento['dt_evento']))->format('d/m/Y'); ?></p>
                            <p><b>Turmas:</b> <?php echo htmlspecialchars($evento['turmas_envolvidas'] ?? 'N/A'); ?></p>
                            
                            <?php if ($evento['status'] == 'Solicitado'): ?>
                                <button class="detalhes-btn" data-id="<?php echo $evento['cd_evento']; ?>">Analisar Pedido</button>
                            <?php else: ?>
                                <button class="detalhes-btn" data-id="<?php echo $evento['cd_evento']; ?>">Ver Detalhes</button>
                            <?php endif; ?>
                        </div> 
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section> 
    </main>
    
    <div id="modal-decisao-coord" class="modal-overlay" style="display: none;">
        <div class="modal-content">
            <div id="modal-left-coord" class="modal-left">Carregando...</div>
            <div id="modal-right-coord" class="modal-right">Carregando...</div>
        </div>
    </div>

    <script src="../js/eventoscoord.js"></script>
    
    <?php if (isset($mensagem_toast)): ?>
    <script>
        // Espera um pequeno instante para garantir que a função showFeedback já foi carregada pelo script externo.
        setTimeout(() => {
            showFeedback("<?php echo addslashes($mensagem_toast); ?>", 'sucesso');
        }, 100);
    </script>
    <?php endif; ?>
</body>
</html>