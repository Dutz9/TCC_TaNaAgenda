<?php

class EventoController extends Banco {

    /**
     * Busca no banco de dados todos os eventos que já foram aprovados.
     * @return array Lista de eventos aprovados.
     */
    public function listarAprovados() {
        try {
            // Chama a Stored Procedure que criamos no Passo 1
            $dados = $this->Consultar('listarEventosAprovados', []);
            return $dados;
        } catch (\Throwable $th) {
            // Em caso de erro, lança a exceção para a página que chamou tratar.
            throw $th;
        }
    }

    // Futuramente, teremos outras funções aqui, como:
    // public function criar($evento) { ... }
    // public function aprovar($cd_evento) { ... }
    // etc...
}

?>