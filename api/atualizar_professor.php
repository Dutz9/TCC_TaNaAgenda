<?php


require_once 'config.php';
require_once 'verifica_sessao.php'; 

header('Content-Type: application/json');

$tipo_usuario_logado = $usuario_logado['tipo_usuario_ic_usuario'];
if ($tipo_usuario_logado !== 'Coordenador' && $tipo_usuario_logado !== 'Administrador') {
    http_response_code(403);
    echo json_encode(['status' => 'erro', 'mensagem' => 'Acesso negado. Apenas Coordenadores ou Administradores.']);
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
    $turmas = $_POST['turmas'] ?? []; 

    if (empty($cd_usuario) || empty($nome) || empty($email)) {
        http_response_code(400); 
        echo json_encode(['status' => 'erro', 'mensagem' => 'Dados incompletos (RM, Nome e Email são obrigatórios).']);
        exit();
    }

    $usuarioController = new UsuarioController();

    $usuarioController->atualizarProfessor($cd_usuario, $nome, $email, $telefone, $turmas);


    echo json_encode(['status' => 'sucesso', 'mensagem' => 'Professor atualizado com sucesso!']);

} catch (Exception $e) {

    http_response_code(500); 
    
    $mensagemErro = $e->getMessage();
    if (strpos($mensagemErro, 'Este e-mail já está em uso') !== false) {
        $mensagemErro = 'Erro: Este e-mail já está em uso por outro usuário.';
    } elseif (strpos($mensagemErro, 'Duplicate entry') !== false) {
         $mensagemErro = 'Erro: Este e-mail já está em uso.';
    }

    echo json_encode(['status' => 'erro', 'mensagem' => $mensagemErro]);
}
?>