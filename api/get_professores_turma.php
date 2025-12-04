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

  if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
      http_response_code(405);
      echo json_encode(['status' => 'erro', 'mensagem' => 'Método não permitido.']);
      exit();
  }

  try {
      $cd_turma = $_GET['turma_id'] ?? null;

      if (empty($cd_turma)) {
          http_response_code(400);
          echo json_encode(['status' => 'erro', 'mensagem' => 'ID da turma não fornecido.']);
          exit();
      }

      $usuarioController = new UsuarioController();
      $lista_professores = $usuarioController->listarProfessoresPorTurma($cd_turma);

      echo json_encode($lista_professores);

  } catch (Exception $e) {
      http_response_code(500);
      echo json_encode(['status' => 'erro', 'mensagem' => $e->getMessage()]);
  }
?>