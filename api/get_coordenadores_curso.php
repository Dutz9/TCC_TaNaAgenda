<?php
// api/get_coordenadores_curso.php

require_once 'config.php';
require_once 'verifica_sessao.php';

header('Content-Type: application/json');

if ($usuario_logado['tipo_usuario_ic_usuario'] !== 'Administrador') {
    http_response_code(403);
    echo json_encode(['status' => 'erro', 'mensagem' => 'Acesso negado.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['status' => 'erro', 'mensagem' => 'Método não permitido.']);
    exit();
}

try {
    $cd_curso = $_GET['curso_id'] ?? null;
    
    // Simulação dos dados: Em uma implementação real, você buscaria em uma
    // tabela `cursos_has_coordenadores`
    $coordenadores_mock = [];

    if ($cd_curso == '2') { // Se for "Desenvolvimento de Sistemas"
        $coordenadores_mock = [
            ['nm_usuario' => 'André (0002)'],
            ['nm_usuario' => 'Karen Rodrigues (2001)']
        ];
    } else if ($cd_curso == '1') { // Se for "Automação Industrial"
        $coordenadores_mock = [
            ['nm_usuario' => 'Beatriz Lima (2011)']
        ];
    }
    
    echo json_encode($coordenadores_mock);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'erro', 'mensagem' => $e->getMessage()]);
}
?>