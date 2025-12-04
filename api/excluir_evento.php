<?php
    // api/excluir_evento.php

    // 1. CONFIGURAÇÃO E SEGURANÇA
    require_once 'config.php';
    require_once 'verifica_sessao.php'; // Garante que o usuário está logado

    // Define a resposta como JSON
    header('Content-Type: application/json');

    // 2. VERIFICAÇÃO DE PERMISSÃO (CORRIGIDA)
    // Permite Coordenador OU Administrador
    $tipo_usuario = $usuario_logado['tipo_usuario_ic_usuario'];
    
    if ($tipo_usuario !== 'Coordenador' && $tipo_usuario !== 'Administrador') {
        http_response_code(403); // Proibido (Forbidden)
        echo json_encode(['status' => 'erro', 'mensagem' => 'Acesso negado. Apenas coordenadores ou administradores podem excluir eventos.']);
        exit();
    }

    // 3. VALIDAÇÃO DO MÉTODO DE REQUISIÇÃO
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405); // Método não permitido
        echo json_encode(['status' => 'erro', 'mensagem' => 'Método não permitido.']);
        exit();
    }

    try {
        // 4. CAPTURA DOS DADOS
        $cd_evento = $_POST['cd_evento'] ?? null;

        if (empty($cd_evento)) {
            http_response_code(400); // Requisição inválida
            echo json_encode(['status' => 'erro', 'mensagem' => 'ID do evento não fornecido.']);
            exit();
        }

        // 5. EXECUÇÃO DA LÓGICA
        $eventoController = new EventoController();
        $eventoController->excluirDefinitivo($cd_evento);

        // 6. RESPOSTA DE SUCESSO
        echo json_encode(['status' => 'sucesso', 'mensagem' => 'Evento excluído com sucesso!']);

    } catch (Exception $e) {
        // 7. RESPOSTA DE ERRO
        http_response_code(500); // Erro interno do servidor
        echo json_encode(['status' => 'erro', 'mensagem' => $e->getMessage()]);
    }
?>