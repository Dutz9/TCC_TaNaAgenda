<?php 
class Banco
{
    private $conexao = null;
    private $cSQL = null;
    private $transacaoAtiva = false;

    public function __construct() {}

    private function Conectar() {
        if ($this->conexao !== null) return;
        try {
            $this->conexao = new PDO('mysql:dbname=escola;host=localhost;', 'root', 'root');
            $this->conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conexao->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, 'SET NAMES utf8mb4');
        } catch (PDOException $Erro) {
            throw new Exception('Erro ao conectar ao Servidor. Tente novamente.');
        }
    }
    
    protected function Desconectar() {
        if ($this->transacaoAtiva) return;
        $this->conexao = null;
    }

    public function iniciarTransacao() {
        $this->Conectar();
        $this->transacaoAtiva = true;
        $this->conexao->beginTransaction();
    }
    public function commitTransacao() {
        if ($this->conexao !== null && $this->transacaoAtiva) {
            $this->conexao->commit();
        }
        $this->transacaoAtiva = false;
        $this->Desconectar();
    }
    public function rollbackTransacao() {
        if ($this->conexao !== null && $this->transacaoAtiva) {
            $this->conexao->rollBack();
        }
        $this->transacaoAtiva = false;
        $this->Desconectar();
    }

    protected function Consultar($nomeProcedure, $parametros = []) {
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
            if (!$this->transacaoAtiva) $this->Desconectar();
            return $dados;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    protected function Executar($nomeProcedure, $parametros = []) {
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
            if (!$this->transacaoAtiva) $this->Desconectar();
        } catch (PDOException $e) {
            throw $e;
        }
    }

    protected function ExecutarSQL($sql, $parametros = []) {
        try {
            $this->Conectar();
            $this->cSQL = $this->conexao->prepare($sql);
            foreach ($parametros as $chave => $valor) {
                $placeholder = (strpos($chave, ':') === 0) ? $chave : ':' . $chave;
                $this->cSQL->bindValue($placeholder, $valor);
            }
            $this->cSQL->execute();
            if (!$this->transacaoAtiva) $this->Desconectar();
        } catch (PDOException $e) {
            throw $e;
        }
    }
}
?>