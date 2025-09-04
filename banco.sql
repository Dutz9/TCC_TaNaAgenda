drop database if exists escola;
CREATE DATABASE escola;
USE escola;


CREATE TABLE tipo_usuario (
    ic_usuario ENUM('Aluno', 'Professor','Administrador') NOT NULL,
    PRIMARY KEY (ic_usuario)
);


CREATE TABLE usuarios (
    cd_usuario VARCHAR(45) NOT NULL,
    nm_usuario VARCHAR(45) NOT NULL,
    cd_senha VARCHAR(45) NOT NULL,
    cd_telefone VARCHAR(45),
    nm_email VARCHAR(45),
    tipo_usuario_ic_usuario ENUM('Aluno', 'Professor') NOT NULL,
    PRIMARY KEY (cd_usuario),
    FOREIGN KEY (tipo_usuario_ic_usuario) REFERENCES tipo_usuario(ic_usuario)
);


CREATE TABLE cursos (
    cd_curso INT NOT NULL AUTO_INCREMENT,
    nm_curso VARCHAR(45) NOT NULL,
    ic_periodo ENUM('Matutino', 'Vespertino', 'Noturno') NOT NULL,
    PRIMARY KEY (cd_curso)
);


CREATE TABLE tipo_turma (
    ic_tipo_turma ENUM('A', 'B') NOT NULL,
    PRIMARY KEY (ic_tipo_turma)
);


CREATE TABLE turmas (
    cd_turma INT NOT NULL AUTO_INCREMENT,
    nm_turma VARCHAR(45) NOT NULL,
    ic_serie ENUM('1', '2', '3') NOT NULL,
    qt_alunos INT NOT NULL,
    qt_aulas_semana INT NOT NULL,
    usuarios_cd_usuario VARCHAR(45) NOT NULL,
    cursos_cd_curso INT NOT NULL,
    tipo_turma_ic_tipo_turma ENUM('A', 'B') NOT NULL,
    PRIMARY KEY (cd_turma),
    FOREIGN KEY (usuarios_cd_usuario) REFERENCES usuarios(cd_usuario),
    FOREIGN KEY (cursos_cd_curso) REFERENCES cursos(cd_curso),
    FOREIGN KEY (tipo_turma_ic_tipo_turma) REFERENCES tipo_turma(ic_tipo_turma)
);


CREATE TABLE eventos (
    cd_evento VARCHAR(45) NOT NULL,
    dt_evento DATE NOT NULL,
    nm_evento VARCHAR(45) NOT NULL,
    horario_inicio VARCHAR(45) NOT NULL,
    horario_fim VARCHAR(45) NOT NULL,
    tipo_evento ENUM('Aula', 'Exame', 'Reuniao') NOT NULL,
    ds_descricao VARCHAR(45),
    PRIMARY KEY (cd_evento)
);

CREATE TABLE resolucao_eventos_usuarios (
    eventos_cd_evento VARCHAR(45) NOT NULL,
    usuarios_cd_usuario VARCHAR(45) NOT NULL,
    PRIMARY KEY (eventos_cd_evento, usuarios_cd_usuario),
    FOREIGN KEY (eventos_cd_evento) REFERENCES eventos(cd_evento),
    FOREIGN KEY (usuarios_cd_usuario) REFERENCES usuarios(cd_usuario)
);


