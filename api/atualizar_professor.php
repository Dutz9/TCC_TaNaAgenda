<?php
// api/atualizar_professor.php

require_once 'config.php';
require_once 'verifica_sessao.php'; // Garante que o usuário está logado

header('Content-Type: application/json');

// 1. VERIFICAÇÃO DE PERMISSÃO (SÓ COORDENADOR PODE ATUALIZAR)
if ($usuario_logado['tipo_usuario_ic_usuario'] !== 'Coordenador') {
    http_response_code(403); // Proibido
    echo json_encode(['status' => 'erro', 'mensagem' => 'Acesso negado.']);
    exit();
}

// 2. VALIDAÇÃO DO MÉTODO
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Método não permitido
    echo json_encode(['status' => 'erro', 'mensagem' => 'Método não permitido.']);
    exit();
}

try {
    // 3. CAPTURA DOS DADOS (AGORA INCLUINDO AS TURMAS)
    $cd_usuario = $_POST['cd_usuario'] ?? null;
    $nome = $_POST['nome'] ?? null;
    $email = $_POST['email'] ?? null;
    $telefone = $_POST['telefone'] ?? null;
    $turmas = $_POST['turmas'] ?? []; // <-- NOSSA NOVA VARIÁVEL

    if (empty($cd_usuario) || empty($nome) || empty($email)) {
        http_response_code(400); // Requisição inválida
        echo json_encode(['status' => 'erro', 'mensagem' => 'Dados incompletos (RM, Nome e Email são obrigatórios).']);
        exit();
    }

    // 4. EXECUÇÃO DA LÓGICA
    $usuarioController = new UsuarioController();
    // Agora passa a lista de turmas para o controller
    $usuarioController->atualizarProfessor($cd_usuario, $nome, $email, $telefone, $turmas);

    // 5. RESPOSTA DE SUCESSO
    echo json_encode(['status' => 'sucesso', 'mensagem' => 'Professor atualizado com sucesso!']);

} catch (Exception $e) {
    // 6. RESPOSTA DE ERRO (ex: "Email já em uso")
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