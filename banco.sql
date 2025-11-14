DROP DATABASE IF EXISTS escola;
CREATE DATABASE escola;
USE escola;

DROP TABLE IF EXISTS tipo_usuario;
CREATE TABLE tipo_usuario (
    ic_usuario ENUM('Coordenador', 'Professor', 'Administrador') NOT NULL,
    PRIMARY KEY (ic_usuario)
) ;


DROP TABLE IF EXISTS usuarios;
CREATE TABLE usuarios (
    cd_usuario VARCHAR(10) NOT NULL,
    nm_usuario VARCHAR(45) NOT NULL,
    cd_senha VARCHAR(255) NOT NULL,
    cd_telefone VARCHAR(45),
    nm_email VARCHAR(45),
    tipo_usuario_ic_usuario ENUM('Coordenador', 'Professor', 'Administrador') NOT NULL,
    PRIMARY KEY (cd_usuario),
    FOREIGN KEY (tipo_usuario_ic_usuario) REFERENCES tipo_usuario(ic_usuario)
) ;


DROP TABLE IF EXISTS cursos;
CREATE TABLE cursos (
    cd_curso INT NOT NULL AUTO_INCREMENT,
    nm_curso VARCHAR(45) NOT NULL,
    ic_periodo ENUM('Manha', 'Tarde', 'Noite') NOT NULL,
    PRIMARY KEY (cd_curso)
);


DROP TABLE IF EXISTS turmas;
CREATE TABLE turmas (
    cd_turma INT NOT NULL AUTO_INCREMENT,
    nm_turma VARCHAR(45) NOT NULL,
    ic_serie ENUM('1', '2', '3') NOT NULL,
    qt_alunos INT NOT NULL,
    cd_sala INT NOT NULL,
    cursos_cd_curso INT NOT NULL,
    PRIMARY KEY (cd_turma),
    FOREIGN KEY (cursos_cd_curso) REFERENCES cursos(cd_curso)
) ;

DROP TABLE IF EXISTS eventos;
CREATE TABLE eventos (
    cd_evento VARCHAR(25) NOT NULL,
    dt_evento DATE NOT NULL,
    nm_evento VARCHAR(45) NOT NULL,
    horario_inicio VARCHAR(10) NOT NULL,
    horario_fim VARCHAR(10) NOT NULL,
    tipo_evento ENUM('Palestra', 'Visita tecnica', 'Reuniao', 'Prova', 'Conselho de Classe', 'Evento Esportivo', 'Outro') NOT NULL,
    ds_descricao TEXT,
    status ENUM('Solicitado', 'Aprovado', 'Recusado') NOT NULL DEFAULT 'Solicitado',
    cd_usuario_solicitante VARCHAR(10) NOT NULL,
    dt_solicitacao DATE NOT NULL,
    cd_usuario_aprovador VARCHAR(10) NULL DEFAULT NULL, -- Nova coluna
    PRIMARY KEY (cd_evento),
    FOREIGN KEY (cd_usuario_solicitante) REFERENCES usuarios(cd_usuario),
    CONSTRAINT `fk_evento_aprovador`
	FOREIGN KEY (`cd_usuario_aprovador`) REFERENCES `usuarios` (`cd_usuario`)
);


DROP TABLE IF EXISTS usuarios_has_turmas;
CREATE TABLE usuarios_has_turmas (
    usuarios_cd_usuario VARCHAR(10) NOT NULL,
    turmas_cd_turma INT NOT NULL,
    PRIMARY KEY (usuarios_cd_usuario, turmas_cd_turma),
    FOREIGN KEY (usuarios_cd_usuario) REFERENCES usuarios(cd_usuario),
    FOREIGN KEY (turmas_cd_turma) REFERENCES turmas(cd_turma)
) ;



DROP TABLE IF EXISTS eventos_has_turmas;
CREATE TABLE eventos_has_turmas (
    eventos_cd_evento VARCHAR(25) NOT NULL,
    turmas_cd_turma INT NOT NULL,
    PRIMARY KEY (eventos_cd_evento, turmas_cd_turma),
    FOREIGN KEY (eventos_cd_evento) REFERENCES eventos(cd_evento),
    FOREIGN KEY (turmas_cd_turma) REFERENCES turmas(cd_turma)
) ;


DROP TABLE IF EXISTS resolucao_eventos_usuarios;
CREATE TABLE resolucao_eventos_usuarios (
    eventos_cd_evento VARCHAR(25) NOT NULL,
    usuarios_cd_usuario VARCHAR(10) NOT NULL,
    status_resolucao ENUM('Aprovado', 'Recusado', 'Pendente') NULL DEFAULT 'Pendente',
    PRIMARY KEY (eventos_cd_evento, usuarios_cd_usuario),
    FOREIGN KEY (eventos_cd_evento) REFERENCES eventos(cd_evento),
    FOREIGN KEY (usuarios_cd_usuario) REFERENCES usuarios(cd_usuario)
);

INSERT INTO tipo_usuario (ic_usuario) VALUES 
('Professor'),
('Coordenador'),
('Administrador');
SELECT * FROM tipo_usuario;


INSERT INTO usuarios (cd_usuario, nm_usuario, cd_senha, cd_telefone, nm_email, tipo_usuario_ic_usuario) VALUES 
('0001', 'Maristela', '123', '13 9999-9999', 'maristela@etec.com', 'Professor'),
('0002', 'André', '1234', '13 8888-8888', 'andre@etec.com', 'Coordenador'),
('0003', 'Marquinhos', '12345', '13 7777-7777', 'marquinhos@etec.com', 'Administrador'),
('1001', 'Ana Silva', 'pass123', '13 9111-1111', 'ana.silva@etec.com', 'Professor'),
('2001', 'Karen Rodrigues', 'coord123', '13 9221-2221', 'karen.rodrigues@etec.com', 'Coordenador'),
('1011', 'Lucas Pereira', 'prof123', '13 9121-1121', 'lucas.pereira@etec.com', 'Professor'),
('1012', 'Mariana Costa', 'prof456', '13 9122-1122', 'mariana.costa@etec.com', 'Professor'),
('1013', 'Rafael Souza', 'prof789', '13 9123-1123', 'rafael.souza@etec.com', 'Professor'),
('1014', 'Sofia Mendes', 'prof101', '13 9124-1124', 'sofia.mendes@etec.com', 'Professor'),
('1015', 'Thiago Almeida', 'prof102', '13 9125-1125', 'thiago.almeida@etec.com', 'Professor'),
('2011', 'Beatriz Lima', 'coord201', '13 9231-2231', 'beatriz.lima@etec.com', 'Coordenador'),
('2012', 'Carlos Ferreira', 'coord202', '13 9232-2232', 'carlos.ferreira@etec.com', 'Coordenador'),
('2013', 'Daniela Santos', 'coord203', '13 9233-2233', 'daniela.santos@etec.com', 'Coordenador'),
('2014', 'Eduardo Ribeiro', 'coord204', '13 9234-2234', 'eduardo.ribeiro@etec.com', 'Coordenador'),
('2015', 'Fernanda Oliveira', 'coord205', '13 9235-2235', 'fernanda.oliveira@etec.com', 'Coordenador');
SELECT * FROM usuarios;




INSERT INTO cursos (cd_curso, nm_curso, ic_periodo) VALUES 
(1,'Automação Industrial', 'Tarde'),
(2,'Desenvolvimento de Sistemas', 'Manha'),
(3,'Edificações', 'Tarde'),
(4,'Eletrônica', 'Manha'),
(5,'Eletrotécnica', 'Manha'),
(6,'Informática para Internet', 'Manha'),
(7,'Programação de Jogos Digitais', 'Tarde'),
(8,'Guia de Turismo Internacional', 'Noite'),
(9,'M-TEC AMS Desenvolvimento de Sistemas', 'Manha'),
(10,'M-TEC Eletrotécnica', 'Manha'),
(11,'M-TEC Mecânica', 'Noite'),
(12,'Agenciamento de Viagem', 'Noite'),
(13,'Desenho de Construção Civil', 'Noite'),
(14,'Eletrônica ANP', 'Noite'),
(15,'Informática', 'Tarde'),
(16,'Mecânica', 'Noite'),
(17,'Mecatrônica', 'Noite'),
(18,'Metalurgia', 'Noite'),
(19,'Soldagem', 'Noite');
SELECT * FROM cursos;



INSERT INTO turmas ( nm_turma, ic_serie, qt_alunos, cd_sala, cursos_cd_curso) VALUES 
('1K2', '1', 30, 1, 1),   -- Automação Industrial, 1ª Série
('2K2', '2', 30, 2, 1),   -- Automação Industrial, 2ª Série
('3K2', '3', 30, 3, 1),   -- Automação Industrial, 3ª Série
('1N1', '1', 30, 4, 2),   -- Desenvolvimento de Sistemas, 1ª Série
('2N1', '2', 30, 4, 2),   -- Desenvolvimento de Sistemas, 2ª Série
('3N1', '3', 30, 4, 2),   -- Desenvolvimento de Sistemas, 3ª Série
('1G2', '1', 30, 5, 3),   -- Edificações, 1ª Série
('2G2', '2', 30, 6, 3),   -- Edificações, 2ª Série
('3G2', '3', 30, 7, 3),   -- Edificações, 3ª Série
('1P1', '1', 30, 8, 4),   -- Eletrônica, 1ª Série
('2P1', '2', 30, 9, 4),   -- Eletrônica, 2ª Série
('3P1', '3', 30, 10, 4),  -- Eletrônica, 3ª Série
('1R1', '1', 30, 11, 5),  -- Eletrotécnica, 1ª Série
('2R1', '2', 30, 11, 5),  -- Eletrotécnica, 2ª Série
('3R1', '3', 30, 11, 5),  -- Eletrotécnica, 3ª Série
('1I1', '1', 30, 12, 6),  -- Informática para Internet, 1ª Série
('2I1', '2', 30, 13, 6),  -- Informática para Internet, 2ª Série
('3I1', '3', 30, 14, 6),  -- Informática para Internet, 3ª Série
('1S2', '1', 30, 15, 7),  -- Programação de Jogos Digitais, 1ª Série
('2S2', '2', 30, 16, 7),  -- Programação de Jogos Digitais, 2ª Série
('3S2', '3', 30, 17, 7),  -- Programação de Jogos Digitais, 3ª Série
('GT3', '1', 30, 18, 8),  -- Guia de Turismo Internacional, 1º Módulo
('MDS3', '1', 30, 19, 9), -- M-TEC AMS Desenvolvimento de Sistemas, 1ª Série
('MDS3', '2', 30, 20, 9), -- M-TEC AMS Desenvolvimento de Sistemas, 2ª Série
('MET3', '1', 30, 21, 10), -- M-TEC Eletrotécnica, 1ª Série
('MET3', '2', 30, 22, 10), -- M-TEC Eletrotécnica, 2ª Série
('MM3', '1', 30, 23, 11), -- M-TEC Mecânica, 1ª Série
('AV3', '2', 30, 24, 12), -- Agenciamento de Viagem, 2º Módulo
('AV3', '3', 30, 25, 12), -- Agenciamento de Viagem, 3º Módulo
('DC3', '1', 30, 26, 13), -- Desenho de Construção Civil, 1º Módulo
('DC3', '2', 30, 27, 13), -- Desenho de Construção Civil, 2º Módulo
('DC3', '3', 30, 28, 13), -- Desenho de Construção Civil, 3º Módulo
('ED3', '1', 30, 29, 3),  -- Técnico em Edificações, 1º Módulo
('ED3', '2', 30, 30, 3),  -- Técnico em Edificações, 2º Módulo
('ED3', '3', 30, 31, 3),  -- Técnico em Edificações, 3º Módulo
('EL3', '3', 30, 32, 4),  -- Técnico em Eletrônica, 3º Módulo
('EAN3', '1', 30, 33, 14), -- Eletrônica ANP, 1º Módulo
('EAN3', '2', 30, 34, 14), -- Eletrônica ANP, 2º Módulo
('ET3', '1', 30, 35, 5),  -- Técnico em Eletrotécnica, 1º Módulo
('ET3', '2', 30, 36, 5),  -- Técnico em Eletrotécnica, 2º Módulo
('ET3', '3', 30, 37, 5),  -- Técnico em Eletrotécnica, 3º Módulo
('INF', '1', 30, 38, 15), -- Técnico em Informática, 1º Módulo
('INF', '2', 30, 39, 15), -- Técnico em Informática, 2º Módulo
('INF', '3', 30, 40, 15), -- Técnico em Informática, 3º Módulo
('MEC', '1', 30, 41, 16), -- Técnico em Mecânica, 1º Módulo
('MEC', '2', 30, 42, 16), -- Técnico em Mecânica, 2º Módulo
('MEC', '3', 30, 43, 16), -- Técnico em Mecânica, 3º Módulo
('MT', '1', 30, 44, 17),  -- Técnico em Mecatrônica, 1º Módulo
('MT', '2', 30, 45, 17),  -- Técnico em Mecatrônica, 2º Módulo
('ML', '1', 30, 46, 18),  -- Técnico em Metalurgia, 1º Módulo
('SO', '2', 30, 47, 19),  -- Técnico em Soldagem, 2º Módulo
('SO', '3', 30, 48, 19);  -- Técnico em Soldagem, 3º Módulo
SELECT * FROM turmas;

INSERT INTO usuarios_has_turmas (usuarios_cd_usuario, turmas_cd_turma) VALUES 
('1001', 1), 
('1001', 2), 
('0001', 3),
('0001', 4), -- Maristela na turma 1N1
('1011', 1), -- Lucas Pereira na turma 1K2
('1011', 2), -- Lucas Pereira na turma 2K2
('1012', 4), -- Mariana Costa na turma 1N1
('1012', 5), -- Mariana Costa na turma 2N1
('1013', 16); -- Rafael Souza na turma 1I1
SELECT * FROM usuarios_has_turmas;

USE escola;

-- =================================================================
-- PARTE 0: LIMPEZA SEGURA (Garante que o script possa ser rodado várias vezes)
-- =================================================================
SET FOREIGN_KEY_CHECKS=0;
-- Limpa apenas os dados que este script cria (EVT_APRESENTACAO_... e professores 1016+)
DELETE FROM resolucao_eventos_usuarios WHERE eventos_cd_evento LIKE 'EVT_APRESENTACAO_%';
DELETE FROM eventos_has_turmas WHERE eventos_cd_evento LIKE 'EVT_APRESENTACAO_%';
DELETE FROM eventos WHERE cd_evento LIKE 'EVT_APRESENTACAO_%';
DELETE FROM usuarios_has_turmas WHERE usuarios_cd_usuario >= '1016';
DELETE FROM usuarios WHERE cd_usuario >= '1016' AND tipo_usuario_ic_usuario = 'Professor';
SET FOREIGN_KEY_CHECKS=1;

-- =================================================================
-- PARTE 1: ADICIONANDO 10 NOVOS PROFESSORES (1016 a 1025)
-- =================================================================
INSERT INTO usuarios (cd_usuario, nm_usuario, cd_senha, nm_email, tipo_usuario_ic_usuario) VALUES 
('1016', 'Fernanda Lima', 'prof123', 'fernanda.lima@etec.com', 'Professor'),
('1017', 'Gustavo Ribeiro', 'prof123', 'gustavo.ribeiro@etec.com', 'Professor'),
('1018', 'Heloisa Santos', 'prof123', 'heloisa.santos@etec.com', 'Professor'),
('1019', 'Igor Andrade', 'prof123', 'igor.andrade@etec.com', 'Professor'),
('1020', 'Julia Martins', 'prof123', 'julia.martins@etec.com', 'Professor'),
('1021', 'Kevin Borges', 'prof123', 'kevin.borges@etec.com', 'Professor'),
('1022', 'Leticia Barros', 'prof123', 'leticia.barros@etec.com', 'Professor'),
('1023', 'Miguel Oliveira', 'prof123', 'miguel.oliveira@etec.com', 'Professor'),
('1024', 'Natalia Costa', 'prof123', 'natalia.costa@etec.com', 'Professor'),
('1025', 'Otávio Pereira', 'prof123', 'otavio.pereira@etec.com', 'Professor');

-- =================================================================
-- PARTE 2: ASSOCIANDO PROFESSORES A TURMAS (Antigos e Novos)
-- =================================================================
INSERT INTO usuarios_has_turmas (usuarios_cd_usuario, turmas_cd_turma) VALUES 
-- Professores antigos em mais turmas:
('1001', 4), ('1011', 4), ('1012', 3), ('1013', 17), ('1014', 16), ('1015', 17),
-- Professores novos:
('1016', 1), ('1016', 2), ('1017', 4), ('1017', 5), ('1017', 6), ('1018', 16), ('1018', 17),
('1019', 7), ('1020', 1), ('1020', 4), ('1020', 7), ('1021', 13), ('1021', 14), ('1021', 15),
('1022', 1), ('1022', 3), ('1022', 5), ('1022', 7), ('1023', 4), ('1023', 5), ('1023', 6),
('1024', 10), ('1024', 11), ('1024', 12), ('1025', 16), ('1025', 17), ('1025', 18);

-- =================================================================
-- PARTE 3: ADICIONANDO 18 NOVOS EVENTOS (Foco: 24/11 a 12/12)
-- =================================================================
-- Eventos Aprovados (Para encher a agenda na apresentação)
INSERT INTO eventos (cd_evento, dt_evento, nm_evento, horario_inicio, horario_fim, tipo_evento, ds_descricao, status, cd_usuario_solicitante, dt_solicitacao, cd_usuario_aprovador) VALUES 
('EVT_APRESENTACAO_01', '2025-11-24', 'Abertura da Semana de TCCs', '08:00', '09:40', 'Palestra', 'Palestra de abertura com Coordenador André.', 'Aprovado', '0002', '2025-11-15', '0002'),
('EVT_APRESENTACAO_02', '2025-11-25', 'Banca TCC Turma 3I1', '13:30', '16:00', 'Outro', 'Apresentação das bancas de TCC da turma 3I1.', 'Aprovado', '1001', '2025-11-15', '0002'),
('EVT_APRESENTACAO_03', '2025-11-25', 'Banca TCC Turma 3G2', '19:20', '22:10', 'Outro', 'Apresentação das bancas de TCC da turma 3G2.', 'Aprovado', '1016', '2025-11-16', '0002'),
('EVT_APRESENTACAO_04', '2025-11-26', 'Palestra: Mercado de Logística', '10:00', '11:40', 'Palestra', 'Palestra com convidado externo sobre o Porto de Santos.', 'Aprovado', '0001', '2025-11-16', '0002'),
('EVT_APRESENTACAO_05', '2025-11-27', 'Visita Técnica (Porto)', '08:00', '12:30', 'Visita Técnica', 'Visita técnica das turmas de Logística ao Porto.', 'Aprovado', '0002', '2025-11-17', '0002'),
('EVT_APRESENTACAO_06', '2025-11-28', 'Prova de Redes (1N1)', '19:20', '21:00', 'Prova', 'Prova bimestral de Redes para a turma 1N1.', 'Aprovado', '1017', '2025-11-18', '0002'),
('EVT_APRESENTACAO_07', '2025-12-01', 'Conselho de Classe (1K2)', '08:50', '10:00', 'Conselho de Classe', 'Conselho de Classe da turma 1K2.', 'Aprovado', '0002', '2025-11-20', '0002'),
('EVT_APRESENTACAO_08', '2025-12-02', 'Amistoso Futsal (Tarde)', '16:20', '18:00', 'Evento Esportivo', 'Jogo amistoso entre 1I1 e 1G2.', 'Aprovado', '1018', '2025-11-20', '0002'),
('EVT_APRESENTACAO_09', '2025-12-03', 'Encerramento Semestre (Noite)', '20:10', '21:00', 'Reunião', 'Reunião geral com professores da noite.', 'Aprovado', '0002', '2025-11-21', '0002'),
('EVT_APRESENTACAO_10', '2025-12-04', 'Feira de Projetos 1I1', '13:30', '16:00', 'Palestra', 'Apresentação da feira de projetos da 1I1.', 'Aprovado', '1022', '2025-11-22', '0002'),
('EVT_APRESENTACAO_11', '2025-12-05', 'Entrega de Notas (Geral)', '07:10', '18:00', 'Reunião', 'Dia reservado para fechamento e entrega de notas.', 'Aprovado', '0002', '2025-11-23', '0002'),
('EVT_APRESENTACAO_12', '2025-12-08', 'Palestra Formatação TCC (3º Anos)', '10:00', '11:40', 'Palestra', 'Palestra sobre normas ABNT para os TCCs.', 'Aprovado', '1001', '2025-11-25', '0002'),
('EVT_APRESENTACAO_13', '2025-12-09', 'Palestra Cibersegurança', '19:20', '21:00', 'Palestra', 'Palestra com especialista em cibersegurança.', 'Aprovado', '1017', '2025-11-25', '0002'),
('EVT_APRESENTACAO_14', '2025-12-10', 'Visita Google (Cancelada)', '08:00', '17:10', 'Visita Técnica', 'Visita à sede do Google (foi recusada pelo coordenador).', 'Recusado', '1011', '2025-11-26', '0002'),
('EVT_APRESENTACAO_15', '2025-12-11', 'Prova Final 2G2', '14:20', '16:00', 'Prova', 'Prova final da turma 2G2.', 'Aprovado', '1016', '2025-11-27', '0002');

-- Eventos SOLICITADOS (Para testar filtros e aprovações)
INSERT INTO eventos (cd_evento, dt_evento, nm_evento, horario_inicio, horario_fim, tipo_evento, ds_descricao, status, cd_usuario_solicitante, dt_solicitacao) VALUES 
('EVT_APRESENTACAO_16', '2025-12-15', 'Planejamento 2026', '10:00', '11:40', 'Reunião', 'Reunião de planejamento para o próximo ano letivo.', 'Solicitado', '1020', '2025-11-28'),
('EVT_APRESENTACAO_17', '2025-12-16', 'Confraternização Professores', '19:20', '22:10', 'Outro', 'Confraternização de fim de ano dos professores.', 'Solicitado', '1011', '2025-11-29'),
('EVT_APRESENTACAO_18', '2025-12-17', 'Teste de Exclusão de Prof (1N1)', '10:00', '10:50', 'Outro', 'Evento para testar a lógica de exclusão de professor.', 'Solicitado', '0001', '2025-11-30');

-- =================================================================
-- PARTE 4: ASSOCIANDO TURMAS E PROFESSORES AOS NOVOS EVENTOS
-- =================================================================
INSERT INTO eventos_has_turmas (eventos_cd_evento, turmas_cd_turma) VALUES 
('EVT_APRESENTACAO_01', 18), ('EVT_APRESENTACAO_01', 9), ('EVT_APRESENTACAO_01', 6), ('EVT_APRESENTACAO_01', 15), ('EVT_APRESENTACAO_01', 12), ('EVT_APRESENTACAO_01', 3),
('EVT_APRESENTACAO_02', 18), ('EVT_APRESENTACAO_03', 9),
('EVT_APRESENTACAO_04', 7), ('EVT_APRESENTACAO_04', 8), ('EVT_APRESENTACAO_04', 9),
('EVT_APRESENTACAO_05', 7), ('EVT_APRESENTACAO_05', 8), ('EVT_APRESENTACAO_05', 9),
('EVT_APRESENTACAO_06', 4), ('EVT_APRESENTACAO_07', 1), ('EVT_APRESENTACAO_08', 16), ('EVT_APRESENTACAO_08', 7),
('EVT_APRESENTACAO_09', 4), ('EVT_APRESENTACAO_09', 5), ('EVT_APRESENTACAO_09', 6),
('EVT_APRESENTACAO_10', 16),
('EVT_APRESENTACAO_11', 1), ('EVT_APRESENTACAO_11', 2), ('EVT_APRESENTACAO_11', 3), ('EVT_APRESENTACAO_11', 4), ('EVT_APRESENTACAO_11', 5), ('EVT_APRESENTACAO_11', 6), 
('EVT_APRESENTACAO_11', 7), ('EVT_APRESENTACAO_11', 8), ('EVT_APRESENTACAO_11', 9), ('EVT_APRESENTACAO_11', 10), ('EVT_APRESENTACAO_11', 11), ('EVT_APRESENTACAO_11', 12), 
('EVT_APRESENTACAO_11', 13), ('EVT_APRESENTACAO_11', 14), ('EVT_APRESENTACAO_11', 15), ('EVT_APRESENTACAO_11', 16), ('EVT_APRESENTACAO_11', 17), ('EVT_APRESENTACAO_11', 18),
('EVT_APRESENTACAO_12', 3), ('EVT_APRESENTACAO_12', 6), ('EVT_APRESENTACAO_12', 9), ('EVT_APRESENTACAO_12', 12), ('EVT_APRESENTACAO_12', 15), ('EVT_APRESENTACAO_12', 18),
('EVT_APRESENTACAO_13', 4), ('EVT_APRESENTACAO_13', 5), ('EVT_APRESENTACAO_13', 6),
('EVT_APRESENTACAO_14', 4), ('EVT_APRESENTACAO_14', 5), ('EVT_APRESENTACAO_14', 6),
('EVT_APRESENTACAO_15', 8),
('EVT_APRESENTACAO_16', 1), ('EVT_APRESENTACAO_16', 4), ('EVT_APRESENTACAO_16', 7),
('EVT_APRESENTACAO_17', 4), ('EVT_APRESENTACAO_17', 5), ('EVT_APRESENTACAO_17', 6),
('EVT_APRESENTACAO_18', 4);

-- **A CORREÇÃO ESTÁ AQUI**
INSERT INTO resolucao_eventos_usuarios (eventos_cd_evento, usuarios_cd_usuario, status_resolucao) VALUES 
('EVT_APRESENTACAO_16', '1001', 'Aprovado'), ('EVT_APRESENTACAO_16', '1011', 'Recusado'), ('EVT_APRESENTACAO_16', '0001', 'Pendente'),
('EVT_APRESENTACAO_16', '1016', 'Pendente'), ('EVT_APRESENTACAO_16', '1017', 'Pendente'), ('EVT_APRESENTACAO_16', '1018', 'Pendente'),
('EVT_APRESENTACAO_16', '1023', 'Pendente'), 
('EVT_APRESENTACAO_16', '1022', 'Pendente'), -- CORRIGIDO: 1026 -> 1022 (Leticia Barros)
('EVT_APRESENTACAO_16', '1019', 'Pendente'),

('EVT_APRESENTACAO_17', '1001', 'Pendente'), ('EVT_APRESENTACAO_17', '0001', 'Pendente'), ('EVT_APRESENTACAO_17', '1012', 'Pendente'),
('EVT_APRESENTACAO_17', '1017', 'Pendente'), ('EVT_APRESENTACAO_17', '1018', 'Pendente'), ('EVT_APRESENTACAO_17', '1020', 'Pendente'),
('EVT_APRESENTACAO_17', '1023', 'Pendente'), 
('EVT_APRESENTACAO_17', '1022', 'Pendente'), -- CORRIGIDO: 1026 -> 1022 (Leticia Barros)

('EVT_APRESENTACAO_18', '1017', 'Pendente'), ('EVT_APRESENTACAO_18', '1018', 'Pendente'),
('EVT_APRESENTACAO_18', '1020', 'Pendente'), ('EVT_APRESENTACAO_18', '1023', 'Pendente');