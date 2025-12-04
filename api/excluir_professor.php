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

      if (empty($cd_usuario)) {
          http_response_code(400); 
          echo json_encode(['status' => 'erro', 'mensagem' => 'RM do professor não fornecido.']);
          exit();
      }

      $usuarioController = new UsuarioController();
      $usuarioController->excluirProfessor($cd_usuario);

      echo json_encode(['status' => 'sucesso', 'mensagem' => 'Professor excluído com sucesso!']);

  } catch (Exception $e) {

      http_response_code(500);
      
      $mensagemErro = $e->getMessage();
  
      if (strpos($mensagemErro, 'Cannot delete or update a parent row') !== false) {
          $mensagemErro = 'Erro: Não é possível excluir este professor pois ele é o solicitante de um ou mais eventos. Cancele os eventos dele primeiro.';
      }

      echo json_encode(['status' => 'erro', 'mensagem' => $mensagemErro]);
  }
?>