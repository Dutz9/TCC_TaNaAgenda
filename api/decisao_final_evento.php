<?php

  require_once 'config.php';
  require_once 'verifica_sessao.php';

  header('Content-Type: application/json');

  // --- CAMADA DE SEGURANÇA EXTRA ---
  // CHAVE: Garante que um coordenador OU Administrador pode executar esta ação
  $tipo_usuario_logado = $usuario_logado['tipo_usuario_ic_usuario'];
  if ($tipo_usuario_logado !== 'Coordenador' && $tipo_usuario_logado !== 'Administrador') {
      http_response_code(403); // Proibido
      echo json_encode(['status' => 'erro', 'mensagem' => 'Acesso negado. Apenas Coordenadores ou Administradores podem executar esta ação.']);
      exit();
  }

  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      http_response_code(405);
      echo json_encode(['status' => 'erro', 'mensagem' => 'Método não permitido.']);
      exit();
  }

    try {
        $cd_evento = $_POST['cd_evento'] ?? null;
        $decisao = $_POST['decisao'] ?? null; // 'Aprovado' ou 'Recusado'

        if (empty($cd_evento) || empty($decisao)) {
            http_response_code(400);
            echo json_encode(['status' => 'erro', 'mensagem' => 'Dados incompletos.']);
            exit();
        }
      
        // Pega o código do coordenador que está na sessão
        $cd_coordenador_logado = $usuario_logado['cd_usuario'];

        $eventoController = new EventoController();
        // Passa o código do coordenador como terceiro parâmetro
        $eventoController->darDecisaoFinal($cd_evento, $decisao, $cd_coordenador_logado);

        echo json_encode(['status' => 'sucesso', 'mensagem' => 'Decisão registrada com sucesso!']);

    } 

  catch (Exception $e) {
      http_response_code(500);
      echo json_encode(['status' => 'erro', 'mensagem' => $e->getMessage()]);
  }

?>