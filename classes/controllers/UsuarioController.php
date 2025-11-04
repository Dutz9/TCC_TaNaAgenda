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

    }
?>