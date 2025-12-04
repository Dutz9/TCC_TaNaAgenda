<?php

  require_once 'config.php';
  require_once 'verifica_sessao.php';

  header('Content-Type: application/json');

  $tipo_usuario_logado = $usuario_logado['tipo_usuario_ic_usuario'];
  if ($tipo_usuario_logado !== 'Coordenador' && $tipo_usuario_logado !== 'Administrador') {
      http_response_code(403);
      echo json_encode(['status' => 'erro', 'mensagem' => 'Acesso negado. Apenas Coordenadores ou Administradores podem excluir turmas.']);
      exit();
  }

  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      http_response_code(405);
      echo json_encode(['status' => 'erro', 'mensagem' => 'Método não permitido.']);
      exit();
  }

  try {
      $cd_turma = $_POST['cd_turma'] ?? null;

      if (empty($cd_turma)) {
          http_response_code(400);
          echo json_encode(['status' => 'erro', 'mensagem' => 'ID da turma não fornecido.']);
          exit();
      }

      $turmaController = new TurmaController();
      $turmaController->excluirTurma($cd_turma);

      echo json_encode(['status' => 'sucesso', 'mensagem' => 'Turma excluída com sucesso!']);

  } catch (Exception $e) {
      http_response_code(500);
      
      $mensagemErro = $e->getMessage();

      
      echo json_encode(['status' => 'erro', 'mensagem' => $mensagemErro]);
  }
?>