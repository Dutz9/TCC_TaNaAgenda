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