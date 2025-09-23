<?php 

	class Banco {
		private $conexao = null;
		private $cSQL = null;
		public function __construct() {}
		private function Conectar()
		{
			try {
				$this->conexao = new PDO('mysql:dbname=escola;host=localhost;', 'root', 'root');
				$this->conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$this->conexao->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, 'SET NAMES utf8mb4');
			} catch (PDOException $Erro) {
				throw new Exception('Erro ao conectar ao Servidor. Tente novamente.');
			}
		}

		
		protected function Desconectar()
		{
			try {
				$this->conexao = null;
			} catch (PDOException $Erro) {
				throw new Exception('Não foi possível fechar a conexão.');
			}
		}

		protected function Consultar($nomeProcedure, $parametros = [])
		{
			try {
				$this->Conectar();

				$listaNomesParametros = [];
				foreach ($parametros as $chave => $valor) {
					$listaNomesParametros[] = ':' . $chave;
				}

				$comando = 'CALL ' . $nomeProcedure;
				if (count($listaNomesParametros) > 0) {
					$comando .= '(' . implode(', ', $listaNomesParametros) . ')';
				}

				$this->cSQL = $this->conexao->prepare($comando);

				foreach ($parametros as $chave => $valor) {
					$this->cSQL->bindValue(':' . $chave, $valor);
				}

				$this->cSQL->execute();
				$dados = $this->cSQL->fetchAll(PDO::FETCH_ASSOC);
				$this->Desconectar();
				return $dados;
			} catch (PDOException $e) {
				throw $e;
				//throw new Exception('Erro inesperado na Consulta. Tente novamente.');
			}
		}

		protected function Executar($nomeProcedure, $parametros = [])
		{
			try {
				$this->Conectar();

				$listaNomesParametros = [];
				foreach ($parametros as $chave => $valor) {
					$listaNomesParametros[] = ':' . $chave;
				}

				$comando = 'CALL ' . $nomeProcedure;
				if (count($listaNomesParametros) > 0) {
					$comando .= '(' . implode(', ', $listaNomesParametros) . ')';
				}

				$this->cSQL = $this->conexao->prepare($comando);

				foreach ($parametros as $chave => $valor) {
					$this->cSQL->bindValue(':' . $chave, $valor);
				}

				$this->cSQL->execute();
				$this->Desconectar();
			} catch (PDOException $e) {
				throw $e;
				//throw new Exception('Erro inesperado na Execução. Tente novamente.');
			}
		}

		/**
			 * Executa um comando SQL direto (INSERT, UPDATE, DELETE).
			 * Este método NÃO adiciona 'CALL', sendo ideal para queries simples.
			 * @param string $sql A query SQL completa com placeholders (ex: :nome).
			 * @param array $parametros Array associativo com os parâmetros para a query.
			 */
			protected function ExecutarSQL($sql, $parametros = [])
			{
					try {
							$this->Conectar();

							$this->cSQL = $this->conexao->prepare($sql);

							foreach ($parametros as $chave => $valor) {
									// Adiciona os dois pontos se o placeholder não os tiver
									$placeholder = (strpos($chave, ':') === 0) ? $chave : ':' . $chave;
									$this->cSQL->bindValue($placeholder, $valor);
							}

							$this->cSQL->execute();
							$this->Desconectar();
					} 
					
					catch (PDOException $e) {
							throw $e;
					}
			}

	}

?>
