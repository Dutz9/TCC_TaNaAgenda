<?php

    require_once 'config.php';
    require_once 'verifica_sessao.php';


    header('Content-Type: application/json');


    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405); 
        echo json_encode(['status' => 'erro', 'mensagem' => 'Método não permitido.']);
        exit();
    }

    try {

        $cd_evento = $_POST['cd_evento'] ?? null;
        $cd_usuario_logado = $usuario_logado['cd_usuario'];

        if (empty($cd_evento)) {
            http_response_code(400);
            echo json_encode(['status' => 'erro', 'mensagem' => 'ID do evento não fornecido.']);
            exit();
        }


        $eventoController = new EventoController();
        $eventoController->cancelarSolicitacao($cd_evento, $cd_usuario_logado);


        echo json_encode(['status' => 'sucesso', 'mensagem' => 'Solicitação de evento cancelada com sucesso!']);

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['status' => 'erro', 'mensagem' => $e->getMessage()]);
    }
?>