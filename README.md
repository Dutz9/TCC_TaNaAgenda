//Primeira vez usando 

baixe o git
abra o visual studio code
control + j para abrir o cmd
 
trocar de powershell para git bash 
cd /
//saindo dos arquivos todos
cd C/xampp/htdocs
// abrir ht docs

git clone https://github.com/Dutz9/TCC_TaNaAgenda
//mandar o arquivo do git hub para seu pc

cd TCC_TaNaAgenda 

code .

programar

depois disso quando terminar dar um

git add . 
//para salvar as mudanças
 
git push 
//para enviar ao git hub as mudanças 

//outras vezes

abrir no git bash o arquivo do tcc
e dar um git pull 
//para trazer os arquivos que foram mudados pelos outros programadores




















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
    cd_senha VARCHAR(45) NOT NULL,
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
    cd_evento VARCHAR(10) NOT NULL,
    dt_evento DATE NOT NULL,
    nm_evento VARCHAR(45) NOT NULL,
    horario_inicio VARCHAR(10) NOT NULL,
    horario_fim VARCHAR(10) NOT NULL,
    tipo_evento ENUM('Palestra', 'Visita tecnica', 'Reuniao') NOT NULL,
    ds_descricao VARCHAR(200),
    status ENUM('Solicitado', 'Aprovado', 'Recusado') NOT NULL DEFAULT 'Solicitado',
    cd_usuario_solicitante VARCHAR(10) NOT NULL,
    PRIMARY KEY (cd_evento),
    FOREIGN KEY (cd_usuario_solicitante) REFERENCES usuarios(cd_usuario)
) ;


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
    eventos_cd_evento VARCHAR(10) NOT NULL,
    turmas_cd_turma INT NOT NULL,
    PRIMARY KEY (eventos_cd_evento, turmas_cd_turma),
    FOREIGN KEY (eventos_cd_evento) REFERENCES eventos(cd_evento),
    FOREIGN KEY (turmas_cd_turma) REFERENCES turmas(cd_turma)
) ;


DROP TABLE IF EXISTS resolucao_eventos_usuarios;
CREATE TABLE resolucao_eventos_usuarios (
    eventos_cd_evento VARCHAR(10) NOT NULL,
    usuarios_cd_usuario VARCHAR(10) NOT NULL,
    status_resolucao ENUM('Aprovado', 'Recusado') NOT NULL DEFAULT 'Aprovado',
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
('0002', 'Andre', '1234', '13 8888-8888', 'andre@etec.com', 'Coordenador'),
('0003', 'Marquinhos', '12345', '13 7777-7777', 'marquinhos@etec.com', 'Administrador'),
('1001', 'Ana Silva', 'pass123', '13 9111-1111', 'ana.silva@etec.com', 'Professor'),
('2001', 'Karen Rodrigues', 'coord123', '13 9221-2221', 'karen.rodrigues@etec.com', 'Coordenador');
SELECT * FROM usuarios;




INSERT INTO cursos (cd_curso, nm_curso, ic_periodo) VALUES 
(1, 'Informática', 'Manha'),
(2, 'Edificações', 'Tarde'),
(3, 'Mecânica', 'Noite'),
(4, 'Eletrotécnica', 'Manha');
SELECT * FROM cursos;



INSERT INTO turmas (cd_turma, nm_turma, ic_serie, qt_alunos, cd_sala, cursos_cd_curso) VALUES 
(1, '3I1', '3', 34, 2, 1),
(2, '2E1', '2', 36, 8, 4),
(3, '3G2', '3', 33, 7, 2);
SELECT * FROM turmas;



INSERT INTO eventos (cd_evento, dt_evento, nm_evento, horario_inicio, horario_fim, tipo_evento, ds_descricao, status, cd_usuario_solicitante) VALUES 
('EVT001', '2025-12-25', 'Palestra USP', '10:00', '10:50', 'Palestra', 'Palestra sobre ex-alunos da ETEC que passaram na USP', 'Aprovado', '1001'),
('EVT002', '2025-10-10', 'Visita à Fábrica', '09:00', '12:00', 'Visita tecnica', 'Visita guiada à indústria de automação', 'Solicitado', '1001'),
('EVT003', '2025-10-15', 'Reunião Pedagógica', '14:00', '16:00', 'Reuniao', 'Planejamento do próximo semestre', 'Aprovado', '0002');
SELECT * FROM eventos;


INSERT INTO usuarios_has_turmas (usuarios_cd_usuario, turmas_cd_turma) VALUES 
('1001', 1), ('1001', 2), 
('0001', 3);
SELECT * FROM usuarios_has_turmas;

INSERT INTO eventos_has_turmas (eventos_cd_evento, turmas_cd_turma) VALUES 
('EVT001', 1), ('EVT001', 2), 
('EVT002', 1);
SELECT * FROM eventos_has_turmas;

INSERT INTO resolucao_eventos_usuarios (eventos_cd_evento, usuarios_cd_usuario, status_resolucao ) VALUES 
('EVT001', '0002', 'Aprovado'),
('EVT002', '0003', 'Aprovado');
SELECT * FROM resolucao_eventos_usuarios;

