<?php
  // api/get_professores_turma.php

  // 1. CONFIGURAÇÃO E SEGURANÇA
  require_once 'config.php';
  require_once 'verifica_sessao.php'; // Garante que o usuário está logado

  // Define a resposta como JSON
  header('Content-Type: application/json');

  // 2. CHAVE: Permitir Coordenador OU Administrador
  $tipo_usuario_logado = $usuario_logado['tipo_usuario_ic_usuario'];
  if ($tipo_usuario_logado !== 'Coordenador' && $tipo_usuario_logado !== 'Administrador') {
      http_response_code(403); // Proibido (Forbidden)
      echo json_encode(['status' => 'erro', 'mensagem' => 'Acesso negado. Apenas Coordenadores ou Administradores.']);
      exit();
  }

  // 3. VALIDAÇÃO DO MÉTODO DE REQUISIÇÃO (GET é apropriado para buscar dados)
  if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
      http_response_code(405); // Método não permitido
      echo json_encode(['status' => 'erro', 'mensagem' => 'Método não permitido.']);
      exit();
  }

  try {
      // 4. CAPTURA DOS DADOS (vem da URL, ex: ...?turma_id=5)
      $cd_turma = $_GET['turma_id'] ?? null;

      if (empty($cd_turma)) {
          http_response_code(400); // Requisição inválida
          echo json_encode(['status' => 'erro', 'mensagem' => 'ID da turma não fornecido.']);
          exit();
      }

      // 5. EXECUÇÃO DA LÓGICA
      $usuarioController = new UsuarioController();
      $lista_professores = $usuarioController->listarProfessoresPorTurma($cd_turma);

      // 6. RESPOSTA DE SUCESSO
      echo json_encode($lista_professores);

  } catch (Exception $e) {
      // 7. RESPOSTA DE ERRO
      http_response_code(500); // Erro interno do servidor
      echo json_encode(['status' => 'erro', 'mensagem' => $e->getMessage()]);
  }
?>