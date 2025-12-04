<?php


  require_once 'config.php';
  require_once 'verifica_sessao.php';

  header('Content-Type: application/json');


  $tipo_usuario_logado = $usuario_logado['tipo_usuario_ic_usuario'];
  if ($tipo_usuario_logado !== 'Coordenador' && $tipo_usuario_logado !== 'Administrador') {
      http_response_code(403);
      echo json_encode(['status' => 'erro', 'mensagem' => 'Acesso negado. Apenas Coordenadores ou Administradores podem atualizar turmas.']);
      exit();
  }


  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      http_response_code(405); 
      echo json_encode(['status' => 'erro', 'mensagem' => 'Método não permitido.']);
      exit();
  }

  try {

      $cd_turma = $_POST['cd_turma'] ?? null;
      $nm_turma = $_POST['nm_turma'] ?? null;
      $ic_serie = $_POST['ic_serie'] ?? null;
      $qt_alunos = $_POST['qt_alunos'] ?? null;
      $cd_sala = $_POST['cd_sala'] ?? null;

      if (empty($cd_turma) || empty($nm_turma) || empty($ic_serie) || empty($qt_alunos) || empty($cd_sala)) {
          http_response_code(400);
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


      $turmaController = new TurmaController();
      $turmaController->atualizarTurma($dadosTurma);

 
      echo json_encode(['status' => 'sucesso', 'mensagem' => 'Turma atualizada com sucesso!']);

  } catch (Exception $e) {

      http_response_code(500); 
      
      $mensagemErro = $e->getMessage();
      if (strpos($mensagemErro, 'Erro: O nome desta turma (Sigla) já está em uso.') !== false) {
          $mensagemErro = 'Erro: O nome (Sigla) desta turma já está em uso.';
      }

      echo json_encode(['status' => 'erro', 'mensagem' => $mensagemErro]);
  }
?>