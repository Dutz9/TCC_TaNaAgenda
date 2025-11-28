<?php

    class UsuarioController extends Banco {

        /**
         * NOVO: Lista TODOS os Professores e Coordenadores com suas associações (Turmas/Cursos).
         */
        public function listarFuncionariosComAssociacoes() {
            try {
                // Chama a nova Stored Procedure
                return $this->Consultar('listarFuncionariosComAssociacoes', []);
            } catch (\Throwable $th) {
                throw $th;
            }
        }
        
        /**
         * NOVO: Atualiza os dados de um COORDENADOR (Nome, Email, Telefone) E seus cursos.
         */
        public function atualizarCoordenador($cdUsuario, $nome, $email, $telefone, $cursos = []) {
            try {
                // Converte o array de cursos [1, 4, 5] em uma string "1,4,5" (será NULL se vazio)
                $cursosString = !empty($cursos) ? implode(',', $cursos) : null;
                
                $this->Executar('atualizarCoordenador', [
                    'pCdUsuario' => $cdUsuario,
                    'pNome' => $nome,
                    'pEmail' => $email,
                    'pTelefone' => $telefone,
                    'pCursosIDs' => $cursosString 
                ]);
            } catch (\Throwable $th) {
                throw $th;
            }
        }

        /**
         * CHAVE: Cria um novo usuário (Professor ou Coordenador) e o associa a Turmas ou Cursos.
         * Esta é uma operação transacional.
         *
         * @param array $dadosUsuario Array com os dados (cd_usuario, nome, email, senha, telefone, tipo)
         * @param array $associacoes Array com os IDs de turmas (para Professor) ou cursos (para Coordenador)
         */
        public function criarUsuarioCompleto($dadosUsuario, $associacoes = []) {
            $this->iniciarTransacao(); // <-- INICIA A TRANSAÇÃO
            try {
                $tipoUsuario = $dadosUsuario['tipo']; // 'Professor' ou 'Coordenador'

                // 1. Cria o usuário - Chama a SP que lida com o tipo de usuário
                $this->Executar('criarProfessorCompleto', [ 
                    'pCdUsuario' => $dadosUsuario['cd_usuario'],
                    'pNome' => $dadosUsuario['nome'],
                    'pEmail' => $dadosUsuario['email'],
                    'pSenha' => $dadosUsuario['senha'],
                    'pTelefone' => $dadosUsuario['telefone'],
                    'pTipo' => $tipoUsuario 
                ]);
                
                $cdUsuario = $dadosUsuario['cd_usuario'];

                // 2. Associa o usuário
                if (!empty($associacoes)) {
                    if ($tipoUsuario === 'Professor') {
                        // Associa a TURMAS
                        foreach ($associacoes as $cdTurma) {
                            $this->ExecutarSQL(
                                'INSERT INTO usuarios_has_turmas (usuarios_cd_usuario, turmas_cd_turma) VALUES (:cd_usuario, :cd_turma)',
                                ['cd_usuario' => $cdUsuario, 'cd_turma' => $cdTurma]
                            );
                        }
                    } elseif ($tipoUsuario === 'Coordenador') {
                        // Associa a CURSOS
                        foreach ($associacoes as $cdCurso) {
                            $this->ExecutarSQL(
                                'INSERT INTO usuarios_has_cursos (usuarios_cd_usuario, cursos_cd_curso) VALUES (:cd_usuario, :cd_curso)',
                                ['cd_usuario' => $cdUsuario, 'cd_curso' => $cdCurso]
                            );
                        }
                    }
                }

                $this->commitTransacao(); // <-- SUCESSO: Confirma tudo

            } catch (\Throwable $th) {
                $this->rollbackTransacao(); // <-- FALHA: Desfaz tudo
                throw $th; 
            }
        }
        
        /**
         * Lista todos os usuários (para admin, por exemplo)
         */
        public function Listar() {
            try {
                $dados = $this->Consultar('listarUsuarios', []);
                return $dados;
            } catch (\Throwable $th) {
                throw $th;
            }
        }

        /**
         * Lista todos os usuários com o perfil de Professor e suas turmas associadas.
         */
        public function listarProfessores() {
            try {
                $dados = $this->Consultar('listarProfessoresComTurmas', []); // Chama a nova procedure
                return $dados;
            } catch (\Throwable $th) {
                throw $th;
            }
        }

        /**
         * Lista todos os usuários com o perfil de Coordenador.
         */
        public function listarCoordenadores() {
            try {
                // A SP listarCoordenadores agora retorna cd_usuario
                return $this->Consultar('listarCoordenadores', []);
            } catch (\Throwable $th) {
                throw $th;
            }
        }

        /**
         * Cria um novo usuário (para admin, por exemplo)
         */
        public function Criar($usuario) {
            try {
                $parametros = [
                    'pEmail' => $usuario->Email,
                    'pNome' => $usuario->Nome,
                    'pSenha' => $usuario->Senha,
                    'pTipo' => $usuario->Tipo
                ];
                $this->Executar('criarUsuario', $parametros);
            } catch (\Throwable $th) {
                throw $th;
            }
        }

        /**
         * Cria um novo professor e o associa às turmas selecionadas.
         * (Antiga função, agora adaptada para chamar a nova)
         */
        public function criarProfessor($dadosProfessor, $turmas = []) {
            // Adaptação para a nova função unificada
            $dadosProfessor['tipo'] = 'Professor';
            return $this->criarUsuarioCompleto($dadosProfessor, $turmas);
        }

        /**
         * Exclui um usuário (para admin)
         */
        public function Excluir($email) {
            try {
                $parametros = ['pEmail' => $email];
                $this->Executar('excluirUsuario', $parametros);
            } catch (\Throwable $th) {
                throw $th;
            }
        }

        /**
         * Verifica o login de um usuário
         */
        public function VerificarAcesso($login, $senha) {
            try {
                $parametros = [
                    'pLogin' => $login,
                    'pSenha' => $senha
                ];
                $dados = $this->Consultar('verificarAcesso', $parametros);
                return $dados;
            } catch (\Throwable $th) {
                throw new Exception('Login e/ou Senha Inválida');
            }
        }

        /**
         * Busca os dados de um usuário para a página de perfil.
         */
        public function buscarDadosUsuario($cdUsuario) {
            try {
                $dados = $this->Consultar('buscarDadosUsuario', ['pCdUsuario' => $cdUsuario]);
                if (count($dados) > 0) {
                    return $dados[0]; // Retorna o usuário
                }
                return null;
            } catch (\Throwable $th) {
                throw $th;
            }
        }

        /**
         * Atualiza os dados do perfil do usuário (Nome e Telefone).
         * Esta é a única versão da função que deve existir.
         */
        public function atualizarDados($cdUsuario, $nome, $telefone) {
            try {
                $this->Executar('atualizarDadosUsuario', [
                    'pCdUsuario' => $cdUsuario,
                    'pNome' => $nome,
                    'pTelefone' => $telefone
                ]);
            } catch (\Throwable $th) {
                throw $th;
            }
        }

        /**
         * Lista a relação de professores e suas turmas para o formulário de eventos.
         */
        public function listarRelacaoProfessorTurma() {
            try {
                return $this->Consultar('listarRelacaoProfessorTurma');
            } catch (\Throwable $th) {
                throw $th;
            }
        }

        /**
         * Altera a senha de um usuário após verificar a senha antiga.
         */
        public function mudarSenha($cdUsuario, $senhaAntiga, $senhaNova) {
            try {
                $this->Executar('mudarSenha', [
                    'pCdUsuario' => $cdUsuario,
                    'pSenhaAntiga' => $senhaAntiga,
                    'pSenhaNova' => $senhaNova
                ]);
            } catch (\Throwable $th) {
                // Joga o erro (ex: "A senha atual está incorreta.") para a página
                throw $th;
            }
        }

        /**
         * Atualiza os dados de um professor (Nome, Email, Telefone) E as suas turmas.
         * @param string $cdUsuario O RM do professor a ser atualizado.
         * @param string $nome O novo nome.
         * @param string $email O novo email.
         * @param string $telefone O novo telefone.
         * @param array $turmas A lista de IDs de turmas para associar.
         */
        public function atualizarProfessor($cdUsuario, $nome, $email, $telefone, $turmas = []) {
            try {
                // Converte o array de turmas [1, 4, 5] em uma string "1,4,5"
                // Se o array estiver vazio, envia NULL
                $turmasString = !empty($turmas) ? implode(',', $turmas) : null;

                $this->Executar('atualizarProfessor', [
                    'pCdUsuario' => $cdUsuario,
                    'pNome' => $nome,
                    'pEmail' => $email,
                    'pTelefone' => $telefone,
                    'pTurmasIDs' => $turmasString // Envia a nova string para a procedure
                ]);
            } catch (\Throwable $th) {
                // Joga o erro (ex: "Email já em uso") para a API tratar
                throw $th;
            }
        }

        /**
         * Exclui um professor do sistema.
         * @param string $cdUsuario O RM do professor a ser excluído.
         */
        public function excluirProfessor($cdUsuario) {
            try {
                $this->Executar('excluirProfessor', [
                    'pCdUsuario' => $cdUsuario
                ]);
            } catch (\Throwable $th) {
                // Joga o erro (ex: "Professor não pode ser excluído pois é solicitante de eventos")
                throw $th;
            }
        }

        /**
         * Lista os nomes de todos os professores associados a um ID de turma específico.
         *
         * @param int $cdTurma O ID da turma.
         * @return array Lista de professores (apenas nomes).
         */
        public function listarProfessoresPorTurma($cdTurma) {
            try {
                // Chama a Stored Procedure que criamos no Passo 1
                $dados = $this->Consultar('listarProfessoresPorTurma', [
                    'pCdTurma' => $cdTurma
                ]);
                return $dados;
            } catch (\Throwable $th) {
                // Se der erro, joga a exceção
                throw $th;
            }
        }

    }
?>