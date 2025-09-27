<?php
// =================================================================
// BLOCO DE CONTROLE E DADOS DA PÁGINA
// =================================================================

// 1. CONFIGURAÇÃO E SEGURANÇA
// Carrega o autoloader (para encontrar as classes) e o guardião da sessão (para proteger a página).
require_once '../api/config.php'; 
require_once '../api/verifica_sessao.php'; 

// 2. CONFIGURAÇÃO DE DATAS
// Define o fuso horário e prepara as variáveis de data para a semana atual.
date_default_timezone_set('America/Sao_Paulo');
$hoje = new DateTime(); // Data e hora de agora
$dia_da_semana_hoje = (int)$hoje->format('N'); // Dia da semana (1=Seg, 7=Dom)

// Calcula o primeiro dia da semana (Segunda-feira)
$inicio_semana = clone $hoje;
$inicio_semana->modify('-' . ($dia_da_semana_hoje - 1) . ' days');

// Cria um array contendo os 6 objetos de data (Seg a Sab) para o cabeçalho
$dias_desta_semana = [];
for ($i = 0; $i < 6; $i++) {
    $dia_atual = clone $inicio_semana;
    $dia_atual->modify("+$i days");
    $dias_desta_semana[] = $dia_atual;
}

// Dicionários em português para evitar problemas de acentuação com o servidor
$meses_pt = ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];
$dias_semana_pt = ['Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sábado'];

// Monta o título "Mês Ano"
$mes_atual_num = (int)$hoje->format('n') - 1;
$mes_ano_atual = $meses_pt[$mes_atual_num] . ' ' . $hoje->format('Y');

// 3. LÓGICA DE EVENTOS
// Busca os eventos aprovados no banco de dados.
$eventoController = new EventoController();
$lista_eventos = $eventoController->listarAprovados();

// Prepara uma matriz (grid) para organizar os eventos por horário e dia da semana
$calendario_grid = [];
$horarios_semana = ["07:10", "08:00", "08:50", "10:00", "10:50", "11:40"];

// Inicia a matriz do calendário vazia
foreach ($horarios_semana as $horario) {
    for ($i = 1; $i <= 6; $i++) { // 1=Seg, 2=Ter... 6=Sab
        $calendario_grid[$horario][$i] = [];
    }
}

// Preenche a matriz com os eventos vindos do banco
foreach ($lista_eventos as $evento) {
    $data_evento_obj = new DateTime($evento['dt_evento']);
    $dia_da_semana_num = (int)$data_evento_obj->format('N');
    $horario_inicio = substr($evento['horario_inicio'], 0, 5);

    // Adiciona o evento na posição correta do grid (se a posição existir)
    if (isset($calendario_grid[$horario_inicio][$dia_da_semana_num])) {
        $calendario_grid[$horario_inicio][$dia_da_semana_num][] = $evento;
    }
}
?>

<script>
    // Cria uma variável JavaScript chamada 'eventosDoBanco' 
    // e o PHP a preenche com os eventos que buscamos do banco, em formato JSON.
    const eventosDoBanco = <?php echo json_encode($lista_eventos); ?>;
</script>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agenda - TáNaAgenda</title>
    <link id="favicon" rel="shortcut icon" href="../image/Favicon-light.png">
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/indexlogado.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
</head>
<body>
    <script src="../js/favicon.js"></script>
    <header class="header">
        <a href="perfil.php">
            <p> <?php echo htmlspecialchars($usuario_logado['nm_usuario']); ?> </p>
        </a>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path fill="#ffffff" d="M320 312C386.3 312 440 258.3 440 192C440 125.7 386.3 72 320 72C253.7 72 200 125.7 200 192C200 258.3 253.7 312 320 312zM290.3 368C191.8 368 112 447.8 112 546.3C112 562.7 125.3 576 141.7 576L498.3 576C514.7 576 528 562.7 528 546.3C528 447.8 448.2 368 349.7 368L290.3 368z"/></svg>
    </header>

    <main>
        <section class="area-lado">
            <a class="area-lado-logo" href="agendaprof.php"><img src="../image/logotipo fundo azul.png" alt=""></a>
            <div class="area-menu">      
                <div class="menu-agenda ativo">
                <img src="../image/icones/agenda.png" alt="">
                    <a href="agendaprof.php"><p>Agenda</p></a>
                </div>
                <div class="menu-meus-eventos">
                <img src="../image/icones/eventos.png" alt="">
                    <a href="meuseventos.php"><p>Eventos</p></a>
                </div>
                <div class="menu-perfil">
                <img src="../image/icones/perfil.png" alt="">
                    <a href="perfil.php"><p>Perfil</p></a>
                </div>  
                <a href="../logout.php"><div class="menu-sair"><p>SAIR</p></div></a> 
                
            </div>
            <section class="filtrar-calendario">
                <h2>Filtrar Calendário</h2>
                <div class="filtrar-calendario-periodo">
                    <h3>Período</h3>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path fill="#ffffff" d="M297.4 438.6C309.9 451.1 330.2 451.1 342.7 438.6L502.7 278.6C515.2 266.1 515.2 245.8 502.7 233.3C490.2 220.8 469.9 220.8 457.4 233.3L320 370.7L182.6 233.4C170.1 220.9 149.8 220.9 137.3 233.4C124.8 245.9 124.8 266.2 137.3 278.7L297.3 438.7z"/></svg>
                </div>
                <div class="divisao-checkbox"></div>
                <div class="filtrar-calendario-periodo">
                    <h3>Turma</h3>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path fill="#ffffff" d="M297.4 438.6C309.9 451.1 330.2 451.1 342.7 438.6L502.7 278.6C515.2 266.1 515.2 245.8 502.7 233.3C490.2 220.8 469.9 220.8 457.4 233.3L320 370.7L182.6 233.4C170.1 220.9 149.8 220.9 137.3 233.4C124.8 245.9 124.8 266.2 137.3 278.7L297.3 438.7z"/></svg>
                </div>
                <div class="divisao-checkbox"></div>
                <div class="filtrar-calendario-periodo">
                    <h3>Evento</h3>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path fill="#ffffff" d="M297.4 438.6C309.9 451.1 330.2 451.1 342.7 438.6L502.7 278.6C515.2 266.1 515.2 245.8 502.7 233.3C490.2 220.8 469.9 220.8 457.4 233.3L320 370.7L182.6 233.4C170.1 220.9 149.8 220.9 137.3 233.4C124.8 245.9 124.8 266.2 137.3 278.7L297.3 438.7z"/></svg>
                </div>
                <div class="divisao-checkbox"></div>
                <div class="filtrar-calendario-periodo">
                    <h3>Calendário</h3>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path fill="#ffffff" d="M297.4 438.6C309.9 451.1 330.2 451.1 342.7 438.6L502.7 278.6C515.2 266.1 515.2 245.8 502.7 233.3C490.2 220.8 469.9 220.8 457.4 233.3L320 370.7L182.6 233.4C170.1 220.9 149.8 220.9 137.3 233.4C124.8 245.9 124.8 266.2 137.3 278.7L297.3 438.7z"/></svg>
                </div>
            </section>
        </section>

        <section class="calendario">
            <div class="header-calendario">
                <div class="header-parte-de-cima">
                    <h3><?php echo $mes_ano_atual; ?></h3>
                    <div class="header-turmas">
                        <h4>Todas as turmas</h4>
                    </div>
                </div>
                <div class="header-divisoes-semanas">
                    <div></div> <?php foreach ($dias_desta_semana as $dia): ?>
                        <div class="dias-da-semana" style="background-color: <?php echo ($hoje->format('Y-m-d') == $dia->format('Y-m-d')) ? '#0479F9' : '#0d102b'; ?>;">
                            <h2>     
                                <?php
                                    // Nosso "dicionário" de dias da semana
                                    $dias_semana_pt = ['Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sábado'];
                                    $dia_semana_num = (int)$dia->format('N') - 1; // Pega o número do dia (1-6) e ajusta para o array (0-5)
                                    echo $dias_semana_pt[$dia_semana_num] . " " . $dia->format('d');
                                ?>                         
                            </h2>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="fundo-grid">
                <div class="calendario-grid">
                    <?php foreach ($horarios_semana as $horario): ?>
                        <div class="time-slot-label"><?php echo $horario; ?></div>
                        
                        <?php for ($dia_num = 1; $dia_num <= 6; $dia_num++): ?>
                            <div class="time-slot">
                                <?php
                                // Verifica se existe algum evento neste horário e dia
                                if (!empty($calendario_grid[$horario][$dia_num])):
                                    // Se houver, percorre cada evento e o exibe
                                    foreach ($calendario_grid[$horario][$dia_num] as $evento):
                                ?>
                                        <div class="event azul"
                                            data-nome="<?php echo htmlspecialchars($evento['nm_evento']); ?>"
                                            data-data="<?php echo htmlspecialchars($evento['dt_evento']); ?>"
                                            data-inicio="<?php echo htmlspecialchars($evento['horario_inicio']); ?>"
                                            data-fim="<?php echo htmlspecialchars($evento['horario_fim']); ?>"
                                            data-descricao="<?php echo htmlspecialchars($evento['ds_descricao']); ?>">
                                            <?php echo htmlspecialchars($evento['nm_evento']); ?>
                                        </div>
                                <?php
                                    endforeach;
                                endif;
                                ?>
                            </div>
                        <?php endfor; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <section class="area-lado-direito">
            <div class="calendario-lado-direito">
                <div class="header-calendario-lado-direito">
                    <h3>Janeiro</h3>
                    <h3>2025</h3>
                </div>
                <div class="dias-da-semana-calendario-lado-direito">
                    <p style="color: #022E5E">Seg</p>
                    <p>Ter</p>
                    <p>Qua</p>
                    <p>Qui</p>
                    <p>Sex</p>
                    <p>Sab</p>
                    <p style="color: #DD2B2B;">Dom</p>
                </div>
                <div class="dias-calendario-lado-direito">
                </div>
            </div>

            <section class="resumo-geral-lado-direito">
                <h3>Resumo geral de hoje:</h3>
                <div class="area-escrita-resumo-geral">
                    <p>Palestra</p>
                    <p>7:10</p>
                </div>
                <div class="area-escrita-resumo-geral" style="border: 2px solid #34CF34;">
                    <p>Visita Técnica</p>
                    <p>7:10</p>
                </div>
                <div class="area-escrita-resumo-geral">
                    <p>Palestra</p>
                    <p>8:50</p>
                </div>
                <div class="area-escrita-resumo-geral" style="border: 2px solid #34CF34;">
                    <p>Visita Técnica</p>
                    <p>10:00</p>
                </div>
            </section>

            <section class="resumo-geral-lado-direito">
                <h3>Resumo geral de amanhã:</h3>
                <div class="area-escrita-resumo-geral">
                    <p>Palestra</p>
                    <p>7:10</p>
                </div>
                <div class="area-escrita-resumo-geral" style="border: 2px solid #34CF34;">
                    <p>Visita Técnica</p>
                    <p>7:10</p>
                </div>
                <div class="area-escrita-resumo-geral" style="border: 2px solid #F9C833;">
                    <p>Oficina</p>
                    <p>8:50</p>
                </div>
                <div class="area-escrita-resumo-geral" style="border: 2px solid #F9C833;">
                    <p>Oficina</p>
                    <p>10:00</p>
                </div>
                <div class="area-escrita-resumo-geral" style="border: 2px solid #F9C833;">
                    <p>Oficina</p>
                    <p>10:50</p>
                </div>
            </section>
        </section>

        <div id="modal-overlay" class="modal-overlay" style="display: none;">
            <div class="modal-content">
                <h3>Carregando...</h3>
            </div>
        </div>

        <div id="day-modal-overlay" class="modal-overlay" style="display: none;">
            <div class="modal-content">
                <h3>Eventos do Dia <span id="selected-day"></span></h3>
                <div><p>Carregando eventos...</p></div>
            </div>
        </div>
    
        <script src="../js/agendaprof.js"></script>

    </main>
</body>
</html>