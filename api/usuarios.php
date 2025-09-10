<?php
	require_once('config.php');
	header('Content-Type: application/json');
	$metodo = $_SERVER['REQUEST_METHOD'];

	$controller = new UsuarioController();

	switch ($metodo) {
		case 'GET':

			$login = null;
			$senha = null;
			if (
				(isset($_GET['l']) && $_GET['l'] != '')
				&& (isset($_GET['s']) && $_GET['s'] != '')
			) {
				$login = $_GET['l'];
				$senha = $_GET['s'];
			}

			try 
			{
				if ($login != null && $senha != null) {
					$dados = $controller->VerificarAcesso($login, $senha);
					if (count($dados) > 0) {
						$_SESSION['usuario'] = $dados[0];
					}
				}
				else {
					$dados = $controller->Listar();
				}
				http_response_code(200);
				echo json_encode($dados);
			} catch (\Throwable $th) {
				http_response_code(404);
				echo json_encode(['mensagem'=>$th->getMessage()]);	
			}
			break;
		case 'POST':
			try {
				$corpo = json_decode(file_get_contents('php://input'), true);
				$chaves = ['email', 'nome', 'senha', 'tipo'];
				if (!validaCorpoRequisicao($corpo)) 
				{
					http_response_code(400);
					echo json_encode(['mensagem'=>'Requisição inválida']);
					exit;
				}
				if (!validaChaves($corpo, $chaves)) 
				{
					http_response_code(400);
					echo json_encode(['mensagem'=>'Requisição inválida']);
					exit;
				}
				$usuario = new Usuario($corpo['email'], $corpo['nome'], $corpo['senha'], $corpo['tipo']);
				$controller->Criar($usuario);

				http_response_code(200);
				echo json_encode(['mensagem'=>'Usuário criado com Sucesso']);
			} catch (\Throwable $th) {
				http_response_code(500);
				echo json_encode(['mensagem'=>$th->getMessage()]);	
			}
			break;
		case 'PUT':
			try {
				$corpo = json_decode(file_get_contents('php://input'), true);
				$chaves = ['email', 'nome', 'senha'];
				if (!validaCorpoRequisicao($corpo)) 
				{
					http_response_code(400);
					echo json_encode(['mensagem'=>'Requisição inválida']);
					exit;
				}
				if (!validaChaves($corpo, $chaves)) 
				{
					http_response_code(400);
					echo json_encode(['mensagem'=>'Requisição inválida']);
					exit;
				}
				$usuario = new Usuario($corpo['email'], $corpo['nome'], $corpo['senha']);
				$controller->AtualizarDados($usuario);

				http_response_code(200);
				echo json_encode(['mensagem'=>'Usuário alterado com Sucesso']);
			} catch (\Throwable $th) {
				http_response_code(500);
				echo json_encode(['mensagem'=>$th->getMessage()]);	
			}
			break;
		case 'DELETE':
			if (!isset($_REQUEST['e']) || $_REQUEST['e'] == ""){
				http_response_code(400);
				echo json_encode(['mensagem'=>'E-mail deve ser informado!']);
				exit;
			}
			try {
				$email = $_REQUEST['e'];
				$controller->Excluir($email);
				http_response_code(200);
				echo json_encode(['mensagem'=>'Usuário excluído com Sucesso']);
			} catch (\Throwable $th) {
				http_response_code(500);
				echo json_encode(['mensagem'=>$th->getMessage()]);	
			}
			break;
		default:
			http_response_code(400);
			echo json_encode(['mensagem'=>'Método Inválido']);
			break;
	}
	function validaCorpoRequisicao($corpo) {
		if (is_null($corpo))
		{
			http_response_code(400);
			echo json_encode(['mensagem'=>'Dados Inváidos!']);
			return false;
		}
		return true;
	}
	function validaChaves($corpo, $campos) {
		for ($i=0; $i < count($campos); $i++) { 
			if (!array_key_exists($campos[$i], $corpo))
			{
				http_response_code(400);
				echo json_encode(['mensagem'=>'Dados incorretos. Verifique a documentação da API e tente novamente!']);
				return false;
			}
			if ($corpo[$campos[$i]] == ''){
				http_response_code(400);
				echo json_encode(['mensagem'=>'Dados incorretos. Verifique a documentação da API e tente novamente!']);
				return false;
			}
		}
		return true;
	}
?>