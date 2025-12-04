<?php 
    require_once '../api/config.php'; 
    require_once '../api/verifica_sessao.php'; 


    if ($usuario_logado['tipo_usuario_ic_usuario'] !== 'Administrador') {
        header('Location: ../tela_prof/agendaprof.php');
        exit();
    }

    $mensagem = '';
    $tipo_mensagem = '';
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $senha_atual = $_POST['senha_atual'] ?? null;
        $senha_nova = $_POST['senha_nova'] ?? null;
        $senha_confirma = $_POST['senha_confirma'] ?? null;

        try {
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
            $usuarioController = new UsuarioController();
            $usuarioController->mudarSenha($usuario_logado['cd_usuario'], $senha_atual, $senha_nova);
            
            $mensagem = "Senha alterada com sucesso!";
            $tipo_mensagem = 'sucesso';

        } catch (Exception $e) {
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
    <title>Segurança (ADM.) - TáNaAgenda</title>
    <link id="favicon" rel="shortcut icon" href="../image/Favicon-light.png">
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/perfil.css">
    <link rel="stylesheet" href="../css/seguranca.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
</head>
<body>
<script src="../js/favicon.js"></script>
    <header class="header">
        <a href="perfiladm.php">
            <p><?php echo htmlspecialchars($usuario_logado['nm_usuario']); ?></p>
        </a>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path fill="#ffffff" d="M320 312C386.3 312 440 258.3 440 192C440 125.7 386.3 72 320 72C253.7 72 200 125.7 200 192C200 258.3 253.7 312 320 312zM290.3 368C191.8 368 112 447.8 112 546.3C112 562.7 125.3 576 141.7 576L498.3 576C514.7 576 528 562.7 528 546.3C528 447.8 448.2 368 349.7 368L290.3 368z"/></svg>
    </header>

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
                
                <div class="menu-cursos">
                <img src="../image/icones/cursos.png" alt="">
                    <a href="cursos.php"><p>Cursos</p></a>
                </div> 
                <div class="menu-turmas">
                <img src="../image/icones/turmas.png" alt="">
                    <a href="turmas.php"><p>Turmas</p></a>
                </div>
                <div class="menu-perfil ativo">
                <img src="../image/icones/perfil.png" alt="">
                    <a href="perfiladm.php"><p>Perfil</p></a>
                </div>  
                <a href="../login.php"><div class="menu-sair"><p>SAIR</p></div></a> 
            </div>
        </section>

    <section class="area-perfil">
        <section class="perfil-container">
            <div class="lado-esquerdo">
                <div class="perfil-info">
                    <img src="../image/icone perfil.svg" alt="Foto de Perfil">
                    <div class="perfil-informacoes">
                        <h3><?php echo htmlspecialchars($usuario_logado['nm_usuario']); ?></h3>
                        <p>Administrador</p>
                    </div>
                </div>
                <section class="perfil-menu">
                    <a href="perfiladm.php">
                        <div class="informacoes-menu"> <img src="../image/Icones/informacoes.png" alt=""> <p>Informações Pessoais</p>
                        </div>
                    </a>
                    <a href="segurancaadm.php">
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

                <form class="informacoes-pessoais" method="POST" action="segurancaadm.php">
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