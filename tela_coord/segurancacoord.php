<?php 
    require_once '../api/config.php'; 
    require_once '../api/verifica_sessao.php'; 

    // Segurança extra: Garante que apenas coordenadores acessem
    if ($usuario_logado['tipo_usuario_ic_usuario'] !== 'Coordenador') {
        header('Location: ../tela_prof/agendaprof.php');
        exit();
    }

    $mensagem = '';
    $tipo_mensagem = '';

    // --- PROCESSAR MUDANÇA DE SENHA (SE FOR POST) ---
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $senha_atual = $_POST['senha_atual'] ?? null;
        $senha_nova = $_POST['senha_nova'] ?? null;
        $senha_confirma = $_POST['senha_confirma'] ?? null;

        try {
            // 1. Validações básicas no PHP
            if (empty($senha_atual) || empty($senha_nova) || empty($senha_confirma)) {
                throw new Exception("Todos os campos são obrigatórios.");
            }
            if ($senha_nova !== $senha_confirma) {
                throw new Exception("A 'Nova Senha' e a 'Confirmação' não são iguais.");
            }
            if ($senha_atual === $senha_nova) {
                throw new Exception("A nova senha não pode ser igual à senha atual.");
            }
            if (strlen($senha_nova) < 3) {
                throw new Exception("A nova senha deve ter pelo menos 3 caracteres.");
            }

            // 2. Tenta trocar a senha no banco
            $usuarioController = new UsuarioController();
            $usuarioController->mudarSenha($usuario_logado['cd_usuario'], $senha_atual, $senha_nova);
            
            $mensagem = "Senha alterada com sucesso!";
            $tipo_mensagem = 'sucesso';

        } catch (Exception $e) {
            // O "tradutor" de erros amigáveis
            $erro = $e->getMessage();
            
            if (strpos($erro, 'A senha atual está incorreta') !== false) {
                $mensagem = "A senha atual está incorreta.";
            } else {
                $mensagem = $erro;
            }
            $tipo_mensagem = 'erro';
        }
    }
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Segurança (Coord.) - TáNaAgenda</title>
    <link id="favicon" rel="shortcut icon" href="../image/Favicon-light.png">
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/perfil.css">
    <link rel="stylesheet" href="../css/seguranca.css">
</head>
<body>
<script src="../js/favicon.js"></script>
    <header class="header">
        <a href="perfilcoord.php">
            <p><?php echo htmlspecialchars($usuario_logado['nm_usuario']); ?></p>
        </a>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="#ffffff" d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512H418.3c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304H178.3z"/></svg>
    </header>

    <section class="area-lado">
        <a class="area-lado-logo" href="agendacoord.php"><img src="../image/logotipo fundo azul.png" alt=""></a>
        <div class="area-menu"> 
            <div class="menu-agenda"><img src="../image/icones/agenda.png" alt=""><a href="agendacoord.php"><p>Agenda</p></a></div>
            <div class="menu-meus-eventos"><img src="../image/icones/eventos.png" alt=""><a href="eventoscoord.php"><p>Eventos</p></a></div>
            <div class="menu-professores"><img src="../image/icones/professores.png" alt=""><a href="professores.php"><p>Professores</p></a></div> 
            <div class="menu-turmas"><img src="../image/icones/turmas.png" alt=""><a href="turmas.php"><p>Turmas</p></a></div> 
            <div class="menu-perfil ativo"><img src="../image/icones/perfil.png" alt=""><a href="perfilcoord.php"><p>Perfil</p></a></div> 
            <a href="../logout.php"><div class="menu-sair"><p>SAIR</p></div></a> 
        </div>
    </section>

    <section class="area-perfil">
        <section class="perfil-container">
            <div class="lado-esquerdo">
                <div class="perfil-info">
                    <img src="../image/icone perfil.svg" alt="Foto de Perfil">
                    <div class="perfil-informacoes">
                        <h3><?php echo htmlspecialchars($usuario_logado['nm_usuario']); ?></h3>
                        <p>Coordenador</p>
                    </div>
                </div>
                <section class="perfil-menu">
                    <a href="perfilcoord.php">
                        <div class="informacoes-menu"> <img src="../image/Icones/informacoes.png" alt=""> <p>Informações Pessoais</p>
                        </div>
                    </a>
                    <a href="segurancacoord.php">
                        <div class="seguranca-menu ativo"> <img src="../image/Icones/seguranca.png" alt=""> <p>Segurança</p>
                        </div>
                    </a>
                </section>
            </div>
            
            <div class="lado-direito">
                <h1>Alterar Senha</h1>
                
                <?php if (!empty($mensagem)): ?>
                    <div class="mensagem <?php echo $tipo_mensagem; ?>"><?php echo $mensagem; ?></div>
                <?php endif; ?>

                <form class="informacoes-pessoais" method="POST" action="segurancacoord.php">
                    <div class="infos">
                        <label for="senha_atual">Senha Atual:</label>
                        <input type="password" id="senha_atual" name="senha_atual" placeholder="Digite sua senha atual" required>
                    </div>
                    <div class="infos">
                        <label for="senha_nova">Nova Senha:</label>
                        <input type="password" id="senha_nova" name="senha_nova" placeholder="Digite a nova senha" required>
                    </div>
                    <div class="infos">
                        <label for="senha_confirma">Confirmar Nova Senha:</label>
                        <input type="password" id="senha_confirma" name="senha_confirma" placeholder="Confirme a nova senha" required>
                    </div>
                    <div class="infos" id="Alterar">
                        <button type="submit" class="btn-salvar">Alterar Senha</button>
                    </div>
                </form>
            </div> 
        </section>
    </section>
</body>
</html>