<?php


require_once 'config.php';
require_once 'verifica_sessao.php'; 

header('Content-Type: application/json');

if ($usuario_logado['tipo_usuario_ic_usuario'] !== 'Administrador') {
    http_response_code(403); 
    echo json_encode(['status' => 'erro', 'mensagem' => 'Acesso negado. Apenas Administradores.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); 
    echo json_encode(['status' => 'erro', 'mensagem' => 'Método não permitido.']);
    exit();
}

try {

    $cd_usuario = $_POST['cd_usuario'] ?? null;
    $nome = $_POST['nome'] ?? null;
    $email = $_POST['email'] ?? null;
    $telefone = $_POST['telefone'] ?? null;
    $cursos = $_POST['cursos'] ?? [];

    if (empty($cd_usuario) || empty($nome) || empty($email)) {
        http_response_code(400); 
        echo json_encode(['status' => 'erro', 'mensagem' => 'Dados incompletos (RM, Nome e Email são obrigatórios).']);
        exit();
    }

    $usuarioController = new UsuarioController();
    $usuarioController->atualizarCoordenador($cd_usuario, $nome, $email, $telefone, $cursos);


    echo json_encode(['status' => 'sucesso', 'mensagem' => 'Coordenador atualizado com sucesso!']);

} catch (Exception $e) {

    http_response_code(500); 
    
    $mensagemErro = $e->getMessage();
    if (strpos($mensagemErro, 'Este e-mail já está em uso') !== false) {
        $mensagemErro = 'Erro: Este e-mail já está em uso por outro usuário.';
    } 

    echo json_encode(['status' => 'erro', 'mensagem' => $mensagemErro]);
}
?>