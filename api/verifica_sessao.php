<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario'])) {
    
    $_SESSION['mensagem_erro'] = 'Acesso negado! Por favor, faça o login.';
    
    header('Location: ../login.php');
    exit();
}

$usuario_logado = $_SESSION['usuario'];

?>