-- MySQL Workbench Forward Engineering (Versão Corrigida para TCC)
-- Melhorias: Adicionados campos para status de eventos e aprovações; padronização de chaves (VARCHAR para cd_usuario); 
-- INSERTs consistentes com dados de teste para fluxos (eventos pendentes/aprovados, vinculações N:N); 
-- comentários em todas as seções; índices para performance em queries comuns (ex.: status de eventos).

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema escola (mantido, com charset para acentos)
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `escola` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci ;
USE `escola` ;

-- -----------------------------------------------------
-- Table `escola`.`cursos`
-- Descrição: Cursos da escola (ex.: Informática). Usado para vincular turmas e coordenadores indiretamente.
-- Melhorias: AUTO_INCREMENT mantido; índice em nm_curso para buscas.
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `escola`.`cursos` (
  `cd_curso` INT NOT NULL AUTO_INCREMENT,
  `nm_curso` VARCHAR(45) NOT NULL,
  `ic_periodo` ENUM('Manha', 'Tarde', 'Noite') NOT NULL,
  PRIMARY KEY (`cd_curso`),
  INDEX `idx_nm_curso` (`nm_curso` ASC))
ENGINE = InnoDB
AUTO_INCREMENT = 9
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;

-- -----------------------------------------------------
-- Table `escola`.`tipo_usuario`
-- Descrição: Tipos de usuários (enum como PK para simplicidade; FK em usuarios).
-- Melhorias: Mantida, mas agora usada corretamente na FK; INSERTs garantem todos os tipos.
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `escola`.`tipo_usuario` (
  `ic_usuario` ENUM('Coordenador', 'Professor', 'Administrador') NOT NULL,
  PRIMARY KEY (`ic_usuario`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;

-- -----------------------------------------------------
-- Table `escola`.`usuarios`
-- Descrição: Usuários (professores, coordenadores, admins). cd_usuario como VARCHAR para códigos custom.
-- Melhorias: Todos INSERTs com cd_usuario como string ('0001' etc.); senha plain-text por simplicidade (futuro: hash); 
-- índice em nm_email para login rápido; mais usuários de teste (10 profs, 10 coords).
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `escola`.`usuarios` (
  `cd_usuario` VARCHAR(45) NOT NULL,
  `nm_usuario` VARCHAR(45) NOT NULL,
  `cd_senha` VARCHAR(45) NOT NULL,  -- Plain-text; futuro: use password_hash
  `cd_telefone` VARCHAR(45) NULL DEFAULT NULL,
  `nm_email` VARCHAR(45) NULL DEFAULT NULL,
  `tipo_usuario_ic_usuario` ENUM('Coordenador', 'Professor', 'Administrador') NOT NULL,
  PRIMARY KEY (`cd_usuario`),
  INDEX `idx_nm_email` (`nm_email` ASC),
  INDEX `tipo_usuario_ic_usuario` (`tipo_usuario_ic_usuario` ASC),
  CONSTRAINT `usuarios_ibfk_1`
    FOREIGN KEY (`tipo_usuario_ic_usuario`)
    REFERENCES `escola`.`tipo_usuario` (`ic_usuario`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;

-- -----------------------------------------------------
-- Table `escola`.`eventos`
-- Descrição: Eventos escolares. cd_evento VARCHAR para códigos como 'EVT_2025...'.
-- Melhorias: Adicionados status (para fluxo aprovação), cd_usuario_solicitante (FK), dt_solicitacao (auto); 
-- índice em status para queries rápidas; INSERTs com status variados para teste.
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `escola`.`eventos` (
  `cd_evento` VARCHAR(45) NOT NULL,
  `dt_evento` DATE NOT NULL,
  `nm_evento` VARCHAR(45) NOT NULL,
  `horario_inicio` VARCHAR(45) NOT NULL,
  `horario_fim` VARCHAR(45) NOT NULL,
  `tipo_evento` ENUM('Palestra', 'Visita tecnica', 'Reuniao') NOT NULL,
  `ds_descricao` VARCHAR(200) NULL DEFAULT NULL,
  `status` ENUM('Solicitado', 'Aprovado', 'Recusado') NOT NULL DEFAULT 'Solicitado',
  `cd_usuario_solicitante` VARCHAR(45) NOT NULL,
  `dt_solicitacao` DATE NOT NULL DEFAULT CURRENT_DATE(),
  PRIMARY KEY (`cd_evento`),
  INDEX `idx_status` (`status` ASC),
  INDEX `idx_solicitante` (`cd_usuario_solicitante` ASC),
  CONSTRAINT `eventos_ibfk_1`
    FOREIGN KEY (`cd_usuario_solicitante`)
    REFERENCES `escola`.`usuarios` (`cd_usuario`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;

-- -----------------------------------------------------
-- Table `escola`.`resolucao_eventos_usuarios`
-- Descrição: Aprovações/recusas N:N (um usuário aprova/recusa um evento).
-- Melhorias: Adicionado status_resolucao para votos individuais (ex.: prof aprova); 
-- útil para fluxo paralelo de profs.
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `escola`.`resolucao_eventos_usuarios` (
  `eventos_cd_evento` VARCHAR(45) NOT NULL,
  `usuarios_cd_usuario` VARCHAR(45) NOT NULL,
  `status_resolucao` ENUM('Aprovado', 'Recusado') NOT NULL DEFAULT 'Aprovado',
  PRIMARY KEY (`eventos_cd_evento`, `usuarios_cd_usuario`),
  INDEX `idx_resolucao_status` (`status_resolucao` ASC),
  CONSTRAINT `resolucao_eventos_usuarios_ibfk_1`
    FOREIGN KEY (`eventos_cd_evento`)
    REFERENCES `escola`.`eventos` (`cd_evento`),
  CONSTRAINT `resolucao_eventos_usuarios_ibfk_2`
    FOREIGN KEY (`usuarios_cd_usuario`)
    REFERENCES `escola`.`usuarios` (`cd_usuario`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;

-- -----------------------------------------------------
-- Table `escola`.`turmas`
-- Descrição: Turmas vinculadas a cursos. Usado para seleção em eventos.
-- Melhorias: AUTO_INCREMENT mantido; mais dados de teste; índice em cursos_cd_curso.
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `escola`.`turmas` (
  `cd_turma` INT NOT NULL AUTO_INCREMENT,
  `nm_turma` VARCHAR(45) NOT NULL,
  `ic_serie` ENUM('1', '2', '3') NOT NULL,
  `qt_alunos` INT NOT NULL,
  `cd_sala` INT NOT NULL,
  `cursos_cd_curso` INT NOT NULL,
  PRIMARY KEY (`cd_turma`),
  INDEX `cursos_cd_curso` (`cursos_cd_curso` ASC),
  CONSTRAINT `turmas_ibfk_1`
    FOREIGN KEY (`cursos_cd_curso`)
    REFERENCES `escola`.`cursos` (`cd_curso`))
ENGINE = InnoDB
AUTO_INCREMENT = 5
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;

-- -----------------------------------------------------
-- Table `escola`.`usuarios_has_turmas`
-- Descrição: N:N professores <-> turmas (para aprovações baseadas em turmas).
-- Melhorias: Comentário adicionado; dados de teste (profs em turmas).
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `escola`.`usuarios_has_turmas` (
  `usuarios_cd_usuario` VARCHAR(45) NOT NULL,
  `turmas_cd_turma` INT NOT NULL,
  PRIMARY KEY (`usuarios_cd_usuario`, `turmas_cd_turma`),
  INDEX `fk_usuarios_has_turmas_turmas1_idx` (`turmas_cd_turma` ASC),
  INDEX `fk_usuarios_has_turmas_usuarios1_idx` (`usuarios_cd_usuario` ASC),
  CONSTRAINT `fk_usuarios_has_turmas_usuarios1`
    FOREIGN KEY (`usuarios_cd_usuario`)
    REFERENCES `escola`.`usuarios` (`cd_usuario`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_usuarios_has_turmas_turmas1`
    FOREIGN KEY (`turmas_cd_turma`)
    REFERENCES `escola`.`turmas` (`cd_turma`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;

-- -----------------------------------------------------
-- Table `escola`.`eventos_has_turmas`
-- Descrição: N:N eventos <-> turmas (um evento pode afetar múltiplas turmas).
-- Melhorias: Dados de teste adicionados (ex.: evento '1' em 2 turmas).
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `escola`.`eventos_has_turmas` (
  `eventos_cd_evento` VARCHAR(45) NOT NULL,
  `turmas_cd_turma` INT NOT NULL,
  PRIMARY KEY (`eventos_cd_evento`, `turmas_cd_turma`),
  INDEX `fk_eventos_has_turmas_turmas1_idx` (`turmas_cd_turma` ASC),
  INDEX `fk_eventos_has_turmas_eventos1_idx` (`eventos_cd_evento` ASC),
  CONSTRAINT `fk_eventos_has_turmas_eventos1`
    FOREIGN KEY (`eventos_cd_evento`)
    REFERENCES `escola`.`eventos` (`cd_evento`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_eventos_has_turmas_turmas1`
    FOREIGN KEY (`turmas_cd_turma`)
    REFERENCES `escola`.`turmas` (`cd_turma`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;

-- -----------------------------------------------------
-- Inserções de Dados de Teste
-- Melhorias: Todos cd_usuario como strings; status variados em eventos; vinculações N:N para teste (ex.: prof '1001' em turma 1; evento '1' em turmas 1 e 2).
-- -----------------------------------------------------

-- Tipos de usuário (garante existência)
INSERT IGNORE INTO `escola`.`tipo_usuario` (`ic_usuario`) VALUES 
('Professor'), ('Administrador'), ('Coordenador');

-- Usuários (corrigidos: cd_usuario strings; mais dados)
INSERT INTO `escola`.`usuarios` (`cd_usuario`, `nm_usuario`, `cd_senha`, `cd_telefone`, `nm_email`, `tipo_usuario_ic_usuario`) VALUES 
('0001', 'Maristela', '123', '13 9999-9999', 'maristela@etec.com', 'Professor'),
('0002', 'Andre', '1234', '13 8888-8888', 'andre@etec.com', 'Coordenador'),
('0003', 'Marquinhos', '12345', '13 7777-7777', 'marquinhos@etec.com', 'Administrador'),
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

-- Cursos (mantidos, com AUTO_INCREMENT)
INSERT INTO `escola`.`cursos` (`cd_curso`, `nm_curso`, `ic_periodo`) VALUES 
(1, 'Informática para Internet', 'Manha'),
(2, 'Edificações', 'Tarde'),
(3, 'Mecanica', 'Noite'),
(4, 'Eletrotecnica', 'Manha'),
(5, 'eletronica', 'Manha'),
(6, 'Desenvolvimento de sistema', 'manha'),
(7, 'Desenvolvimento de jogos', 'Tarde'),
(8, 'Automação', 'Tarde');

-- Turmas (mantidas, com mais uma para teste)
INSERT INTO `escola`.`turmas` (`cd_turma`, `nm_turma`, `ic_serie`, `qt_alunos`, `cd_sala`, `cursos_cd_curso`) VALUES 
(1, '3I1', '3', 34, 2, 1),
(2, '2P1', '2', 36, 8, 4),
(3, '3G2', '3', 33, 7, 2),
(4, '3K2', '3', 30, 6, 7);

-- Usuários_has_turmas (dados de teste: profs em turmas para aprovações)
INSERT INTO `escola`.`usuarios_has_turmas` (`usuarios_cd_usuario`, `turmas_cd_turma`) VALUES 
('1001', 1), ('1001', 2),  -- Ana em 2 turmas
('1002', 1), ('1003', 3), ('1004', 4);

-- Eventos (corrigidos: cd_evento strings; status variados; solicitante '1001' para alguns)
INSERT INTO `escola`.`eventos` (`cd_evento`, `dt_evento`, `nm_evento`, `horario_inicio`, `horario_fim`, `tipo_evento`, `ds_descricao`, `status`, `cd_usuario_solicitante`, `dt_solicitacao`) VALUES
('EVT001', '2025-12-25', 'Palestra USP', '10:00', '10:50', 'Palestra', 'palestra sobre ex-alunos da etec que passaram na USP', 'Aprovado', '1001', '2025-09-18'),
('EVT002', '2025-10-10', 'Visita à Fábrica', '09:00', '12:00', 'Visita tecnica', 'Visita guiada à indústria de automação', 'Solicitado', '1001', '2025-09-18'),
('EVT003', '2025-10-15', 'Reunião Pedagógica', '14:00', '16:00', 'Reuniao', 'Planejamento do próximo semestre', 'Aprovado', '1001', '2025-09-18'),
('EVT004', '2025-11-05', 'Palestra sobre Carreira', '08:30', '09:30', 'Palestra', 'Dicas para ingressar no mercado de tecnologia', 'Aprovado', '1001', '2025-09-18'),
('EVT005', '2025-11-20', 'Visita ao Museu da Tecnologia', '13:00', '16:00', 'Visita tecnica', 'Exploração de inovações tecnológicas', 'Recusado', '1001', '2025-09-18'),
('EVT006', '2025-12-01', 'Workshop de Programação', '10:00', '12:00', 'Palestra', 'Introdução ao desenvolvimento de jogos', 'Aprovado', '1002', '2025-09-18'),
('EVT007', '2025-12-10', 'Reunião de Coordenadores', '15:00', '17:00', 'Reuniao', 'Discussão sobre melhorias no currículo', 'Aprovado', '2001', '2025-09-18'),
('EVT008', '2026-01-15', 'Visita à Usina', '07:00', '11:00', 'Visita tecnica', 'Estudo sobre geração de energia', 'Solicitado', '1003', '2025-09-18'),
('EVT009', '2026-01-20', 'Palestra de Sustentabilidade', '09:00', '10:30', 'Palestra', 'Impacto ambiental das indústrias', 'Aprovado', '1001', '2025-09-18'),
('EVT010', '2026-02-01', 'Reunião de Pais', '18:00', '20:00', 'Reuniao', 'Apresentação dos resultados do ano', 'Aprovado', '1001', '2025-09-18'),
('EVT011', '2026-02-10', 'Feira de Ciências', '08:00', '14:00', 'Palestra', 'Exposição de projetos dos alunos', 'Recusado', '1004', '2025-09-18');

-- Eventos_has_turmas (dados de teste: N:N)
INSERT INTO `escola`.`eventos_has_turmas` (`eventos_cd_evento`, `turmas_cd_turma`) VALUES 
('EVT001', 1), ('EVT001', 2),  -- Evento 1 em 2 turmas
('EVT002', 1), ('EVT003', 3), ('EVT005', 4);

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;