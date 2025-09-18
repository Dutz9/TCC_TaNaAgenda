-- Stored Procedures para TCC (Versão Corrigida)
-- Melhorias: Nomes/colunas atualizados para matching com schema (usuarios, cd_senha, etc.); 
-- senhas plain-text (comentado para futuro hash); novas procs para eventos/aprovações; 
-- tratamento de erros consistente; listagem de coordenadores adicionada; comentários em todas.

DELIMITER $$

-- Procedure para listar todos os usuários (corrigida: nomes de tabelas/colunas atualizados)
DROP PROCEDURE IF EXISTS listarUsuarios$$
CREATE PROCEDURE listarUsuarios()
BEGIN
    -- Lista email, nome, tipo (usa enum direto da tabela usuarios para simplicidade)
    SELECT u.nm_email, u.nm_usuario, u.tipo_usuario_ic_usuario AS tipo_usuario
    FROM usuarios u
    ORDER BY u.nm_usuario;
END$$

-- Procedure para listar professores (corrigida: filtro por 'Professor')
DROP PROCEDURE IF EXISTS listarProfessores$$
CREATE PROCEDURE listarProfessores()
BEGIN
    SELECT u.nm_email, u.nm_usuario, u.tipo_usuario_ic_usuario AS tipo_usuario
    FROM usuarios u
    WHERE u.tipo_usuario_ic_usuario = 'Professor'
    ORDER BY u.nm_usuario;
END$$

-- Procedure para listar coordenadores (nova: similar a profs, mas para coords)
DROP PROCEDURE IF EXISTS listarCoordenadores$$
CREATE PROCEDURE listarCoordenadores()
BEGIN
    SELECT u.nm_email, u.nm_usuario, u.tipo_usuario_ic_usuario AS tipo_usuario
    FROM usuarios u
    WHERE u.tipo_usuario_ic_usuario = 'Coordenador'
    ORDER BY u.nm_usuario;
END$$

-- Procedure para criar usuário (corrigida: usa email para check duplicata; gera cd_usuario sequencial simples baseado em tipo)
DROP PROCEDURE IF EXISTS criarUsuario$$
CREATE PROCEDURE criarUsuario(
    IN pEmail VARCHAR(45), 
    IN pNome VARCHAR(45), 
    IN pSenha VARCHAR(45),  -- Plain-text; futuro: use password_hash
    IN pTipo ENUM('Coordenador', 'Professor', 'Administrador')
)
BEGIN
    DECLARE qtd INT DEFAULT 0;
    DECLARE novoCdUsuario VARCHAR(45) DEFAULT '';
        
    SELECT COUNT(*) INTO qtd FROM usuarios WHERE nm_email = pEmail;
    IF (qtd = 0) THEN
        -- Gera cd_usuario simples: tipo + sequencial (ex.: PROF001)
        SET novoCdUsuario = CASE pTipo
            WHEN 'Professor' THEN CONCAT('PROF', LPAD((SELECT COUNT(*) + 1 FROM usuarios WHERE tipo_usuario_ic_usuario = 'Professor'), 3, '0'))
            WHEN 'Coordenador' THEN CONCAT('COORD', LPAD((SELECT COUNT(*) + 1 FROM usuarios WHERE tipo_usuario_ic_usuario = 'Coordenador'), 3, '0'))
            ELSE CONCAT('ADMIN', LPAD((SELECT COUNT(*) + 1 FROM usuarios WHERE tipo_usuario_ic_usuario = 'Administrador'), 3, '0'))
        END;
        INSERT INTO usuarios (cd_usuario, nm_usuario, cd_senha, nm_email, tipo_usuario_ic_usuario) 
        VALUES (novoCdUsuario, pNome, pSenha, pEmail, pTipo);
    ELSE
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'E-mail já cadastrado';
    END IF;
END$$

-- Procedure para atualizar dados de usuário (corrigida: usa email para identificar)
DROP PROCEDURE IF EXISTS atualizaDadosUsuario$$
CREATE PROCEDURE atualizaDadosUsuario(
    IN pEmail VARCHAR(45), 
    IN pNome VARCHAR(45), 
    IN pSenha VARCHAR(45)  -- Plain-text
)
BEGIN
    DECLARE qtd INT DEFAULT 0;
    
    SELECT COUNT(*) INTO qtd FROM usuarios WHERE nm_email = pEmail;
    IF (qtd = 0) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Usuário não encontrado!';
    ELSE
        UPDATE usuarios SET nm_usuario = pNome, cd_senha = pSenha
        WHERE nm_email = pEmail;
    END IF;
END$$

-- Procedure para excluir usuário (corrigida: usa email)
DROP PROCEDURE IF EXISTS excluirUsuario$$
CREATE PROCEDURE excluirUsuario(IN pEmail VARCHAR(45))
BEGIN
    DECLARE qtd INT DEFAULT 0;
    
    SELECT COUNT(*) INTO qtd FROM usuarios WHERE nm_email = pEmail;
    IF (qtd = 0) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Usuário não encontrado!';
    ELSE
        DELETE FROM usuarios WHERE nm_email = pEmail;
    END IF;
END$$

-- Procedure para verificar acesso/login (corrigida: plain-text; retorna cd_usuario etc. para sessão)
DROP PROCEDURE IF EXISTS verificarAcesso$$
CREATE PROCEDURE verificarAcesso(
    IN pLogin VARCHAR(45), 
    IN pSenha VARCHAR(45)  -- Plain-text; futuro: compare com password_verify
)
BEGIN
    DECLARE qtd INT DEFAULT 0;
    
    SELECT COUNT(*) INTO qtd FROM usuarios 
    WHERE nm_email = pLogin AND cd_senha = pSenha;
        
    IF (qtd = 0) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Login e/ou Senha inválidos';
    ELSE
        SELECT u.cd_usuario, u.nm_usuario, u.nm_email, u.tipo_usuario_ic_usuario
        FROM usuarios u
        WHERE u.nm_email = pLogin AND u.cd_senha = pSenha;
    END IF;
END$$

-- Procedure para criar evento (nova: salva com status 'Solicitado')
DROP PROCEDURE IF EXISTS criarEvento$$
CREATE PROCEDURE criarEvento(
    IN pCdEvento VARCHAR(45),
    IN pDtEvento DATE,
    IN pNmEvento VARCHAR(45),
    IN pHorarioInicio VARCHAR(45),
    IN pHorarioFim VARCHAR(45),
    IN pTipoEvento ENUM('Palestra', 'Visita tecnica', 'Reuniao'),
    IN pDsDescricao VARCHAR(200),
    IN pCdUsuarioSolicitante VARCHAR(45)
)
BEGIN
    DECLARE qtd INT DEFAULT 0;
    
    SELECT COUNT(*) INTO qtd FROM eventos WHERE cd_evento = pCdEvento;
    IF (qtd > 0) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Código de evento já existe';
    ELSE
        INSERT INTO eventos (cd_evento, dt_evento, nm_evento, horario_inicio, horario_fim, tipo_evento, ds_descricao, status, cd_usuario_solicitante, dt_solicitacao)
        VALUES (pCdEvento, pDtEvento, pNmEvento, pHorarioInicio, pHorarioFim, pTipoEvento, pDsDescricao, 'Solicitado', pCdUsuarioSolicitante, CURRENT_DATE());
    END IF;
END$$

-- Procedure para aprovar evento (nova: atualiza status para coord)
DROP PROCEDURE IF EXISTS aprovarEvento$$
CREATE PROCEDURE aprovarEvento(IN pCdEvento VARCHAR(45))
BEGIN
    DECLARE qtd INT DEFAULT 0;
    
    SELECT COUNT(*) INTO qtd FROM eventos WHERE cd_evento = pCdEvento AND status = 'Solicitado';
    IF (qtd = 0) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Evento não encontrado ou não pendente';
    ELSE
        UPDATE eventos SET status = 'Aprovado' WHERE cd_evento = pCdEvento;
    END IF;
END$$

-- Procedure para recusar evento (nova: similar a aprovar)
DROP PROCEDURE IF EXISTS recusarEvento$$
CREATE PROCEDURE recusarEvento(IN pCdEvento VARCHAR(45))
BEGIN
    DECLARE qtd INT DEFAULT 0;
    
    SELECT COUNT(*) INTO qtd FROM eventos WHERE cd_evento = pCdEvento AND status = 'Solicitado';
    IF (qtd = 0) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Evento não encontrado ou não pendente';
    ELSE
        UPDATE eventos SET status = 'Recusado' WHERE cd_evento = pCdEvento;
    END IF;
END$$

-- Procedure para registrar aprovação de professor (nova: para fluxo paralelo, via N:N)
DROP PROCEDURE IF EXISTS registrarAprovacaoProfessor$$
CREATE PROCEDURE registrarAprovacaoProfessor(
    IN pCdEvento VARCHAR(45),
    IN pCdUsuario VARCHAR(45),
    IN pStatus ENUM('Aprovado', 'Recusado')
)
BEGIN
    DECLARE qtd INT DEFAULT 0;
    
    SELECT COUNT(*) INTO qtd FROM resolucao_eventos_usuarios WHERE eventos_cd_evento = pCdEvento AND usuarios_cd_usuario = pCdUsuario;
    IF (qtd > 0) THEN
        UPDATE resolucao_eventos_usuarios SET status_resolucao = pStatus WHERE eventos_cd_evento = pCdEvento AND usuarios_cd_usuario = pCdUsuario;
    ELSE
        INSERT INTO resolucao_eventos_usuarios (eventos_cd_evento, usuarios_cd_usuario, status_resolucao) VALUES (pCdEvento, pCdUsuario, pStatus);
    END IF;
END$$

-- Procedure para listar eventos solicitados (nova: para coord ver pendentes)
DROP PROCEDURE IF EXISTS listarEventosSolicitados$$
CREATE PROCEDURE listarEventosSolicitados()
BEGIN
    SELECT e.cd_evento, e.nm_evento, e.dt_evento, e.horario_inicio, e.horario_fim, e.tipo_evento, e.ds_descricao,
           u.nm_usuario AS solicitante, e.dt_solicitacao
    FROM eventos e
    JOIN usuarios u ON e.cd_usuario_solicitante = u.cd_usuario
    WHERE e.status = 'Solicitado'
    ORDER BY e.dt_solicitacao DESC;
END$$

-- Procedure para listar meus eventos (nova: para prof ver seus eventos por status)
DROP PROCEDURE IF EXISTS listarMeusEventos$$
CREATE PROCEDURE listarMeusEventos(IN pCdUsuario VARCHAR(45))
BEGIN
    SELECT e.cd_evento, e.nm_evento, e.dt_evento, e.horario_inicio, e.horario_fim, e.tipo_evento, e.ds_descricao,
           e.status, e.dt_solicitacao
    FROM eventos e
    WHERE e.cd_usuario_solicitante = pCdUsuario
    ORDER BY e.dt_solicitacao DESC;
END$$

DELIMITER ;