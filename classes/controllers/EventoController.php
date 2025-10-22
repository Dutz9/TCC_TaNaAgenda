<?php

class EventoController extends Banco {

    /**
     * Busca no banco de dados todos os eventos que já foram aprovados.
     * @return array Lista de eventos aprovados.
     */
    public function listarAprovados($dataInicio, $dataFim) {
        try {
            $parametros = [
                'pDataInicio' => $dataInicio,
                'pDataFim' => $dataFim
            ];
            $dados = $this->Consultar('listarEventosAprovados', $parametros);
            return $dados;
        } 
        
        catch (\Throwable $th) {
            // Em caso de erro, lança a exceção para a página que chamou tratar.
            throw $th;
        }
    }

    public function criar($dadosEvento) {
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
            
            // Associa as turmas
            foreach ($dadosEvento['turmas'] as $cdTurma) {
                $this->ExecutarSQL(
                    'INSERT INTO eventos_has_turmas (eventos_cd_evento, turmas_cd_turma) VALUES (:cd_evento, :cd_turma)',
                    ['cd_evento' => $cdEvento, 'cd_turma' => $cdTurma]
                );
            }
    
            // --- NOVA LÓGICA ---
            // Associa os professores para aprovação (status Pendente)
            if (!empty($dadosEvento['professores'])) {
                foreach ($dadosEvento['professores'] as $cdProfessor) {
                    // Chama a procedure que já tínhamos, mas com o status 'Pendente'
                    $this->Executar('registrarAprovacaoProfessor', [
                        'pCdEvento' => $cdEvento,
                        'pCdUsuario' => $cdProfessor,
                        'pStatus' => 'Pendente' // Salva como 'Pendente' (graças ao Passo A)
                    ]);
                }
            }
    
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    // Adicione esta nova função dentro da classe EventoController
    public function criarAprovado($dadosEvento) {
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
            
            // Associa as turmas
            foreach ($dadosEvento['turmas'] as $cdTurma) {
                $this->ExecutarSQL(
                    'INSERT INTO eventos_has_turmas (eventos_cd_evento, turmas_cd_turma) VALUES (:cd_evento, :cd_turma)',
                    ['cd_evento' => $cdEvento, 'cd_turma' => $cdTurma]
                );
            }
            
            // (O coordenador não precisa pré-popular a lista de aprovação)
    
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

    // Adicione esta nova função dentro da classe EventoController
    public function listarParaCoordenador($cdUsuario) {
    try {
        return $this->Consultar('listarEventosParaCoordenador', ['pCdUsuario' => $cdUsuario]);
    } 
    
    catch (\Throwable $th) {
        throw $th;
    }
}

    // Adicione estas duas funções na classe EventoController
    public function aprovarDefinitivo($cdEvento) {
        try {
            $this->Executar('aprovarEventoDefinitivo', ['pCdEvento' => $cdEvento]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function recusarDefinitivo($cdEvento) {
        try {
            $this->Executar('recusarEventoDefinitivo', ['pCdEvento' => $cdEvento]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    // Adicione esta nova função na classe EventoController
    public function darDecisaoFinal($cdEvento, $decisao, $cdCoordenador) {
        try {
            // Agora os parâmetros incluem o código do coordenador
            $parametros = [
                'pCdEvento' => $cdEvento,
                'pCdCoordenador' => $cdCoordenador
            ];

            if ($decisao === 'Aprovado') {
                $this->Executar('aprovarEventoDefinitivo', $parametros);
            } 
            
            elseif ($decisao === 'Recusado') {
                $this->Executar('recusarEventoDefinitivo', $parametros);
            } 
            
            else {
                throw new Exception("Decisão inválida.");
            }
        } 
        
        catch (\Throwable $th) {
            throw $th;
        }
    }

}

?>