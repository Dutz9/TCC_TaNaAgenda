<?php
  // api/excluir_professor.php

  require_once 'config.php';
  require_once 'verifica_sessao.php'; // Garante que o usuário está logado

  header('Content-Type: application/json');

  // 1. VERIFICAÇÃO DE PERMISSÃO
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
      // 3. CAPTURA DOS DADOS
      $cd_usuario = $_POST['cd_usuario'] ?? null;

      if (empty($cd_usuario)) {
          http_response_code(400); // Requisição inválida
          echo json_encode(['status' => 'erro', 'mensagem' => 'RM do professor não fornecido.']);
          exit();
      }

      // 4. EXECUÇÃO DA LÓGICA
      $usuarioController = new UsuarioController();
      $usuarioController->excluirProfessor($cd_usuario);

      // 5. RESPOSTA DE SUCESSO
      echo json_encode(['status' => 'sucesso', 'mensagem' => 'Professor excluído com sucesso!']);

  } catch (Exception $e) {
      // 6. RESPOSTA DE ERRO
      http_response_code(500);
      
      $mensagemErro = $e->getMessage();
      // "Traduz" o erro de chave estrangeira
      if (strpos($mensagemErro, 'Cannot delete or update a parent row') !== false) {
          $mensagemErro = 'Erro: Não é possível excluir este professor pois ele é o solicitante de um ou mais eventos. Cancele os eventos dele primeiro.';
      }

      echo json_encode(['status' => 'erro', 'mensagem' => $mensagemErro]);
  }
?>