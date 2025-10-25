<?php
    // =================================================================
    // BLOCO DE CONTROLE E DADOS - AGENDA DO COORDENADOR (v2 com Filtros)
    // =================================================================

    // 1. CONFIGURAÇÃO E SEGURANÇA
    require_once '../api/config.php';
    require_once '../api/verifica_sessao.php';

    // Segurança extra: Garante que apenas coordenadores acessem
    if ($usuario_logado['tipo_usuario_ic_usuario'] !== 'Coordenador') {
        header('Location: ../login.php');
        exit();
    }

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

    // 3. BUSCA DE DADOS PARA OS FILTROS
    $turmaController = new TurmaController();
    $lista_turmas_filtro = $turmaController->listar();
    $tipos_evento = ['Palestra', 'Visita Técnica', 'Reunião', 'Prova', 'Conselho de Classe', 'Evento Esportivo', 'Outro'];

    // 4. LEITURA DOS FILTROS DA URL
    $filtros = [
        'periodo' => $_GET['periodo'] ?? [],
        'turma' => $_GET['turma'] ?? [],
        'tipo' => $_GET['tipo'] ?? []
    ];
    // Limpa filtros vazios que podem vir da URL
    foreach ($filtros as $chave => $valor) {
        if (empty($valor)) {
            $filtros[$chave] = [];
        }
    }

    // 5. LÓGICA DE EVENTOS (COM FILTROS)
    $data_inicio_mes = $hoje->format('Y-m-01');
    $data_fim_mes = $hoje->format('Y-m-t');
    $eventoController = new EventoController();
    $lista_eventos = $eventoController->listarAprovados($data_inicio_mes, $data_fim_mes, $filtros);

    // 6. PROCESSAMENTO PARA O GRID (COM FILTRO DE HORÁRIO)
    $horarios_todos = [
        "07:10", "08:00", "08:50", "10:00", "10:50", "11:40",
        "13:30", "14:20", "15:10", "16:20", "17:10", "18:00",
        "18:30", "19:20", "20:10", "21:20", "22:10"
    ];

    if (!empty($filtros['periodo'])) {
        $horarios_semana = [];
        if (in_array('Manha', $filtros['periodo'])) { $horarios_semana = array_merge($horarios_semana, ["07:10", "08:00", "08:50", "10:00", "10:50", "11:40"]); }
        if (in_array('Tarde', $filtros['periodo'])) { $horarios_semana = array_merge($horarios_semana, ["13:30", "14:20", "15:10", "16:20", "17:10", "18:00"]); }
        if (in_array('Noite', $filtros['periodo'])) { $horarios_semana = array_merge($horarios_semana, ["18:30", "19:20", "20:10", "21:20", "22:10"]); }
        if (empty($horarios_semana)) $horarios_semana = $horarios_todos;
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
    <title>Agenda (Coord.) - TáNaAgenda</title>
    <link id="favicon" rel="shortcut icon" href="../image/Favicon-light.png">
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/indexlogado.css">
</head>
<body>
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
                <div class="menu-agenda ativo"><img src="../image/icones/agenda.png" alt=""><a href="agendacoord.php"><p>Agenda</p></a></div>
                <div class="menu-meus-eventos"><img src="../image/icones/eventos.png" alt=""><a href="eventoscoord.php"><p>Eventos</p></a></div>
                <div class="menu-professores"><img src="../image/icones/professores.png" alt=""><a href="professores.php"><p>Professores</p></a></div>
                <div class="menu-turmas"><img src="../image/icones/turmas.png" alt=""><a href="turmas.php"><p>Turmas</p></a></div>
                <div class="menu-perfil"><img src="../image/icones/perfil.png" alt=""><a href="perfilcoord.php"><p>Perfil</p></a></div>
                <a href="../logout.php"><div class="menu-sair"><p>SAIR</p></div></a>
            </div>

            <form id="form-filtros-agenda" action="agendacoord.php" method="GET">
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
                        <a href="agendacoord.php" class="botao-limpar-filtro">Limpar</a>
                        <button type="submit" class="botao-filtrar">Filtrar</button>
                    </div>
                </section>
            </form>
        </section>

        <section class="calendario">
            <div class="header-calendario">
                <div class="header-parte-de-cima">
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
                <div class="header-calendario-lado-direito"><h3>Janeiro</h3><h3>2025</h3></div>
                <div class="dias-da-semana-calendario-lado-direito"><p class="dia-semana seg">Seg</p><p class="dia-semana ter">Ter</p><p class="dia-semana qua">Qua</p><p class="dia-semana qui">Qui</p><p class="dia-semana sex">Sex</p><p class="dia-semana sab">Sab</p><p class="dia-semana dom">Dom</p></div>
                <div class="dias-calendario-lado-direito"></div>
            </div>
            <section class="resumo-geral-lado-direito"><h3>Resumo geral de hoje:</h3><div class="container-scroll-eventos"></div></section>
            <section class="resumo-geral-lado-direito"><h3>Resumo geral de amanhã:</h3><div class="container-scroll-eventos"></div></section>
        </section>

        <div id="modal-overlay" class="modal-overlay" style="display: none;"><div class="modal-content"><h3>Carregando...</h3></div></div>
        <div id="day-modal-overlay" class="modal-overlay" style="display: none;"><div class="modal-content"><h3>Eventos do Dia <span id="selected-day"></span></h3><div><p>Carregando eventos...</p></div></div></div>
        
        <script src="../js/agendacoord.js"></script>
    </main>
</body>
</html>