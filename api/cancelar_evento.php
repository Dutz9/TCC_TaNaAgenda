<?php
    // api/cancelar_evento.php

    // 1. CONFIGURAÇÃO E SEGURANÇA
    require_once 'config.php';
    require_once 'verifica_sessao.php'; // Garante que o usuário está logado

    // Define a resposta como JSON
    header('Content-Type: application/json');

    // 2. VALIDAÇÃO DO MÉTODO DE REQUISIÇÃO
    // Apenas requisições POST são permitidas para segurança
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405); // Método não permitido
        echo json_encode(['status' => 'erro', 'mensagem' => 'Método não permitido.']);
        exit();
    }

    try {
        // 3. CAPTURA DOS DADOS
        $cd_evento = $_POST['cd_evento'] ?? null;
        $cd_usuario_logado = $usuario_logado['cd_usuario']; // Pega o usuário da sessão

        if (empty($cd_evento)) {
            http_response_code(400); // Requisição inválida
            echo json_encode(['status' => 'erro', 'mensagem' => 'ID do evento não fornecido.']);
            exit();
        }

        // 4. EXECUÇÃO DA LÓGICA
        $eventoController = new EventoController();
        // Chama o método que criamos, passando o ID do evento E o ID do usuário
        // A Stored Procedure no banco vai garantir que ele só apague se for o dono
        $eventoController->cancelarSolicitacao($cd_evento, $cd_usuario_logado);

        // 5. RESPOSTA DE SUCESSO
        echo json_encode(['status' => 'sucesso', 'mensagem' => 'Solicitação de evento cancelada com sucesso!']);

    } catch (Exception $e) {
        // 6. RESPOSTA DE ERRO
        http_response_code(500); // Erro interno do servidor
        echo json_encode(['status' => 'erro', 'mensagem' => $e->getMessage()]);
    }
?>