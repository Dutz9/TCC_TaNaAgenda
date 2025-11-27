<?php
  // classes/controllers/CursoController.php

  class CursoController extends Banco {

      /**
       * Lista todos os cursos disponíveis (usado em dropdowns).
       */
      public function listar() {
          try {
              return $this->Consultar('listarCursos', []);
          } catch (\Throwable $th) {
              throw $th;
          }
      }

      /**
       * Lista todos os cursos com contagem de turmas e coordenadores.
       * (Usado na página de administração de cursos)
       */
      public function listarComContagem() {
          try {
              return $this->Consultar('listarCursosComContagem', []);
          } catch (\Throwable $th) {
              throw $th;
          }
      }

      /**
       * Cria um novo curso.
       */
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

      /**
       * Atualiza os dados de um curso.
       */
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
      
      /**
       * Exclui um curso do sistema.
       */
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