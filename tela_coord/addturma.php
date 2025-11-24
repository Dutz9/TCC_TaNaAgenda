<?php 
    require_once '../api/config.php'; 
    require_once '../api/verifica_sessao.php'; 

    // Garante que apenas coordenadores acessem
    if ($usuario_logado['tipo_usuario_ic_usuario'] !== 'Coordenador') {
        header('Location: ../tela_prof/agendaprof.php');
        exit();
    }

    $mensagem = '';
    $tipo_mensagem = '';
    $turmaController = new TurmaController();

    // --- PROCESSAMENTO DO FORMULÁRIO (POST) ---
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        try {
            // Captura os dados do formulário
            $nm_turma = $_POST['nm_turma'] ?? null;
            $ic_serie = $_POST['ic_serie'] ?? null;
            $qt_alunos = $_POST['qt_alunos'] ?? null;
            $cd_sala = $_POST['cd_sala'] ?? null;
            $cd_curso = $_POST['cd_curso'] ?? null;

            // Validações
            if (empty($nm_turma) || empty($ic_serie) || empty($qt_alunos) || empty($cd_sala) || empty($cd_curso)) {
                throw new Exception("Todos os campos são obrigatórios.");
            }
            if (!is_numeric($qt_alunos) || $qt_alunos <= 0) {
                throw new Exception("A quantidade de alunos deve ser um número positivo.");
            }
            
            $dadosTurma = [
                'nm_turma' => $nm_turma,
                'ic_serie' => $ic_serie,
                'qt_alunos' => $qt_alunos,
                'cd_sala' => $cd_sala,
                'cd_curso' => $cd_curso
            ];

            // Chama o controller para criar a turma
            $turmaController->criarTurma($dadosTurma);
            
            // Redireciona com mensagem de sucesso
            $_SESSION['mensagem_sucesso'] = "Turma '".htmlspecialchars($nm_turma)."' adicionada com sucesso!";
            header('Location: turmas.php');
            exit();

        } catch (Exception $e) {
            $erro = $e->getMessage();
            // "Traduz" o erro do banco de dados
            if (strpos($erro, 'Erro: O nome (Sigla) desta turma já está em uso.') !== false) {
                $mensagem = 'Erro: A Sigla (Nome) desta turma já está em uso.';
            } else {
                $mensagem = $erro;
            }
            $tipo_mensagem = 'erro';
        }
    }

    // --- CARREGAMENTO DE DADOS (GET) ---
    // Busca a lista de cursos para o dropdown
    $cursoController = new CursoController();
    $lista_cursos = $cursoController->listar();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Turma - TáNaAgenda</title>
    <link id="favicon" rel="shortcut icon" href="../image/Favicon-light.png">
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/criarevento.css"> 
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
</head>
<body>
<script src="../js/favicon.js"></script>
    <header class="header">
        <button class="menu-toggle" id="menu-toggle">☰</button>
        <a href="perfilcoord.php">
            <p> <?php echo htmlspecialchars($usuario_logado['nm_usuario']); ?> </p>
        </a>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="#ffffff" d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512H418.3c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304H178.3z"/></svg>
    </header>

    <main>
        <section class="area-lado">
            <a class="area-lado-logo" href="agendacoord.php"><img src="../image/logotipo fundo azul.png" alt=""></a>
            <div class="area-menu">
                <div class="menu-agenda"><img src="../image/icones/agenda.png" alt=""><a href="agendacoord.php"><p>Agenda</p></a></div>
                <div class="menu-meus-eventos"><img src="../image/icones/eventos.png" alt=""><a href="eventoscoord.php"><p>Eventos</p></a></div>
                <div class="menu-professores"><img src="../image/icones/professores.png" alt=""><a href="professores.php"><p>Professores</p></a></div> 
                <div class="menu-turmas ativo"><img src="../image/icones/turmas.png" alt=""><a href="turmas.php"><p>Turmas</p></a></div> 
                <div class="menu-perfil"><img src="../image/icones/perfil.png" alt=""><a href="perfilcoord.php"><p>Perfil</p></a></div> 
                <a href="../logout.php"><div class="menu-sair"><p>SAIR</p></div></a> 
            </div>
        </section>

        <div class="conteudo-principal">
            <section class="formulario-evento"> <form action="addturma.php" method="POST">
                    <h2>Adicionar Turma</h2>

                    <?php if (!empty($mensagem)): ?>
                        <div class="mensagem <?php echo $tipo_mensagem; ?>"><?php echo $mensagem; ?></div>
                    <?php endif; ?>

                    <div class="linha-form">
                        <div class="campo">
                            <label for="nm_turma">Sigla da Turma (Nome Único):</label>
                            <input type="text" id="nm_turma" name="nm_turma" placeholder="Ex: 3I1" required>
                        </div>
                        <div class="campo">
                            <label for="cd_curso">Curso:</label>
                            <select id="cd_curso" name="cd_curso" required>
                                <option value="" disabled selected>Selecione o curso</option>
                                <?php foreach($lista_cursos as $curso): ?>
                                    <option value="<?php echo $curso['cd_curso']; ?>">
                                        <?php echo htmlspecialchars($curso['nm_curso']) . ' (' . htmlspecialchars($curso['ic_periodo']) . ')'; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="linha-form">
                        <div class="campo">
                            <label for="cd_sala">Sala:</label>
                            <input type="text" id="cd_sala" name="cd_sala" placeholder="Ex: Sala 04" required>
                        </div>
                        <div class="campo">
                            <label for="qt_alunos">Quantidade de Alunos:</label>
                            <input type="number" id="qt_alunos" name="qt_alunos" placeholder="Ex: 30" required min="0">
                        </div>
                    </div>
                    <div class="linha-form">
                        <div class="campo">
                            <label for="ic_serie">Série/Módulo:</label>
                            <select id="ic_serie" name="ic_serie" required>
                                <option value="" disabled selected>Selecione a série</option>
                                <option value="1">1º</option>
                                <option value="2">2º</option>
                                <option value="3">3º</option>
                            </select>
                        </div>
                        <div class="campo">
                            </div>
                    </div>

                    <div class="botoes">
                        <a href="turmas.php" class="botao-cancelar">Cancelar</a>
                        <button type="submit" class="botao-enviar">Adicionar Turma</button>
                    </div>
                </form>
            </section>
        </div>
    </main>
    <div class="menu-overlay" id="menu-overlay"></div>
    </body>
</html>