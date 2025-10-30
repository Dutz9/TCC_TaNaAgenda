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
    IN pSenha VARCHAR(255)  -- Plain-text; futuro: compare com password_verify
)
BEGIN
    DECLARE qtd INT DEFAULT 0;
    
    -- A MUDANÇA ESTÁ AQUI, NO "OR"
    SELECT COUNT(*) INTO qtd FROM usuarios 
    WHERE (nm_email = pLogin OR cd_usuario = pLogin) AND cd_senha = pSenha;
        
    IF (qtd = 0) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Login e/ou Senha inválidos';
    ELSE
        SELECT u.cd_usuario, u.nm_usuario, u.nm_email, u.tipo_usuario_ic_usuario
        FROM usuarios u
        WHERE (nm_email = pLogin OR cd_usuario = pLogin) AND cd_senha = pSenha;
    END IF;
END$$

-- Procedure para criar evento (nova: salva com status 'Solicitado')
-- Procedure para criar evento (VERSÃO CORRIGIDA SEM CARACTERES INVÁLIDOS)
DROP PROCEDURE IF EXISTS `criarEvento`$$
CREATE PROCEDURE `criarEvento`(
    IN pCdEvento VARCHAR(45),
    IN pDtEvento DATE,
    IN pNmEvento VARCHAR(45),
    IN pHorarioInicio VARCHAR(45),
    IN pHorarioFim VARCHAR(45),
    IN pTipoEvento ENUM('Palestra', 'Visita tecnica', 'Reuniao', 'Prova', 'Conselho de Classe', 'Evento Esportivo', 'Outro'),
    IN pDsDescricao TEXT, -- Mudança de VARCHAR(200) para TEXT
    IN pCdUsuarioSolicitante VARCHAR(45)
)
BEGIN
    INSERT INTO eventos (cd_evento, dt_evento, nm_evento, horario_inicio, horario_fim, tipo_evento, ds_descricao, status, cd_usuario_solicitante, dt_solicitacao)
    VALUES (pCdEvento, pDtEvento, pNmEvento, pHorarioInicio, pHorarioFim, pTipoEvento, pDsDescricao, 'Solicitado', pCdUsuarioSolicitante, CURDATE());
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

-- 1. Garante que a TABELA aceita 'Pendente' (pode já ter sido feito)
ALTER TABLE resolucao_eventos_usuarios 
MODIFY COLUMN status_resolucao ENUM('Aprovado', 'Recusado', 'Pendente') NULL DEFAULT 'Pendente';

-- 2. Atualiza a PROCEDURE para aceitar 'Pendente'
DROP PROCEDURE IF EXISTS `registrarAprovacaoProfessor`$$
CREATE PROCEDURE `registrarAprovacaoProfessor`(
    IN pCdEvento VARCHAR(45),
    IN pCdUsuario VARCHAR(45),
    IN pStatus ENUM('Aprovado', 'Recusado', 'Pendente') -- <-- CORREÇÃO AQUI
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

DROP PROCEDURE IF EXISTS `listarEventosAprovados`$$
CREATE PROCEDURE `listarEventosAprovados`(
    IN pDataInicio DATE,
    IN pDataFim DATE,
    -- Os parâmetros agora são VARCHAR para aceitar listas (ex: "Manha,Tarde")
    IN pPeriodo VARCHAR(100),
    IN pCdTurma VARCHAR(255),
    IN pTipoEvento VARCHAR(255)
)
BEGIN
    SELECT
        e.cd_evento, e.nm_evento, e.dt_evento, e.horario_inicio, e.horario_fim, e.tipo_evento, e.ds_descricao,
        solicitante.tipo_usuario_ic_usuario AS tipo_solicitante,
        (SELECT GROUP_CONCAT(t.nm_turma SEPARATOR ', ') 
         FROM eventos_has_turmas eht 
         JOIN turmas t ON eht.turmas_cd_turma = t.cd_turma 
         WHERE eht.eventos_cd_evento = e.cd_evento) AS turmas_envolvidas,
        (SELECT GROUP_CONCAT(DISTINCT u.nm_usuario SEPARATOR ', ') 
         FROM resolucao_eventos_usuarios reu
         JOIN usuarios u ON reu.usuarios_cd_usuario = u.cd_usuario
         WHERE reu.eventos_cd_evento = e.cd_evento) AS professores_envolvidos
        
    FROM eventos e
    JOIN usuarios solicitante ON e.cd_usuario_solicitante = solicitante.cd_usuario
    LEFT JOIN eventos_has_turmas eht_filtro ON e.cd_evento = eht_filtro.eventos_cd_evento
    
    WHERE 
        e.status = 'Aprovado'
        AND e.dt_evento BETWEEN pDataInicio AND pDataFim
        
        -- NOVOS FILTROS (agora usando FIND_IN_SET)
        AND (pTipoEvento IS NULL OR FIND_IN_SET(e.tipo_evento, pTipoEvento))
        AND (pCdTurma IS NULL OR FIND_IN_SET(eht_filtro.turmas_cd_turma, pCdTurma))
        
        -- Filtro de Período com lógica FIND_IN_SET
        AND (pPeriodo IS NULL
            OR (FIND_IN_SET('Manha', pPeriodo) AND e.horario_inicio < '13:00:00')
            OR (FIND_IN_SET('Tarde', pPeriodo) AND e.horario_inicio >= '13:00:00' AND e.horario_inicio < '18:30:00')
            OR (FIND_IN_SET('Noite', pPeriodo) AND e.horario_inicio >= '18:30:00')
        )
        
    GROUP BY e.cd_evento
    ORDER BY e.dt_evento, e.horario_inicio;
END$$

DROP PROCEDURE IF EXISTS `listarTurmas`$$
CREATE PROCEDURE `listarTurmas`()
BEGIN
    SELECT cd_turma, nm_turma, qt_alunos -- <-- ADICIONAMOS qt_alunos
    FROM turmas 
    ORDER BY nm_turma;
END$$

DROP PROCEDURE IF EXISTS `criarEventoAprovado`$$
CREATE PROCEDURE `criarEventoAprovado`(
    IN pCdEvento VARCHAR(45),
    IN pDtEvento DATE,
    IN pNmEvento VARCHAR(45),
    IN pHorarioInicio VARCHAR(45),
    IN pHorarioFim VARCHAR(45),
    IN pTipoEvento ENUM('Palestra', 'Visita tecnica', 'Reuniao', 'Prova', 'Conselho de Classe', 'Evento Esportivo', 'Outro'),
    IN pDsDescricao TEXT, -- Mudança de VARCHAR(200) para TEXT
    IN pCdUsuarioSolicitante VARCHAR(45)
)
BEGIN
    INSERT INTO eventos (cd_evento, dt_evento, nm_evento, horario_inicio, horario_fim, tipo_evento, ds_descricao, status, cd_usuario_solicitante, dt_solicitacao)
    VALUES (pCdEvento, pDtEvento, pNmEvento, pHorarioInicio, pHorarioFim, pTipoEvento, pDsDescricao, 'Aprovado', pCdUsuarioSolicitante, CURDATE());
END$$

DROP PROCEDURE IF EXISTS `listarEventosParaProfessor`$$
CREATE PROCEDURE `listarEventosParaProfessor`(
    IN pCdUsuario VARCHAR(25),
    IN pStatus ENUM('Solicitado', 'Aprovado', 'Recusado'),
    -- 1. ATUALIZAÇÃO AQUI: Mudamos 'Pendente' para 'OutrosProfessores'
    IN pSolicitante ENUM('Todos', 'Eu', 'OutrosProfessores', 'Coordenador'), 
    IN pCdTurma INT,
    IN pTipoEvento ENUM('Palestra', 'Visita Técnica', 'Reunião', 'Prova', 'Conselho de Classe', 'Evento Esportivo', 'Outro'),
    IN pDataFiltro ENUM('Todos', 'Proximos7Dias', 'EsteMes', 'MesPassado', 'ProximoMes')
)
BEGIN
    SELECT
        e.cd_evento, e.nm_evento, e.dt_evento, e.horario_inicio, e.horario_fim,
        e.ds_descricao, e.status, e.cd_usuario_solicitante, e.dt_solicitacao,
        solicitante.nm_usuario AS nm_solicitante,
        solicitante.tipo_usuario_ic_usuario AS tipo_solicitante,
        (SELECT GROUP_CONCAT(t_inner.nm_turma SEPARATOR ', ') FROM eventos_has_turmas eht_inner JOIN turmas t_inner ON eht_inner.turmas_cd_turma = t_inner.cd_turma WHERE eht_inner.eventos_cd_evento = e.cd_evento) AS turmas_envolvidas,
        (SELECT SUM(t_inner.qt_alunos) FROM eventos_has_turmas eht_inner JOIN turmas t_inner ON eht_inner.turmas_cd_turma = t_inner.cd_turma WHERE eht_inner.eventos_cd_evento = e.cd_evento) AS total_alunos,
        (SELECT CONCAT('[', GROUP_CONCAT(JSON_OBJECT('nome', u.nm_usuario, 'status', reu.status_resolucao)), ']') FROM resolucao_eventos_usuarios reu JOIN usuarios u ON reu.usuarios_cd_usuario = u.cd_usuario WHERE reu.eventos_cd_evento = e.cd_evento AND u.cd_usuario != e.cd_usuario_solicitante) AS respostas_professores,
        (SELECT status_resolucao FROM resolucao_eventos_usuarios WHERE eventos_cd_evento = e.cd_evento AND usuarios_cd_usuario = pCdUsuario) AS minha_resposta
        
    FROM eventos e
    JOIN usuarios solicitante ON e.cd_usuario_solicitante = solicitante.cd_usuario
    LEFT JOIN eventos_has_turmas eht_filtro ON e.cd_evento = eht_filtro.eventos_cd_evento
        
    WHERE 
        ( -- A regra de relevância principal está correta e não muda
            e.cd_usuario_solicitante = pCdUsuario 
            OR EXISTS (SELECT 1 FROM resolucao_eventos_usuarios reu_check WHERE reu_check.eventos_cd_evento = e.cd_evento AND reu_check.usuarios_cd_usuario = pCdUsuario)
            OR (solicitante.tipo_usuario_ic_usuario = 'Coordenador' AND EXISTS (SELECT 1 FROM eventos_has_turmas eht_check JOIN usuarios_has_turmas uht_check ON eht_check.turmas_cd_turma = uht_check.turmas_cd_turma WHERE eht_check.eventos_cd_evento = e.cd_evento AND uht_check.usuarios_cd_usuario = pCdUsuario))
        )
        AND (pStatus IS NULL OR e.status = pStatus)
        AND (pTipoEvento IS NULL OR e.tipo_evento = pTipoEvento)
        AND (pCdTurma IS NULL OR eht_filtro.turmas_cd_turma = pCdTurma)
        
        -- 2. LÓGICA DE FILTRO ATUALIZADA
        AND (pSolicitante IS NULL OR pSolicitante = 'Todos'
            OR (pSolicitante = 'Eu' AND e.cd_usuario_solicitante = pCdUsuario)
            -- Esta é a nova lógica:
            OR (pSolicitante = 'OutrosProfessores' 
                AND e.cd_usuario_solicitante != pCdUsuario
                AND solicitante.tipo_usuario_ic_usuario = 'Professor')
            OR (pSolicitante = 'Coordenador' 
                AND solicitante.tipo_usuario_ic_usuario = 'Coordenador')
        )
        
        AND (pDataFiltro IS NULL OR pDataFiltro = 'Todos' OR
            (pDataFiltro = 'Proximos7Dias' AND e.dt_evento BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)) OR
            (pDataFiltro = 'EsteMes' AND MONTH(e.dt_evento) = MONTH(CURDATE()) AND YEAR(e.dt_evento) = YEAR(CURDATE())) OR
            (pDataFiltro = 'MesPassado' AND MONTH(e.dt_evento) = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) AND YEAR(e.dt_evento) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))) OR
            (pDataFiltro = 'ProximoMes' AND MONTH(e.dt_evento) = MONTH(DATE_ADD(CURDATE(), INTERVAL 1 MONTH)) AND YEAR(e.dt_evento) = YEAR(DATE_ADD(CURDATE(), INTERVAL 1 MONTH)))
        )
        
    GROUP BY e.cd_evento
    ORDER BY dt_solicitacao DESC;

END$$

CREATE PROCEDURE `listarRelacaoProfessorTurma`()
BEGIN
    SELECT 
        uht.turmas_cd_turma,
        u.cd_usuario,
        u.nm_usuario
    FROM 
        usuarios_has_turmas uht
    JOIN 
        usuarios u ON uht.usuarios_cd_usuario = u.cd_usuario
    WHERE 
        u.tipo_usuario_ic_usuario = 'Professor';
END$$

DROP PROCEDURE IF EXISTS `listarEventosParaCoordenador`$$
CREATE PROCEDURE `listarEventosParaCoordenador`(
    IN pCdUsuario VARCHAR(25),
    IN pStatus ENUM('Solicitado', 'Aprovado', 'Recusado'),
    IN pSolicitante ENUM('Todos', 'Eu', 'Professores'), -- Lógica de filtro diferente aqui
    IN pCdTurma INT,
    IN pTipoEvento ENUM('Palestra', 'Visita Técnica', 'Reunião', 'Prova', 'Conselho de Classe', 'Evento Esportivo', 'Outro'),
    IN pDataFiltro ENUM('Todos', 'Proximos7Dias', 'EsteMes', 'MesPassado', 'ProximoMes')
)
BEGIN
    SELECT
        e.cd_evento, e.nm_evento, e.dt_evento, e.horario_inicio, e.horario_fim,
        e.ds_descricao, e.status, e.cd_usuario_solicitante, e.dt_solicitacao,
        solicitante.nm_usuario AS nm_solicitante,
        solicitante.tipo_usuario_ic_usuario AS tipo_solicitante,
        (SELECT GROUP_CONCAT(t_inner.nm_turma SEPARATOR ', ') FROM eventos_has_turmas eht_inner JOIN turmas t_inner ON eht_inner.turmas_cd_turma = t_inner.cd_turma WHERE eht_inner.eventos_cd_evento = e.cd_evento) AS turmas_envolvidas,
        (SELECT SUM(t_inner.qt_alunos) FROM eventos_has_turmas eht_inner JOIN turmas t_inner ON eht_inner.turmas_cd_turma = t_inner.cd_turma WHERE eht_inner.eventos_cd_evento = e.cd_evento) AS total_alunos,
        CASE 
            WHEN solicitante.tipo_usuario_ic_usuario = 'Professor' THEN
                (SELECT CONCAT('[', GROUP_CONCAT(DISTINCT JSON_OBJECT('nome', u.nm_usuario, 'status', reu.status_resolucao)), ']')
                 FROM resolucao_eventos_usuarios reu JOIN usuarios u ON reu.usuarios_cd_usuario = u.cd_usuario
                 WHERE reu.eventos_cd_evento = e.cd_evento AND u.cd_usuario != e.cd_usuario_solicitante)
            ELSE 
                (SELECT CONCAT('[', GROUP_CONCAT(DISTINCT JSON_OBJECT('nome', u.nm_usuario)), ']')
                 FROM resolucao_eventos_usuarios reu JOIN usuarios u ON reu.usuarios_cd_usuario = u.cd_usuario
                 WHERE reu.eventos_cd_evento = e.cd_evento)
        END AS respostas_professores
        
    FROM eventos e
    JOIN usuarios solicitante ON e.cd_usuario_solicitante = solicitante.cd_usuario
    LEFT JOIN eventos_has_turmas eht_filtro ON e.cd_evento = eht_filtro.eventos_cd_evento
        
    WHERE 
        -- 1. Regra de Relevância (O que eu posso ver)
        (
            e.status = 'Solicitado' -- Eventos pendentes de professores
            OR e.cd_usuario_solicitante = pCdUsuario -- Eventos que eu criei
            OR e.cd_usuario_aprovador = pCdUsuario -- Eventos que eu julguei
        )
        
        -- 2. FILTROS
        AND (pStatus IS NULL OR e.status = pStatus)
        AND (pTipoEvento IS NULL OR e.tipo_evento = pTipoEvento)
        AND (pCdTurma IS NULL OR eht_filtro.turmas_cd_turma = pCdTurma)
        
        -- Lógica do Filtro de Solicitante para o COORDENADOR
        AND (pSolicitante IS NULL OR pSolicitante = 'Todos'
            OR (pSolicitante = 'Eu' AND e.cd_usuario_solicitante = pCdUsuario)
            OR (pSolicitante = 'Professores' AND solicitante.tipo_usuario_ic_usuario = 'Professor')
        )
        
        -- Lógica de Data
        AND (pDataFiltro IS NULL OR pDataFiltro = 'Todos'
            OR (pDataFiltro = 'Proximos7Dias' AND e.dt_evento BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY))
            OR (pDataFiltro = 'EsteMes' AND MONTH(e.dt_evento) = MONTH(CURDATE()) AND YEAR(e.dt_evento) = YEAR(CURDATE()))
            OR (pDataFiltro = 'MesPassado' AND MONTH(e.dt_evento) = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) AND YEAR(e.dt_evento) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)))
            OR (pDataFiltro = 'ProximoMes' AND MONTH(e.dt_evento) = MONTH(DATE_ADD(CURDATE(), INTERVAL 1 MONTH)) AND YEAR(e.dt_evento) = YEAR(DATE_ADD(CURDATE(), INTERVAL 1 MONTH)))
        )
        
    GROUP BY e.cd_evento
    ORDER BY FIELD(e.status, 'Solicitado', 'Aprovado', 'Recusado'), dt_solicitacao DESC;

END$$

-- Procedure para o coordenador APROVAR um evento (versão atualizada)
DROP PROCEDURE IF EXISTS `aprovarEventoDefinitivo`$$
CREATE PROCEDURE `aprovarEventoDefinitivo`(IN pCdEvento VARCHAR(25), IN pCdCoordenador VARCHAR(10))
BEGIN
    UPDATE eventos 
    SET 
        status = 'Aprovado',
        cd_usuario_aprovador = pCdCoordenador -- <-- AQUI a mágica acontece
    WHERE 
        cd_evento = pCdEvento;
END$$

-- Procedure para o coordenador RECUSAR um evento (versão atualizada)
DROP PROCEDURE IF EXISTS `recusarEventoDefinitivo`$$
CREATE PROCEDURE `recusarEventoDefinitivo`(IN pCdEvento VARCHAR(25), IN pCdCoordenador VARCHAR(10))
BEGIN
    UPDATE eventos 
    SET 
        status = 'Recusado',
        cd_usuario_aprovador = pCdCoordenador -- <-- E AQUI também
    WHERE 
        cd_evento = pCdEvento;
END$$

CREATE PROCEDURE `cancelarSolicitacaoEvento`(
    IN pCdEvento VARCHAR(25),
    IN pCdUsuarioSolicitante VARCHAR(10)
)
BEGIN
    -- 1. Apaga primeiro as respostas dos professores (chaves estrangeiras)
    DELETE FROM resolucao_eventos_usuarios 
    WHERE eventos_cd_evento = pCdEvento;
    
    -- 2. Apaga as turmas associadas (chaves estrangeiras)
    DELETE FROM eventos_has_turmas 
    WHERE eventos_cd_evento = pCdEvento;
    
    -- 3. Finalmente, apaga o evento principal
    --    (com uma dupla verificação de segurança:
    --     só apaga se o ID do usuário for o mesmo do solicitante
    --     E o status ainda for 'Solicitado')
    DELETE FROM eventos 
    WHERE cd_evento = pCdEvento 
      AND cd_usuario_solicitante = pCdUsuarioSolicitante
      AND status = 'Solicitado';
END$$

CREATE PROCEDURE `excluirEventoDefinitivo`(
    IN pCdEvento VARCHAR(25)
)
BEGIN
    -- 1. Apaga primeiro as respostas dos professores (chaves estrangeiras)
    DELETE FROM resolucao_eventos_usuarios 
    WHERE eventos_cd_evento = pCdEvento;
    
    -- 2. Apaga as turmas associadas (chaves estrangeiras)
    DELETE FROM eventos_has_turmas 
    WHERE eventos_cd_evento = pCdEvento;
    
    -- 3. Finalmente, apaga o evento principal
    -- (Sem verificação de status ou solicitante, pois o Coordenador tem poder total)
    DELETE FROM eventos 
    WHERE cd_evento = pCdEvento;
END$$

CREATE PROCEDURE `buscarEventoParaEdicao`(
    IN pCdEvento VARCHAR(25),
    IN pCdUsuarioSolicitante VARCHAR(10)
)
BEGIN
    SELECT
        e.nm_evento,
        e.dt_evento,
        e.horario_inicio,
        e.horario_fim,
        e.tipo_evento,
        e.ds_descricao,
        -- Usamos GROUP_CONCAT para pegar os IDs das turmas como uma string (ex: "1,4,5")
        GROUP_CONCAT(DISTINCT eht.turmas_cd_turma) AS turmas_ids,
        -- Usamos GROUP_CONCAT para pegar os IDs dos professores convidados
        GROUP_CONCAT(DISTINCT reu.usuarios_cd_usuario) AS professores_ids
    FROM eventos e
    LEFT JOIN eventos_has_turmas eht ON e.cd_evento = eht.eventos_cd_evento
    LEFT JOIN resolucao_eventos_usuarios reu ON e.cd_evento = reu.eventos_cd_evento
    WHERE
        e.cd_evento = pCdEvento
        -- Condição de Segurança: Só pode editar se for o dono
        AND e.cd_usuario_solicitante = pCdUsuarioSolicitante
        -- Condição de Segurança: Só pode editar se ainda estiver "Solicitado"
        AND e.status = 'Solicitado'
    GROUP BY
        e.cd_evento;
END$$

DELIMITER ;