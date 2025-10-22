<?php
class EventoController extends Banco {

    public function listarAprovados($dataInicio, $dataFim) {
        try {
            $parametros = ['pDataInicio' => $dataInicio, 'pDataFim' => $dataFim];
            $dados = $this->Consultar('listarEventosAprovados', $parametros);
            return $dados;
        } catch (\Throwable $th) { throw $th; }
    }

    public function criar($dadosEvento) {
        $this->iniciarTransacao(); // <-- INICIA A TRANSAÇÃO
        try {
            // 1. Cria o evento
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

            $cdEvento = $dadosEvento['cd_evento'];
            
            // 2. Associa as turmas
            foreach ($dadosEvento['turmas'] as $cdTurma) {
                $this->ExecutarSQL(
                    'INSERT INTO eventos_has_turmas (eventos_cd_evento, turmas_cd_turma) VALUES (:cd_evento, :cd_turma)',
                    ['cd_evento' => $cdEvento, 'cd_turma' => $cdTurma]
                );
            }

            // 3. Associa os professores para aprovação
            if (!empty($dadosEvento['professores'])) {
                foreach ($dadosEvento['professores'] as $cdProfessor) {
                    $this->Executar('registrarAprovacaoProfessor', [
                        'pCdEvento' => $cdEvento,
                        'pCdUsuario' => $cdProfessor,
                        'pStatus' => 'Pendente' // Agora 'Pendente' é aceito
                    ]);
                }
            }
            
            $this->commitTransacao(); // <-- SUCESSO: Confirma tudo

        } catch (\Throwable $th) {
            $this->rollbackTransacao(); // <-- FALHA: Desfaz tudo
            throw $th; // Joga o erro para o formulário
        }
    }

    public function criarAprovado($dadosEvento) {
        $this->iniciarTransacao(); // <-- INICIA A TRANSAÇÃO
        try {
            // 1. Cria o evento
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

            $cdEvento = $dadosEvento['cd_evento'];
            
            // 2. Associa as turmas
            foreach ($dadosEvento['turmas'] as $cdTurma) {
                $this->ExecutarSQL(
                    'INSERT INTO eventos_has_turmas (eventos_cd_evento, turmas_cd_turma) VALUES (:cd_evento, :cd_turma)',
                    ['cd_evento' => $cdEvento, 'cd_turma' => $cdTurma]
                );
            }

            $this->commitTransacao(); // <-- SUCESSO: Confirma tudo

        } catch (\Throwable $th) {
            $this->rollbackTransacao(); // <-- FALHA: Desfaz tudo
            throw $th;
        }
    }

    public function listarParaProfessor($cdUsuario) {
        try {
            return $this->Consultar('listarEventosParaProfessor', ['pCdUsuario' => $cdUsuario]);
        } catch (\Throwable $th) { throw $th; }
    }

    public function listarParaCoordenador($cdUsuario) {
        try {
            return $this->Consultar('listarEventosParaCoordenador', ['pCdUsuario' => $cdUsuario]);
        } catch (\Throwable $th) { throw $th; }
    }

    public function registrarRespostaProfessor($cdEvento, $cdUsuario, $statusResposta) {
        try {
            $this->Executar('registrarAprovacaoProfessor', [
                'pCdEvento' => $cdEvento,
                'pCdUsuario' => $cdUsuario,
                'pStatus' => $statusResposta
            ]);
        } catch (\Throwable $th) { throw $th; }
    }

    public function darDecisaoFinal($cdEvento, $decisao, $cdCoordenador) {
        try {
            $parametros = [
                'pCdEvento' => $cdEvento,
                'pCdCoordenador' => $cdCoordenador
            ];
            if ($decisao === 'Aprovado') {
                $this->Executar('aprovarEventoDefinitivo', $parametros);
            } elseif ($decisao === 'Recusado') {
                $this->Executar('recusarEventoDefinitivo', $parametros);
            } else {
                throw new Exception("Decisão inválida.");
            }
        } catch (\Throwable $th) { throw $th; }
    }
}
?>