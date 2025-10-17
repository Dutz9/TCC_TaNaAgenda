<?php
// 1. CONFIGURAÇÃO E SEGURANÇA
require_once '../api/config.php'; 
require_once '../api/verifica_sessao.php'; 

// 2. BUSCA DOS DADOS
$eventoController = new EventoController();
$cd_usuario_logado = $usuario_logado['cd_usuario'];
$lista_eventos = $eventoController->listarParaProfessor($cd_usuario_logado);

// 3. LÓGICA PARA MENSAGEM DE FEEDBACK (TOAST)
if (isset($_SESSION['mensagem_sucesso'])) {
    $mensagem_toast = $_SESSION['mensagem_sucesso'];
    unset($_SESSION['mensagem_sucesso']);
}
?>
<script>
    // Ponte de dados do PHP para o JavaScript
    const eventosDaPagina = <?php echo json_encode($lista_eventos); ?>;
    const usuario_logado = <?php echo json_encode($usuario_logado); ?>;
</script>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus Eventos - TáNaAgenda</title>
    <link id="favicon" rel="shortcut icon" href="../image/Favicon-light.png">
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/meuseventos.css">
</head>
<body>
    <script src="../js/favicon.js"></script>
    <header class="header">
        <a href="perfil.php">
            <p><?php echo htmlspecialchars($usuario_logado['nm_usuario']); ?></p>
        </a>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="#ffffff" d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512H418.3c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304H178.3z"/></svg>
    </header>

    <main>
        <section class="area-lado">
            <a class="area-lado-logo" href="agendaprof.php"><img src="../image/logotipo fundo azul.png" alt=""></a>
            <div class="area-menu"> 
                <div class="menu-agenda"><img src="../image/icones/agenda.png" alt=""><a href="agendaprof.php"><p>Agenda</p></a></div>
                <div class="menu-meus-eventos ativo"><img src="../image/icones/eventos.png" alt=""><a href="meuseventos.php"><p>Eventos</p></a></div>
                <div class="menu-perfil"><img src="../image/icones/perfil.png" alt=""><a href="perfil.php"><p>Perfil</p></a></div> 
                <a href="../logout.php"><div class="menu-sair"><p>SAIR</p></div></a> 
            </div>
        </section>

        <section class="area-notificacoes">
            <div id="toast-notification" class="feedback-bar"></div>
            
            <h2>Eventos</h2>
            <div class="notificacao-container">
                <a href="criarevento.php" class="criar-btn"> 
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="#ffffff" d="M256 512a256 256 0 1 0 0-512 256 256 0 1 0 0 512zM232 344l0-64-64 0c-13.3 0-24-10.7-24-24s10.7-24 24-24l64 0 0-64c0-13.3 10.7-24 24-24s24 10.7 24 24l0 64 64 0c13.3 0 24 10.7 24 24s-10.7 24-24 24l-64 0 0 64c0 13.3-10.7 24-24 24s-24-10.7-24-24z"/></svg>
                </a>

                <?php if (empty($lista_eventos)): ?>
                    <p class="sem-eventos">Nenhum evento para mostrar no momento.</p>
                <?php else: ?>
                    <?php foreach ($lista_eventos as $evento): 
                        $dt_solicitacao = (new DateTime($evento['dt_solicitacao']))->format('d/m/Y');
                        $dt_evento = (new DateTime($evento['dt_evento']))->format('d/m/Y');
                        $cor_status = 'status-' . strtolower($evento['status']);
                    ?>
                        <div class="notificacao">
                            <h3><?php echo htmlspecialchars($evento['nm_evento']); ?></h3>
                            <p class="<?php echo $cor_status; ?>"><b>Status:</b> <?php echo $evento['status']; ?></p>
                            <p><b>Solicitado por:</b> <?php echo ($evento['cd_usuario_solicitante'] == $cd_usuario_logado) ? 'Você' : htmlspecialchars($evento['nm_solicitante']); ?></p>
                            <p><b>Data de Solicitação:</b> <?php echo $dt_solicitacao; ?></p>
                            <p><b>Data do Evento:</b> <?php echo $dt_evento; ?></p>
                            <p><b>Turmas:</b> <?php echo htmlspecialchars($evento['turmas_envolvidas'] ?? 'N/A'); ?></p>
                            
                            <div class="botoes-acao">
                                <button class="detalhes-btn" data-id="<?php echo $evento['cd_evento']; ?>">Mais Detalhes</button>
                                
                                <div class="opcoes-resposta">
                                    <?php if ($evento['cd_usuario_solicitante'] != $cd_usuario_logado && $evento['minha_resposta'] !== null): 
                                        $cor_minha_resposta = ($evento['minha_resposta'] == 'Aprovado') ? 'status-aprovado' : 'status-recusado';
                                    ?>
                                        <p class="<?php echo $cor_minha_resposta; ?>">Sua resposta: <?php echo $evento['minha_resposta']; ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div> 
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section> 
    </main>
    
    <div id="modal-detalhes-evento" class="modal-overlay" style="display: none;">
        <div class="modal-content">
            <div class="modal-left">Carregando...</div>
            <div class="modal-right">Carregando...</div>
        </div>
    </div>

    <script src="../js/meuseventos.js"></script>

    <?php if (isset($mensagem_toast)): ?>
    <script>
        setTimeout(() => {
            showToast("<?php echo addslashes($mensagem_toast); ?>", 'sucesso');
        }, 100);
    </script>
    <?php endif; ?>
</body>
</html>