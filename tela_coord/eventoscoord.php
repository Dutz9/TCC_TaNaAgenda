<?php

require_once '../api/config.php'; 
require_once '../api/verifica_sessao.php'; 

// Busca todos os eventos pendentes para o coordenador
$eventoController = new EventoController();
$lista_eventos_pendentes = $eventoController->listarParaCoordenador();

?>

<script>
    // Ponte de dados para o JavaScript
    const eventosDaPagina = <?php echo json_encode($lista_eventos_pendentes); ?>;
</script>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eventos - TáNaAgenda</title>
    <link id="favicon" rel="shortcut icon" href="../image/Favicon-light.png">
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/coordenador.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
</head>
<body>
<script src="../js/favicon.js"></script>
    <header class="header">
        <a href="perfilcoord.php">
            <p><?php echo htmlspecialchars($usuario_logado['nm_usuario']); ?></p>
        </a>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path fill="#ffffff" d="M320 312C386.3 312 440 258.3 440 192C440 125.7 386.3 72 320 72C253.7 72 200 125.7 200 192C200 258.3 253.7 312 320 312zM290.3 368C191.8 368 112 447.8 112 546.3C112 562.7 125.3 576 141.7 576L498.3 576C514.7 576 528 562.7 528 546.3C528 447.8 448.2 368 349.7 368L290.3 368z"/></svg>
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
            <h2>Eventos</h2>
            <div class="notificacao-container">
                <a href="criareventocoord.php" class="criar-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="#ffffff" d="M256 512a256 256 0 1 0 0-512 256 256 0 1 0 0 512zM232 344l0-64-64 0c-13.3 0-24-10.7-24-24s10.7-24 24-24l64 0 0-64c0-13.3 10.7-24 24-24s24 10.7 24 24l0 64 64 0c13.3 0 24 10.7 24 24s-10.7 24-24 24l-64 0 0 64c0 13.3-10.7 24-24 24s-24-10.7-24-24z"/></svg>
                </a>

                <?php if (empty($lista_eventos_pendentes)): ?>
                    <p class="sem-eventos">Nenhum evento pendente no momento.</p>
                <?php else: ?>
                    <?php foreach ($lista_eventos_pendentes as $evento): ?>
                        <div class="notificacao">
                            <h3><?php echo htmlspecialchars($evento['nm_evento']); ?></h3>
                            <p><b>Solicitado por:</b> <?php echo htmlspecialchars($evento['nm_solicitante']); ?></p>
                            <p><b>Data do Evento:</b> <?php echo (new DateTime($evento['dt_evento']))->format('d/m/Y'); ?></p>
                            <p><b>Turmas:</b> <?php echo htmlspecialchars($evento['turmas_envolvidas'] ?? 'N/A'); ?></p>
                            <button class="detalhes-btn" data-id="<?php echo $evento['cd_evento']; ?>">Analisar Pedido</button>
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
</body>
</html>