
DELIMITER $$

DROP PROCEDURE IF EXISTS listarUsuarios$$
CREATE PROCEDURE listarUsuarios()
BEGIN
    SELECT u.nm_email, u.nm_usuario, u.tipo_usuario_ic_usuario AS tipo_usuario
    FROM usuarios u
    ORDER BY u.nm_usuario;
END$$

DROP PROCEDURE IF EXISTS `listarProfessores`$$
CREATE PROCEDURE `listarProfessores`()
BEGIN
    SELECT 
        u.cd_usuario, 
        u.nm_usuario, 
        u.nm_email, 
        u.cd_telefone,
        u.tipo_usuario_ic_usuario AS tipo_usuario
    FROM usuarios u
    WHERE u.tipo_usuario_ic_usuario = 'Professor'
    ORDER BY u.nm_usuario;
END$$

DROP PROCEDURE IF EXISTS `listarProfessores`$$
DROP PROCEDURE IF EXISTS `listarProfessoresComTurmas`$$
CREATE PROCEDURE `listarProfessoresComTurmas`()
BEGIN
    SELECT 
        u.cd_usuario, 
        u.nm_usuario, 
        u.nm_email, 
        u.cd_telefone,
        (SELECT GROUP_CONCAT(t.nm_turma SEPARATOR ', ') 
         FROM usuarios_has_turmas uht
         JOIN turmas t ON uht.turmas_cd_turma = t.cd_turma
         WHERE uht.usuarios_cd_usuario = u.cd_usuario
        ) AS turmas_associadas
    FROM usuarios u
    WHERE u.tipo_usuario_ic_usuario = 'Professor'
    ORDER BY u.nm_usuario;
END$$

DROP PROCEDURE IF EXISTS listarCoordenadores$$
CREATE PROCEDURE listarCoordenadores()
BEGIN
    SELECT u.cd_usuario, u.nm_email, u.nm_usuario, u.cd_telefone, u.tipo_usuario_ic_usuario AS tipo_usuario
    FROM usuarios u
    WHERE u.tipo_usuario_ic_usuario = 'Coordenador'
    ORDER BY u.nm_usuario;
END$$

DROP PROCEDURE IF EXISTS criarUsuario$$
CREATE PROCEDURE criarUsuario(
    IN pEmail VARCHAR(45), 
    IN pNome VARCHAR(45), 
    IN pSenha VARCHAR(45),
    IN pTipo ENUM('Coordenador', 'Professor', 'Administrador')
)
BEGIN
    DECLARE qtd INT DEFAULT 0;
    DECLARE novoCdUsuario VARCHAR(45) DEFAULT '';
        
    SELECT COUNT(*) INTO qtd FROM usuarios WHERE nm_email = pEmail;
    IF (qtd = 0) THEN
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

DROP PROCEDURE IF EXISTS verificarAcesso$$
CREATE PROCEDURE verificarAcesso(
    IN pLogin VARCHAR(45), 
    IN pSenha VARCHAR(255)
)
BEGIN
    DECLARE qtd INT DEFAULT 0;
    
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

DROP PROCEDURE IF EXISTS `criarEvento`$$
CREATE PROCEDURE `criarEvento`(
    IN pCdEvento VARCHAR(45),
    IN pDtEvento DATE,
    IN pNmEvento VARCHAR(45),
    IN pHorarioInicio VARCHAR(45),
    IN pHorarioFim VARCHAR(45),
    IN pTipoEvento ENUM('Palestra', 'Visita tecnica', 'Reuniao', 'Prova', 'Conselho de Classe', 'Evento Esportivo', 'Outro'),
    IN pDsDescricao TEXT,
    IN pCdUsuarioSolicitante VARCHAR(45)
)
BEGIN
    INSERT INTO eventos (cd_evento, dt_evento, nm_evento, horario_inicio, horario_fim, tipo_evento, ds_descricao, status, cd_usuario_solicitante, dt_solicitacao)
    VALUES (pCdEvento, pDtEvento, pNmEvento, pHorarioInicio, pHorarioFim, pTipoEvento, pDsDescricao, 'Solicitado', pCdUsuarioSolicitante, CURDATE());
END$$

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

ALTER TABLE resolucao_eventos_usuarios 
MODIFY COLUMN status_resolucao ENUM('Aprovado', 'Recusado', 'Pendente') NULL DEFAULT 'Pendente';
DROP PROCEDURE IF EXISTS `registrarAprovacaoProfessor`$$
CREATE PROCEDURE `registrarAprovacaoProfessor`(
    IN pCdEvento VARCHAR(45),
    IN pCdUsuario VARCHAR(45),
    IN pStatus ENUM('Aprovado', 'Recusado', 'Pendente'),
    IN pDsMotivo TEXT
)
BEGIN
    DECLARE qtd INT DEFAULT 0;
    
    SELECT COUNT(*) INTO qtd FROM resolucao_eventos_usuarios WHERE eventos_cd_evento = pCdEvento AND usuarios_cd_usuario = pCdUsuario;
    
    IF (qtd > 0) THEN
        UPDATE resolucao_eventos_usuarios 
        SET status_resolucao = pStatus, 
            ds_motivo = pDsMotivo
        WHERE eventos_cd_evento = pCdEvento AND usuarios_cd_usuario = pCdUsuario;
    ELSE
        INSERT INTO resolucao_eventos_usuarios (eventos_cd_evento, usuarios_cd_usuario, status_resolucao, ds_motivo) 
        VALUES (pCdEvento, pCdUsuario, pStatus, pDsMotivo);
    END IF;
END$$

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
        
   
        AND (pTipoEvento IS NULL OR FIND_IN_SET(e.tipo_evento, pTipoEvento))
        AND (pCdTurma IS NULL OR FIND_IN_SET(eht_filtro.turmas_cd_turma, pCdTurma))
        

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
    SELECT cd_turma, nm_turma, qt_alunos 
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
    IN pDsDescricao TEXT, 
    IN pCdUsuarioSolicitante VARCHAR(45)
)
BEGIN
    INSERT INTO eventos (cd_evento, dt_evento, nm_evento, horario_inicio, horario_fim, tipo_evento, ds_descricao, status, cd_usuario_solicitante, dt_solicitacao)
    VALUES (pCdEvento, pDtEvento, pNmEvento, pHorarioInicio, pHorarioFim, pTipoEvento, pDsDescricao, 'Aprovado', pCdUsuarioSolicitante, CURDATE());
END$$


DROP PROCEDURE IF EXISTS `listarEventosParaCoordenador`$$
CREATE PROCEDURE `listarEventosParaCoordenador`(
    IN pCdUsuario VARCHAR(25),
    IN pStatus ENUM('Solicitado', 'Aprovado', 'Recusado'),
    IN pSolicitante ENUM('Todos', 'Eu', 'Professores'),
    IN pCdTurma INT,
    IN pTipoEvento ENUM('Palestra', 'Visita Técnica', 'Reunião', 'Prova', 'Conselho de Classe', 'Evento Esportivo', 'Outro'),
    IN pDataFiltro ENUM('Todos', 'Proximos7Dias', 'EsteMes', 'MesPassado', 'ProximoMes')
)
BEGIN

    SET SESSION group_concat_max_len = 1000000;

    SELECT
        e.cd_evento, e.nm_evento, e.dt_evento, e.horario_inicio, e.horario_fim,
        e.ds_descricao, e.status, e.cd_usuario_solicitante, e.dt_solicitacao,
        solicitante.nm_usuario AS nm_solicitante,
        solicitante.tipo_usuario_ic_usuario AS tipo_solicitante,
        (SELECT GROUP_CONCAT(t_inner.nm_turma SEPARATOR ', ') FROM eventos_has_turmas eht_inner JOIN turmas t_inner ON eht_inner.turmas_cd_turma = t_inner.cd_turma WHERE eht_inner.eventos_cd_evento = e.cd_evento) AS turmas_envolvidas,
        (SELECT SUM(t_inner.qt_alunos) FROM eventos_has_turmas eht_inner JOIN turmas t_inner ON eht_inner.turmas_cd_turma = t_inner.cd_turma WHERE eht_inner.eventos_cd_evento = e.cd_evento) AS total_alunos,
        CASE 
            WHEN solicitante.tipo_usuario_ic_usuario = 'Professor' THEN
                (SELECT CONCAT('[', GROUP_CONCAT(DISTINCT JSON_OBJECT('nome', u.nm_usuario, 'status', reu.status_resolucao, 'motivo', IFNULL(reu.ds_motivo, ''))), ']')
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
        (
            e.status = 'Solicitado'
            OR e.cd_usuario_solicitante = pCdUsuario
            OR e.cd_usuario_aprovador = pCdUsuario
            OR e.status IN ('Aprovado', 'Recusado')
        )
        AND (pStatus IS NULL OR e.status = pStatus)
        AND (pTipoEvento IS NULL OR e.tipo_evento = pTipoEvento)
        AND (pCdTurma IS NULL OR eht_filtro.turmas_cd_turma = pCdTurma)
        AND (pSolicitante IS NULL OR pSolicitante = 'Todos'
            OR (pSolicitante = 'Eu' AND e.cd_usuario_solicitante = pCdUsuario)
            OR (pSolicitante = 'Professores' AND solicitante.tipo_usuario_ic_usuario = 'Professor')
        )
        AND (pDataFiltro IS NULL OR pDataFiltro = 'Todos'
            OR (pDataFiltro = 'Proximos7Dias' AND e.dt_evento BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY))
            OR (pDataFiltro = 'EsteMes' AND MONTH(e.dt_evento) = MONTH(CURDATE()) AND YEAR(e.dt_evento) = YEAR(CURDATE()))
            OR (pDataFiltro = 'MesPassado' AND MONTH(e.dt_evento) = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) AND YEAR(e.dt_evento) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)))
            OR (pDataFiltro = 'ProximoMes' AND MONTH(e.dt_evento) = MONTH(DATE_ADD(CURDATE(), INTERVAL 1 MONTH)) AND YEAR(e.dt_evento) = YEAR(DATE_ADD(CURDATE(), INTERVAL 1 MONTH)))
        )
        
    GROUP BY e.cd_evento
    ORDER BY FIELD(e.status, 'Solicitado', 'Aprovado', 'Recusado'), dt_solicitacao DESC;

END$$


DROP PROCEDURE IF EXISTS `aprovarEventoDefinitivo`$$
CREATE PROCEDURE `aprovarEventoDefinitivo`(IN pCdEvento VARCHAR(25), IN pCdCoordenador VARCHAR(10))
BEGIN
    UPDATE eventos 
    SET 
        status = 'Aprovado',
        cd_usuario_aprovador = pCdCoordenador 
    WHERE 
        cd_evento = pCdEvento;
END$$


DROP PROCEDURE IF EXISTS `recusarEventoDefinitivo`$$
CREATE PROCEDURE `recusarEventoDefinitivo`(IN pCdEvento VARCHAR(25), IN pCdCoordenador VARCHAR(10))
BEGIN
    UPDATE eventos 
    SET 
        status = 'Recusado',
        cd_usuario_aprovador = pCdCoordenador 
    WHERE 
        cd_evento = pCdEvento;
END$$

CREATE PROCEDURE `cancelarSolicitacaoEvento`(
    IN pCdEvento VARCHAR(25),
    IN pCdUsuarioSolicitante VARCHAR(10)
)
BEGIN
    DELETE FROM resolucao_eventos_usuarios 
    WHERE eventos_cd_evento = pCdEvento;
    
    DELETE FROM eventos_has_turmas 
    WHERE eventos_cd_evento = pCdEvento;
    
    DELETE FROM eventos 
    WHERE cd_evento = pCdEvento 
      AND cd_usuario_solicitante = pCdUsuarioSolicitante
      AND status = 'Solicitado';
END$$

CREATE PROCEDURE `excluirEventoDefinitivo`(
    IN pCdEvento VARCHAR(25)
)
BEGIN

    DELETE FROM resolucao_eventos_usuarios 
    WHERE eventos_cd_evento = pCdEvento;
    

    DELETE FROM eventos_has_turmas 
    WHERE eventos_cd_evento = pCdEvento;
    
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
        GROUP_CONCAT(DISTINCT eht.turmas_cd_turma) AS turmas_ids,
        GROUP_CONCAT(DISTINCT reu.usuarios_cd_usuario) AS professores_ids
    FROM eventos e
    LEFT JOIN eventos_has_turmas eht ON e.cd_evento = eht.eventos_cd_evento
    LEFT JOIN resolucao_eventos_usuarios reu ON e.cd_evento = reu.eventos_cd_evento
    WHERE
        e.cd_evento = pCdEvento
        AND e.cd_usuario_solicitante = pCdUsuarioSolicitante
        AND e.status = 'Solicitado'
    GROUP BY
        e.cd_evento;
END$$

CREATE PROCEDURE `atualizarSolicitacaoEvento`(
    IN pCdEvento VARCHAR(25),
    IN pNmEvento VARCHAR(45),
    IN pDtEvento DATE,
    IN pHorarioInicio VARCHAR(10),
    IN pHorarioFim VARCHAR(10),
    IN pTipoEvento ENUM('Palestra', 'Visita Técnica', 'Reunião', 'Prova', 'Conselho de Classe', 'Evento Esportivo', 'Outro'),
    IN pDsDescricao TEXT
)
BEGIN
    UPDATE eventos SET
        nm_evento = pNmEvento,
        dt_evento = pDtEvento,
        horario_inicio = pHorarioInicio,
        horario_fim = pHorarioFim,
        tipo_evento = pTipoEvento,
        ds_descricao = pDsDescricao
    WHERE
        cd_evento = pCdEvento;
        
    DELETE FROM eventos_has_turmas WHERE eventos_cd_evento = pCdEvento;
    DELETE FROM resolucao_eventos_usuarios WHERE eventos_cd_evento = pCdEvento;
    
END$$

CREATE PROCEDURE `buscarEventoParaEdicaoCoordenador`(
    IN pCdEvento VARCHAR(25)
)
BEGIN
    SELECT
        e.nm_evento,
        e.dt_evento,
        e.horario_inicio,
        e.horario_fim,
        e.tipo_evento,
        e.ds_descricao,
        GROUP_CONCAT(DISTINCT eht.turmas_cd_turma) AS turmas_ids,
        GROUP_CONCAT(DISTINCT reu.usuarios_cd_usuario) AS professores_ids
    FROM eventos e
    LEFT JOIN eventos_has_turmas eht ON e.cd_evento = eht.eventos_cd_evento
    LEFT JOIN resolucao_eventos_usuarios reu ON e.cd_evento = reu.eventos_cd_evento
    WHERE
        e.cd_evento = pCdEvento
    GROUP BY
        e.cd_evento;
END$$

DROP PROCEDURE IF EXISTS `buscarDadosUsuario`$$
CREATE PROCEDURE `buscarDadosUsuario`(IN pCdUsuario VARCHAR(10))
BEGIN
    SELECT nm_usuario, nm_email, cd_telefone, cd_usuario
    FROM usuarios
    WHERE cd_usuario = pCdUsuario;
END$$

DROP PROCEDURE IF EXISTS `atualizarDadosUsuario`$$
CREATE PROCEDURE `atualizarDadosUsuario`(
    IN pCdUsuario VARCHAR(10),
    IN pNome VARCHAR(45),
    IN pTelefone VARCHAR(45)
)
BEGIN
    UPDATE usuarios 
    SET 
        nm_usuario = pNome,
        cd_telefone = pTelefone
    WHERE 
        cd_usuario = pCdUsuario;
END$$

CREATE PROCEDURE `mudarSenha`(
    IN pCdUsuario VARCHAR(10),
    IN pSenhaAntiga VARCHAR(255),
    IN pSenhaNova VARCHAR(255)
)
BEGIN
    DECLARE usuarioExiste INT DEFAULT 0;
    SELECT COUNT(*) INTO usuarioExiste 
    FROM usuarios 
    WHERE cd_usuario = pCdUsuario AND cd_senha = pSenhaAntiga;
    IF (usuarioExiste = 0) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'A senha atual está incorreta.';
    ELSE
        UPDATE usuarios 
        SET cd_senha = pSenhaNova
        WHERE cd_usuario = pCdUsuario;
    END IF;
END$$

DROP PROCEDURE IF EXISTS `atualizarProfessor`$$
CREATE PROCEDURE `atualizarProfessor`(
    IN pCdUsuario VARCHAR(10),
    IN pNome VARCHAR(45),
    IN pEmail VARCHAR(45),
    IN pTelefone VARCHAR(45),
    IN pTurmasIDs VARCHAR(255) 
)
BEGIN
    DECLARE emailCount INT DEFAULT 0;
    
    SELECT COUNT(*) INTO emailCount FROM usuarios 
    WHERE nm_email = pEmail AND cd_usuario != pCdUsuario;

    IF (emailCount > 0) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Erro: Este e-mail já está em uso por outro usuário.';
    ELSE
        UPDATE usuarios SET
            nm_usuario = pNome,
            nm_email = pEmail,
            cd_telefone = pTelefone
        WHERE
            cd_usuario = pCdUsuario;
            
        DELETE FROM usuarios_has_turmas WHERE usuarios_cd_usuario = pCdUsuario;
        

        IF pTurmasIDs IS NOT NULL AND pTurmasIDs != '' THEN
            SET @sql = NULL;
            SELECT
                GROUP_CONCAT(
                    CONCAT('(', QUOTE(pCdUsuario), ',', QUOTE(turma_id), ')')
                )
            INTO @sql
            FROM
                JSON_TABLE(
                    CONCAT('[', pTurmasIDs, ']'),
                    '$[*]' COLUMNS (turma_id INT PATH '$')
                ) AS jt;
            IF @sql IS NOT NULL THEN
                SET @sql = CONCAT('INSERT INTO usuarios_has_turmas (usuarios_cd_usuario, turmas_cd_turma) VALUES ', @sql);
                PREPARE stmt FROM @sql;
                EXECUTE stmt;
                DEALLOCATE PREPARE stmt;
            END IF;
            
        END IF;
        
    END IF;
END$$

DROP PROCEDURE IF EXISTS `excluirProfessor`$$
CREATE PROCEDURE `excluirProfessor`(
    IN pCdUsuario VARCHAR(10)
)
BEGIN
    DELETE FROM resolucao_eventos_usuarios WHERE usuarios_cd_usuario = pCdUsuario;
    DELETE FROM usuarios_has_turmas WHERE usuarios_cd_usuario = pCdUsuario;
    DELETE FROM usuarios WHERE cd_usuario = pCdUsuario;
END$$

DROP PROCEDURE IF EXISTS `criarProfessor`$$
CREATE PROCEDURE `criarProfessor`(
    IN pCdUsuario VARCHAR(10), 
    IN pNome VARCHAR(45),
    IN pEmail VARCHAR(45),
    IN pSenha VARCHAR(255),
    IN pTelefone VARCHAR(45)
)
BEGIN
    CALL criarProfessorCompleto(pCdUsuario, pNome, pEmail, pSenha, pTelefone, 'Professor');
END$$

DROP PROCEDURE IF EXISTS `criarProfessorCompleto`$$
CREATE PROCEDURE `criarProfessorCompleto`(
    IN pCdUsuario VARCHAR(10),
    IN pNome VARCHAR(45),
    IN pEmail VARCHAR(45),
    IN pSenha VARCHAR(255),
    IN pTelefone VARCHAR(45),
    IN pTipo ENUM('Coordenador', 'Professor', 'Administrador')
)
BEGIN
    DECLARE emailCount INT DEFAULT 0;
    DECLARE rmCount INT DEFAULT 0;

    SELECT COUNT(*) INTO rmCount FROM usuarios 
    WHERE cd_usuario = pCdUsuario;
    
    SELECT COUNT(*) INTO emailCount FROM usuarios 
    WHERE nm_email = pEmail;

    IF (rmCount > 0) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Erro: Este RM já está cadastrado.';
    ELSEIF (emailCount > 0) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Erro: Este e-mail já está em uso.';
    ELSE
        INSERT INTO usuarios (cd_usuario, nm_usuario, nm_email, cd_senha, cd_telefone, tipo_usuario_ic_usuario)
        VALUES (pCdUsuario, pNome, pEmail, pSenha, pTelefone, pTipo); 
    END IF;
END$$

DROP PROCEDURE IF EXISTS `listarTurmasComContagem`$$
CREATE PROCEDURE `listarTurmasComContagem`()
BEGIN
    SELECT 
        t.cd_turma,
        t.nm_turma,
        t.ic_serie,
        t.qt_alunos,
        t.cd_sala,
        c.nm_curso,
        c.ic_periodo,
        (SELECT COUNT(uht.usuarios_cd_usuario) 
         FROM usuarios_has_turmas uht 
         WHERE uht.turmas_cd_turma = t.cd_turma
        ) AS contagem_professores
    FROM turmas t
    JOIN cursos c ON t.cursos_cd_curso = c.cd_curso
    ORDER BY t.nm_turma;
END$$

DROP PROCEDURE IF EXISTS `listarProfessoresPorTurma`$$
CREATE PROCEDURE `listarProfessoresPorTurma`(
    IN pCdTurma INT
)
BEGIN
    SELECT 
        u.nm_usuario
    FROM usuarios_has_turmas uht
    JOIN usuarios u ON uht.usuarios_cd_usuario = u.cd_usuario
    WHERE 
        uht.turmas_cd_turma = pCdTurma
        AND u.tipo_usuario_ic_usuario = 'Professor'
    ORDER BY
        u.nm_usuario;
END$$

DROP PROCEDURE IF EXISTS `atualizarTurma`$$
CREATE PROCEDURE `atualizarTurma`(
    IN pCdTurma INT,
    IN pNmTurma VARCHAR(45),
    IN pIcSerie VARCHAR(10),
    IN pQtAlunos INT,
    IN pCdSala VARCHAR(45)
)
BEGIN

    DECLARE nomeCount INT DEFAULT 0;
    SELECT COUNT(*) INTO nomeCount FROM turmas
    WHERE nm_turma = pNmTurma AND cd_turma != pCdTurma;

    IF (nomeCount > 0) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Erro: O nome desta turma (Sigla) já está em uso.';
    ELSE
        UPDATE turmas SET
            nm_turma = pNmTurma,
            ic_serie = pIcSerie,
            qt_alunos = pQtAlunos,
            cd_sala = pCdSala
        WHERE
            cd_turma = pCdTurma;
    END IF;
END$$

DROP PROCEDURE IF EXISTS `excluirTurma`$$
CREATE PROCEDURE `excluirTurma`(
    IN pCdTurma INT
)
BEGIN
    DELETE FROM usuarios_has_turmas WHERE turmas_cd_turma = pCdTurma;
    DELETE FROM eventos_has_turmas WHERE turmas_cd_turma = pCdTurma;
    DELETE FROM turmas WHERE cd_turma = pCdTurma;
END$$

DROP PROCEDURE IF EXISTS `criarTurma`$$
CREATE PROCEDURE `criarTurma`(
    IN pNmTurma VARCHAR(45),
    IN pIcSerie VARCHAR(10),
    IN pQtAlunos INT,
    IN pCdSala VARCHAR(45),
    IN pCdCurso INT 
)
BEGIN
    DECLARE nomeCount INT DEFAULT 0;

    SELECT COUNT(*) INTO nomeCount FROM turmas
    WHERE nm_turma = pNmTurma;

    IF (nomeCount > 0) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Erro: O nome (Sigla) desta turma já está em uso.';
    ELSE

        INSERT INTO turmas (nm_turma, ic_serie, qt_alunos, cd_sala, cursos_cd_curso)
        VALUES (pNmTurma, pIcSerie, pQtAlunos, pCdSala, pCdCurso);
    END IF;
END$$

DROP PROCEDURE IF EXISTS `listarCursos`$$
CREATE PROCEDURE `listarCursos`()
BEGIN
    SELECT cd_curso, nm_curso, ic_periodo 
    FROM cursos 
    ORDER BY nm_curso;
END$$

DROP PROCEDURE IF EXISTS `listarCursosComContagem`$$
CREATE PROCEDURE `listarCursosComContagem`()
BEGIN
    SELECT 
        c.cd_curso,
        c.nm_curso,
        c.ic_periodo,
        (SELECT COUNT(t.cd_turma) 
         FROM turmas t 
         WHERE t.cursos_cd_curso = c.cd_curso
        ) AS contagem_turmas,
        
        (SELECT COUNT(uhc.usuarios_cd_usuario)
         FROM usuarios_has_cursos uhc
         WHERE uhc.cursos_cd_curso = c.cd_curso
        ) AS contagem_coordenadores,
        IFNULL((SELECT GROUP_CONCAT(u.nm_usuario SEPARATOR ', ')
         FROM usuarios_has_cursos uhc
         JOIN usuarios u ON uhc.usuarios_cd_usuario = u.cd_usuario
         WHERE uhc.cursos_cd_curso = c.cd_curso
        ), '') AS coordenadores_associados
        
    FROM cursos c
    ORDER BY c.nm_curso;
END$$

DROP PROCEDURE IF EXISTS `criarCurso`$$
CREATE PROCEDURE `criarCurso`(
    IN pNmCurso VARCHAR(45),
    IN pIcPeriodo ENUM('Manha', 'Tarde', 'Noite')

)
BEGIN
    DECLARE nomeCount INT DEFAULT 0;
    SELECT COUNT(*) INTO nomeCount FROM cursos
    WHERE nm_curso = pNmCurso;

    IF (nomeCount > 0) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Erro: Este curso já está cadastrado.';
    ELSE
        INSERT INTO cursos (nm_curso, ic_periodo)
        VALUES (pNmCurso, pIcPeriodo);
    END IF;
END$$

DROP PROCEDURE IF EXISTS `atualizarCurso`$$
CREATE PROCEDURE `atualizarCurso`(
    IN pCdCurso INT,
    IN pNmCurso VARCHAR(45),
    IN pIcPeriodo ENUM('Manha', 'Tarde', 'Noite')
)
BEGIN
    DECLARE nomeCount INT DEFAULT 0;
    SELECT COUNT(*) INTO nomeCount FROM cursos
    WHERE nm_curso = pNmCurso AND cd_curso != pCdCurso;

    IF (nomeCount > 0) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Erro: O nome deste curso já está em uso.';
    ELSE
        UPDATE cursos SET
            nm_curso = pNmCurso,
            ic_periodo = pIcPeriodo
        WHERE
            cd_curso = pCdCurso;
    END IF;
END$$

DROP PROCEDURE IF EXISTS `excluirCurso`$$
CREATE PROCEDURE `excluirCurso`(
    IN pCdCurso INT
)
BEGIN
    DECLARE turmaCount INT DEFAULT 0;
    SELECT COUNT(*) INTO turmaCount FROM turmas
    WHERE cursos_cd_curso = pCdCurso;

    IF (turmaCount > 0) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Erro: Não é possível excluir o curso pois ele possui turmas vinculadas.';
    ELSE
        DELETE FROM cursos WHERE cd_curso = pCdCurso;
    END IF;
END$$
DROP PROCEDURE IF EXISTS `listarFuncionariosComAssociacoes`$$
CREATE PROCEDURE `listarFuncionariosComAssociacoes`()
BEGIN
    SELECT 
        u.cd_usuario, 
        u.nm_usuario, 
        u.nm_email, 
        u.cd_telefone,
        u.tipo_usuario_ic_usuario AS tipo_usuario,
        (SELECT GROUP_CONCAT(t.nm_turma SEPARATOR ' | ') 
         FROM usuarios_has_turmas uht
         JOIN turmas t ON uht.turmas_cd_turma = t.cd_turma
         WHERE uht.usuarios_cd_usuario = u.cd_usuario
        ) AS turmas_associadas_nomes,
        (SELECT GROUP_CONCAT(c.nm_curso SEPARATOR ' | ')
         FROM usuarios_has_cursos uhc
         JOIN cursos c ON uhc.cursos_cd_curso = c.cd_curso
         WHERE uhc.usuarios_cd_usuario = u.cd_usuario
        ) AS cursos_associados_nomes
        
    FROM usuarios u
    WHERE u.tipo_usuario_ic_usuario IN ('Professor', 'Coordenador')
    ORDER BY FIELD(u.tipo_usuario_ic_usuario, 'Coordenador', 'Professor'), u.nm_usuario;
END$$

DROP PROCEDURE IF EXISTS `atualizarCoordenador`$$
CREATE PROCEDURE `atualizarCoordenador`(
    IN pCdUsuario VARCHAR(10),
    IN pNome VARCHAR(45),
    IN pEmail VARCHAR(45),
    IN pTelefone VARCHAR(45),
    IN pCursosIDs VARCHAR(255) 
)
BEGIN
    DECLARE emailCount INT DEFAULT 0;
    
    SELECT COUNT(*) INTO emailCount FROM usuarios 
    WHERE nm_email = pEmail AND cd_usuario != pCdUsuario;

    IF (emailCount > 0) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Erro: Este e-mail já está em uso por outro usuário.';
    ELSE
        UPDATE usuarios SET
            nm_usuario = pNome,
            nm_email = pEmail,
            cd_telefone = pTelefone
        WHERE
            cd_usuario = pCdUsuario;
            
        DELETE FROM usuarios_has_cursos WHERE usuarios_cd_usuario = pCdUsuario;
        
        IF pCursosIDs IS NOT NULL AND pCursosIDs != '' THEN
        
            SET @sql = NULL;
            SELECT
                GROUP_CONCAT(
                    CONCAT('(', QUOTE(pCdUsuario), ',', QUOTE(curso_id), ')')
                )
            INTO @sql
            FROM
                JSON_TABLE(
                    CONCAT('[', pCursosIDs, ']'),
                    '$[*]' COLUMNS (curso_id INT PATH '$')
                ) AS jt;

            IF @sql IS NOT NULL THEN
                SET @sql = CONCAT('INSERT INTO usuarios_has_cursos (usuarios_cd_usuario, cursos_cd_curso) VALUES ', @sql);
                PREPARE stmt FROM @sql;
                EXECUTE stmt;
                DEALLOCATE PREPARE stmt;
            END IF;
        END IF;
    END IF;
END$$

DROP PROCEDURE IF EXISTS `atualizarProfessor`$$
CREATE PROCEDURE `atualizarProfessor`(
    IN pCdUsuario VARCHAR(10),
    IN pNome VARCHAR(45),
    IN pEmail VARCHAR(45),
    IN pTelefone VARCHAR(45),
    IN pTurmasIDs VARCHAR(255)
)
BEGIN
    DECLARE emailCount INT DEFAULT 0;
    
    SELECT COUNT(*) INTO emailCount FROM usuarios 
    WHERE nm_email = pEmail AND cd_usuario != pCdUsuario;

    IF (emailCount > 0) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Erro: Este e-mail já está em uso por outro usuário.';
    ELSE
        UPDATE usuarios SET
            nm_usuario = pNome,
            nm_email = pEmail,
            cd_telefone = pTelefone
        WHERE
            cd_usuario = pCdUsuario;
            
        DELETE FROM usuarios_has_turmas WHERE usuarios_cd_usuario = pCdUsuario;
        
        IF pTurmasIDs IS NOT NULL AND pTurmasIDs != '' THEN
        
            SET @sql = NULL;
            SELECT
                GROUP_CONCAT(
                    CONCAT('(', QUOTE(pCdUsuario), ',', QUOTE(turma_id), ')')
                )
            INTO @sql
            FROM
                JSON_TABLE(
                    CONCAT('[', pTurmasIDs, ']'),
                    '$[*]' COLUMNS (turma_id INT PATH '$')
                ) AS jt;

            IF @sql IS NOT NULL THEN
                SET @sql = CONCAT('INSERT INTO usuarios_has_turmas (usuarios_cd_usuario, turmas_cd_turma) VALUES ', @sql);
                PREPARE stmt FROM @sql;
                EXECUTE stmt;
                DEALLOCATE PREPARE stmt;
            END IF;
            
        END IF;
    END IF;
END$$

DROP PROCEDURE IF EXISTS `listarRelacaoProfessorTurma`$$
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


DROP PROCEDURE IF EXISTS `listarEventosParaProfessor`$$
CREATE PROCEDURE `listarEventosParaProfessor`(
    IN pCdUsuario VARCHAR(25),
    IN pStatus ENUM('Solicitado', 'Aprovado', 'Recusado'),
    IN pSolicitante ENUM('Todos', 'Eu', 'OutrosProfessores', 'Coordenador'), 
    IN pCdTurma INT,
    IN pTipoEvento ENUM('Palestra', 'Visita Técnica', 'Reunião', 'Prova', 'Conselho de Classe', 'Evento Esportivo', 'Outro'),
    IN pDataFiltro ENUM('Todos', 'Proximos7Dias', 'EsteMes', 'MesPassado', 'ProximoMes')
)
BEGIN

    SET SESSION group_concat_max_len = 1000000;

    SELECT
        e.cd_evento, e.nm_evento, e.dt_evento, e.horario_inicio, e.horario_fim,
        e.ds_descricao, e.status, e.cd_usuario_solicitante, e.dt_solicitacao,
        solicitante.nm_usuario AS nm_solicitante,
        solicitante.tipo_usuario_ic_usuario AS tipo_solicitante,
        (SELECT GROUP_CONCAT(t_inner.nm_turma SEPARATOR ', ') FROM eventos_has_turmas eht_inner JOIN turmas t_inner ON eht_inner.turmas_cd_turma = t_inner.cd_turma WHERE eht_inner.eventos_cd_evento = e.cd_evento) AS turmas_envolvidas,
        (SELECT SUM(t_inner.qt_alunos) FROM eventos_has_turmas eht_inner JOIN turmas t_inner ON eht_inner.turmas_cd_turma = t_inner.cd_turma WHERE eht_inner.eventos_cd_evento = e.cd_evento) AS total_alunos,
        (SELECT CONCAT('[', GROUP_CONCAT(JSON_OBJECT('nome', u.nm_usuario, 'status', reu.status_resolucao, 'motivo', IFNULL(reu.ds_motivo, ''))), ']') 
         FROM resolucao_eventos_usuarios reu JOIN usuarios u ON reu.usuarios_cd_usuario = u.cd_usuario 
         WHERE reu.eventos_cd_evento = e.cd_evento AND u.cd_usuario != e.cd_usuario_solicitante
        ) AS respostas_professores,
        
        (SELECT status_resolucao FROM resolucao_eventos_usuarios WHERE eventos_cd_evento = e.cd_evento AND usuarios_cd_usuario = pCdUsuario) AS minha_resposta
        
    FROM eventos e
    JOIN usuarios solicitante ON e.cd_usuario_solicitante = solicitante.cd_usuario
    LEFT JOIN eventos_has_turmas eht_filtro ON e.cd_evento = eht_filtro.eventos_cd_evento
        
    WHERE 
        (
            e.cd_usuario_solicitante = pCdUsuario 
            OR EXISTS (SELECT 1 FROM resolucao_eventos_usuarios reu_check WHERE reu_check.eventos_cd_evento = e.cd_evento AND reu_check.usuarios_cd_usuario = pCdUsuario)
            OR (solicitante.tipo_usuario_ic_usuario = 'Coordenador' 
                AND EXISTS (SELECT 1 
                            FROM eventos_has_turmas eht_check 
                            JOIN usuarios_has_turmas uht_check ON eht_check.turmas_cd_turma = uht_check.turmas_cd_turma 
                            WHERE eht_check.eventos_cd_evento = e.cd_evento AND uht_check.usuarios_cd_usuario = pCdUsuario)
            )
        )
        AND (pStatus IS NULL OR e.status = pStatus)
        AND (pTipoEvento IS NULL OR e.tipo_evento = pTipoEvento)
        AND (pCdTurma IS NULL OR eht_filtro.turmas_cd_turma = pCdTurma)
        AND (pSolicitante IS NULL OR pSolicitante = 'Todos'
            OR (pSolicitante = 'Eu' AND e.cd_usuario_solicitante = pCdUsuario)
            OR (pSolicitante = 'OutrosProfessores' AND e.cd_usuario_solicitante != pCdUsuario AND solicitante.tipo_usuario_ic_usuario = 'Professor')
            OR (pSolicitante = 'Coordenador' AND solicitante.tipo_usuario_ic_usuario = 'Coordenador')
        )
        AND (pDataFiltro IS NULL OR pDataFiltro = 'Todos' OR
            (pDataFiltro = 'Proximos7Dias' AND e.dt_evento BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)) OR
            (pDataFiltro = 'EsteMes' AND MONTH(e.dt_evento) = MONTH(CURDATE()) AND YEAR(e.dt_evento) = YEAR(CURDATE())) OR
            (pDataFiltro = 'MesPassado' AND MONTH(e.dt_evento) = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) AND YEAR(e.dt_evento) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))) OR
            (pDataFiltro = 'ProximoMes' AND MONTH(e.dt_evento) = MONTH(DATE_ADD(CURDATE(), INTERVAL 1 MONTH)) AND YEAR(e.dt_evento) = YEAR(DATE_ADD(CURDATE(), INTERVAL 1 MONTH)))
        )
        
    GROUP BY e.cd_evento
    ORDER BY FIELD(e.status, 'Solicitado', 'Aprovado', 'Recusado'), e.dt_solicitacao DESC;
END$$
DELIMITER ;