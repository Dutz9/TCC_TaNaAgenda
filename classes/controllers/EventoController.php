<?php
// Certifique-se de que a classe Banco e outras classes necessárias estejam disponíveis
// Exemplo: require_once '../classes/base/banco.php'; // Ajuste o caminho se necessário
// Para o EventoController, é comum que ele dependa de UsuarioController para listar coordenadores.
// require_once 'UsuarioController.php'; // Ajuste o caminho se necessário para esta dependência.

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

    // NOVO: Método para associar um professor a um evento
    public function associarProfessorAEvento($cdEvento, $cdUsuarioProfessor) {
        try {
            $this->ExecutarSQL(
                'INSERT IGNORE INTO eventos_has_professores (eventos_cd_evento, usuarios_cd_usuario) VALUES (:cd_evento, :cd_usuario)',
                ['cd_evento' => $cdEvento, 'cd_usuario' => $cdUsuarioProfessor]
            );
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    // NOVO: Método para notificar um coordenador sobre um evento
    public function notificarCoordenadorSobreEvento($cdEvento, $cdUsuarioCoordenador) {
        try {
            // Usa a SP para registrar que o coordenador está ciente/envolvido
            $this->Executar('notificarCoordenadoresSobreEvento', [
                'pCdEvento' => $cdEvento,
                'pCdUsuarioCoordenador' => $cdUsuarioCoordenador
            ]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    // Modificada esta função para incluir a associação de professores e notificação de coordenadores
    public function criarAprovado($dadosEvento) {
        try {
            // 1. Cria o evento com status 'Aprovado'
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

            // 2. Associa as turmas ao evento
            foreach ($dadosEvento['turmas'] as $cdTurma) {
                $this->ExecutarSQL(
                    'INSERT INTO eventos_has_turmas (eventos_cd_evento, turmas_cd_turma) VALUES (:cd_evento, :cd_turma)',
                    ['cd_evento' => $cdEvento, 'cd_turma' => $cdTurma]
                );
            }

            // 3. Associa os professores selecionados manualmente ao evento
            // Apenas se houver professores selecionados
            if (!empty($dadosEvento['professores_envolvidos'])) {
                foreach ($dadosEvento['professores_envolvidos'] as $cdProfessor) {
                    $this->associarProfessorAEvento($cdEvento, $cdProfessor);
                }
            }

            // 4. Notifica TODOS os coordenadores sobre o evento (mesmo que ele tenha criado)
            // É importante que UsuarioController já tenha sido incluído em algum lugar
            // para que esta linha funcione.
            $usuarioController = new UsuarioController();
            $coordenadores = $usuarioController->listarTodosCoordenadores();
            foreach ($coordenadores as $coord) {
                $this->notificarCoordenadorSobreEvento($cdEvento, $coord['cd_usuario']);
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
            // Este método provavelmente precisa do cd_coordenador também, como em darDecisaoFinal
            // Se for usado sem o cd_coordenador, a coluna cd_usuario_aprovador ficará NULL.
            // A SP 'aprovarEventoDefinitivo' já espera o pCdCoordenador.
            throw new Exception("Use 'darDecisaoFinal' para aprovação definitiva com o ID do coordenador.");
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function recusarDefinitivo($cdEvento) {
        try {
             // Este método provavelmente precisa do cd_coordenador também, como em darDecisaoFinal
             // Se for usado sem o cd_coordenador, a coluna cd_usuario_aprovador ficará NULL.
             // A SP 'recusarEventoDefinitivo' já espera o pCdCoordenador.
             throw new Exception("Use 'darDecisaoFinal' para recusa definitiva com o ID do coordenador.");
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