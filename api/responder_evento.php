<?php

  // Inicia o ambiente, carrega as classes e o mais importante:
  // verifica se o usuário está logado, protegendo nosso endpoint.
  require_once 'config.php';
  require_once 'verifica_sessao.php';

  // Define que a resposta será no formato JSON
  header('Content-Type: application/json');

  // Garante que a requisição seja do tipo POST para segurança
  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      http_response_code(405); // Método não permitido
      echo json_encode(['status' => 'erro', 'mensagem' => 'Método não permitido.']);
      exit();
  }

  try {
      // Pega os dados enviados pelo JavaScript
      $cd_evento = $_POST['cd_evento'] ?? null;
      $resposta = $_POST['resposta'] ?? null; // 'Aprovado' ou 'Recusado'

      // Validação básica dos dados
      if (empty($cd_evento) || empty($resposta)) {
          http_response_code(400); // Requisição inválida
          echo json_encode(['status' => 'erro', 'mensagem' => 'Dados incompletos.']);
          exit();
      }
      
      // Pega o código do usuário que está logado na sessão
      $cd_usuario = $usuario_logado['cd_usuario'];

      // Usa o controller para salvar a resposta no banco
      $eventoController = new EventoController();
      $eventoController->registrarRespostaProfessor($cd_evento, $cd_usuario, $resposta);

      // Se tudo deu certo, envia uma resposta de sucesso
      echo json_encode(['status' => 'sucesso', 'mensagem' => 'Resposta registrada com sucesso!']);

  } 

  catch (Exception $e) {
      // Se qualquer erro acontecer, captura e envia uma resposta de erro
      http_response_code(500); // Erro interno do servidor
      echo json_encode(['status' => 'erro', 'mensagem' => $e->getMessage()]);
  }
?>