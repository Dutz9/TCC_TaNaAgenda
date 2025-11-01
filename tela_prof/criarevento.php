<?php 
    require_once '../api/config.php'; 
    require_once '../api/verifica_sessao.php'; 

    $eventoController = new EventoController();
    $modo_edicao = false;
    $dados_edicao = null;
    $cd_evento_edicao = null;
    $turmas_selecionadas = [];
    $professores_selecionados_map = [];

    // --- DETECÇÃO DO MODO (CRIAR vs EDITAR) ---
    if (isset($_GET['edit']) && !empty($_GET['edit'])) {
        $modo_edicao = true;
        $cd_evento_edicao = $_GET['edit'];
        
        $dados_edicao = $eventoController->buscarParaEditar($cd_evento_edicao, $usuario_logado['cd_usuario']);
        
        if ($dados_edicao === null) {
            $_SESSION['mensagem_sucesso'] = "Erro: Evento não encontrado ou você não tem permissão para editá-lo.";
            header('Location: meuseventos.php');
            exit();
        }
        
        if (!empty($dados_edicao['turmas_ids'])) {
            $turmas_selecionadas = explode(',', $dados_edicao['turmas_ids']);
        }
        if (!empty($dados_edicao['professores_ids'])) {
            $professores_selecionados = explode(',', $dados_edicao['professores_ids']);
            foreach ($professores_selecionados as $prof_id) {
                if (!empty($prof_id)) { $professores_selecionados_map[$prof_id] = true; }
            }
        }
    }

    // --- CARREGAMENTO DE DADOS PARA OS FORMULÁRIOS ---
    $turmaController = new TurmaController();
    $lista_turmas = $turmaController->listar();
    $usuarioController = new UsuarioController();
    $relacao_prof_turma_raw = $usuarioController->listarRelacaoProfessorTurma();
    $relacao_turma_prof = [];
    foreach ($relacao_prof_turma_raw as $rel) {
        $turma_id = $rel['turmas_cd_turma'];
        if (!isset($relacao_turma_prof[$turma_id])) { $relacao_turma_prof[$turma_id] = []; }
        $relacao_turma_prof[$turma_id][] = ['id' => $rel['cd_usuario'], 'nome' => $rel['nm_usuario']];
    }
    $mapa_alunos_turma = [];
    foreach ($lista_turmas as $turma) {
        $mapa_alunos_turma[$turma['cd_turma']] = $turma['qt_alunos'];
    }

    // --- PROCESSAMENTO DO FORMULÁRIO (POST) ---
    $mensagem = '';
    $tipo_mensagem = '';
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        try {
            $titulo = trim($_POST['titulo']);
            if (empty($titulo)) { throw new Exception("O título não pode ser vazio."); }
            if (strlen($titulo) > 45) { throw new Exception("O título é muito longo (máx 45 caracteres)."); }

            $dadosEvento = [
                'nm_evento' => $titulo,
                'dt_evento' => $_POST['data'],
                'horario_inicio' => $_POST['horario_inicio'],
                'horario_fim' => $_POST['horario_fim'],
                'tipo_evento' => $_POST['tipo'],
                'ds_descricao' => $_POST['descricao'],
                'turmas' => $_POST['turmas'] ?? [],
                'professores' => $_POST['professores_notificar'] ?? [],
                'cd_usuario_solicitante' => $usuario_logado['cd_usuario']
            ];

            // --- A LÓGICA DE DECISÃO ESTÁ AQUI ---
            if ($modo_edicao) {
                // MODO DE ATUALIZAÇÃO
                // Passa o ID do evento e os novos dados para o controller
                $eventoController->atualizarSolicitacao($cd_evento_edicao, $dadosEvento);
                $_SESSION['mensagem_sucesso'] = "Evento atualizado com sucesso!";
            } else {
                // MODO DE CRIAÇÃO
                $dadosEvento['cd_evento'] = uniqid('EVT_'); // Só precisa de um novo ID se for criação
                $eventoController->criar($dadosEvento);
                $_SESSION['mensagem_sucesso'] = "Evento solicitado com sucesso!";
            }
            
            header('Location: meuseventos.php');
            exit();

        } catch (PDOException $e) {
            $codigoErro = $e->getCode();
            if ($codigoErro == '22001') {
                $mensagem = "Erro: Os dados inseridos são longos demais. Verifique o título ou a descrição.";
            } else {
                $mensagem = "Erro de banco de dados: " . $e->getMessage();
            }
            $tipo_mensagem = 'erro';
        } catch (Exception $e) {
            $mensagem = "Erro: " . $e->getMessage();
            $tipo_mensagem = 'erro';
        }
    }
?>

<script>
    const relacaoTurmaProfessor = <?php echo json_encode($relacao_turma_prof); ?>;
    const mapaAlunosTurma = <?php echo json_encode($mapa_alunos_turma); ?>;
    const usuario_logado = <?php echo json_encode($usuario_logado); ?>;
    const modoEdicao = <?php echo $modo_edicao ? 'true' : 'false'; ?>; 
    const professoresSelecionados = <?php echo json_encode($professores_selecionados_map); ?>;
</script>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $modo_edicao ? 'Editar Evento' : 'Criar Evento'; ?> - TáNaAgenda</title>
    <link id="favicon" rel="shortcut icon" href="../image/Favicon-light.png">
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/criarevento.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css"/>
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js" defer></script> 
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
            <a class="area-lado-logo" href="agendaprof.php"><img src="../image/logotipo fundo azul.png" alt="Logotipo TáNaAgenda"></a>
            <div class="area-menu"> 
                <div class="menu-agenda"><img src="../image/icones/agenda.png" alt=""><a href="agendaprof.php"><p>Agenda</p></a></div>
                <div class="menu-meus-eventos ativo"><img src="../image/icones/eventos.png" alt=""><a href="meuseventos.php"><p>Eventos</p></a></div>
                <div class="menu-perfil"><img src="../image/icones/perfil.png" alt=""><a href="perfil.php"><p>Perfil</p></a></div> 
                <a href="../logout.php"><div class="menu-sair"><p>SAIR</p></div></a> 
            </div>
        </section>

        <div class="conteudo-principal">
            <section class="formulario-evento">
                <form action="criarevento.php<?php echo $modo_edicao ? '?edit=' . $cd_evento_edicao : ''; ?>" method="POST">
                    <h2><?php echo $modo_edicao ? 'Editar Evento' : 'Criar Evento'; ?></h2>

                    <?php if (!empty($mensagem)): ?>
                        <div class="mensagem <?php echo $tipo_mensagem; ?>"><?php echo $mensagem; ?></div>
                    <?php endif; ?>

                    <div class="linha-form">
                        <div class="campo">
                            <label for="titulo">Título do Evento (máx. 45 caracteres)</label>
                            <input type="text" id="titulo" name="titulo" placeholder="Ex: Visita à USP" maxlength="45" required 
                                   value="<?php echo htmlspecialchars($dados_edicao['nm_evento'] ?? ''); ?>">
                        </div>
                        <div class="campo">
                            <label for="tipo">Tipo do Evento</label>
                            <select id="tipo" name="tipo" required>
                                <?php $tipo_selecionado = $dados_edicao['tipo_evento'] ?? ''; ?>
                                <option value="Palestra" <?php if($tipo_selecionado == 'Palestra') echo 'selected'; ?>>Palestra</option>
                                <option value="Visita Técnica" <?php if($tipo_selecionado == 'Visita Técnica') echo 'selected'; ?>>Visita Técnica</option>
                                <option value="Reunião" <?php if($tipo_selecionado == 'Reunião') echo 'selected'; ?>>Reunião</option>
                                <option value="Prova" <?php if($tipo_selecionado == 'Prova') echo 'selected'; ?>>Prova</option>
                                <option value="Conselho de Classe" <?php if($tipo_selecionado == 'Conselho de Classe') echo 'selected'; ?>>Conselho de Classe</option>
                                <option value="Evento Esportivo" <?php if($tipo_selecionado == 'Evento Esportivo') echo 'selected'; ?>>Evento Esportivo</option>
                                <option value="Outro" <?php if($tipo_selecionado == 'Outro') echo 'selected'; ?>>Outro</option>
                            </select>
                        </div>
                    </div>

                    <div class="linha-form">
                        <div class="campo">
                            <label for="data">Data do Evento</label>
                            <input type="date" id="data" name="data" required
                                   value="<?php echo htmlspecialchars($dados_edicao['dt_evento'] ?? ''); ?>">
                        </div>
                        <div class="campo">
                            <label for="selecao-turmas">Turmas Envolvidas</label>
                            <select id="selecao-turmas" name="turmas[]" multiple required>
                                <?php foreach ($lista_turmas as $turma): ?>
                                    <option value="<?php echo $turma['cd_turma']; ?>"
                                        <?php if (in_array($turma['cd_turma'], $turmas_selecionadas)) echo 'selected'; ?>>
                                        <?php echo htmlspecialchars($turma['nm_turma']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="linha-form">
                        <div class="campo">
                            <label for="horario_inicio">Horário de Início</label>
                            <select id="horario_inicio" name="horario_inicio" required>
                                <?php $horario_selecionado = $dados_edicao['horario_inicio'] ?? ''; ?>
                                <option value="" disabled <?php if(empty($horario_selecionado)) echo 'selected'; ?>>Selecione</option>
                                <optgroup label="Manhã">
                                    <option <?php if($horario_selecionado == '07:10') echo 'selected'; ?>>07:10</option>
                                    <option <?php if($horario_selecionado == '08:00') echo 'selected'; ?>>08:00</option>
                                    <option <?php if($horario_selecionado == '08:50') echo 'selected'; ?>>08:50</option>
                                    <option <?php if($horario_selecionado == '10:00') echo 'selected'; ?>>10:00</option>
                                    <option <?php if($horario_selecionado == '10:50') echo 'selected'; ?>>10:50</option>
                                    <option <?php if($horario_selecionado == '11:40') echo 'selected'; ?>>11:40</option>
                                </optgroup>
                                <optgroup label="Tarde">
                                    <option <?php if($horario_selecionado == '13:30') echo 'selected'; ?>>13:30</option>
                                    <option <?php if($horario_selecionado == '14:20') echo 'selected'; ?>>14:20</option>
                                    <option <?php if($horario_selecionado == '15:10') echo 'selected'; ?>>15:10</option>
                                    <option <?php if($horario_selecionado == '16:20') echo 'selected'; ?>>16:20</option>
                                    <option <?php if($horario_selecionado == '17:10') echo 'selected'; ?>>17:10</option>
                                    <option <?php if($horario_selecionado == '18:00') echo 'selected'; ?>>18:00</option>
                                </optgroup>
                                <optgroup label="Noite">
                                    <option <?php if($horario_selecionado == '18:30') echo 'selected'; ?>>18:30</option>
                                    <option <?php if($horario_selecionado == '19:20') echo 'selected'; ?>>19:20</option>
                                    <option <?php if($horario_selecionado == '20:10') echo 'selected'; ?>>20:10</option>
                                    <option <?php if($horario_selecionado == '21:20') echo 'selected'; ?>>21:20</option>
                                    <option <?php if($horario_selecionado == '22:10') echo 'selected'; ?>>22:10</option>
                                </optgroup>
                            </select>
                        </div>
                         <div class="campo">
                            <label for="horario_fim">Horário de Encerramento</label>
                            <select id="horario_fim" name="horario_fim" required>
                                <?php $horario_selecionado = $dados_edicao['horario_fim'] ?? ''; ?>
                                <option value="" disabled <?php if(empty($horario_selecionado)) echo 'selected'; ?>>Selecione</option>
                                <optgroup label="Manhã">
                                    <option <?php if($horario_selecionado == '08:00') echo 'selected'; ?>>08:00</option>
                                    <option <?php if($horario_selecionado == '08:50') echo 'selected'; ?>>08:50</option>
                                    <option <?php if($horario_selecionado == '09:40') echo 'selected'; ?>>09:40</option>
                                    <option <?php if($horario_selecionado == '10:50') echo 'selected'; ?>>10:50</option>
                                    <option <?php if($horario_selecionado == '11:40') echo 'selected'; ?>>11:40</option>
                                    <option <?php if($horario_selecionado == '12:30') echo 'selected'; ?>>12:30</option>
                                </optgroup>
                                <optgroup label="Tarde">
                                    <option <?php if($horario_selecionado == '14:20') echo 'selected'; ?>>14:20</option>
                                    <option <?php if($horario_selecionado == '15:10') echo 'selected'; ?>>15:10</option>
                                    <option <?php if($horario_selecionado == '16:00') echo 'selected'; ?>>16:00</option>
                                    <option <?php if($horario_selecionado == '17:10') echo 'selected'; ?>>17:10</option>
                                    <option <?php if($horario_selecionado == '18:00') echo 'selected'; ?>>18:00</option>
                                    <option <?php if($horario_selecionado == '18:50') echo 'selected'; ?>>18:50</option>
                                </optgroup>
                                <optgroup label="Noite">
                                    <option <?php if($horario_selecionado == '19:20') echo 'selected'; ?>>19:20</option>
                                    <option <?php if($horario_selecionado == '20:10') echo 'selected'; ?>>20:10</option>
                                    <option <?php if($horario_selecionado == '21:00') echo 'selected'; ?>>21:00</option>
                                    <option <?php if($horario_selecionado == '22:10') echo 'selected'; ?>>22:10</option>
                                    <option <?php if($horario_selecionado == '23:00') echo 'selected'; ?>>23:00</option>
                                </optgroup>
                            </select>
                        </div>
                    </div>

                    <div class="linha-form">
                         <div class="campo">
                            <label>Professores a Notificar (automático)</label>
                            <div id="display-professores" class="display-box"><p>Carregando professores...</p></div>
                        </div>
                        <div class="campo">
                            <label>Total de Alunos (automático)</label>
                            <input type="text" id="display-total-alunos" value="0" readonly class="display-box-alunos">
                        </div>
                    </div>

                    <div class="linha-form">
                        <div class="campo" style="width: 100%;">
                            <label for="descricao">Descrição</label>
                            <textarea id="descricao" name="descricao" placeholder="Descreva brevemente o evento..." required><?php echo htmlspecialchars($dados_edicao['ds_descricao'] ?? ''); ?></textarea>
                        </div>
                    </div>

                    <div class="botoes">
                        <a href="meuseventos.php" class="botao-cancelar">Cancelar</a>
                        <button type="submit" class="botao-enviar">
                            <?php echo $modo_edicao ? 'Salvar Alterações' : 'Enviar Solicitação'; ?>
                        </button>
                    </div>
                </form>
            </section>
        </div>
    </main>

    <div id="modal-confirm-remove-prof" class="confirm-modal-overlay" style="display: none;">
        <div class="confirm-modal-content">
            <h3>Confirmar Ação</h3>
            <p>Você tem certeza que deseja remover o professor <b id="modal-prof-name"></b> da lista de notificações?</p>
            <div class="confirmation-buttons">
                <button id="btn-confirm-no" class="botao-cancelar">Não, cancelar</button>
                <button id="btn-confirm-yes" class="botao-enviar" style="background-color: #dc3545;">Sim, remover</button>
            </div>
        </div>
    </div>
    
    <script src="../js/criarevento.js" defer></script>
</body>
</html>