<?php
    class TurmaController extends Banco {
    
        public function listar() {
        return $this->Consultar('listarTurmas');
    }

    /**
     *
     *
     *
     * @return array
     */
    public function listarComContagem() {
        try {
            $dados = $this->Consultar('listarTurmasComContagem', []);
            return $dados;
        } catch (\Throwable $th) {

            throw $th;
        }
    }

    /**
     * 
     * @param array $dadosTurma 
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

            throw $th;
        }
    }

    /**
     * 
     * @param int $cdTurma 
     */
    public function excluirTurma($cdTurma) {
        try {
            $this->Executar('excluirTurma', [
                'pCdTurma' => $cdTurma
            ]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * 
     *
     * @param array $dadosTurma 
     */
    public function criarTurma($dadosTurma) {
        $this->iniciarTransacao();
        try {
            $this->Executar('criarTurma', [
                'pNmTurma' => $dadosTurma['nm_turma'],
                'pIcSerie' => $dadosTurma['ic_serie'],
                'pQtAlunos' => $dadosTurma['qt_alunos'],
                'pCdSala' => $dadosTurma['cd_sala'],
                'pCdCurso' => $dadosTurma['cd_curso']
            ]);


            $this->commitTransacao(); 

        } catch (\Throwable $th) {
            $this->rollbackTransacao();

            throw $th; 
        }
    }

}
?>