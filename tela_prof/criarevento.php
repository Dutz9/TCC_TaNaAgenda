<?php 

require_once '../api/config.php'; 
require_once '../api/verifica_sessao.php'; 

// --- CARREGAMENTO DE DADOS PARA O FORMULÁRIO ---
$turmaController = new TurmaController();
$lista_turmas = $turmaController->listar();

// Busca a relação de professores e turmas
$usuarioController = new UsuarioController();
$relacao_prof_turma_raw = $usuarioController->listarRelacaoProfessorTurma();

// Organiza a relação em um formato fácil para o JavaScript usar
// Ex: [ 'id_da_turma' => [ {id: prof_id, nome: 'Nome Prof'} ], ... ]
$relacao_turma_prof = [];
foreach ($relacao_prof_turma_raw as $rel) {
    $turma_id = $rel['turmas_cd_turma'];
    if (!isset($relacao_turma_prof[$turma_id])) {
        $relacao_turma_prof[$turma_id] = [];
    }
    $relacao_turma_prof[$turma_id][] = [
        'id' => $rel['cd_usuario'],
        'nome' => $rel['nm_usuario']
    ];
}

// --- PROCESSAMENTO DO FORMULÁRIO (se for enviado) ---
$mensagem = '';
$tipo_mensagem = '';
// --- PROCESSAMENTO DO FORMULÁRIO (se for enviado) ---
$mensagem = '';
$tipo_mensagem = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Instancia o controller aqui no início do bloco
        $eventoController = new EventoController();

        // Monta o array de dados PEGANDO as informações do formulário via $_POST
        $dadosEvento = [
            'cd_evento' => uniqid('EVT_'),
            'nm_evento' => $_POST['titulo'],
            'dt_evento' => $_POST['data'], // <--- Agora estamos pegando a data
            'horario_inicio' => $_POST['horario_inicio'],
            'horario_fim' => $_POST['horario_fim'],
            'tipo_evento' => $_POST['tipo'],
            'ds_descricao' => $_POST['descricao'],
            'turmas' => $_POST['turmas'] ?? [], // Usa ?? [] para o caso de nenhuma turma ser selecionada
            'cd_usuario_solicitante' => $usuario_logado['cd_usuario']
        ];
        
        // Agora sim, chama o método para criar o evento com os dados corretos
        $eventoController->criar($dadosEvento);

        $mensagem = "Evento solicitado com sucesso!";
        $tipo_mensagem = 'sucesso';

    } catch (Exception $e) {
        $mensagem = "Erro ao criar evento: " . $e->getMessage();
        $tipo_mensagem = 'erro';
    }
}

?>

    <script>
        // Ponte de dados: passa a relação Turma->Professor para o JavaScript
        const relacaoTurmaProfessor = <?php echo json_encode($relacao_turma_prof); ?>;
    </script>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Evento - TáNaAgenda</title>
    <link id="favicon" rel="shortcut icon" href="../image/Favicon-light.png">
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/index.css">
    <link rel="stylesheet" href="../css/coordenador.css">
    <link rel="stylesheet" href="../css/criarevento.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css"/>
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js" defer></script> 
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
                <div class="menu-agenda">
                <img src="../image/icones/agenda.png" alt="">
                    <a href="agendaprof.php"><p>Agenda</p></a>
                </div>
                <div class="menu-meus-eventos ativo">
                <img src="../image/icones/eventos.png" alt="">
                    <a href="meuseventos.php"><p>Eventos</p></a>
                </div>
                <div class="menu-perfil">
                <img src="../image/icones/perfil.png" alt="">
                    <a href="perfil.php"><p>Perfil</p></a>
                </div>  
                <a href="../login.php"><div class="menu-sair"><p>SAIR</p></div></a> 
            </div>
        </section>

        <section class="formulario-evento">
            <form action="criarevento.php" method="POST">
                <h2>Criar Evento</h2>

                <?php if (!empty($mensagem)): ?>
                    <div class="mensagem <?php echo $tipo_mensagem; ?>"><?php echo $mensagem; ?></div>
                <?php endif; ?>

                <div class="linha-form">
                    <div class="campo">
                        <label for="titulo">Título do Evento</label>
                        <input type="text" id="titulo" name="titulo" placeholder="Título" required>
                    </div>
                    <div class="campo">
                        <label for="selecao-turmas">Turmas Envolvidas</label>
                        <select id="selecao-turmas" name="turmas[]" multiple="multiple" required>
                            <?php foreach ($lista_turmas as $turma): ?>
                                <option value="<?php echo $turma['cd_turma']; ?>">
                                    <?php echo htmlspecialchars($turma['nm_turma']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="linha-form">
                    <div class="campo">
                        <label for="horario_inicio">Hora de Início</label>
                        <select id="horario_inicio" name="horario_inicio" required>
                            <option>07:10</option>
                            <option>08:00</option>
                            <option>08:50</option>
                            <option>10:00</option>
                            <option>10:50</option>
                            <option>11:40</option>
                        </select>
                    </div>
                    <div class="campo">
                        <label for="horario_fim">Hora de Fim</label>
                        <select id="horario_fim" name="horario_fim" required>
                            <option>08:00</option>
                            <option>08:50</option>
                            <option>09:40</option>
                            <option>10:50</option>
                            <option>11:40</option>
                            <option>12:30</option>
                        </select>
                    </div>
                </div>
                <div class="linha-form">
                    <div class="campo">
                        <label for="data">Data do Evento</label>
                        <input type="date" id="data" name="data" required>
                    </div>
                    <div class="campo">
                        <label>Professores a Notificar (automático)</label>
                        <div id="display-professores" class="display-box">
                            <p>Selecione uma ou mais turmas...</p>
                        </div>
                    </div>
                </div>
                <div class="linha-form">
                    <div class="campo">
                        <label for="tipo">Tipo do Evento</label>
                        <select id="tipo" name="tipo" required>
                            <option value="Palestra">Palestra</option>
                            <option value="Visita tecnica">Visita Técnica</option>
                            <option value="Reuniao">Reunião</option>
                        </select>
                    </div>
                    <div class="campo">
                        <label for="descricao">Descrição</label>
                        <input type="text" id="descricao" name="descricao" placeholder="Descrição do evento" required>
                    </div>
                </div>
                <div class="botoes">
                    <a href="meuseventos.php" class="botao-cancelar">Cancelar</a>
                    <button type="submit" class="botao-enviar">Enviar Solicitação</button>
                </div>
            </form>
        </section>

        <script src="../js/criarevento.js" defer></script>

</body>
</html>