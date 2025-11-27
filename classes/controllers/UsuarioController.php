<?php

    class UsuarioController extends Banco {

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
            // A Stored Procedure listarCoordenadores é a que está faltando no seu Controller
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
         * Esta é uma operação transacional.
         *
         * @param array $dadosProfessor Array com os dados (cd_usuario, nome, email, senha, telefone)
         * @param array $turmas Array com os IDs das turmas para associar
         */
        public function criarProfessor($dadosProfessor, $turmas = []) {
            $this->iniciarTransacao(); // <-- INICIA A TRANSAÇÃO
            try {
                // 1. Cria o usuário professor
                $this->Executar('criarProfessor', [
                    'pCdUsuario' => $dadosProfessor['cd_usuario'],
                    'pNome' => $dadosProfessor['nome'],
                    'pEmail' => $dadosProfessor['email'],
                    'pSenha' => $dadosProfessor['senha'],
                    'pTelefone' => $dadosProfessor['telefone']
                ]);
                
                // 2. Associa o professor às turmas selecionadas
                if (!empty($turmas)) {
                    foreach ($turmas as $cdTurma) {
                        $this->ExecutarSQL(
                            'INSERT INTO usuarios_has_turmas (usuarios_cd_usuario, turmas_cd_turma) VALUES (:cd_usuario, :cd_turma)',
                            [
                                'cd_usuario' => $dadosProfessor['cd_usuario'],
                                'cd_turma' => $cdTurma
                            ]
                        );
                    }
                }

                $this->commitTransacao(); // <-- SUCESSO: Confirma tudo

            } catch (\Throwable $th) {
                $this->rollbackTransacao(); // <-- FALHA: Desfaz tudo
                // Joga o erro (ex: "RM já existe") para o formulário
                throw $th; 
            }
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