<?php
    // =================================================================
    // BLOCO DE DADOS - PÁGINA PÚBLICA (INDEX) (v3 com Navegação)
    // =================================================================

    // 1. CONFIGURAÇÃO (SEM VERIFICAÇÃO DE SESSÃO)
    require_once 'config_local.php';

    // 2. LEITURA DOS FILTROS (VEM ANTES DAS DATAS)
    $filtros = [
        'periodo' => $_GET['periodo'] ?? [],
        'turma' => $_GET['turma'] ?? [],
        'tipo' => $_GET['tipo'] ?? []
    ];
    foreach ($filtros as $chave => $valor) {
        if (empty($valor)) { $filtros[$chave] = []; }
    }
    $filtros_url = http_build_query($filtros);

    // 3. CONFIGURAÇÃO DE DATAS (COM LÓGICA DE NAVEGAÇÃO)
    date_default_timezone_set('America/Sao_Paulo');
    $hoje = new DateTime(); // Referência para o dia atual

    if (isset($_GET['week']) && !empty($_GET['week'])) {
        try { $data_base = new DateTime($_GET['week']); } 
        catch (Exception $e) { $data_base = new DateTime(); }
    } else {
        $data_base = new DateTime();
    }

    $dia_da_semana_num = (int)$data_base->format('N');
    $inicio_semana = clone $data_base;
    if ($dia_da_semana_num != 1) {
        $inicio_semana->modify('-' . ($dia_da_semana_num - 1) . ' days');
    }

    // --- CÁLCULO DOS LINKS DE NAVEGAÇÃO ---
    $link_semana_anterior = 'index.php?week=' . (clone $inicio_semana)->modify('-7 days')->format('Y-m-d') . '&' . $filtros_url;
    $link_proxima_semana = 'index.php?week=' . (clone $inicio_semana)->modify('+7 days')->format('Y-m-d') . '&' . $filtros_url;
    $link_hoje = 'index.php?' . $filtros_url;

    $dias_desta_semana = [];
    for ($i = 0; $i < 6; $i++) {
        $dia_atual = clone $inicio_semana;
        $dia_atual->modify("+$i days");
        $dias_desta_semana[] = $dia_atual;
    }

    $meses_pt = ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];
    $dias_semana_pt = ['Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sábado'];

    $mes_ano_inicio_obj = $dias_desta_semana[0];
    $mes_ano_fim_obj = $dias_desta_semana[5];
    $mes_ano_inicio = $meses_pt[(int)$mes_ano_inicio_obj->format('n') - 1] . ' ' . $mes_ano_inicio_obj->format('Y');
    $mes_ano_fim = $meses_pt[(int)$mes_ano_fim_obj->format('n') - 1] . ' ' . $mes_ano_fim_obj->format('Y');

    if ($mes_ano_inicio == $mes_ano_fim) {
        $mes_ano_atual = $mes_ano_inicio;
    } else {
        if ($mes_ano_inicio_obj->format('Y') != $mes_ano_fim_obj->format('Y')) {
            $mes_ano_atual = $mes_ano_inicio . ' / ' . $mes_ano_fim;
        } else {
            $mes_ano_atual = $meses_pt[(int)$mes_ano_inicio_obj->format('n') - 1] . ' / ' . $meses_pt[(int)$mes_ano_fim_obj->format('n') - 1] . ' ' . $mes_ano_fim_obj->format('Y');
        }
    }

    // 4. BUSCA DE DADOS PARA OS FILTROS
    $turmaController = new TurmaController();
    $lista_turmas_filtro = $turmaController->listar();
    $tipos_evento = ['Palestra', 'Visita Técnica', 'Reunião', 'Prova', 'Conselho de Classe', 'Evento Esportivo', 'Outro'];

    // 5. LÓGICA DE EVENTOS (BUSCA NO BANCO)
    $data_inicio_semana = $dias_desta_semana[0]->format('Y-m-d');
    $data_fim_semana = $dias_desta_semana[5]->format('Y-m-d');
    $eventoController = new EventoController();
    $lista_eventos = $eventoController->listarAprovados($data_inicio_semana, $data_fim_semana, $filtros);

    // 6. PROCESSAMENTO PARA O GRID
    $horarios_todos = [ "07:10", "08:00", "08:50", "10:00", "10:50", "11:40", "13:30", "14:20", "15:10", "16:20", "17:10", "18:00", "18:30", "19:20", "20:10", "21:20", "22:10" ];
    if (!empty($filtros['periodo'])) {
        $horarios_semana = [];
        if (in_array('Manha', $filtros['periodo'])) { $horarios_semana = array_merge($horarios_semana, ["07:10", "08:00", "08:50", "10:00", "10:50", "11:40"]); }
        if (in_array('Tarde', $filtros['periodo'])) { $horarios_semana = array_merge($horarios_semana, ["13:30", "14:20", "15:10", "16:20", "17:10", "18:00"]); }
        if (in_array('Noite', $filtros['periodo'])) { $horarios_semana = array_merge($horarios_semana, ["18:30", "19:20", "20:10", "21:20", "22:10"]); }
        if (empty($horarios_semana)) $horarios_semana = [];
    } else {
        $horarios_semana = $horarios_todos;
    }
    $calendario_grid = [];
    $dias_para_grid = [];
    foreach ($dias_desta_semana as $dia) { $dias_para_grid[] = $dia->format('Y-m-d'); }
    foreach ($horarios_semana as $horario) {
        foreach ($dias_para_grid as $data_chave) { $calendario_grid[$horario][$data_chave] = []; }
    }
    foreach ($lista_eventos as $evento) {
        $data_evento_chave = $evento['dt_evento'];
        $horario_inicio = substr($evento['horario_inicio'], 0, 5);
        if (isset($calendario_grid[$horario_inicio][$data_evento_chave])) {
            $calendario_grid[$horario_inicio][$data_evento_chave][] = $evento;
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
            
            <form id="form-filtros-agenda" action="index.php" method="GET">
                <?php if (isset($_GET['week'])): ?>
                    <input type="hidden" name="week" value="<?php echo htmlspecialchars($_GET['week']); ?>">
                <?php endif; ?>

                <section class="filtrar-calendario">
                    <h2>Filtrar Calendário</h2>
                    
                    <div class="filtro-item">
                        <div class="filtro-header">
                            <h3>Período</h3>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" class="filtro-seta"><path fill="#ffffff" d="M137.4 374.6c12.5 12.5 32.8 12.5 45.3 0l128-128c9.2-9.2 11.9-22.9 6.9-34.9s-16.6-19.8-29.6-19.8L32 192c-12.9 0-24.6 7.8-29.6 19.8s-2.2 25.7 6.9 34.9l128 128z"/></svg>
                        </div>
                        <div class="filtro-opcoes">
                            <label><input type="checkbox" name="periodo[]" value="Manha" <?php if(in_array('Manha', $filtros['periodo'])) echo 'checked'; ?>> Manhã</label>
                            <label><input type="checkbox" name="periodo[]" value="Tarde" <?php if(in_array('Tarde', $filtros['periodo'])) echo 'checked'; ?>> Tarde</label>
                            <label><input type="checkbox" name="periodo[]" value="Noite" <?php if(in_array('Noite', $filtros['periodo'])) echo 'checked'; ?>> Noite</label>
                        </div>
                    </div>
                    
                    <div class="filtro-item">
                        <div class="filtro-header">
                            <h3>Turma</h3>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" class="filtro-seta"><path fill="#ffffff" d="M137.4 374.6c12.5 12.5 32.8 12.5 45.3 0l128-128c9.2-9.2 11.9-22.9 6.9-34.9s-16.6-19.8-29.6-19.8L32 192c-12.9 0-24.6 7.8-29.6 19.8s-2.2 25.7 6.9 34.9l128 128z"/></svg>
                        </div>
                        <div class="filtro-opcoes" id="filtro-opcoes-turmas">
                            <?php foreach($lista_turmas_filtro as $turma): ?>
                                <label><input type="checkbox" name="turma[]" value="<?php echo $turma['cd_turma']; ?>" <?php if(in_array($turma['cd_turma'], $filtros['turma'])) echo 'checked'; ?>>
                                    <?php echo htmlspecialchars($turma['nm_turma']); ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="filtro-item">
                        <div class="filtro-header">
                            <h3>Tipo de Evento</h3>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" class="filtro-seta"><path fill="#ffffff" d="M137.4 374.6c12.5 12.5 32.8 12.5 45.3 0l128-128c9.2-9.2 11.9-22.9 6.9-34.9s-16.6-19.8-29.6-19.8L32 192c-12.9 0-24.6 7.8-29.6 19.8s-2.2 25.7 6.9 34.9l128 128z"/></svg>
                        </div>
                        <div class="filtro-opcoes">
                             <?php foreach($tipos_evento as $tipo): ?>
                                <label><input type="checkbox" name="tipo[]" value="<?php echo $tipo; ?>" <?php if(in_array($tipo, $filtros['tipo'])) echo 'checked'; ?>>
                                    <?php echo $tipo; ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <div class="filtro-botoes">
                        <a href="index.php" class="botao-limpar-filtro">Limpar</a>
                        <button type="submit" class="botao-filtrar">Filtrar</button>
                    </div>
                </section>
            </form>
        </section>

        <section class="calendario">
            <div class="header-calendario">
                <div class="header-parte-de-cima">
                    <div class="navegacao-calendario">
                        <a href="<?php echo $link_semana_anterior; ?>" class="nav-btn" title="Semana Anterior">&lt;</a>
                        <!-- <a href="<?php echo $link_hoje; ?>" class="nav-btn today">Hoje</a> -->
                        <a href="<?php echo $link_proxima_semana; ?>" class="nav-btn" title="Próxima Semana">&gt;</a>
                    </div>
                    <h3><?php echo $mes_ano_atual; ?></h3>
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
                        <?php foreach ($dias_desta_semana as $dia):
                            $data_chave_atual = $dia->format('Y-m-d');
                        ?>
                            <div class="time-slot">
                                <?php
                                if (!empty($calendario_grid[$horario][$data_chave_atual])):
                                    foreach ($calendario_grid[$horario][$data_chave_atual] as $evento):
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
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <section class="area-lado-direito">
            <div class="calendario-lado-direito">
                <div class="header-calendario-lado-direito">
                    <div id="mini-cal-prev" class="mini-cal-nav" title="Mês Anterior">&lt;</div>
                    <h3>Janeiro</h3>
                    <h3>2025</h3>
                    <div id="mini-cal-next" class="mini-cal-nav" title="Próximo Mês">&gt;</div>
                </div>
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