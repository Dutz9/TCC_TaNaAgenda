<?php
  require_once 'config.php';
  require_once 'verifica_sessao.php';

  header('Content-Type: application/json');

  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      http_response_code(405);
      echo json_encode(['status' => 'erro', 'mensagem' => 'Método não permitido.']);
      exit();
  }

  try {
      $cd_evento = $_POST['cd_evento'] ?? null;
      $resposta = $_POST['resposta'] ?? null;
      $motivo = $_POST['motivo'] ?? null;

      if (empty($cd_evento) || empty($resposta)) {
          http_response_code(400);
          echo json_encode(['status' => 'erro', 'mensagem' => 'Dados incompletos.']);
          exit();
      }

      if ($resposta === 'Recusado' && (empty($motivo) || trim($motivo) === '')) {
          http_response_code(400);
          echo json_encode(['status' => 'erro', 'mensagem' => 'O motivo da recusa é obrigatório.']);
          exit();
      }
      
      $cd_usuario = $usuario_logado['cd_usuario'];

      $eventoController = new EventoController();
      $eventoController->registrarRespostaProfessor($cd_evento, $cd_usuario, $resposta, $motivo);

      echo json_encode(['status' => 'sucesso', 'mensagem' => 'Resposta registrada com sucesso!']);

  } catch (Exception $e) {
      http_response_code(500);
      echo json_encode(['status' => 'erro', 'mensagem' => $e->getMessage()]);
  }
?>