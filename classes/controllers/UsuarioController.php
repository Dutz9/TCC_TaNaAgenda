<?php

class UsuarioController extends Banco {

public function Listar() {
    try {
        $dados =$this->Consultar('listarUsuarios', []);
        return $dados;
    } catch (\Throwable $th) {
        throw $th;
    }
}

public function Criar($usuario) {
    try {
        $parametros = [
            'pEmail'=>$usuario->Email,
            'pNome'=>$usuario->Nome,
            'pSenha'=>$usuario->Senha,
            'pTipo'=>$usuario->Tipo
        ];
        $this->Executar('criarUsuario', $parametros);
    } catch (\Throwable $th) {
        throw $th;
    }
}

public function AtualizarDados($usuario) {
    try {
        $parametros = [
            'pEmail'=>$usuario->Email,
            'pNome'=>$usuario->Nome,
            'pSenha'=>$usuario->Senha
        ];
        $this->Executar('atualizaDadosUsuario', $parametros);
    } catch (\Throwable $th) {
        throw $th;
    }
}

public function Excluir($email) {
    try {
        $parametros = [
            'pEmail'=>$email
        ];
        $this->Executar('excluirUsuario', $parametros);
    } catch (\Throwable $th) {
        throw $th;
    }
}

public function VerificarAcesso($login, $senha) {
    try {
        $parametros = [
            'pLogin'=>$login,
            'pSenha'=>$senha
        ];
        $dados = $this->Consultar('verificarAcesso', $parametros);
        return $dados;
    } catch (\Throwable $th) {
        throw new Exception('Login e/ou Senha Inválida');
    }
}

// Este método já existia e deve permanecer apenas uma vez.
public function listarRelacaoProfessorTurma() {
    return $this->Consultar('listarRelacaoProfessorTurma');
}

// NOVOS MÉTODOS ADICIONADOS
public function listarTodosProfessores() {
    try {
        return $this->Consultar('listarTodosProfessores');
    } catch (\Throwable $th) {
        throw $th;
    }
}

public function listarTodosCoordenadores() {
    try {
        return $this->Consultar('listarTodosCoordenadores');
    } catch (\Throwable $th) {
        throw $th;
    }
}

}
?>