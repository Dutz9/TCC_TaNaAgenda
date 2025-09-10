drop database if exists escola;

CREATE DATABASE escola;

USE escola;
 
Drop table if exists tipo_usuario;

CREATE TABLE tipo_usuario (

    ic_usuario ENUM('Coordenador', 'Professor','Administrador') NOT NULL,

    PRIMARY KEY (ic_usuario)

);
 
Drop table if exists usuarios;

CREATE TABLE usuarios (

    cd_usuario VARCHAR(45) NOT NULL,

    nm_usuario VARCHAR(45) NOT NULL,

    cd_senha VARCHAR(45) NOT NULL,

    cd_telefone VARCHAR(45),

    nm_email VARCHAR(45),

    tipo_usuario_ic_usuario ENUM('Coordenador', 'Professor','Administrador') NOT NULL,

    PRIMARY KEY (cd_usuario),

    FOREIGN KEY (tipo_usuario_ic_usuario) REFERENCES tipo_usuario(ic_usuario)

);
 
Drop table if exists cursos;

CREATE TABLE cursos (

    cd_curso INT NOT NULL AUTO_INCREMENT,

    nm_curso VARCHAR(45) NOT NULL,

    ic_periodo ENUM('Manha','Tarde','Noite') NOT NULL,

    PRIMARY KEY (cd_curso)

);
 
Drop table if exists turmas;

CREATE TABLE turmas (

    cd_turma INT NOT NULL AUTO_INCREMENT,

    nm_turma VARCHAR(45) NOT NULL,

    ic_serie ENUM('1', '2', '3') NOT NULL,

    qt_alunos INT NOT NULL,
   
   cd_sala int not null,

    cursos_cd_curso INT NOT NULL,

    PRIMARY KEY (cd_turma),

    FOREIGN KEY (cursos_cd_curso) REFERENCES cursos(cd_curso)

);
 
 
Drop table if exists eventos;

CREATE TABLE eventos (

    cd_evento VARCHAR(45) NOT NULL,

    dt_evento DATE NOT NULL,

    nm_evento VARCHAR(45) NOT NULL,

    horario_inicio VARCHAR(45) NOT NULL,

    horario_fim VARCHAR(45) NOT NULL,

    tipo_evento ENUM('Palestra', 'Visita tecnica', 'Reuniao') NOT NULL,

    ds_descricao VARCHAR(200),

    PRIMARY KEY (cd_evento)

);
 
Drop table if exists resolucao_eventos_usuarios;

CREATE TABLE resolucao_eventos_usuarios (

    eventos_cd_evento VARCHAR(45) NOT NULL,

    usuarios_cd_usuario VARCHAR(45) NOT NULL,

    PRIMARY KEY (eventos_cd_evento, usuarios_cd_usuario),

    FOREIGN KEY (eventos_cd_evento) REFERENCES eventos(cd_evento),

    FOREIGN KEY (usuarios_cd_usuario) REFERENCES usuarios(cd_usuario)

);
 
 
insert into tipo_usuario(ic_usuario) values ('Professor');

insert into tipo_usuario(ic_usuario) values ('Administrador');

insert into tipo_usuario(ic_usuario) values ('Coordenador');
 
insert into usuarios (cd_usuario, nm_usuario, cd_senha, cd_telefone, nm_email, tipo_usuario_ic_usuario) values 

(1,'Maristela','123','13 9999-9999','maristela@etec.com','Professor'),
(2,'Andre','1234','13 8888-8888','andre@etec.com','Coordenador'),
(3,'Marquinhos','12345','13 7777-7777','marquinhos@etec.com','Administrador'),
('1001', 'Ana Silva', 'pass123', '13 9111-1111', 'ana.silva@etec.com', 'Professor'),
('1002', 'Bruno Costa', 'pass456', '13 9112-1112', 'bruno.costa@etec.com', 'Professor'),
('1003', 'Carla Souza', 'pass789', '13 9113-1113', 'carla.souza@etec.com', 'Professor'),
('1004', 'Diego Lima', 'pass101', '13 9114-1114', 'diego.lima@etec.com', 'Professor'),
('1005', 'Elisa Pereira', 'pass102', '13 9115-1115', 'elisa.pereira@etec.com', 'Professor'),
('1006', 'Felipe Santos', 'pass103', '13 9116-1116', 'felipe.santos@etec.com', 'Professor'),
('1007', 'Gabriela Almeida', 'pass104', '13 9117-1117', 'gabriela.almeida@etec.com', 'Professor'),
('1008', 'Hugo Ferreira', 'pass105', '13 9118-1118', 'hugo.ferreira@etec.com', 'Professor'),
('1009', 'Isabela Mendes', 'pass106', '13 9119-1119', 'isabela.mendes@etec.com', 'Professor'),
('1010', 'João Oliveira', 'pass107', '13 9120-1120', 'joao.oliveira@etec.com', 'Professor'),
('2001', 'Karen Rodrigues', 'coord123', '13 9221-2221', 'karen.rodrigues@etec.com', 'Coordenador'),
('2002', 'Lucas Martins', 'coord456', '13 9222-2222', 'lucas.martins@etec.com', 'Coordenador'),
('2003', 'Mariana Gomes', 'coord789', '13 9223-2223', 'mariana.gomes@etec.com', 'Coordenador'),
('2004', 'Nelson Carvalho', 'coord101', '13 9224-2224', 'nelson.carvalho@etec.com', 'Coordenador'),
('2005', 'Olga Ferreira', 'coord102', '13 9225-2225', 'olga.ferreira@etec.com', 'Coordenador'),
('2006', 'Paulo Henrique', 'coord103', '13 9226-2226', 'paulo.henrique@etec.com', 'Coordenador'),
('2007', 'Quiteria Lopes', 'coord104', '13 9227-2227', 'quiteria.lopes@etec.com', 'Coordenador'),
('2008', 'Rafael Almeida', 'coord105', '13 9228-2228', 'rafael.almeida@etec.com', 'Coordenador'),
('2009', 'Sofia Castro', 'coord106', '13 9229-2229', 'sofia.castro@etec.com', 'Coordenador'),
('2010', 'Tiago Ribeiro', 'coord107', '13 9230-2230', 'tiago.ribeiro@etec.com', 'Coordenador');
 
select * from usuarios;
 
insert into cursos( cd_curso,nm_curso,ic_periodo) values 

(1,'Informática para Internet','Manha'),
(2,'Edificações','Tarde'),
(3,'Mecanica','Noite'),
 (4,'Eletrotecnica','Manha'),
 (5,'eletronica','Manha'),
 (6,'Desenvolvimento de sistema','manha'),
 (7,'Desenvolvimento de jogos','Tarde'),
 (8,'Automação','Tarde');
select * from cursos;
 
 
insert into turmas (nm_turma, ic_serie, cursos_cd_curso, qt_alunos,cd_sala) values 
('3I1','3',1,'34','2'),
('2P1','2',4,'36','8'),
('3G2','3',2,'33','7'),
('3K2','3','7','30','6');
select * from turmas;
 
insert into eventos(  cd_evento,dt_evento,nm_evento,horario_inicio ,horario_fim ,tipo_evento,ds_descricao) values
 (1,'2025-12-25','Palestra USP ','10:00','10:50','Palestra','palestra sobra ex-alunos da etec que passaram na USP');
 
select * from eventos;
 
 