<?php
// api/excluir_curso.php

require_once 'config.php';
require_once 'verifica_sessao.php';

header('Content-Type: application/json');

// 1. VERIFICAÇÃO DE PERMISSÃO
if ($usuario_logado['tipo_usuario_ic_usuario'] !== 'Administrador') {
    http_response_code(403);
    echo json_encode(['status' => 'erro', 'mensagem' => 'Acesso negado. Apenas administradores.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'erro', 'mensagem' => 'Método não permitido.']);
    exit();
}

try {
    // 2. CAPTURA DOS DADOS
    $cd_curso = $_POST['cd_curso'] ?? null;

    if (empty($cd_curso)) {
        http_response_code(400);
        echo json_encode(['status' => 'erro', 'mensagem' => 'ID do curso não fornecido.']);
        exit();
    }

    // 3. EXECUÇÃO DA LÓGICA
    $cursoController = new CursoController();
    $cursoController->excluirCurso($cd_curso);

    // 4. RESPOSTA DE SUCESSO
    echo json_encode(['status' => 'sucesso', 'mensagem' => 'Curso excluído com sucesso!']);

} catch (Exception $e) {
    // 5. RESPOSTA DE ERRO (Trata erro de curso com turmas vinculadas vindo da SP)
    http_response_code(500);
    
    $mensagemErro = $e->getMessage();
    if (strpos($mensagemErro, 'Erro: Não é possível excluir o curso pois ele possui turmas vinculadas.') !== false) {
        $mensagemErro = 'Erro: Não é possível excluir o curso pois ele possui turmas vinculadas. Exclua as turmas primeiro.';
    }

    echo json_encode(['status' => 'erro', 'mensagem' => $mensagemErro]);
}
?>