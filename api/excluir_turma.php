<?php
  // api/excluir_turma.php

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
      $cd_turma = $_POST['cd_turma'] ?? null;

      if (empty($cd_turma)) {
          http_response_code(400); // Requisição inválida
          echo json_encode(['status' => 'erro', 'mensagem' => 'ID da turma não fornecido.']);
          exit();
      }

      // 4. EXECUÇÃO DA LÓGICA
      $turmaController = new TurmaController();
      $turmaController->excluirTurma($cd_turma);

      // 5. RESPOSTA DE SUCESSO
      echo json_encode(['status' => 'sucesso', 'mensagem' => 'Turma excluída com sucesso!']);

  } catch (Exception $e) {
      // 6. RESPOSTA DE ERRO
      http_response_code(500);
      
      $mensagemErro = $e->getMessage();
      // (Não precisamos "traduzir" erros aqui, pois a procedure já apaga as associações)
      
      echo json_encode(['status' => 'erro', 'mensagem' => $mensagemErro]);
  }
?>