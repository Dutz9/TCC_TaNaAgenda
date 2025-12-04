<?php


require_once 'config.php';
require_once 'verifica_sessao.php';

header('Content-Type: application/json');

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

    $cd_curso = $_POST['cd_curso'] ?? null;
    $nm_curso = $_POST['nm_curso'] ?? null;
    $ic_periodo = $_POST['ic_periodo'] ?? null;

    if (empty($cd_curso) || empty($nm_curso) || empty($ic_periodo)) {
        http_response_code(400);
        echo json_encode(['status' => 'erro', 'mensagem' => 'Dados incompletos.']);
        exit();
    }
    
    $dadosCurso = [
        'cd_curso' => $cd_curso,
        'nm_curso' => $nm_curso,
        'ic_periodo' => $ic_periodo
    ];

    $cursoController = new CursoController();
    $cursoController->atualizarCurso($dadosCurso);


    echo json_encode(['status' => 'sucesso', 'mensagem' => 'Curso atualizado com sucesso!']);

} catch (Exception $e) {

    http_response_code(500); 
    
    $mensagemErro = $e->getMessage();
    if (strpos($mensagemErro, 'Erro: O nome deste curso já está em uso.') !== false) {
        $mensagemErro = 'Erro: O nome deste curso já está em uso.';
    }

    echo json_encode(['status' => 'erro', 'mensagem' => $mensagemErro]);
}
?>