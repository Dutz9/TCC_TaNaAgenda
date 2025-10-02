<?php 
require_once '../api/config.php'; 
require_once '../api/verifica_sessao.php'; 
require_once '../classes/controllers/EventoController.php'; // Certifique-se de incluir
require_once '../classes/controllers/UsuarioController.php'; // Certifique-se de incluir
require_once '../classes/controllers/TurmaController.php'; // Certifique-se de incluir

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
        $titulo = trim($_POST['titulo']); // Remover espaços em branco no início/fim

        // Validação de backend para o título
        if (empty($titulo)) {
            throw new Exception("O título do evento não pode ser vazio.");
        }
        if (strlen($titulo) > 10) {
            throw new Exception("O título do evento não pode exceder 10 caracteres.");
        }
        
        $eventoController = new EventoController();
        $dadosEvento = [
            'cd_evento' => uniqid('EVT_'),
            'nm_evento' => $titulo, // Use a variável $titulo validada
            'dt_evento' => $_POST['data'],
            'horario_inicio' => $_POST['horario_inicio'],
            'horario_fim' => $_POST['horario_fim'],
            'tipo_evento' => $_POST['tipo'],
            'ds_descricao' => $_POST['descricao'],
            'turmas' => $_POST['turmas'] ?? [],
            'professores_envolvidos' => $_POST['professores_envolvidos'] ?? [], // <--- NOVO: Professores selecionados manualmente
            'cd_usuario_solicitante' => $usuario_logado['cd_usuario']
        ];
        
        $eventoController->criarAprovado($dadosEvento);
        $mensagem = "Evento criado e publicado com sucesso! Todos os coordenadores e professores envolvidos foram notificados.";
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
// Não precisamos mais da relação professor-turma para o display automático
// $relacao_prof_turma_raw = $usuarioController->listarRelacaoProfessorTurma();
// $relacao_turma_prof = [];
// foreach ($relacao_prof_turma_raw as $rel) {
//     $turma_id = $rel['turmas_cd_turma'];
//     if (!isset($relacao_turma_prof[$turma_id])) {
//         $relacao_turma_prof[$turma_id] = [];
//     }
//     $relacao_turma_prof[$turma_id][] = ['id' => $rel['cd_usuario'], 'nome' => $rel['nm_usuario']];
// }

// <--- NOVO: Carregar a lista de todos os professores
$lista_professores = $usuarioController->listarTodosProfessores();

?>
<script>
    // Remove a variável 'relacaoTurmaProfessor' se não for mais usada para evitar erros no JS
    // const relacaoTurmaProfessor = <?php echo json_encode($relacao_turma_prof); ?>;
</script>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Evento (Coord.) - TáNaAgenda</title>
    <link id="favicon" rel="shortcut icon" href="../image/Favicon-light.png">
    <link rel="stylesheet" href="../css/global.css">    
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
                            <input type="text" id="titulo" name="titulo" placeholder="Ex: Palestra USP" maxlength="10" required>
                            <small id="titulo-contador" style="color: #888; font-size: 0.8em; margin-top: 5px; display: block;"></small>
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
                            <label for="horario_inicio">Horário de Início</label>
                            <select id="horario_inicio" name="horario_inicio" required>
                            <option value="" disabled selected>Selecione uma opção</option>
                            <option>07:10</option>
                            <option>08:00</option>
                            <option>08:50</option>
                            <option>10:00</option>
                            <option>10:50</option>
                            <option>11:40</option>
                            
                            <option>13:30</option>
                            <option>14:20</option>
                            <option>15:10</option>
                            <option>16:20</option>
                            <option>17:10</option>
                            <option>18:00</option>

                            <option>18:30</option>
                            <option>19:20</option>
                            <option>20:10</option>
                            <option>21:20</option>
                            <option>22:10</option>
                            </select>
                        </div>
                        <div class="campo">
                            <label for="horario_fim">Horário de Encerramento</label>
                            <select id="horario_fim" name="horario_fim" required>
                            <option value="" disabled selected>Selecione uma opção</option>
                            <option>08:00</option>
                            <option>08:50</option>
                            <option>09:40</option>
                            <option>10:50</option>
                            <option>11:40</option>

                            <option>12:30</option>
                            <option>14:20</option>
                            <option>15:10</option>
                            <option>16:00</option>
                            <option>17:10</option>
                            <option>18:50</option>

                            <option>19:20</option>
                            <option>20:10</option>
                            <option>21:00</option>
                            <option>22:10</option>
                            <option>23:00</option>
                            </select>
                            <span id="error-message-fim" style="color: red;"></span> <!-- Novo span de erro para horário fim -->
                        </div>
                    </div>
                    <div class="linha-form">
                        <div class="campo">
                            <label for="data">Data do Evento</label>
                            <input type="date" id="data" name="data" required>
                            <span id="error-message"></span>
                        </div>
                         <div class="campo">
                            <label for="tipo">Tipo do Evento</label>
                            <select id="tipo" name="tipo" required>
                                <option value="Palestra">Palestra</option>
                                <option value="Visita Tecnica">Visita Técnica</option>
                                <option value="Reuniao">Reunião</option>
                                <option value="Prova">Prova</option>
                                <option value="Conselho de Classe">Conselho de Classe</option>
                                <option value="Evento Esportivo">Evento Esportivo</option>
                                <option value="Outro">Outro</option>
                            </select>
                        </div>
                    </div>
                    <div class="linha-form">
                        <!-- <div class="campo">
                            <label>Professores Envolvidos (automático)</label>
                            <div id="display-professores" class="display-box">
                                <p>Selecione uma ou mais turmas...</p>
                            </div>
                        </div> -->
                        <!-- NOVO: Campo de seleção manual de professores -->
                        <div class="campo">
                            <label for="selecao-professores">Professores a Notificar</label>
                            <select id="selecao-professores" name="professores_envolvidos[]" multiple="multiple">
                                <?php foreach ($lista_professores as $professor): ?>
                                    <option value="<?php echo $professor['cd_usuario']; ?>"><?php echo htmlspecialchars($professor['nm_usuario']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="campo">
                            <label for="descricao">Descrição</label>
                            <textarea id="descricao" name="descricao" placeholder="Descreva brevemente o evento..." required></textarea>
                        </div>
                    </div>
                    <div class="botoes">
                        <a href="eventoscoord.php" class="botao-cancelar">Cancelar</a>
                        <button type="submit" class="botao-enviar">Criar e Publicar</button>
                    </div>
                </form>
            </section>
        </div>
    </main>

    <script src="../js/criarevento.js"></script>
</body>
</html>