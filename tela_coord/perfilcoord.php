<?php 
    require_once '../api/config.php'; 
    require_once '../api/verifica_sessao.php'; 

    // Garante que apenas coordenadores acessem esta página
    if ($usuario_logado['tipo_usuario_ic_usuario'] !== 'Coordenador') {
        header('Location: ../tela_prof/agendaprof.php');
        exit();
    }

    $usuarioController = new UsuarioController();
    $mensagem = '';
    $tipo_mensagem = '';

    // --- PROCESSAR ATUALIZAÇÃO (SE FOR POST) ---
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        try {
            $nome = $_POST['nome'];
            $telefone = $_POST['telefone'];
            
            if (empty($nome)) {
                throw new Exception("O nome não pode ficar em branco.");
            }
            
            // Atualiza no banco
            $usuarioController->atualizarDados($usuario_logado['cd_usuario'], $nome, $telefone);
            
            // Atualiza a sessão para refletir o novo nome imediatamente
            $_SESSION['usuario_logado']['nm_usuario'] = $nome;
            $usuario_logado['nm_usuario'] = $nome; // Atualiza a variável local também
            
            $mensagem = "Dados atualizados com sucesso!";
            $tipo_mensagem = 'sucesso';
            
        } catch (Exception $e) {
            $mensagem = "Erro ao atualizar: " . $e->getMessage();
            $tipo_mensagem = 'erro';
        }
    }

    // --- BUSCAR DADOS ATUAIS PARA EXIBIR ---
    $dados_usuario = $usuarioController->buscarDadosUsuario($usuario_logado['cd_usuario']);
    if (!$dados_usuario) {
        $dados_usuario = $usuario_logado;
        $dados_usuario['cd_telefone'] = 'Não encontrado';
    }
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil (Coord.) - TáNaAgenda</title>
    <link id="favicon" rel="shortcut icon" href="../image/Favicon-light.png">
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/perfil.css"> </head>
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
                        <p>Coordenador</p> </div>
                </div>
                <section class="perfil-menu">
                    <a href="perfilcoord.php">
                        <div class="informacoes-menu ativo"> <img src="../image/Icones/informacoes.png" alt=""> <p>Informações Pessoais</p>
                        </div>
                    </a>
                    <a href="segurancacoord.php"> <div class="seguranca-menu">
                           <img src="../image/Icones/seguranca.png" alt=""> <p>Segurança</p>
                        </div>
                    </a>
                </section>
            </div>
            
            <div class="lado-direito">
                <h1>Informações Pessoais</h1>

                <?php if (!empty($mensagem)): ?>
                    <div class="mensagem <?php echo $tipo_mensagem; ?>"><?php echo $mensagem; ?></div>
                <?php endif; ?>

                <form class="informacoes-pessoais" method="POST" action="perfilcoord.php"> <div class="infos" id="nome">
                        <label for="nome">Nome Completo:</label>
                        <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($dados_usuario['nm_usuario']); ?>">
                    </div>
                    <div class="infos" id="email">
                        <label for="email">E-mail:</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($dados_usuario['nm_email']); ?>" readonly>
                    </div>
                    <div class="infos" id="telefone">
                        <label for="telefone">Telefone:</label>
                        <input type="tel" id="telefone" name="telefone" value="<?php echo htmlspecialchars($dados_usuario['cd_telefone']); ?>">
                    </div>
                    <div class="infos" id="rm">
                        <label for="rm">RM:</label>
                        <input type="text" id="rm" name="rm" value="<?php echo htmlspecialchars($dados_usuario['cd_usuario']); ?>" readonly>
                    </div>
                    <div class="infos" id="Salvar">
                        <button type="submit" class="btn-salvar">Salvar Alterações</button>
                    </div>
                </form>
            </div>
        </section>
    </section>
</body>
</html>