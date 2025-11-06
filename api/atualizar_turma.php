<?php
  // api/atualizar_turma.php

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
      // 3. CAPTURA DOS DADOS
      $cd_turma = $_POST['cd_turma'] ?? null;
      $nm_turma = $_POST['nm_turma'] ?? null;
      $ic_serie = $_POST['ic_serie'] ?? null;
      $qt_alunos = $_POST['qt_alunos'] ?? null;
      $cd_sala = $_POST['cd_sala'] ?? null;

      if (empty($cd_turma) || empty($nm_turma) || empty($ic_serie) || empty($qt_alunos) || empty($cd_sala)) {
          http_response_code(400); // Requisição inválida
          echo json_encode(['status' => 'erro', 'mensagem' => 'Dados incompletos. Todos os campos são obrigatórios.']);
          exit();
      }
      
      $dadosTurma = [
          'cd_turma' => $cd_turma,
          'nm_turma' => $nm_turma,
          'ic_serie' => $ic_serie,
          'qt_alunos' => $qt_alunos,
          'cd_sala' => $cd_sala
      ];

      // 4. EXECUÇÃO DA LÓGICA
      $turmaController = new TurmaController();
      $turmaController->atualizarTurma($dadosTurma);

      // 5. RESPOSTA DE SUCESSO
      echo json_encode(['status' => 'sucesso', 'mensagem' => 'Turma atualizada com sucesso!']);

  } catch (Exception $e) {
      // 6. RESPOSTA DE ERRO (ex: "Nome já em uso")
      http_response_code(500); 
      
      $mensagemErro = $e->getMessage();
      if (strpos($mensagemErro, 'Erro: O nome desta turma (Sigla) já está em uso.') !== false) {
          $mensagemErro = 'Erro: O nome (Sigla) desta turma já está em uso.';
      }

      echo json_encode(['status' => 'erro', 'mensagem' => $mensagemErro]);
  }
?>