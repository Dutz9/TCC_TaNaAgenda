<?php 
require_once '../api/config.php'; 
require_once '../api/verifica_sessao.php'; 

// Garante que apenas coordenadores acessem esta página
if ($usuario_logado['tipo_usuario_ic_usuario'] !== 'Coordenador') {
    header('Location: ../tela_prof/agendaprof.php');
    exit();
}

// --- PROCESSAMENTO DO FORMULÁRIO ---
$mensagem = '';
$tipo_mensagem = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $eventoController = new EventoController();
        $dadosEvento = [
            'cd_evento' => uniqid('EVT_'),
            'nm_evento' => $_POST['titulo'],
            'dt_evento' => $_POST['data'],
            'horario_inicio' => $_POST['horario_inicio'],
            'horario_fim' => $_POST['horario_fim'],
            'tipo_evento' => $_POST['tipo'],
            'ds_descricao' => $_POST['descricao'],
            'turmas' => $_POST['turmas'] ?? [],
            'cd_usuario_solicitante' => $usuario_logado['cd_usuario']
        ];
        
        $eventoController->criarAprovado($dadosEvento);
        $mensagem = "Evento criado e publicado com sucesso!";
        $tipo_mensagem = 'sucesso';
    } catch (Exception $e) {
        $mensagem = "Erro ao criar evento: " . $e->getMessage();
        $tipo_mensagem = 'erro';
    }
}

// --- CARREGAMENTO DE DADOS PARA O FORMULÁRIO ---
$turmaController = new TurmaController();
$lista_turmas = $turmaController->listar();

$usuarioController = new UsuarioController();
$relacao_prof_turma_raw = $usuarioController->listarRelacaoProfessorTurma();

$relacao_turma_prof = [];
foreach ($relacao_prof_turma_raw as $rel) {
    $turma_id = $rel['turmas_cd_turma'];
    if (!isset($relacao_turma_prof[$turma_id])) {
        $relacao_turma_prof[$turma_id] = [];
    }
    $relacao_turma_prof[$turma_id][] = ['id' => $rel['cd_usuario'], 'nome' => $rel['nm_usuario']];
}
?>
<script>
    // Ponte de dados do PHP para o JavaScript
    const relacaoTurmaProfessor = <?php echo json_encode($relacao_turma_prof); ?>;
</script>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Evento (Coord.) - TáNaAgenda</title>
    <link rel="shortcut icon" href="../image/Favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/criarevento.css">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css"/>
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js" defer></script>
</head>
<body>

    <header class="header">
        <a href="perfilcoord.php"><p><?php echo htmlspecialchars($usuario_logado['nm_usuario']); ?></p></a>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="#ffffff" d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512H418.3c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304H178.3z"/></svg>
    </header>

    <main>
        <section class="area-lado">
            <a href="agendacoord.php"><img src="../image/logotipo fundo azul.png" alt=""></a>
            <div class="area-menu">
                <div class="menu-agenda"><img src="../image/icones/agenda.png" alt=""><a href="agendacoord.php"><p>Agenda</p></a></div>
                <div class="menu-meus-eventos ativo"><img src="../image/icones/eventos.png" alt=""><a href="eventoscoord.php"><p>Eventos</p></a></div>
                <div class="menu-professores"><img src="../image/icones/professores.png" alt=""><a href="professores.php"><p>Professores</p></a></div> 
                <div class="menu-turmas"><img src="../image/icones/turmas.png" alt=""><a href="turmas.php"><p>Turmas</p></a></div> 
                <div class="menu-perfil"><img src="../image/icones/perfil.png" alt=""><a href="perfilcoord.php"><p>Perfil</p></a></div> 
                <a href="../logout.php"><div class="menu-sair"><p>SAIR</p></div></a> 
            </div>
        </section>
        
        <div class="conteudo-principal">
            <section class="formulario-evento">
                <form action="criareventocoord.php" method="POST">
                    <h2>Criar Evento</h2>

                    <?php if (!empty($mensagem)): ?>
                        <div class="mensagem <?php echo $tipo_mensagem; ?>"><?php echo $mensagem; ?></div>
                    <?php endif; ?>

                    <div class="linha-form">
                        <div class="campo">
                            <label for="titulo">Título do Evento</label>
                            <input type="text" id="titulo" name="titulo" placeholder="Ex: Reunião Geral" required>
                        </div>
                        <div class="campo">
                             <label for="selecao-turmas">Turmas Envolvidas</label>
                            <select id="selecao-turmas" name="turmas[]" multiple="multiple" required>
                                <?php foreach ($lista_turmas as $turma): ?>
                                    <option value="<?php echo $turma['cd_turma']; ?>"><?php echo htmlspecialchars($turma['nm_turma']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="linha-form">
                        <div class="campo">
                            <label for="horario_inicio">Hora de Início</label>
                            <select id="horario_inicio" name="horario_inicio" required>
                                <option>07:10</option><option>08:00</option><option>08:50</option><option>10:00</option><option>10:50</option><option>11:40</option>
                            </select>
                        </div>
                        <div class="campo">
                            <label for="horario_fim">Hora de Fim</label>
                            <select id="horario_fim" name="horario_fim" required>
                               <option>08:00</option><option>08:50</option><option>09:40</option><option>10:50</option><option>11:40</option><option>12:30</option>
                            </select>
                        </div>
                    </div>
                    <div class="linha-form">
                        <div class="campo">
                            <label for="data">Data do Evento</label>
                            <input type="date" id="data" name="data" required>
                        </div>
                         <div class="campo">
                            <label for="tipo">Tipo do Evento</label>
                            <select id="tipo" name="tipo" required>
                                <option value="Palestra">Palestra</option>
                                <option value="Visita Técnica">Visita Técnica</option>
                                <option value="Reunião">Reunião</option>
                                <option value="Prova">Prova</option>
                                <option value="Conselho de Classe">Conselho de Classe</option>
                                <option value="Evento Esportivo">Evento Esportivo</option>
                                <option value="Outro">Outro</option>
                            </select>
                        </div>
                    </div>
                    <div class="linha-form">
                         <div class="campo">
                            <label>Professores Envolvidos (automático)</label>
                            <div id="display-professores" class="display-box">
                                <p>Selecione uma ou mais turmas...</p>
                            </div>
                        </div>
                        <div class="campo">
                            <label for="descricao">Descrição</label>
                            <textarea id="descricao" name="descricao" placeholder="Descreva brevemente o evento..." required></textarea>
                        </div>
                    </div>
                    <div class="botoes">
                        <a href="eventoscoord.php" class="botao-cancelar">Cancelar</a>
                        <button type="submit" class="botao-enviar">Criar e Notificar</button>
                    </div>
                </form>
            </section>
        </div>
    </main>

    <script src="../js/criarevento.js"></script>
</body>
</html>