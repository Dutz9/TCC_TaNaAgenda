<?php
  // classes/controllers/CursoController.php

  class CursoController extends Banco {

      /**
       * Lista todos os cursos disponíveis.
       */
      public function listar() {
          try {
              return $this->Consultar('listarCursos', []);
          } catch (\Throwable $th) {
              throw $th;
          }
      }
  }
?>