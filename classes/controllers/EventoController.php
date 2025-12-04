<?php
class EventoController extends Banco {


    public function listarAprovados($dataInicio, $dataFim, $filtros = []) {
        try {
            $periodoFiltro = !empty($filtros['periodo']) ? implode(',', $filtros['periodo']) : null;
            $turmaFiltro = !empty($filtros['turma']) ? implode(',', $filtros['turma']) : null;
            $tipoFiltro = !empty($filtros['tipo']) ? implode(',', $filtros['tipo']) : null;

            $parametros = [
                'pDataInicio' => $dataInicio,
                'pDataFim' => $dataFim,
                'pPeriodo' => $periodoFiltro,
                'pCdTurma' => $turmaFiltro,
                'pTipoEvento' => $tipoFiltro
            ];
            
            return $this->Consultar('listarEventosAprovados', $parametros);
        } catch (\Throwable $th) {
            throw $th;
        }
    }


    public function criar($dadosEvento) {
        $this->iniciarTransacao();
        try {
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
            
            foreach ($dadosEvento['turmas'] as $cdTurma) {
                $this->ExecutarSQL(
                    'INSERT INTO eventos_has_turmas (eventos_cd_evento, turmas_cd_turma) VALUES (:cd_evento, :cd_turma)',
                    ['cd_evento' => $cdEvento, 'cd_turma' => $cdTurma]
                );
            }

            if (!empty($dadosEvento['professores'])) {
                foreach ($dadosEvento['professores'] as $cdProfessor) {
                    $this->Executar('registrarAprovacaoProfessor', [
                        'pCdEvento' => $cdEvento,
                        'pCdUsuario' => $cdProfessor,
                        'pStatus' => 'Pendente',
                        'pDsMotivo' => null 
                    ]);
                }
            }
            
            $this->commitTransacao();

        } catch (\Throwable $th) {
            $this->rollbackTransacao();
            throw $th;
        }
    }

    public function criarAprovado($dadosEvento) {
        $this->iniciarTransacao();
        try {
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
            
            foreach ($dadosEvento['turmas'] as $cdTurma) {
                $this->ExecutarSQL(
                    'INSERT INTO eventos_has_turmas (eventos_cd_evento, turmas_cd_turma) VALUES (:cd_evento, :cd_turma)',
                    ['cd_evento' => $cdEvento, 'cd_turma' => $cdTurma]
                );
            }
    
            if (!empty($dadosEvento['professores'])) {
                foreach ($dadosEvento['professores'] as $cdProfessor) {

                    $this->Executar('registrarAprovacaoProfessor', [
                        'pCdEvento' => $cdEvento,
                        'pCdUsuario' => $cdProfessor,
                        'pStatus' => 'Pendente',
                        'pDsMotivo' => null
                    ]);
                }
            }
    
            $this->commitTransacao();
    
        } catch (\Throwable $th) {
            $this->rollbackTransacao();
            throw $th;
        }
    }

    public function listarParaProfessor($cdUsuario, $filtros = []) {
        try {
            $parametros = [
                'pCdUsuario' => $cdUsuario,
                'pStatus' => $filtros['status'] ?? null,
                'pSolicitante' => $filtros['solicitante'] ?? null,
                'pCdTurma' => $filtros['turma'] ?? null,
                'pTipoEvento' => $filtros['tipo'] ?? null,
                'pDataFiltro' => $filtros['data'] ?? null
            ];
            return $this->Consultar('listarEventosParaProfessor', $parametros);
        } catch (\Throwable $th) { 
            throw $th; 
        }
    }


    public function listarParaCoordenador($cdUsuario, $filtros = []) {
        try {
            $parametros = [
                'pCdUsuario' => $cdUsuario,
                'pStatus' => $filtros['status'] ?? null,
                'pSolicitante' => $filtros['solicitante'] ?? null,
                'pCdTurma' => $filtros['turma'] ?? null,
                'pTipoEvento' => $filtros['tipo'] ?? null,
                'pDataFiltro' => $filtros['data'] ?? null
            ];
            return $this->Consultar('listarEventosParaCoordenador', $parametros);
        } catch (\Throwable $th) { 
            throw $th; 
        }
    }

    public function listarParaAdministrador($cdUsuario, $filtros = []) {
        return $this->listarParaCoordenador($cdUsuario, $filtros); 
    }


    public function registrarRespostaProfessor($cdEvento, $cdUsuario, $statusResposta, $motivo = null) {
        try {
            $this->Executar('registrarAprovacaoProfessor', [
                'pCdEvento' => $cdEvento,
                'pCdUsuario' => $cdUsuario,
                'pStatus' => $statusResposta,
                'pDsMotivo' => $motivo 
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

    public function cancelarSolicitacao($cdEvento, $cdUsuario) {
        try {
            $this->Executar('cancelarSolicitacaoEvento', [
                'pCdEvento' => $cdEvento,
                'pCdUsuarioSolicitante' => $cdUsuario
            ]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function excluirDefinitivo($cdEvento) {
        try {
            $this->Executar('excluirEventoDefinitivo', [
                'pCdEvento' => $cdEvento
            ]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function buscarParaEditar($cdEvento, $cdUsuario) {
        try {
            $dados = $this->Consultar('buscarEventoParaEdicao', [
                'pCdEvento' => $cdEvento,
                'pCdUsuarioSolicitante' => $cdUsuario
            ]);
            if (count($dados) > 0) {
                return $dados[0];
            } else {
                return null;
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }


    public function atualizarSolicitacao($cdEvento, $dadosEvento) {
        $this->iniciarTransacao();
        try {

            $this->Executar('atualizarSolicitacaoEvento', [
                'pCdEvento' => $cdEvento,
                'pNmEvento' => $dadosEvento['nm_evento'],
                'pDtEvento' => $dadosEvento['dt_evento'],
                'pHorarioInicio' => $dadosEvento['horario_inicio'],
                'pHorarioFim' => $dadosEvento['horario_fim'],
                'pTipoEvento' => $dadosEvento['tipo_evento'],
                'pDsDescricao' => $dadosEvento['ds_descricao']
            ]);
            
            foreach ($dadosEvento['turmas'] as $cdTurma) {
                $this->ExecutarSQL(
                    'INSERT INTO eventos_has_turmas (eventos_cd_evento, turmas_cd_turma) VALUES (:cd_evento, :cd_turma)',
                    ['cd_evento' => $cdEvento, 'cd_turma' => $cdTurma]
                );
            }

            if (!empty($dadosEvento['professores'])) {
                foreach ($dadosEvento['professores'] as $cdProfessor) {
                    $this->Executar('registrarAprovacaoProfessor', [
                        'pCdEvento' => $cdEvento,
                        'pCdUsuario' => $cdProfessor,
                        'pStatus' => 'Pendente',
                        'pDsMotivo' => null
                    ]);
                }
            }
            
            $this->commitTransacao();

        } catch (\Throwable $th) {
            $this->rollbackTransacao();
            throw $th;
        }
    }

    public function buscarParaEditarCoordenador($cdEvento) {
        try {
            $dados = $this->Consultar('buscarEventoParaEdicaoCoordenador', [
                'pCdEvento' => $cdEvento
            ]);
            if (count($dados) > 0) {
                return $dados[0];
            } else {
                return null; 
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function contarPendenciasProfessor($cdUsuario) {
        try {
            $dados = $this->Consultar('contarNotificacoesProfessor', ['pCdUsuario' => $cdUsuario]);
            return $dados[0]['total'] ?? 0;
        } catch (\Throwable $th) {
            return 0;
        }
    }

    public function contarPendenciasCoordenador() {
        try {
            $dados = $this->Consultar('contarNotificacoesCoordenador', []);
            return $dados[0]['total'] ?? 0;
        } catch (\Throwable $th) {
            return 0;
        }
    }
}
?>