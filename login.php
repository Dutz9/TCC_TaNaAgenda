<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - TáNaAgenda</title>
    <link rel="shortcut icon" href="image/Favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="css/login.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
</head>
<body>
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
 
        <form action="login.php" method="get" id="form_login">
            <h2>
                RM:
            </h2>
            <input type="text" name="Rm" id="rm" placeholder="RM">
           <h2> Senha:</h2>
            <input type="text" name="Senha" placeholder="SENHA">
            <div class="entrar">
                <a href="tela_prof/meuseventos.php"><button type="button" class="botao-entrar">Entrar</button></a>
                </div>
        </form>
        <h3>
           <a href="esqueciMinhaSenha.php"> Esqueci minha senha</a>
        </h3>
     </div>
</body>
</html>