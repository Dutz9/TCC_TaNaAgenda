<?php

// Garante que a sessão seja iniciada apenas uma vez.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 1. VERIFICA SE O USUÁRIO ESTÁ LOGADO
// Se a variável de sessão 'usuario' não existir, significa que ele não fez login.
if (!isset($_SESSION['usuario'])) {
    
    // Cria uma mensagem de erro para mostrar na tela de login.
    $_SESSION['mensagem_erro'] = 'Acesso negado! Por favor, faça o login.';
    
    // 2. EXPULSA O USUÁRIO
    // Redireciona o usuário de volta para a tela de login.
    // O '../' é para voltar da pasta 'tela_prof' ou 'tela_coord' para a raiz do projeto.
    header('Location: ../login.php');
    exit(); // Encerra o script para garantir que nada mais seja executado.
}

// 3. DISPONIBILIZA OS DADOS DO USUÁRIO
// Se o usuário está logado, criamos uma variável fácil de usar com seus dados.
$usuario_logado = $_SESSION['usuario'];

?>