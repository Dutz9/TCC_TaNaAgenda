<?php 
    // 1. Gire a chave: Carrega o autoloader e inicia a sessão (config.php)
    require_once '../api/config.php'; 

    // 2. Chame o guardião: Ele verifica a sessão E cria a variável $usuario_logado para nós.
    require_once '../api/verifica_sessao.php'; 

    // 1. GARANTE QUE É ADMINISTRADOR
    if ($usuario_logado['tipo_usuario_ic_usuario'] !== 'Administrador') {
        header('Location: ../tela_prof/agendaprof.php');
        exit();
    }

    $mensagem = '';
    $tipo_mensagem = '';
    $usuarioController = new UsuarioController();
    $turmaController = new TurmaController();
    $cursoController = new CursoController();

    // --- CARREGAMENTO DE DADOS PARA OS SELECTS ---
    $lista_turmas = $turmaController->listar();
    $lista_cursos = $cursoController->listar();

    // --- PROCESSAMENTO DO FORMULÁRIO (POST) ---
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        try {
            // CHAVE: Captura os dados com os nomes corretos do formulário
            $rm = $_POST['rm'] ?? null;
            $nome = $_POST['nome'] ?? null;
            $email = $_POST['email'] ?? null;
            $telefone = $_POST['telefone'] ?? null;
            $senha = $_POST['senha'] ?? null;
            $confirma_senha = $_POST['confirma_senha'] ?? null;
            $cargo = $_POST['cargo'] ?? null; // 'Professor' ou 'Coordenador'
            
            // Lógica para determinar a associação e quais dados pegar
            $associacoes = [];
            
            // CHAVE: O nome do campo de associação é 'associacoes[]' no JS
            if ($cargo === 'Professor' || $cargo === 'Coordenador') {
                $associacoes = $_POST['associacoes'] ?? []; 
            }
            
            // Mapeamento do cargo
            $tipo_db = 'Professor'; 
            if ($cargo === 'Coordenador') {
                $tipo_db = 'Coordenador';
            }
            
            // --- Validações ---
            if (empty($rm) || empty($nome) || empty($email) || empty($senha) || empty($confirma_senha)) {
                throw new Exception("Os campos obrigatórios (RM, Nome, Email, Senha e Cargo) não foram preenchidos.");
            }
            if ($senha !== $confirma_senha) {
                throw new Exception("As senhas não coincidem.");
            }
            if (strlen($senha) < 3) {
                throw new Exception("A senha deve ter pelo menos 3 caracteres.");
            }
            
            $dadosUsuario = [
                'cd_usuario' => $rm,
                'nome' => $nome,
                'email' => $email,
                'senha' => $senha,
                'telefone' => $telefone,
                'tipo' => $tipo_db // O tipo final que será salvo no banco
            ];

            // CHAVE: Chama a nova função unificada
            $usuarioController->criarUsuarioCompleto($dadosUsuario, $associacoes);
            
            $_SESSION['mensagem_sucesso'] = "Funcionário '".htmlspecialchars($nome)."' adicionado como {$tipo_db} com sucesso!";
            header('Location: professoresadm.php');
            exit();

        } catch (Exception $e) {
            $erro = $e->getMessage();
            if (strpos($erro, 'Erro: Este RM já está cadastrado.') !== false) {
                $mensagem = "Erro: Este RM já está cadastrado.";
            } elseif (strpos($erro, 'Erro: Este e-mail já está em uso.') !== false) {
                $mensagem = "Erro: Este e-mail já está em uso.";
            } else {
                $mensagem = $erro;
            }
            $tipo_mensagem = 'erro';
        }
    }
?>

<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js" defer></script>
<script>
    const listaTurmas = <?php echo json_encode($lista_turmas); ?>;
    const listaCursos = <?php echo json_encode($lista_cursos); ?>;
</script>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Professor ou Coordenador - TáNaAgenda</title>
    <link id="favicon" rel="shortcut icon" href="../image/Favicon-light.png">
    <link rel="stylesheet" href="../css/global.css">
    <!-- CHAVE: Usaremos criarevento.css para o layout de 2 colunas -->
    <link rel="stylesheet" href="../css/criarevento.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css"/>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
</head>
<body>
<script src="../js/favicon.js"></script>
    <header class="header">
        <a href="perfiladm.php">
            <p> <?php echo htmlspecialchars($usuario_logado['nm_usuario']); ?> </p>
        </a>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path fill="#ffffff" d="M320 312C386.3 312 440 258.3 440 192C440 125.7 386.3 72 320 72C253.7 72 200 125.7 200 192C200 258.3 253.7 312 320 312zM290.3 368C191.8 368 112 447.8 112 546.3C112 562.7 125.3 576 141.7 576L498.3 576C514.7 576 528 562.7 528 546.3C528 447.8 448.2 368 349.7 368L290.3 368z"/></svg>
    </header>

    <main>
    <section class="area-lado">
            <a class="area-lado-logo" href="agendaadm.php"><img src="../image/logotipo fundo azul.png" alt=""></a>
            <div class="area-menu">
                <div class="menu-agenda">
                <img src="../image/icones/agenda.png" alt="">
                    <a href="agendaadm.php"><p>Agenda</p></a>
                </div>
                <div class="menu-meus-eventos">
                <img src="../image/icones/eventos.png" alt="">
                    <a href="eventosadm.php"><p>Eventos</p></a>
                </div>
                <div class="menu-professores ativo">
                <img src="../image/icones/professores.png" alt="">
                    <a href="professoresadm.php"><p>Professores e Coordenadores</p></a>
                </div> 
                
                <div class="menu-cursos">
                <img src="../image/icones/cursos.png" alt="">
                    <a href="cursos.php"><p>Cursos</p></a>
                </div> 
                <div class="menu-turmas">
                <img src="../image/icones/turmas.png" alt="">
                    <a href="turmas.php"><p>Turmas</p></a>
                </div>
                <div class="menu-perfil">
                <img src="../image/icones/perfil.png" alt="">
                    <a href="perfiladm.php"><p>Perfil</p></a>
                </div>  
                <a href="../logout.php"><div class="menu-sair"><p>SAIR</p></div></a> 
            </div>
        </section>

        <!-- CHAVE 1: Adicione o wrapper conteudo-principal para herdar o layout base -->
        <div class="conteudo-principal">
            <!-- CHAVE 2: Use a classe formulario-evento para herdar as regras de linha e campo do criarevento.css -->
            <section class="formulario-evento"> 
                
                <h2>Adicionar Professor ou Coordenador</h2>
                
                <?php if (!empty($mensagem)): ?>
                    <div class="mensagem <?php echo $tipo_mensagem; ?>">
                        <?php echo htmlspecialchars($mensagem); ?>
                    </div>
                <?php endif; ?>

                <form action="addprofessoradm.php" method="POST">
                    <div class="linha-form">
                        <div  class="campo">
                            <label  for="nome">Nome do Professor/Coordenador:</label>
                            <!-- CHAVE: name="nome" -->
                            <input  type="text" id="nome" name="nome" placeholder="Nome" required>
                        </div>
                        <div  class="campo">
                            <label  for="rm">RM:</label>
                            <!-- CHAVE: name="rm" -->
                            <input  type="text" id="rm" name="rm" placeholder="RM do professor" required>
                        </div>
                    </div>

                    <div class="linha-form">
                        <div  class="campo">
                            <label  for="email">Email:</label>
                             <!-- CHAVE: name="email" -->
                            <input  type="text" id="email" name="email" placeholder="professor@gmail.com" required>
                        </div>

                        <div class="campo">
                            <label for="senha">Senha</label>
                            <!-- CHAVE: name="senha" -->
                            <input type="password" id="senha" name="senha" placeholder="Crie uma senha inicial de acesso" required>
                        </div>
                    </div>
                    
                    <div class="linha-form">
                        <div  class="campo">
                            <label  for="telefone">Telefone:</label>
                            <!-- CHAVE: name="telefone" -->
                            <input  type="text" id="telefone" name="telefone" placeholder="Telefone">
                        </div>

                        <div class="campo">
                            <label for="confirma_senha">Confirmar Senha:</label>
                            <!-- CHAVE: name="confirma_senha" -->
                            <input type="password" id="confirma_senha" name="confirma_senha" placeholder="Confirme a senha" required>
                        </div>
                    </div>

                    <div class="linha-form">
                        <div  class="campo">
                            <label  for="cargo">Cargo:</label>
                            <!-- CHAVE: name="cargo" -->
                            <select id="cargo" name="cargo" required>
                                <option value="" disabled selected>Cargo do funcionário</option>
                                <option value="Professor">Professor</option>
                                <option value="Coordenador">Coordenador</option>
                            </select>
                        </div>
                        
                        <!-- CAMPO DE ASSOCIAÇÃO DINÂMICO (CONTÊINER PRINCIPAL) -->
                        <div id="campo-associacao" class="campo">
                            <!-- O conteúdo será injetado pelo JavaScript -->
                            <label for="turmas-select">Associações</label>
                            <!-- Select de TURMAS (Invisível por padrão) -->
                            <!-- CHAVE: name="associacoes[]" para que apenas um seja enviado -->
                            <select id="turmas-select" name="associacoes[]" multiple style="display:none;"></select>
                             <!-- Select de CURSOS (Invisível por padrão) -->
                            <!-- CHAVE: name="associacoes[]" para que apenas um seja enviado -->
                            <select id="cursos-select" name="associacoes[]" multiple style="display:none;"></select>
                        </div>
                    </div>
                    
                    <div class="botoes">
                        <a href="professoresadm.php" class="botao-cancelar">Cancelar</a>
                        <button type="submit" class="botao-enviar">Adicionar Funcionário</button>
                    </div>
                </form>
            </section>
        </div>
    </main>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- Variáveis Globais de Dados ---
        const listaTurmas = <?php echo json_encode($lista_turmas); ?>;
        const listaCursos = <?php echo json_encode($lista_cursos); ?>;

        // --- Elementos do Formulário ---
        const cargoSelect = document.getElementById('cargo');
        const campoAssociacao = document.getElementById('campo-associacao');
        const labelAssociacao = campoAssociacao.querySelector('label');
        const turmasSelect = document.getElementById('turmas-select');
        const cursosSelect = document.getElementById('cursos-select');
        
        let choicesTurmas = null;
        let choicesCursos = null;

        /**
         * Inicializa a biblioteca Choices.js em um select.
         * @param {HTMLElement} selectElement - O elemento <select>
         * @param {Array} dataArray - O array de objetos (cursos ou turmas)
         * @param {string} idKey - A chave do ID ('cd_turma' ou 'cd_curso')
         * @param {string} labelKey - A chave do nome ('nm_turma' ou 'nm_curso')
         * @returns {Choices} Instância do Choices.js
         */
        function inicializarChoices(selectElement, dataArray, idKey, labelKey) {
            // 1. Destruição do Choices.js anterior (se houver)
            if (selectElement.classList.contains('choices__input')) {
                // Remove o container Choices anterior manualmente para evitar bugs
                const parent = selectElement.parentNode;
                const choicesWrapper = parent.querySelector('.choices');
                if(choicesWrapper) {
                    parent.removeChild(choicesWrapper);
                    parent.appendChild(selectElement); // Reinsere o SELECT original
                }
            }
            
            // 2. Limpa o select antes de preencher
            selectElement.innerHTML = ''; 
            
            const options = dataArray.map(item => ({
                value: String(item[idKey]),
                label: item[labelKey]
            }));

            // Adiciona as opções antes de instanciar
            options.forEach(opt => {
                const optionElement = document.createElement('option');
                optionElement.value = opt.value;
                optionElement.textContent = opt.label;
                selectElement.appendChild(optionElement);
            });

            // 3. Inicializa a instância
            const choicesInstance = new Choices(selectElement, {
                removeItemButton: true,
                placeholder: true,
                placeholderValue: 'Selecione as associações...',
                searchEnabled: true,
                allowHTML: false
            });
            
            return choicesInstance;
        }

        /**
         * Lógica principal para mostrar/ocultar e inicializar o select correto.
         */
        function atualizarAssociacoes() {
            const cargo = cargoSelect.value;
            
            // 1. Destroi instâncias Choices ativas para liberar recursos (e limpa os SELECTs)
            if (choicesTurmas) { choicesTurmas.destroy(); choicesTurmas = null; turmasSelect.innerHTML = ''; }
            if (choicesCursos) { choicesCursos.destroy(); choicesCursos = null; cursosSelect.innerHTML = ''; }

            // 2. Esconde os selects e remove o atributo 'name' para evitar envio de ambos
            turmasSelect.style.display = 'none';
            cursosSelect.style.display = 'none';
            turmasSelect.removeAttribute('name');
            cursosSelect.removeAttribute('name');


            // 3. Lógica para Professor
            if (cargo === 'Professor') {
                labelAssociacao.textContent = 'Turmas Associadas:';
                turmasSelect.style.display = 'block';
                // CHAVE: Adiciona o atributo name de volta
                turmasSelect.setAttribute('name', 'associacoes[]');
                
                choicesTurmas = inicializarChoices(turmasSelect, listaTurmas, 'cd_turma', 'nm_turma');

            // 4. Lógica para Coordenador
            } else if (cargo === 'Coordenador') {
                labelAssociacao.textContent = 'Cursos Coordenados:';
                cursosSelect.style.display = 'block';
                // CHAVE: Adiciona o atributo name de volta
                cursosSelect.setAttribute('name', 'associacoes[]'); 
                
                choicesCursos = inicializarChoices(cursosSelect, listaCursos, 'cd_curso', 'nm_curso');
            } else {
                // Se nenhum cargo válido for selecionado
                labelAssociacao.textContent = '';
            }
        }

        // --- Event Listeners ---
        cargoSelect.addEventListener('change', atualizarAssociacoes);
        
        // Inicializa o dropdown ao carregar a página
        atualizarAssociacoes();
    });
    </script>
</body>
</html>