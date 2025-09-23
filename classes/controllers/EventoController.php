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

    public function criar($dadosEvento) {
        try {
            // Chama a Stored Procedure para criar o evento
            $this->Executar('criarEvento', [
                'pCdEvento' => $dadosEvento['cd_evento'],
                'pDtEvento' => $dadosEvento['dt_evento'],
                'pNmEvento' => $dadosEvento['nm_evento'],
                'pHorarioInicio' => $dadosEvento['horario_inicio'],
                'pHorarioFim' => $dadosEvento['horario_fim'],
                'pTipoEvento' => $dadosEvento['tipo_evento'],
                'pDsDescricao' => $dadosEvento['ds_descricao'],
                'pCdUsuarioSolicitante' => $dadosEvento['cd_usuario_solicitante']
            ]);

            // Após criar o evento, associa as turmas a ele
            $cdEvento = $dadosEvento['cd_evento'];
            foreach ($dadosEvento['turmas'] as $cdTurma) {
                // Agora usando o método correto para SQL direto
                $this->ExecutarSQL(
                    'INSERT INTO eventos_has_turmas (eventos_cd_evento, turmas_cd_turma) VALUES (:cd_evento, :cd_turma)',
                    ['cd_evento' => $cdEvento, 'cd_turma' => $cdTurma]
                ); 
            }  

        } catch (\Throwable $th) {
            throw $th;
        }
    }

    // Adicione esta nova função dentro da classe EventoController
    public function criarAprovado($dadosEvento) {
        try {
            // Chama a nova Stored Procedure
            $this->Executar('criarEventoAprovado', [
                'pCdEvento' => $dadosEvento['cd_evento'],
                'pDtEvento' => $dadosEvento['dt_evento'],
                'pNmEvento' => $dadosEvento['nm_evento'],
                'pHorarioInicio' => $dadosEvento['horario_inicio'],
                'pHorarioFim' => $dadosEvento['horario_fim'],
                'pTipoEvento' => $dadosEvento['tipo_evento'],
                'pDsDescricao' => $dadosEvento['ds_descricao'],
                'pCdUsuarioSolicitante' => $dadosEvento['cd_usuario_solicitante']
            ]);

            // A lógica para associar as turmas é a mesma
            $cdEvento = $dadosEvento['cd_evento'];
            foreach ($dadosEvento['turmas'] as $cdTurma) {
                $this->ExecutarSQL(
                    'INSERT INTO eventos_has_turmas (eventos_cd_evento, turmas_cd_turma) VALUES (:cd_evento, :cd_turma)',
                    ['cd_evento' => $cdEvento, 'cd_turma' => $cdTurma]
                );
            }

        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function listarParaProfessor($cdUsuario) {
        try {
            return $this->Consultar('listarEventosParaProfessor', ['pCdUsuario' => $cdUsuario]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function registrarRespostaProfessor($cdEvento, $cdUsuario, $statusResposta) {
        try {
            // Usa a Stored Procedure que já existe para inserir/atualizar a resposta
            $this->Executar('registrarAprovacaoProfessor', [
                'pCdEvento' => $cdEvento,
                'pCdUsuario' => $cdUsuario,
                'pStatus' => $statusResposta // 'Aprovado' ou 'Recusado'
            ]);
        } catch (\Throwable $th) {
            // Se der erro, joga a exceção para a API tratar
            throw $th;
        }
    }

}

?>