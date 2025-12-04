<?php

    class UsuarioController extends Banco {

        public function listarFuncionariosComAssociacoes() {
            try {
                return $this->Consultar('listarFuncionariosComAssociacoes', []);
            } catch (\Throwable $th) {
                throw $th;
            }
        }
        

        public function atualizarCoordenador($cdUsuario, $nome, $email, $telefone, $cursos = []) {
            try {
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
         * 
         * 
         *
         * @param array $dadosUsuario 
         * @param array $associacoes 
         */
        public function criarUsuarioCompleto($dadosUsuario, $associacoes = []) {
            $this->iniciarTransacao();
            try {
                $tipoUsuario = $dadosUsuario['tipo'];

                $this->Executar('criarProfessorCompleto', [ 
                    'pCdUsuario' => $dadosUsuario['cd_usuario'],
                    'pNome' => $dadosUsuario['nome'],
                    'pEmail' => $dadosUsuario['email'],
                    'pSenha' => $dadosUsuario['senha'],
                    'pTelefone' => $dadosUsuario['telefone'],
                    'pTipo' => $tipoUsuario 
                ]);
                
                $cdUsuario = $dadosUsuario['cd_usuario'];

                if (!empty($associacoes)) {
                    if ($tipoUsuario === 'Professor') {
                        foreach ($associacoes as $cdTurma) {
                            $this->ExecutarSQL(
                                'INSERT INTO usuarios_has_turmas (usuarios_cd_usuario, turmas_cd_turma) VALUES (:cd_usuario, :cd_turma)',
                                ['cd_usuario' => $cdUsuario, 'cd_turma' => $cdTurma]
                            );
                        }
                    } elseif ($tipoUsuario === 'Coordenador') {
                        foreach ($associacoes as $cdCurso) {
                            $this->ExecutarSQL(
                                'INSERT INTO usuarios_has_cursos (usuarios_cd_usuario, cursos_cd_curso) VALUES (:cd_usuario, :cd_curso)',
                                ['cd_usuario' => $cdUsuario, 'cd_curso' => $cdCurso]
                            );
                        }
                    }
                }

                $this->commitTransacao();

            } catch (\Throwable $th) {
                $this->rollbackTransacao();
                throw $th; 
            }
        }
        
        public function Listar() {
            try {
                $dados = $this->Consultar('listarUsuarios', []);
                return $dados;
            } catch (\Throwable $th) {
                throw $th;
            }
        }

        public function listarProfessores() {
            try {
                $dados = $this->Consultar('listarProfessoresComTurmas', []);
                return $dados;
            } catch (\Throwable $th) {
                throw $th;
            }
        }

        public function listarCoordenadores() {
            try {

                return $this->Consultar('listarCoordenadores', []);
            } catch (\Throwable $th) {
                throw $th;
            }
        }


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


        public function criarProfessor($dadosProfessor, $turmas = []) {

            $dadosProfessor['tipo'] = 'Professor';
            return $this->criarUsuarioCompleto($dadosProfessor, $turmas);
        }


        public function Excluir($email) {
            try {
                $parametros = ['pEmail' => $email];
                $this->Executar('excluirUsuario', $parametros);
            } catch (\Throwable $th) {
                throw $th;
            }
        }


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


        public function buscarDadosUsuario($cdUsuario) {
            try {
                $dados = $this->Consultar('buscarDadosUsuario', ['pCdUsuario' => $cdUsuario]);
                if (count($dados) > 0) {
                    return $dados[0];
                }
                return null;
            } catch (\Throwable $th) {
                throw $th;
            }
        }


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


        public function listarRelacaoProfessorTurma() {
            try {
                return $this->Consultar('listarRelacaoProfessorTurma');
            } catch (\Throwable $th) {
                throw $th;
            }
        }


        public function mudarSenha($cdUsuario, $senhaAntiga, $senhaNova) {
            try {
                $this->Executar('mudarSenha', [
                    'pCdUsuario' => $cdUsuario,
                    'pSenhaAntiga' => $senhaAntiga,
                    'pSenhaNova' => $senhaNova
                ]);
            } catch (\Throwable $th) {

                throw $th;
            }
        }

        /**
         *
         * @param string $cdUsuario
         * @param string $nome
         * @param string $email
         * @param string $telefone
         * @param array $turmas
         */
        public function atualizarProfessor($cdUsuario, $nome, $email, $telefone, $turmas = []) {
            try {

                $turmasString = !empty($turmas) ? implode(',', $turmas) : null;

                $this->Executar('atualizarProfessor', [
                    'pCdUsuario' => $cdUsuario,
                    'pNome' => $nome,
                    'pEmail' => $email,
                    'pTelefone' => $telefone,
                    'pTurmasIDs' => $turmasString 
                ]);
            } catch (\Throwable $th) {
                throw $th;
            }
        }

        /**
         * 
         * @param string $cdUsuario 
         */
        public function excluirProfessor($cdUsuario) {
            try {
                $this->Executar('excluirProfessor', [
                    'pCdUsuario' => $cdUsuario
                ]);
            } catch (\Throwable $th) {

                throw $th;
            }
        }

        /**
         * 
         *
         * @param int $cdTurma 
         * @return array 
         */
        public function listarProfessoresPorTurma($cdTurma) {
            try {
                $dados = $this->Consultar('listarProfessoresPorTurma', [
                    'pCdTurma' => $cdTurma
                ]);
                return $dados;
            } catch (\Throwable $th) {
                throw $th;
            }
        }

    }
?>