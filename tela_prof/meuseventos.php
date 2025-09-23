<?php
// 1. CONFIGURAÇÃO E SEGURANÇA
require_once '../api/config.php'; 
require_once '../api/verifica_sessao.php'; 

// 2. BUSCA DOS DADOS
$eventoController = new EventoController();
$cd_usuario_logado = $usuario_logado['cd_usuario'];
$lista_eventos = $eventoController->listarParaProfessor($cd_usuario_logado);
?>

<script>
    // Passa a lista de eventos para uma variável JavaScript
    const eventosDaPagina = <?php echo json_encode($lista_eventos); ?>;
</script>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus Eventos - TáNaAgenda</title>
    <link rel="shortcut icon" href="../image/Favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/meuseventos.css">
</head>
<body>
    <header class="header">
        <a href="perfil.php"><p><?php echo htmlspecialchars($usuario_logado['nm_usuario']); ?></p></a>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path fill="#ffffff" d="M320 312C386.3 312 440 258.3 440 192C440 125.7 386.3 72 320 72C253.7 72 200 125.7 200 192C200 258.3 253.7 312 320 312zM290.3 368C191.8 368 112 447.8 112 546.3C112 562.7 125.3 576 141.7 576L498.3 576C514.7 576 528 562.7 528 546.3C528 447.8 448.2 368 349.7 368L290.3 368z"/></svg>
    </header>

    <main>
        <section class="area-lado">
            <a href="agendaprof.php"><img src="../image/logotipo fundo azul.png" alt=""></a>
            <div class="area-menu"> 
                <div class="menu-agenda"><img src="../image/icones/agenda.png" alt=""><a href="agendaprof.php"><p>Agenda</p></a></div>
                <div class="menu-meus-eventos ativo"><img src="../image/icones/eventos.png" alt=""><a href="meuseventos.php"><p>Eventos</p></a></div>
                <div class="menu-perfil"><img src="../image/icones/perfil.png" alt=""><a href="perfil.php"><p>Perfil</p></a></div>
                <a href="../logout.php"><div class="menu-sair"><p>SAIR</p></div></a> 
            </div>
        </section>

        <section class="area-notificacoes">
            <h2>Eventos</h2>
            <div class="notificacao-container">
                <a href="criarevento.php" class="criar-btn"> 
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="#ffffff" d="M256 512a256 256 0 1 0 0-512 256 256 0 1 0 0 512zM232 344l0-64-64 0c-13.3 0-24-10.7-24-24s10.7-24 24-24l64 0 0-64c0-13.3 10.7-24 24-24s24 10.7 24 24l0 64 64 0c13.3 0 24 10.7 24 24s-10.7 24-24 24l-64 0 0 64c0 13.3-10.7 24-24 24s-24-10.7-24-24z"/></svg>
                </a>

                <?php if (empty($lista_eventos)): ?>
                    <p class="sem-eventos">Nenhum evento para mostrar no momento.</p>
                <?php else: ?>
                    <?php foreach ($lista_eventos as $evento): 
                        // Formata as datas para exibição
                        $dt_solicitacao = (new DateTime($evento['dt_solicitacao']))->format('d/m/Y');
                        $dt_evento = (new DateTime($evento['dt_evento']))->format('d/m/Y');
                        
                        // Define a cor e o texto do status
                        $cor_status = '';
                        switch ($evento['status']) {
                            case 'Aprovado': $cor_status = 'status-aprovado'; break;
                            case 'Recusado': $cor_status = 'status-recusado'; break;
                            default: $cor_status = 'status-solicitado'; break;
                        }
                    ?>
                        <div class="notificacao">
                            <h3><?php echo htmlspecialchars($evento['nm_evento']); ?></h3>
                            <p class="<?php echo $cor_status; ?>"><b>Status: </b> <?php echo $evento['status']; ?></p>
                            
                            <?php if ($evento['cd_usuario_solicitante'] == $cd_usuario_logado): ?>
                                <p><b>Solicitado por: </b> Você</p>
                            <?php else: ?>
                                <p><b>Solicitado por: </b> <?php echo htmlspecialchars($evento['nm_solicitante']); ?></p>
                            <?php endif; ?>

                            <p><b>Data de Solicitação: </b> <?php echo $dt_solicitacao; ?></p>
                            <p><b>Data do Evento: </b> <?php echo $dt_evento; ?></p>
                            <p><b>Turmas: </b> <?php echo htmlspecialchars($evento['turmas_envolvidas'] ?? 'N/A'); ?></p>
                            
                            <div class="botoes-acao">
                                <button class="detalhes-btn" data-id="<?php echo $evento['cd_evento']; ?>">Mais Detalhes</button>
                                
                                <div class="opcoes-resposta">
                                    <?php 
                                    // CONDIÇÃO 1: Se é um evento de outro prof, está solicitado E eu AINDA NÃO RESPONDI...
                                    if ($evento['status'] == 'Solicitado' && $evento['cd_usuario_solicitante'] != $cd_usuario_logado && $evento['minha_resposta'] === null): 
                                    ?>
                                        <button class="btn-recusar" data-id="<?php echo $evento['cd_evento']; ?>">Recusar</button>
                                        <button class="btn-aprovar" data-id="<?php echo $evento['cd_evento']; ?>">Aprovar</button>
                                    <?php 
                                    // CONDIÇÃO 2: Se é um evento de outro prof e EU JÁ RESPONDI...
                                    elseif ($evento['cd_usuario_solicitante'] != $cd_usuario_logado && $evento['minha_resposta'] !== null): 
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

        <div id="modal-detalhes-evento" class="modal-overlay" style="display: none;">
            <div class="modal-content">
                <div class="modal-left">
                    Carregando...
                </div>
                <div class="modal-right">
                    Carregando...
                </div>
            </div>
        </div>

    </main>
    
    <script src="../js/meuseventos.js"></script>

</body>
</html>