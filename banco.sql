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
    cd_usuario_aprovador VARCHAR(10) NULL DEFAULT NULL, 
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

DROP TABLE IF EXISTS usuarios_has_cursos;
CREATE TABLE usuarios_has_cursos (
    usuarios_cd_usuario VARCHAR(10) NOT NULL,
    cursos_cd_curso INT NOT NULL,
    PRIMARY KEY (usuarios_cd_usuario, cursos_cd_curso),
    FOREIGN KEY (usuarios_cd_usuario) REFERENCES usuarios(cd_usuario),
    FOREIGN KEY (cursos_cd_curso) REFERENCES cursos(cd_curso)
);


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



INSERT INTO usuarios_has_cursos (usuarios_cd_usuario, cursos_cd_curso) VALUES
('0002', 2), 
('2001', 2), 
('2011', 1), 
('2012', 4), 
('2013', 3); 


INSERT INTO turmas ( nm_turma, ic_serie, qt_alunos, cd_sala, cursos_cd_curso) VALUES 
('1K2', '1', 36, 1, 1),  
('2K2', '2', 33, 2, 1),  
('3K2', '3', 32, 3, 1),   
('1N1', '1', 36, 4, 2),  
('2N1', '2', 34, 4, 2),  
('3N1', '3', 32, 4, 2),   
('1G2', '1', 36, 5, 3),  
('2G2', '2', 35, 6, 3),   
('3G2', '3', 35, 7, 3),   
('1P1', '1', 36, 8, 4),   
('2P1', '2', 34, 9, 4),   
('3P1', '3', 31, 10, 4),  
('1R1', '1', 36, 11, 5),  
('2R1', '2', 34, 11, 5),  
('3R1', '3', 35, 11, 5),  
('1I1', '1', 36, 12, 6),  
('2I1', '2', 35, 13, 6), 
('3I1', '3', 31, 14, 6),  
('1S2', '1', 36, 15, 7),  
('2S2', '2', 35, 16, 7),  
('3S2', '3', 32, 17, 7),  
('GT3', '1', 36, 18, 8), 
('1MDS3', '1', 34, 19, 9), 
('2MDS3', '2', 33, 20, 9), 
('1MET1', '1', 36, 21, 10), 
('2MET2', '2', 34, 22, 10), 
('1MM3', '1', 34, 23, 11), 
('2AV3', '2', 36, 24, 12),
('3AV3', '3', 32, 25, 12), 
('1DC3', '1', 20, 26, 13), 
('2DC3', '2', 18, 27, 13), 
('3DC3', '3', 22, 28, 13),
('1ED3', '1', 15, 29, 3),  
('2ED3', '2', 23, 30, 3),  
('3ED3', '3', 22, 31, 3),  
('3EL3', '3', 29, 32, 4),  
('1EAN3', '1', 14, 33, 14), 
('2EAN3', '2', 13, 34, 14), 
('1ET3', '1', 30, 35, 5), 
('2ET3', '2', 26, 36, 5),  
('3ET3', '3', 20, 37, 5),  
('1INF', '1', 30, 38, 15),
('2INF', '2', 29, 39, 15),
('3INF', '3', 28, 40, 15), 
('1MEC', '1', 15, 41, 16), 
('2MEC', '2', 16, 42, 16), 
('3MEC', '3', 18, 43, 16), 
('1MT', '1',24, 44, 17),  
('2MT', '2', 23, 45, 17),  
('1ML', '1', 21, 46, 18),  
('2SO', '2', 13, 47, 19),  
('3SO', '3', 10, 48, 19); 
SELECT * FROM turmas;

INSERT INTO usuarios_has_turmas (usuarios_cd_usuario, turmas_cd_turma) VALUES 
('1001', 1), 
('1001', 2), 
('0001', 3),
('0001', 4), 
('1011', 1), 
('1011', 2), 
('1012', 4), 
('1012', 5),
('1013', 16); 
SELECT * FROM usuarios_has_turmas;

USE escola;


SET FOREIGN_KEY_CHECKS=0;


DELETE FROM resolucao_eventos_usuarios WHERE eventos_cd_evento LIKE 'EVT_FINAL_%';
DELETE FROM eventos_has_turmas WHERE eventos_cd_evento LIKE 'EVT_FINAL_%';
DELETE FROM eventos WHERE cd_evento LIKE 'EVT_FINAL_%';
DELETE FROM usuarios_has_turmas WHERE usuarios_cd_usuario >= '1016';


DELETE FROM usuarios_has_turmas WHERE usuarios_cd_usuario = '1001' AND turmas_cd_turma = 4;
DELETE FROM usuarios_has_turmas WHERE usuarios_cd_usuario = '1011' AND turmas_cd_turma = 4;
DELETE FROM usuarios_has_turmas WHERE usuarios_cd_usuario = '1012' AND turmas_cd_turma = 3;
DELETE FROM usuarios_has_turmas WHERE usuarios_cd_usuario = '1013' AND turmas_cd_turma = 17;
DELETE FROM usuarios_has_turmas WHERE usuarios_cd_usuario = '1014' AND turmas_cd_turma = 16;
DELETE FROM usuarios_has_turmas WHERE usuarios_cd_usuario = '1015' AND turmas_cd_turma = 17;


DELETE FROM usuarios WHERE cd_usuario >= '1016' AND tipo_usuario_ic_usuario = 'Professor';

SET FOREIGN_KEY_CHECKS=1;

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
('1025', 'Otávio Pereira', 'prof123', 'otavio.pereira@etec.com', 'Professor'),
('1026', 'Patricia Gomes', 'prof123', 'patricia.gomes@etec.com', 'Professor'),
('1027', 'Ricardo Alves', 'prof123', 'ricardo.alves@etec.com', 'Professor'),
('1028', 'Simone Jesus', 'prof123', 'simone.jesus@etec.com', 'Professor');


INSERT INTO usuarios_has_turmas (usuarios_cd_usuario, turmas_cd_turma) VALUES 

('1001', 4), ('1011', 4), ('1012', 3), ('1013', 17), ('1014', 16), ('1015', 17),
('1016', 1), ('1016', 2), ('1017', 4), ('1017', 5), ('1017', 6), 
('1018', 16), ('1018', 17), ('1019', 7), 
('1020', 1), ('1020', 4), ('1020', 7), 
('1021', 13), ('1021', 14), ('1021', 15),
('1022', 1), ('1022', 3), ('1022', 5), ('1022', 7), 
('1023', 4), ('1023', 5), ('1023', 6),
('1024', 10), ('1024', 11), ('1024', 12), 
('1025', 16), ('1025', 17), ('1025', 18),
('1026', 2), ('1026', 8), ('1026', 9), 
('1027', 7), ('1027', 8), ('1027', 9),
('1028', 16), ('1028', 17), ('1028', 18);


INSERT INTO eventos (cd_evento, dt_evento, nm_evento, horario_inicio, horario_fim, tipo_evento, ds_descricao, status, cd_usuario_solicitante, dt_solicitacao, cd_usuario_aprovador) VALUES 
('EVT_FINAL_01', '2025-11-24', 'Abertura TCCs', '08:00', '09:40', 'Palestra', 'Abertura oficial.', 'Aprovado', '0002', '2025-11-15', '0002'),
('EVT_FINAL_02', '2025-11-28', 'Banca TCC 3I1-A', '08:00', '12:30', 'Outro', 'Banca TCC Grupo A.', 'Aprovado', '1001', '2025-11-15', '0002'),
('EVT_FINAL_03', '2025-11-25', 'Banca TCC 3G2-A', '19:20', '22:10', 'Outro', 'Banca TCC Grupo A.', 'Aprovado', '1016', '2025-11-16', '0002'),
('EVT_FINAL_04', '2025-11-26', 'Palestra: Logística', '10:00', '11:40', 'Palestra', 'Convidado externo.', 'Aprovado', '0001', '2025-11-16', '0002'),
('EVT_FINAL_05', '2025-11-27', 'Visita Porto', '08:00', '12:30', 'Visita Técnica', 'Visita ao Porto.', 'Aprovado', '0002', '2025-11-17', '0002'),
('EVT_FINAL_06', '2025-11-28', 'Prova Redes (1N1)', '19:20', '21:00', 'Prova', 'Prova bimestral.', 'Aprovado', '1017', '2025-11-18', '0002'),
('EVT_FINAL_07', '2025-12-01', 'Conselho 1K2', '08:50', '10:00', 'Conselho de Classe', 'Conselho de Classe.', 'Aprovado', '0002', '2025-11-20', '0002'),
('EVT_FINAL_08', '2025-12-02', 'Amistoso Futsal', '16:20', '18:00', 'Evento Esportivo', '1I1 x 1G2.', 'Aprovado', '1018', '2025-11-20', '0002'),
('EVT_FINAL_09', '2025-12-03', 'Encerramento Noite', '20:10', '21:00', 'Reunião', 'Reunião geral.', 'Aprovado', '0002', '2025-11-21', '0002'),
('EVT_FINAL_50', '2025-12-04', 'Feira Projetos 1I1', '13:30', '16:00', 'Palestra', 'Apresentação.', 'Aprovado', '1022', '2025-11-22', '0002'),
('EVT_FINAL_51', '2025-12-04', 'Reunião 1N1', '13:30', '16:00', 'Reunião', 'Reunião.', 'Aprovado', '1022', '2025-11-22', '0002'),
('EVT_FINAL_10', '2025-12-04', 'Visita 1P1', '13:30', '16:00', 'Visita Técnica', 'Visita..', 'Aprovado', '1022', '2025-11-22', '0002'),
('EVT_FINAL_11', '2025-12-05', 'Entrega Notas', '07:10', '18:00', 'Reunião', 'Fechamento de notas.', 'Aprovado', '0002', '2025-11-23', '0002'),
('EVT_FINAL_12', '2025-12-08', 'Palestra ABNT', '10:00', '11:40', 'Palestra', 'Normas ABNT.', 'Aprovado', '1001', '2025-11-25', '0002');

INSERT INTO eventos (cd_evento, dt_evento, nm_evento, horario_inicio, horario_fim, tipo_evento, ds_descricao, status, cd_usuario_solicitante, dt_solicitacao, cd_usuario_aprovador) VALUES 
('EVT_FINAL_13', '2025-12-09', 'Cibersegurança', '19:20', '21:00', 'Palestra', 'Especialista convidado.', 'Aprovado', '1017', '2025-11-25', '0002'), -- ds_descricao estava faltando
('EVT_FINAL_14', '2025-12-10', 'Visita Google', '08:00', '17:10', 'Visita Técnica', 'Cancelada.', 'Recusado', '1011', '2025-11-26', '0002'),
('EVT_FINAL_15', '2025-12-11', 'Prova Final 2G2', '14:20', '16:00', 'Prova', 'Prova final.', 'Aprovado', '1016', '2025-11-27', '0002'),
('EVT_FINAL_31', '2025-11-24', 'Prova PW 1N1', '19:20', '21:00', 'Prova', 'Prova PW III.', 'Aprovado', '1011', '2025-11-15', '0002'),
('EVT_FINAL_32', '2025-11-26', 'Apresentação 1I1', '08:50', '10:00', 'Outro', 'Trabalhos.', 'Aprovado', '1013', '2025-11-16', '0002'),
('EVT_FINAL_33', '2025-11-27', 'Palestra Finanças', '20:10', '22:10', 'Palestra', 'Finanças Pessoais.', 'Aprovado', '1023', '2025-11-18', '0002'),
('EVT_FINAL_34', '2025-12-01', 'Prova BD 1N1', '21:20', '22:10', 'Prova', 'Prova Banco de Dados.', 'Aprovado', '0001', '2025-11-20', '0002'),
('EVT_FINAL_35', '2025-12-02', 'Defesa TCC 3N1', '19:20', '21:00', 'Outro', 'Grupo C.', 'Aprovado', '1017', '2025-11-21', '0002'),
('EVT_FINAL_36', '2025-12-04', 'Recuperação 1K2', '08:00', '09:40', 'Prova', 'Eletrônica.', 'Aprovado', '1016', '2025-11-22', '0002'),
('EVT_FINAL_37', '2025-12-08', 'Reunião Pais Noite', '18:30', '19:20', 'Reunião', 'Pais e mestres.', 'Aprovado', '0002', '2025-11-25', '0002'),
('EVT_FINAL_38', '2025-12-09', 'Festa 3º Anos', '08:50', '11:40', 'Outro', 'Confraternização.', 'Aprovado', '1001', '2025-11-26', '0002'),
('EVT_FINAL_39', '2025-12-11', 'Gincana Escolar', '08:00', '11:40', 'Evento Esportivo', 'Pátio.', 'Aprovado', '1020', '2025-11-27', '0002'),
('EVT_FINAL_40', '2025-12-12', 'Encerramento Manhã', '10:50', '11:40', 'Outro', 'Cerimônia.', 'Aprovado', '0002', '2025-11-28', '0002');


INSERT INTO eventos (cd_evento, dt_evento, nm_evento, horario_inicio, horario_fim, tipo_evento, ds_descricao, status, cd_usuario_solicitante, dt_solicitacao) VALUES 
('EVT_FINAL_41', '2025-12-15', 'Planejamento 2026', '10:00', '11:40', 'Reunião', 'Planejamento.', 'Solicitado', '1020', '2025-11-28'),
('EVT_FINAL_42', '2025-12-16', 'Confraternização Profs', '19:20', '22:10', 'Outro', 'Fim de ano.', 'Solicitado', '1011', '2025-11-29'),
('EVT_FINAL_43', '2025-12-17', 'Teste Exclusão 1N1', '10:00', '10:50', 'Outro', 'Teste.', 'Solicitado', '0001', '2025-11-30'),
('EVT_FINAL_44', '2025-12-18', 'Viagem Formatura', '07:10', '18:00', 'Visita Técnica', 'Hopi Hari.', 'Solicitado', '1025', '2025-11-30'),
('EVT_FINAL_45', '2025-12-19', 'Exame Final 1I1', '14:20', '16:00', 'Prova', 'Lógica.', 'Solicitado', '1013', '2025-12-01');

INSERT INTO eventos (cd_evento, dt_evento, nm_evento, horario_inicio, horario_fim, tipo_evento, ds_descricao, status, cd_usuario_solicitante, dt_solicitacao, cd_usuario_aprovador) VALUES 
('EVT_FINAL_46', '2025-10-30', 'Festa Halloween', '19:20', '22:10', 'Outro', 'Recusado.', 'Recusado', '1012', '2025-10-15', '0002'),
('EVT_FINAL_47', '2025-11-20', 'Visita FUTEBOL', '08:00', '17:10', 'Visita Técnica', 'Museu do futebol.', 'Recusado', '1015', '2025-11-01', '0002');


INSERT INTO eventos_has_turmas (eventos_cd_evento, turmas_cd_turma) VALUES 
('EVT_FINAL_01', 18), ('EVT_FINAL_01', 9), ('EVT_FINAL_01', 6), ('EVT_FINAL_01', 15),
('EVT_FINAL_02', 18), ('EVT_FINAL_03', 9), ('EVT_FINAL_04', 7), ('EVT_FINAL_04', 8),
('EVT_FINAL_05', 7), ('EVT_FINAL_05', 8), ('EVT_FINAL_06', 4), ('EVT_FINAL_07', 1),
('EVT_FINAL_08', 16), ('EVT_FINAL_09', 4), ('EVT_FINAL_09', 5), ('EVT_FINAL_10', 16),
('EVT_FINAL_11', 1), ('EVT_FINAL_11', 2), ('EVT_FINAL_11', 3), ('EVT_FINAL_11', 4),
('EVT_FINAL_12', 3), ('EVT_FINAL_12', 6), ('EVT_FINAL_13', 4), ('EVT_FINAL_13', 5),
('EVT_FINAL_14', 4), ('EVT_FINAL_14', 5), ('EVT_FINAL_15', 8), ('EVT_FINAL_31', 4),
('EVT_FINAL_32', 16), ('EVT_FINAL_33', 4), ('EVT_FINAL_34', 4), ('EVT_FINAL_35', 6),
('EVT_FINAL_36', 1), ('EVT_FINAL_37', 4), ('EVT_FINAL_37', 5), ('EVT_FINAL_38', 16),
('EVT_FINAL_38', 17), ('EVT_FINAL_39', 1), ('EVT_FINAL_39', 7), ('EVT_FINAL_40', 2),
('EVT_FINAL_41', 1), ('EVT_FINAL_41', 4), ('EVT_FINAL_42', 4), ('EVT_FINAL_42', 5),
('EVT_FINAL_43', 4), ('EVT_FINAL_44', 18), ('EVT_FINAL_45', 16), ('EVT_FINAL_46', 4),
('EVT_FINAL_47', 16);


INSERT INTO resolucao_eventos_usuarios (eventos_cd_evento, usuarios_cd_usuario, status_resolucao) VALUES 
('EVT_FINAL_41', '1001', 'Aprovado'), ('EVT_FINAL_41', '1011', 'Recusado'), 
('EVT_FINAL_41', '1016', 'Pendente'), ('EVT_FINAL_41', '1017', 'Pendente'),
('EVT_FINAL_42', '1001', 'Pendente'), ('EVT_FINAL_42', '0001', 'Pendente'), 
('EVT_FINAL_42', '1012', 'Pendente'), ('EVT_FINAL_42', '1017', 'Pendente'),
('EVT_FINAL_43', '1017', 'Pendente'), ('EVT_FINAL_43', '1020', 'Pendente'), 
('EVT_FINAL_43', '1023', 'Pendente'),
('EVT_FINAL_44', '1028', 'Pendente'),
('EVT_FINAL_45', '1014', 'Aprovado'), ('EVT_FINAL_45', '1018', 'Pendente'), 
('EVT_FINAL_45', '1025', 'Pendente');

ALTER TABLE resolucao_eventos_usuarios ADD COLUMN ds_motivo TEXT NULL;