<?php
// Inclui o arquivo de configuração para carregar as classes e iniciar a sessão
require_once 'config_local.php';
 
// Inicia a sessão. Essencial para manter o usuário logado.
session_start();
 
// Variável para guardar a mensagem de erro, se houver
$mensagemErro = '';
 
// Verifica se o formulário foi enviado (se a requisição é do tipo POST)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
 
    // 1. Pega os dados do formulário de forma segura
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $senha = isset($_POST['senha']) ? trim($_POST['senha']) : '';
 
    // Validação simples para ver se os campos não estão vazios
    if (!empty($email) && !empty($senha)) {
        try {
            // 2. Cria uma instância do nosso controlador
            $controller = new UsuarioController();
 
            // 3. Chama o método para verificar o acesso
            $dadosUsuario = $controller->VerificarAcesso($email, $senha);
 
            // 4. Se o login for bem-sucedido, $dadosUsuario conterá os dados
            if ($dadosUsuario && count($dadosUsuario) > 0) {
                
                // Guarda os dados do usuário na sessão
                $_SESSION['usuario'] = $dadosUsuario[0];
 
                // 5. Redireciona com base no tipo de usuário
                $tipoUsuario = $_SESSION['usuario']['tipo_usuario_ic_usuario'];
 
                if ($tipoUsuario == 'Professor') {
                    header('Location: tela_prof/agendaprof.php');
                    exit(); // Encerra o script após o redirecionamento
                } 
                
                elseif ($tipoUsuario == 'Coordenador') {
                    header('Location: tela_coord/agendacoord.php');
                    exit(); // Encerra o script após o redirecionamento
                } 

                elseif ($tipoUsuario == 'Administrador') {
                    header('Location: tela_adm/agendaadm.php');
                    exit(); // Encerra o script após o redirecionamento
                } 
                
                else {
                    // Caso seja outro tipo de usuário (ex: Administrador)
                    // Por enquanto, podemos apenas mostrar um erro ou redirecionar para uma página padrão.
                    $mensagemErro = 'Tipo de usuário não tem uma página de destino.';
                }
 
            } else {
                // Isso não deveria acontecer se a procedure lança exceção, mas é uma segurança extra.
                $mensagemErro = 'Login e/ou Senha Inválida.';
            }
 
        } catch (Exception $e) {
            // Se a Stored Procedure retornar erro (login/senha inválidos), o catch vai pegar a mensagem
            $mensagemErro = $e->getMessage();
        }
    } 
    
    else {
        $mensagemErro = 'Por favor, preencha todos os campos.';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - TáNaAgenda</title>
    <link id="favicon" rel="shortcut icon" href="image/Favicon-light.png">
    <link rel="stylesheet" href="css/login.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
</head>
<body>
    <script src="js/favicon.js"></script>
    <div class="login">
        <div class="parteDeCima">
            <div id="Primeira_Parte" class="PartesDeCima">
                <a href="index.php">
                    <img class="seta" src="image/Seta.svg" alt="seta para voltar">
                </a>
            </div>
            <div id="Segunda_Parte" class="PartesDeCima">
                <img class="image" src="image/logotipo fundo branco.png" alt="Logotipo">
            </div>
            <div id="Terceira_Parte" class="PartesDeCima">
            </div>
        </div>
 
        <h1>Login</h1>
 
        <form action="login.php" method="POST" id="form_login">
            
            <?php if (!empty($mensagemErro)): ?>
                <div class="mensagem-erro"><?php echo htmlspecialchars($mensagemErro); ?></div>
            <?php endif; ?>
 
            <h2>E-mail ou RM:</h2>
            <input type="text" name="email" id="email" placeholder="Digite seu e-mail ou RM" required>
            
            <h2>Senha:</h2>
            <input type="password" name="senha" placeholder="Senha" required>
            
            <div class="entrar">
                <button type="submit" class="botao-entrar">Entrar</button>
            </div>
        </form>
        
        <h3>
            <a href="esqueciMinhaSenha.php">Esqueci minha senha</a>
        </h3>
    </div>
    <div class="menu-overlay" id="menu-overlay"></div>
</body>
</html>