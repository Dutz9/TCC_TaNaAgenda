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
    $usuarioController = new UsuarioController();

    // --- PROCESSAMENTO DO FORMULÁRIO (POST) ---
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        try {
            $rm = $_POST['rm'] ?? null;
            $nome = $_POST['nome'] ?? null;
            $email = $_POST['email'] ?? null;
            $telefone = $_POST['telefone'] ?? null;
            $senha = $_POST['senha'] ?? null;
            $confirma_senha = $_POST['confirma_senha'] ?? null;
            $turmas = $_POST['turmas'] ?? [];

            // --- Validações Corrigidas ---
            if (empty($rm) || empty($nome) || empty($email) || empty($senha) || empty($confirma_senha)) {
                throw new Exception("Todos os campos, exceto telefone, são obrigatórios.");
            }

            // --- CORREÇÃO (Bug 3) ---
            // Verifica se as senhas são iguais
            if ($senha !== $confirma_senha) {
                throw new Exception("As senhas não coincidem.");
            }
            // --- FIM DA CORREÇÃO ---

            if (strlen($senha) < 3) {
                throw new Exception("A senha deve ter pelo menos 3 caracteres.");
            }
            
            $dadosProfessor = [
                'cd_usuario' => $rm,
                'nome' => $nome,
                'email' => $email,
                'senha' => $senha,
                'telefone' => $telefone
            ];

            $usuarioController->criarProfessor($dadosProfessor, $turmas);
            
            $_SESSION['mensagem_sucesso'] = "Professor '".htmlspecialchars($nome)."' adicionado com sucesso!";
            header('Location: professores.php');
            exit();

        } catch (Exception $e) {
            $erro = $e->getMessage();
            if (strpos($erro, 'Erro: Este RM já está cadastrado.') !== false) {
                $mensagem = "Erro: Este RM já está cadastrado.";
            } elseif (strpos($erro, 'Erro: Este e-mail já está em uso.') !== false) {
                $mensagem = "Erro: Este e-mail já está em uso.";
            } else {
                $mensagem = $erro;
            }
            $tipo_mensagem = 'erro';
        }
    }

    // --- CARREGAMENTO DE DADOS (GET) ---
    $turmaController = new TurmaController();
    $lista_turmas = $turmaController->listar();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Professor - TáNaAgenda</title>
    <link id="favicon" rel="shortcut icon" href="../image/Favicon-light.png">
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/criarevento.css"> 
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css"/>
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js" defer></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
</head>
<body>
<script src="../js/favicon.js"></script>
    <header class="header">
        <a href="perfilcoord.php">
            <p> <?php echo htmlspecialchars($usuario_logado['nm_usuario']); ?> </p>
        </a>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path fill="#ffffff" d="M320 312C386.3 312 440 258.3 440 192C440 125.7 386.3 72 320 72C253.7 72 200 125.7 200 192C200 258.3 253.7 312 320 312zM290.3 368C191.8 368 112 447.8 112 546.3C112 562.7 125.3 576 141.7 576L498.3 576C514.7 576 528 562.7 528 546.3C528 447.8 448.2 368 349.7 368L290.3 368z"/></svg>
    </header>

    <main>
        <section class="area-lado">
            <a class="area-lado-logo" href="agendacoord.php"><img src="../image/logotipo fundo azul.png" alt=""></a>
            <div class="area-menu">
                <div class="menu-agenda"><img src="../image/icones/agenda.png" alt=""><a href="agendacoord.php"><p>Agenda</p></a></div>
                <div class="menu-meus-eventos"><img src="../image/icones/eventos.png" alt=""><a href="eventoscoord.php"><p>Eventos</p></a></div>
                <div class="menu-professores ativo"><img src="../image/icones/professores.png" alt=""><a href="professores.php"><p>Professores</p></a></div> 
                <div class="menu-turmas"><img src="../image/icones/turmas.png" alt=""><a href="turmas.php"><p>Turmas</p></a></div> 
                <div class="menu-perfil"><img src="../image/icones/perfil.png" alt=""><a href="perfilcoord.php"><p>Perfil</p></a></div>
                <a href="../logout.php"><div class="menu-sair"><p>SAIR</p></div></a> 
            </div>
        </section>

        <div class="conteudo-principal">
            <section class="formulario-evento">
                <form action="addprofessor.php" method="POST">
                    <h2>Adicionar Professor</h2>

                    <?php if (!empty($mensagem)): ?>
                        <div class="mensagem <?php echo $tipo_mensagem; ?>"><?php echo $mensagem; ?></div>
                    <?php endif; ?>

                    <div class="linha-form">
                        <div class="campo">
                            <label for="nome">Nome do Professor:</label>
                            <input type="text" id="nome" name="nome" placeholder="Nome Sobrenome" required>
                        </div>
                        <div class="campo">
                            <label for="rm">RM (apenas números):</label>
                            <input type="text" id="rm" name="rm" placeholder="RM do professor" 
                                   inputmode="numeric" pattern="[0-9]*" required>
                        </div>
                    </div>
                    <div class="linha-form">
                        <div class="campo">
                            <label for="email">Email:</label>
                            <input type="email" id="email" name="email" placeholder="professor@email.com" required>
                        </div>
                        <div class="campo">
                            <label for="telefone">Telefone (apenas números):</label>
                            <input type="tel" id="telefone" name="telefone" placeholder="(Opcional)" 
                                   inputmode="numeric" pattern="[0-9]*" maxlength="15">
                        </div>
                    </div>
                    <div class="linha-form">
                        <div class="campo">
                            <label for="senha">Senha Inicial:</label>
                            <input type="password" id="senha" name="senha" placeholder="Senha provisória" required>
                        </div>
                        <div class="campo">
                            <label for="confirma_senha">Confirmar Senha:</label>
                            <input type="password" id="confirma_senha" name="confirma_senha" placeholder="Repita a senha" required>
                        </div>
                    </div>
                    <div class="linha-form">
                         <div class="campo" style="width: 100%;">
                            <label for="selecao-turmas">Associar Turmas (Opcional)</label>
                            <select id="selecao-turmas" name="turmas[]" multiple>
                                <?php foreach ($lista_turmas as $turma): ?>
                                    <option value="<?php echo $turma['cd_turma']; ?>"><?php echo htmlspecialchars($turma['nm_turma']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="botoes">
                        <a href="professores.php" class="botao-cancelar">Cancelar</a>
                        <button type="submit" class="botao-enviar">Adicionar Professor</button>
                    </div>
                </form>
            </section>
        </div>
    </main>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const turmasElement = document.getElementById('selecao-turmas');
            if(turmasElement) {
                const choices = new Choices(turmasElement, {
                    removeItemButton: true,
                    placeholder: true,
                    placeholderValue: 'Clique para selecionar as turmas...',
                    searchEnabled: true
                });
            }
        });
    </script>
</body>
</html>