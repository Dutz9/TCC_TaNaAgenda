<?php
    class TurmaController extends Banco {
    
        public function listar() {
        // Agora chamando a Stored Procedure, como a função Consultar espera.
        return $this->Consultar('listarTurmas');
    }

    /**
     * Lista todas as turmas com detalhes completos, incluindo
     * curso, período e contagem de professores associados.
     *
     * @return array Lista de turmas.
     */
    public function listarComContagem() {
        try {
            // Chama a Stored Procedure que criamos no Passo 1
            $dados = $this->Consultar('listarTurmasComContagem', []);
            return $dados;
        } catch (\Throwable $th) {
            // Se der erro, joga a exceção
            throw $th;
        }
    }

    /**
     * Atualiza os dados de uma turma.
     * @param array $dadosTurma Array com os dados (cd_turma, nome, serie, qt_alunos, sala)
     */
    public function atualizarTurma($dadosTurma) {
        try {
            $this->Executar('atualizarTurma', [
                'pCdTurma' => $dadosTurma['cd_turma'],
                'pNmTurma' => $dadosTurma['nm_turma'],
                'pIcSerie' => $dadosTurma['ic_serie'],
                'pQtAlunos' => $dadosTurma['qt_alunos'],
                'pCdSala' => $dadosTurma['cd_sala']
            ]);
        } catch (\Throwable $th) {
            // Joga o erro (ex: "Nome já em uso") para a API tratar
            throw $th;
        }
    }

    /**
     * Exclui uma turma do sistema.
     * @param int $cdTurma O ID da turma a ser excluída.
     */
    public function excluirTurma($cdTurma) {
        try {
            $this->Executar('excluirTurma', [
                'pCdTurma' => $cdTurma
            ]);
        } catch (\Throwable $th) {
            // Joga o erro (ex: "Turma não pode ser excluída pois tem professores")
            throw $th;
        }
    }

    /**
     * Cria uma nova turma no banco de dados.
     *
     * @param array $dadosTurma Array com os dados (nome, serie, qt_alunos, sala, cd_curso)
     */
    public function criarTurma($dadosTurma) {
        $this->iniciarTransacao(); // <-- INICIA A TRANSAÇÃO
        try {
            // 1. Cria a turma
            $this->Executar('criarTurma', [
                'pNmTurma' => $dadosTurma['nm_turma'],
                'pIcSerie' => $dadosTurma['ic_serie'],
                'pQtAlunos' => $dadosTurma['qt_alunos'],
                'pCdSala' => $dadosTurma['cd_sala'],
                'pCdCurso' => $dadosTurma['cd_curso']
            ]);
            
            // (Futuramente, se o formulário também associasse professores,
            // a lógica de inserção na tabela 'usuarios_has_turmas' viria aqui)

            $this->commitTransacao(); // <-- SUCESSO: Confirma tudo

        } catch (\Throwable $th) {
            $this->rollbackTransacao(); // <-- FALHA: Desfaz tudo
            // Joga o erro (ex: "Nome da turma já em uso") para o formulário
            throw $th; 
        }
    }

}
?>