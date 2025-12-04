<?php

  class CursoController extends Banco {

 
      public function listar() {
          try {
              return $this->Consultar('listarCursos', []);
          } catch (\Throwable $th) {
              throw $th;
          }
      }


      public function listarComContagem() {
        try {
            return $this->Consultar('listarCursosComContagem', []);
        } catch (\Throwable $th) {
            throw $th;
        }
    }


      public function criarCurso($dadosCurso) {
        try {
            $this->Executar('criarCurso', [
                'pNmCurso' => $dadosCurso['nm_curso'],
                'pIcPeriodo' => $dadosCurso['ic_periodo']
            ]);
        } catch (\Throwable $th) {
            throw $th;
        }
      }


      public function atualizarCurso($dadosCurso) {
        try {
            $this->Executar('atualizarCurso', [
                'pCdCurso' => $dadosCurso['cd_curso'],
                'pNmCurso' => $dadosCurso['nm_curso'],
                'pIcPeriodo' => $dadosCurso['ic_periodo']
            ]);
        } catch (\Throwable $th) {
            throw $th;
        }
      }
      

      public function excluirCurso($cdCurso) {
        try {
            $this->Executar('excluirCurso', [
                'pCdCurso' => $cdCurso
            ]);
        } catch (\Throwable $th) {
            throw $th;
        }
      }
  }
?>