<?php
 


    require_once 'config.php';
    require_once 'verifica_sessao.php';


    header('Content-Type: application/json');

    $tipo_usuario = $usuario_logado['tipo_usuario_ic_usuario'];
    
    if ($tipo_usuario !== 'Coordenador' && $tipo_usuario !== 'Administrador') {
        http_response_code(403);
        echo json_encode(['status' => 'erro', 'mensagem' => 'Acesso negado. Apenas coordenadores ou administradores podem excluir eventos.']);
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['status' => 'erro', 'mensagem' => 'Método não permitido.']);
        exit();
    }

    try {

        $cd_evento = $_POST['cd_evento'] ?? null;

        if (empty($cd_evento)) {
            http_response_code(400);
            echo json_encode(['status' => 'erro', 'mensagem' => 'ID do evento não fornecido.']);
            exit();
        }

        $eventoController = new EventoController();
        $eventoController->excluirDefinitivo($cd_evento);

        echo json_encode(['status' => 'sucesso', 'mensagem' => 'Evento excluído com sucesso!']);

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['status' => 'erro', 'mensagem' => $e->getMessage()]);
    }
?>