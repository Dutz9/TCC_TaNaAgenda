<?php

class TurmaController extends Banco {
  
    public function listar() {
    // Agora chamando a Stored Procedure, como a função Consultar espera.
    return $this->Consultar('listarTurmas');
}

}

?>