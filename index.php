<?php
// =================================================================
// BLOCO DE DADOS - PÁGINA PÚBLICA (INDEX)
// =================================================================

// 1. CONFIGURAÇÃO (SEM VERIFICAÇÃO DE SESSÃO)
require_once 'config_local.php';

// 2. CONFIGURAÇÃO DE DATAS
date_default_timezone_set('America/Sao_Paulo');
$hoje = new DateTime();
$dia_da_semana_hoje = (int)$hoje->format('N');
$inicio_semana = clone $hoje;
$inicio_semana->modify('-' . ($dia_da_semana_hoje - 1) . ' days');
$dias_desta_semana = [];
for ($i = 0; $i < 6; $i++) {
    $dia_atual = clone $inicio_semana;
    $dia_atual->modify("+$i days");
    $dias_desta_semana[] = $dia_atual;
}
$meses_pt = ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];
$dias_semana_pt = ['Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sábado'];
$mes_atual_num = (int)$hoje->format('n') - 1;
$mes_ano_atual = $meses_pt[$mes_atual_num] . ' ' . $hoje->format('Y');

// 3. LÓGICA DE EVENTOS (COM FILTRO DE DATA)
$data_inicio_semana = $dias_desta_semana[0]->format('Y-m-d');
$data_fim_semana = $dias_desta_semana[5]->format('Y-m-d');
$eventoController = new EventoController();
$lista_eventos = $eventoController->listarAprovados($data_inicio_semana, $data_fim_semana);

// 4. PROCESSAMENTO PARA O GRID
$calendario_grid = [];
$horarios_semana = [
    "07:10", "08:00", "08:50", "10:00", "10:50", "11:40",
    "13:30", "14:20", "15:10", "16:20", "17:10", "18:00",
    "18:30", "19:20", "20:10", "21:20", "22:10"
];
foreach ($horarios_semana as $horario) {
    for ($i = 1; $i <= 6; $i++) { $calendario_grid[$horario][$i] = []; }
}
foreach ($lista_eventos as $evento) {
    $dia_da_semana_num = (int)(new DateTime($evento['dt_evento']))->format('N');
    $horario_inicio = substr($evento['horario_inicio'], 0, 5);
    if (isset($calendario_grid[$horario_inicio][$dia_da_semana_num])) {
        $calendario_grid[$horario_inicio][$dia_da_semana_num][] = $evento;
    }
}
?>
<script>
    const eventosDoBanco = <?php echo json_encode($lista_eventos); ?>;
</script>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agenda - TáNaAgenda</title>
    <link id="favicon" rel="shortcut icon" href="image/Favicon-light.png">
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/index.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
</head>
<body>
    <script src="js/favicon.js"></script>
    <header class="header">
        <a href="login.php"><p>Login</p></a>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="#ffffff" d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512H418.3c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304H178.3z"/></svg>
    </header>

    <main>
        <section class="area-lado">
            <a class="area-lado-logo" href="index.php"><img src="image/logotipo fundo azul.png" alt="Logotipo TáNaAgenda"></a>
            <section class="filtrar-calendario">
                <h2>Filtrar Calendário</h2>
                </section>
        </section>

        <section class="calendario">
            <div class="header-calendario">
                <div class="header-parte-de-cima">
                    <h3><?php echo $mes_ano_atual; ?></h3>
                    <div class="header-turmas"><h4>Todas as turmas</h4></div>
                </div>
                <div class="header-divisoes-semanas">
                    <div></div>
                    <?php foreach ($dias_desta_semana as $dia): ?>
                        <div class="dias-da-semana" style="background-color: <?php echo ($hoje->format('Y-m-d') == $dia->format('Y-m-d')) ? '#0479F9' : '#0d102b'; ?>;">
                            <h2><?php $dia_semana_num = (int)$dia->format('N') - 1; echo $dias_semana_pt[$dia_semana_num] . " " . $dia->format('d'); ?></h2>
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
                                <?php if (!empty($calendario_grid[$horario][$dia_num])):
                                    foreach ($calendario_grid[$horario][$dia_num] as $evento):
                                        $tipo_classe = 'tipo-' . str_replace(' ', '-', strtolower($evento['tipo_evento']));
                                ?>
                                    <div class="event <?php echo $tipo_classe; ?>"
                                        data-nome="<?php echo htmlspecialchars($evento['nm_evento']); ?>"
                                        data-data="<?php echo htmlspecialchars($evento['dt_evento']); ?>"
                                        data-inicio="<?php echo htmlspecialchars($evento['horario_inicio']); ?>"
                                        data-fim="<?php echo htmlspecialchars($evento['horario_fim']); ?>"
                                        data-descricao="<?php echo htmlspecialchars($evento['ds_descricao']); ?>"
                                        data-turmas="<?php echo htmlspecialchars($evento['turmas_envolvidas']); ?>"
                                        data-professores="<?php echo htmlspecialchars($evento['professores_envolvidos']); ?>">
                                        <?php echo htmlspecialchars($evento['nm_evento']); ?>
                                    </div>
                                <?php endforeach; endif; ?>
                            </div>
                        <?php endfor; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <section class="area-lado-direito">
            <div class="calendario-lado-direito">
                <div class="header-calendario-lado-direito"><h3>Janeiro</h3><h3>2025</h3></div>
                <div class="dias-da-semana-calendario-lado-direito"><p class="dia-semana seg">Seg</p><p class="dia-semana ter">Ter</p><p class="dia-semana qua">Qua</p><p class="dia-semana qui">Qui</p><p class="dia-semana sex">Sex</p><p class="dia-semana sab">Sab</p><p class="dia-semana dom">Dom</p></div>
                <div class="dias-calendario-lado-direito"></div>
            </div>
            <section class="resumo-geral-lado-direito"><h3>Resumo geral de hoje:</h3><div class="container-scroll-eventos"></div></section>
            <section class="resumo-geral-lado-direito"><h3>Resumo geral de amanhã:</h3><div class="container-scroll-eventos"></div></section>
        </section>

        <div id="modal-overlay" class="modal-overlay" style="display: none;"><div class="modal-content"><h3>Carregando...</h3></div></div>
        <div id="day-modal-overlay" class="modal-overlay" style="display: none;"><div class="modal-content"><h3>Eventos do Dia <span id="selected-day"></span></h3><div><p>Carregando eventos...</p></div></div></div>
        
        <script src="js/index.js"></script>
    </main>
</body>
</html>