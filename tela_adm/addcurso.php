<?php 
    require_once '../api/config.php'; 
    require_once '../api/verifica_sessao.php'; 

    // Garante que apenas administradores acessem
    if ($usuario_logado['tipo_usuario_ic_usuario'] !== 'Administrador') {
        header('Location: ../tela_prof/agendaprof.php');
        exit();
    }

    $mensagem = '';
    $tipo_mensagem = '';
    
    // Instancia o Controller (presume-se que CursoController.php foi atualizado)
    $cursoController = new CursoController();

    // --- PROCESSAMENTO DO FORMULÁRIO (POST) ---
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        try {
            // CAPTURA DOS DADOS DO FORMULÁRIO ESTÁTICO:
            // O campo "Nome do curso" usa name="titulo" no HTML (vamos capturar como nm_curso)
            $nm_curso = $_POST['titulo'] ?? null; 
            // O campo "Período" usa id/name="periodo" no HTML (vamos capturar como ic_periodo)
            $ic_periodo = $_POST['periodo'] ?? null; 
            
            // --- VALIDAÇÕES BÁSICAS ---
            if (empty($nm_curso) || empty($ic_periodo)) {
                throw new Exception("O nome do curso e o período são obrigatórios.");
            }
            // Mapeamento dos valores do SELECT (manha/tarde/noite) para os ENUMs do BD (Manha/Tarde/Noite)
            $mapa_periodo = [
                'manha' => 'Manha',
                'tarde' => 'Tarde',
                'noite' => 'Noite'
            ];
            $periodo_db = $mapa_periodo[$ic_periodo] ?? null;

            if (empty($periodo_db)) {
                throw new Exception("Período inválido selecionado.");
            }
            
            $dadosCurso = [
                'nm_curso' => $nm_curso,
                'ic_periodo' => $periodo_db // Usa o valor do ENUM corrigido
            ];

            // 4. EXECUÇÃO DA LÓGICA (cria o curso)
            $cursoController->criarCurso($dadosCurso);
            
            // 5. REDIRECIONA COM MENSAGEM DE SUCESSO
            $_SESSION['mensagem_sucesso'] = "Curso '".htmlspecialchars($nm_curso)."' adicionado com sucesso!";
            header('Location: cursos.php');
            exit();

        } catch (Exception $e) {
            $erro = $e->getMessage();
            // "Traduz" o erro do banco de dados (da SP)
            if (strpos($erro, 'Erro: Este curso já está cadastrado.') !== false) {
                $mensagem = 'Erro: Este curso já está cadastrado.';
            } else {
                $mensagem = $erro;
            }
            $tipo_mensagem = 'erro';
            
            // É útil reter os valores em caso de erro, mas neste formulário simples não o faremos.
        }
    }
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Curso - TáNaAgenda</title>
    <link id="favicon" rel="shortcut icon" href="../image/Favicon-light.png">
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/addcurso.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
</head>
<body>
<script src="../js/favicon.js"></script>
    <header class="header">
        <a href="perfiladm.php">
            <p> <?php echo htmlspecialchars($usuario_logado['nm_usuario']); ?> </p>
        </a>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path fill="#ffffff" d="M320 312C386.3 312 440 258.3 440 192C440 125.7 386.3 72 320 72C253.7 72 200 125.7 200 192C200 258.3 253.7 312 320 312zM290.3 368C191.8 368 112 447.8 112 546.3C112 562.7 125.3 576 141.7 576L498.3 576C514.7 576 528 562.7 528 546.3C528 447.8 448.2 368 349.7 368L290.3 368z"/></svg>
    </header>

    <main>
        
    <section class="area-lado">
            <a class="area-lado-logo" href="agendaadm.php"><img src="../image/logotipo fundo azul.png" alt=""></a>
            <div class="area-menu">
                <div class="menu-agenda">
                <img src="../image/icones/agenda.png" alt="">
                    <a href="agendaadm.php"><p>Agenda</p></a>
                </div>
                <div class="menu-meus-eventos">
                <img src="../image/icones/eventos.png" alt="">
                    <a href="eventosadm.php"><p>Eventos</p></a>
                </div>
                <div class="menu-professores">
                <img src="../image/icones/professores.png" alt="">
                    <a href="professoresadm.php"><p>Professores e Coordenadores</p></a>
                </div> 
                
                <div class="menu-cursos ativo">
                <img src="../image/icones/cursos.png" alt="">
                    <a href="cursos.php"><p>Cursos</p></a>
                </div> 
                <div class="menu-turmas">
                <img src="../image/icones/turmas.png" alt="">
                    <a href="turmas.php"><p>Turmas</p></a>
                </div>
                <div class="menu-perfil">
                <img src="../image/icones/perfil.png" alt="">
                    <a href="perfiladm.php"><p>Perfil</p></a>
                </div>  
                <a href="../logout.php"><div class="menu-sair"><p>SAIR</p></div></a> 
            </div>
        </section>

        <section class="formulario-turma" >
            <h2>Adicionar Curso</h2>

            <!-- Exibe a mensagem de erro/sucesso do PHP -->
            <?php if (!empty($mensagem)): ?>
                <div class="mensagem <?php echo $tipo_mensagem; ?>" style="width: 80%; margin: 0 auto 20px auto; padding: 10px; border-radius: 5px; text-align: center; color: white; background-color: <?php echo ($tipo_mensagem == 'erro') ? '#dc3545' : '#28a745'; ?>;">
                    <?php echo htmlspecialchars($mensagem); ?>
                </div>
            <?php endif; ?>
            
            <form action="addcurso.php" method="POST">
                <div class="linha-form">
                    <div  class="campo">
                        <label  for="titulo">Nome do curso</label>
                        <!-- ATENÇÃO: Adicionado o atributo name="titulo" para o PHP capturar -->
                        <input  type="text" id="titulo" name="titulo" placeholder="Curso" required>
                    </div>
                    <div class="campo">
                        <label for="duracao-curso">Duração do curso</label>
                        <!-- Este campo é apenas informativo e não é enviado -->
                        <select id="duracao-curso" disabled>
                            <option value="3-anos" selected>3 anos (Padrão)</option>
                            <option value="1-ano">1 ano</option>
                            <option value="2-anos">2 anos</option>
                            <option value="4-anos">4 anos</option>
                        </select>
                    </div>
                </div>

                <div class="linha-form">
                    <div class="campo">
                        <label for="periodo">Período</label>
                        <!-- ATENÇÃO: Adicionado o atributo name="periodo" para o PHP capturar -->
                        <select id="periodo" name="periodo" required>
                            <option value="" disabled selected>Selecione</option>
                            <option value="manha">Manhã</option>
                            <option value="tarde">Tarde</option>
                            <option value="noite">Noite</option>
                        </select>
                    </div>
                    <!-- Campo em branco para manter o alinhamento de 2 colunas -->
                    <div class="campo"></div> 
                </div>

                <div class="linha-botoes">
                    <div class="botoes">
                        <!-- Botão Cancelar (volta sem enviar) -->
                        <a href="cursos.php" class="botao-cancelar">Cancelar</a>
                        <!-- Botão Enviar (submete o formulário) -->
                        <button type="submit" class="botao-enviar">Adicionar Curso</button>
                    </div>
                </div>
            </form>
        </section>
    </main>
</body>
</html>