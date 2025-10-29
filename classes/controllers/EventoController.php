<?php
class EventoController extends Banco {

    /**
     * Busca no banco de dados todos os eventos que já foram aprovados,
     * aplicando filtros de data, período, turma e tipo.
     *
     * @param string $dataInicio Data de início (YYYY-MM-DD)
     * @param string $dataFim Data de fim (YYYY-MM-DD)
     * @param array $filtros Array associativo com os filtros (periodo, turma, tipo)
     * @return array Lista de eventos aprovados e filtrados.
     */
    public function listarAprovados($dataInicio, $dataFim, $filtros = []) {
        try {
            // Converte os arrays de filtro em strings separadas por vírgula
            // Se o array estiver vazio, envia NULL
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
            
            $dados = $this->Consultar('listarEventosAprovados', $parametros);
            return $dados;
        } catch (\Throwable $th) {
            throw $th;
        }
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
    
            // --- A CORREÇÃO ESTÁ AQUI ---
            // 3. Associa os professores (que não foram excluídos) ao evento
            // Mesmo sendo um evento de coordenador, usamos a tabela 'resolucao' para saber quem está "envolvido".
            if (!empty($dadosEvento['professores'])) {
                foreach ($dadosEvento['professores'] as $cdProfessor) {
                    // Salvamos como 'Pendente' para consistência, mesmo que não exija ação.
                    $this->Executar('registrarAprovacaoProfessor', [
                        'pCdEvento' => $cdEvento,
                        'pCdUsuario' => $cdProfessor,
                        'pStatus' => 'Pendente' 
                    ]);
                }
            }
            // --- FIM DA CORREÇÃO ---
    
            $this->commitTransacao(); // <-- SUCESSO: Confirma tudo
    
        } catch (\Throwable $th) {
            $this->rollbackTransacao(); // <-- FALHA: Desfaz tudo
            throw $th;
        }
    }

    /**
     * Lista os eventos relevantes para um professor, aplicando filtros.
     * @param string $cdUsuario O código do usuário logado.
     * @param array $filtros Um array associativo com os filtros da página.
     * @return array A lista de eventos filtrada.
     */
    public function listarParaProfessor($cdUsuario, $filtros = []) {
        try {
            // Define os parâmetros que a Stored Procedure espera
            $parametros = [
                'pCdUsuario' => $cdUsuario,
                // Usa o 'operador de coalescência nula' (??) para enviar NULL se o filtro não existir
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

    /**
     * Lista os eventos relevantes para um Coordenador, aplicando filtros.
     * @param string $cdUsuario O código do usuário logado.
     * @param array $filtros Um array associativo com os filtros da página.
     * @return array A lista de eventos filtrada.
     */
    public function listarParaCoordenador($cdUsuario, $filtros = []) {
        try {
            // Define os parâmetros que a Stored Procedure espera
            $parametros = [
                'pCdUsuario' => $cdUsuario,
                // Usa o 'operador de coalescência nula' (??) para enviar NULL se o filtro não existir
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

    // Adicione esta nova função dentro da classe EventoController
    
    /**
     * Cancela uma solicitação de evento.
     * Chama a procedure que apaga o evento e suas associações,
     * mas apenas se o usuário logado for o solicitante e o status for 'Solicitado'.
     *
     * @param string $cdEvento O ID do evento a ser cancelado.
     * @param string $cdUsuario O ID do usuário que está tentando cancelar.
     */
    public function cancelarSolicitacao($cdEvento, $cdUsuario) {
        try {
            // Chama a Stored Procedure que criamos no Passo 1
            $this->Executar('cancelarSolicitacaoEvento', [
                'pCdEvento' => $cdEvento,
                'pCdUsuarioSolicitante' => $cdUsuario
            ]);
        } catch (\Throwable $th) {
            // Se der erro (ex: tentar apagar evento de outro), joga a exceção
            throw $th;
        }
    }

}

?>