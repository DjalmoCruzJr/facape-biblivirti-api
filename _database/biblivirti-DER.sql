SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

DROP SCHEMA IF EXISTS `biblivirti` ;
CREATE SCHEMA IF NOT EXISTS `biblivirti` DEFAULT CHARACTER SET utf8 ;
USE `biblivirti` ;

-- -----------------------------------------------------
-- Table `biblivirti`.`questao`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `biblivirti`.`questao` ;

CREATE TABLE IF NOT EXISTS `biblivirti`.`questao` (
  `qenid` INT(11) NOT NULL AUTO_INCREMENT,
  `qecdesc` VARCHAR(50) NOT NULL,
  `qectext` TEXT NOT NULL,
  `qelanex` TINYINT(1) NOT NULL DEFAULT 0,
  `qecanex` VARCHAR(255) NULL DEFAULT NULL,
  `qedcadt` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `qedaldt` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`qenid`, `qelanex`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `biblivirti`.`alternativa`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `biblivirti`.`alternativa` ;

CREATE TABLE IF NOT EXISTS `biblivirti`.`alternativa` (
  `alnid` INT(11) NOT NULL AUTO_INCREMENT,
  `alnidqe` INT(11) NOT NULL,
  `alctext` VARCHAR(100) NOT NULL,
  `allcert` TINYINT(1) NOT NULL DEFAULT '0',
  `aldcadt` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `aldaldt` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`alnid`),
  CONSTRAINT `FKALQE`
    FOREIGN KEY (`alnidqe`)
    REFERENCES `biblivirti`.`questao` (`qenid`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COMMENT = '									';

CREATE INDEX `fk_ALTERNATIVA_QUESTAO1_idx` ON `biblivirti`.`alternativa` (`alnidqe` ASC);


-- -----------------------------------------------------
-- Table `biblivirti`.`areainteresse`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `biblivirti`.`areainteresse` ;

CREATE TABLE IF NOT EXISTS `biblivirti`.`areainteresse` (
  `ainid` INT(11) NOT NULL AUTO_INCREMENT,
  `aicdesc` VARCHAR(50) NOT NULL,
  `aidcadt` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `aidaldt` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`ainid`))
ENGINE = InnoDB
AUTO_INCREMENT = 7
DEFAULT CHARACTER SET = utf8;

CREATE UNIQUE INDEX `AICDESC_UNIQUE` ON `biblivirti`.`areainteresse` (`aicdesc` ASC);


-- -----------------------------------------------------
-- Table `biblivirti`.`usuario`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `biblivirti`.`usuario` ;

CREATE TABLE IF NOT EXISTS `biblivirti`.`usuario` (
  `usnid` INT(11) NOT NULL AUTO_INCREMENT,
  `uscfbid` VARCHAR(100) NULL DEFAULT NULL,
  `uscnome` VARCHAR(50) NULL DEFAULT NULL,
  `uscmail` VARCHAR(50) NOT NULL,
  `usclogn` VARCHAR(50) NOT NULL,
  `uscsenh` VARCHAR(32) NOT NULL,
  `uscfoto` VARCHAR(255) NULL DEFAULT NULL,
  `uscstat` CHAR(1) NOT NULL DEFAULT 'A',
  `usdcadt` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `usdaldt` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`usnid`))
ENGINE = InnoDB
AUTO_INCREMENT = 21
DEFAULT CHARACTER SET = utf8;

CREATE UNIQUE INDEX `USCMAIL_UNIQUE` ON `biblivirti`.`usuario` (`uscmail` ASC);

CREATE UNIQUE INDEX `USCLOGIN_UNIQUE` ON `biblivirti`.`usuario` (`usclogn` ASC);

CREATE UNIQUE INDEX `USCFBID_UNIQUE` ON `biblivirti`.`usuario` (`uscfbid` ASC);


-- -----------------------------------------------------
-- Table `biblivirti`.`material`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `biblivirti`.`material` ;

CREATE TABLE IF NOT EXISTS `biblivirti`.`material` (
  `manid` INT(11) NOT NULL AUTO_INCREMENT,
  `macdesc` VARCHAR(100) NOT NULL,
  `mactipo` CHAR(1) NOT NULL,
  `malanex` TINYINT(1) NOT NULL DEFAULT 0,
  `macurl` VARCHAR(255) NULL DEFAULT NULL,
  `macnivl` CHAR(1) NULL DEFAULT NULL,
  `macstat` CHAR(1) NOT NULL DEFAULT 'A',
  `madcadt` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `madaldt` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`manid`))
ENGINE = InnoDB
AUTO_INCREMENT = 7
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `biblivirti`.`avaliacao`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `biblivirti`.`avaliacao` ;

CREATE TABLE IF NOT EXISTS `biblivirti`.`avaliacao` (
  `avnid` INT(11) NOT NULL AUTO_INCREMENT,
  `avnidus` INT(11) NOT NULL,
  `avnidma` INT(11) NOT NULL,
  `avcstat` CHAR(1) NOT NULL,
  `avdindt` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `avdtedt` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`avnid`),
  CONSTRAINT `FKAVUS`
    FOREIGN KEY (`avnidus`)
    REFERENCES `biblivirti`.`usuario` (`usnid`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fkavma`
    FOREIGN KEY (`avnidma`)
    REFERENCES `biblivirti`.`material` (`manid`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE INDEX `fk_AVALIACAO_USUARIO1_idx` ON `biblivirti`.`avaliacao` (`avnidus` ASC);

CREATE INDEX `fk_AVALIACAO_MATERIAL1_idx` ON `biblivirti`.`avaliacao` (`avnidma` ASC);


-- -----------------------------------------------------
-- Table `biblivirti`.`comentario`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `biblivirti`.`comentario` ;

CREATE TABLE IF NOT EXISTS `biblivirti`.`comentario` (
  `cenid` INT(11) NOT NULL AUTO_INCREMENT,
  `cenidus` INT(11) NOT NULL,
  `cenidma` INT(11) NOT NULL,
  `cenidce` INT(11) NULL DEFAULT NULL,
  `cectext` TEXT NOT NULL,
  `celanex` TINYINT(1) NOT NULL DEFAULT 0,
  `cecanex` VARCHAR(255) NULL DEFAULT NULL,
  `cecstat` CHAR(1) NOT NULL,
  `cedcadt` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `cedaldt` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`cenid`),
  CONSTRAINT `FKCEMA`
    FOREIGN KEY (`cenidma`)
    REFERENCES `biblivirti`.`material` (`manid`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `FKCEUS`
    FOREIGN KEY (`cenidus`)
    REFERENCES `biblivirti`.`usuario` (`usnid`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fkcece`
    FOREIGN KEY (`cenidce`)
    REFERENCES `biblivirti`.`comentario` (`cenid`)
    ON DELETE SET NULL
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE INDEX `fk_COMENTARIO_USUARIO1_idx` ON `biblivirti`.`comentario` (`cenidus` ASC);

CREATE INDEX `fk_COMENTARIO_MATERIAL1_idx` ON `biblivirti`.`comentario` (`cenidma` ASC);

CREATE INDEX `fk_COMENTARIO_COMENTARIO1_idx` ON `biblivirti`.`comentario` (`cenidce` ASC);


-- -----------------------------------------------------
-- Table `biblivirti`.`conteudo`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `biblivirti`.`conteudo` ;

CREATE TABLE IF NOT EXISTS `biblivirti`.`conteudo` (
  `conid` INT(11) NOT NULL AUTO_INCREMENT,
  `cocdesc` VARCHAR(100) NOT NULL,
  `codcadt` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `codaldt` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`conid`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE UNIQUE INDEX `cocdesc_UNIQUE` ON `biblivirti`.`conteudo` (`cocdesc` ASC);


-- -----------------------------------------------------
-- Table `biblivirti`.`conteudomaterial`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `biblivirti`.`conteudomaterial` ;

CREATE TABLE IF NOT EXISTS `biblivirti`.`conteudomaterial` (
  `cmnidco` INT(11) NOT NULL,
  `cmnidma` INT(11) NOT NULL,
  PRIMARY KEY (`cmnidco`, `cmnidma`),
  CONSTRAINT `fkcmco`
    FOREIGN KEY (`cmnidco`)
    REFERENCES `biblivirti`.`conteudo` (`conid`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fkcmma`
    FOREIGN KEY (`cmnidma`)
    REFERENCES `biblivirti`.`material` (`manid`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE INDEX `fk_CONTEUDOMATERIAL_MATERIAL1_idx` ON `biblivirti`.`conteudomaterial` (`cmnidma` ASC);


-- -----------------------------------------------------
-- Table `biblivirti`.`grupo`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `biblivirti`.`grupo` ;

CREATE TABLE IF NOT EXISTS `biblivirti`.`grupo` (
  `grnid` INT(11) NOT NULL AUTO_INCREMENT,
  `grnidai` INT(11) NOT NULL,
  `grcnome` VARCHAR(50) NOT NULL,
  `grcfoto` VARCHAR(255) NULL DEFAULT NULL,
  `grctipo` CHAR(1) NOT NULL DEFAULT 'A',
  `grdcadt` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `grdaldt` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`grnid`),
  CONSTRAINT `FKGRAI`
    FOREIGN KEY (`grnidai`)
    REFERENCES `biblivirti`.`areainteresse` (`ainid`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 10
DEFAULT CHARACTER SET = utf8
COMMENT = '				';

CREATE INDEX `fk_GRUPO_AREAINTERESSE1_idx` ON `biblivirti`.`grupo` (`grnidai` ASC);


-- -----------------------------------------------------
-- Table `biblivirti`.`duvida`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `biblivirti`.`duvida` ;

CREATE TABLE IF NOT EXISTS `biblivirti`.`duvida` (
  `dvnid` INT(11) NOT NULL AUTO_INCREMENT,
  `dvnidus` INT(11) NOT NULL,
  `dvnidgr` INT(11) NOT NULL,
  `dvnidco` INT(11) NOT NULL,
  `dvctext` TEXT NOT NULL,
  `dvlanex` TINYINT(1) NOT NULL DEFAULT 0,
  `dvcanex` VARCHAR(255) NULL DEFAULT NULL,
  `dvcstat` CHAR(1) NOT NULL,
  `dvlanon` TINYINT(1) NOT NULL,
  `dvdcadt` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `dvdaldt` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`dvnid`),
  CONSTRAINT `FKDVCO`
    FOREIGN KEY (`dvnidco`)
    REFERENCES `biblivirti`.`conteudo` (`conid`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `FKDVGR`
    FOREIGN KEY (`dvnidgr`)
    REFERENCES `biblivirti`.`grupo` (`grnid`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `FKDVUS`
    FOREIGN KEY (`dvnidus`)
    REFERENCES `biblivirti`.`usuario` (`usnid`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE INDEX `fk_DUVIDA_USUARIO1_idx` ON `biblivirti`.`duvida` (`dvnidus` ASC);

CREATE INDEX `fk_DUVIDA_GRUPO1_idx` ON `biblivirti`.`duvida` (`dvnidgr` ASC);

CREATE INDEX `fk_DUVIDA_CONTEUDO1_idx` ON `biblivirti`.`duvida` (`dvnidco` ASC);


-- -----------------------------------------------------
-- Table `biblivirti`.`duvidaresposta`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `biblivirti`.`duvidaresposta` ;

CREATE TABLE IF NOT EXISTS `biblivirti`.`duvidaresposta` (
  `drnid` INT(11) NOT NULL AUTO_INCREMENT,
  `drniddv` INT(11) NOT NULL,
  `drnidus` INT(11) NOT NULL,
  `drctext` TEXT NOT NULL,
  `drlanex` TINYINT(1) NOT NULL DEFAULT 0,
  `drcanex` VARCHAR(255) NULL DEFAULT NULL,
  `drcstat` CHAR(1) NOT NULL,
  `drdcadt` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `drdaldt` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`drnid`),
  CONSTRAINT `FKDRDV`
    FOREIGN KEY (`drniddv`)
    REFERENCES `biblivirti`.`duvida` (`dvnid`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `FKDRUS`
    FOREIGN KEY (`drnidus`)
    REFERENCES `biblivirti`.`usuario` (`usnid`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE INDEX `fk_DUVIDARESPOSTA_DUVIDA1_idx` ON `biblivirti`.`duvidaresposta` (`drniddv` ASC);

CREATE INDEX `fk_DUVIDARESPOSTA_USUARIO1_idx` ON `biblivirti`.`duvidaresposta` (`drnidus` ASC);


-- -----------------------------------------------------
-- Table `biblivirti`.`grupoconteudo`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `biblivirti`.`grupoconteudo` ;

CREATE TABLE IF NOT EXISTS `biblivirti`.`grupoconteudo` (
  `gcnidgr` INT(11) NOT NULL,
  `gcnidco` INT(11) NOT NULL,
  PRIMARY KEY (`gcnidgr`, `gcnidco`),
  CONSTRAINT `FKGCCO`
    FOREIGN KEY (`gcnidco`)
    REFERENCES `biblivirti`.`conteudo` (`conid`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `FKGCGR`
    FOREIGN KEY (`gcnidgr`)
    REFERENCES `biblivirti`.`grupo` (`grnid`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE INDEX `fk_GRUPOCONTEUDO_CONTEUDO1_idx` ON `biblivirti`.`grupoconteudo` (`gcnidco` ASC);


-- -----------------------------------------------------
-- Table `biblivirti`.`grupomaterial`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `biblivirti`.`grupomaterial` ;

CREATE TABLE IF NOT EXISTS `biblivirti`.`grupomaterial` (
  `gmnidgr` INT(11) NOT NULL,
  `gmnidma` INT(11) NOT NULL,
  PRIMARY KEY (`gmnidgr`, `gmnidma`),
  CONSTRAINT `FKGMGR`
    FOREIGN KEY (`gmnidgr`)
    REFERENCES `biblivirti`.`grupo` (`grnid`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `FKGMMA`
    FOREIGN KEY (`gmnidma`)
    REFERENCES `biblivirti`.`material` (`manid`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE INDEX `fk_GRUPOMATERIAL_MATERIAL1_idx` ON `biblivirti`.`grupomaterial` (`gmnidma` ASC);


-- -----------------------------------------------------
-- Table `biblivirti`.`grupousuario`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `biblivirti`.`grupousuario` ;

CREATE TABLE IF NOT EXISTS `biblivirti`.`grupousuario` (
  `gunidgr` INT(11) NOT NULL,
  `gunidus` INT(11) NOT NULL,
  `guladm` TINYINT(1) NOT NULL DEFAULT '0',
  `gucstat` CHAR(1) NOT NULL DEFAULT 'A',
  `gudcadt` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `gudaldt` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`gunidgr`, `gunidus`),
  CONSTRAINT `FKGUGR`
    FOREIGN KEY (`gunidgr`)
    REFERENCES `biblivirti`.`grupo` (`grnid`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `FKGUUS`
    FOREIGN KEY (`gunidus`)
    REFERENCES `biblivirti`.`usuario` (`usnid`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE INDEX `fk_GRUPOUSUARIO_USUARIO_idx` ON `biblivirti`.`grupousuario` (`gunidus` ASC);


-- -----------------------------------------------------
-- Table `biblivirti`.`historicoacesso`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `biblivirti`.`historicoacesso` ;

CREATE TABLE IF NOT EXISTS `biblivirti`.`historicoacesso` (
  `hanid` INT(11) NOT NULL AUTO_INCREMENT,
  `hanidus` INT(11) NOT NULL,
  `hactipo` CHAR(1) NOT NULL DEFAULT 'M',
  `hanidma` INT(11) NULL,
  `hadendt` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `hadsadt` TIMESTAMP NULL,
  PRIMARY KEY (`hanid`),
  CONSTRAINT `FKHAMA`
    FOREIGN KEY (`hanidma`)
    REFERENCES `biblivirti`.`material` (`manid`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `FKHAUS`
    FOREIGN KEY (`hanidus`)
    REFERENCES `biblivirti`.`usuario` (`usnid`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE INDEX `fk_HISTORICOACESSO_USUARIO1_idx` ON `biblivirti`.`historicoacesso` (`hanidus` ASC);

CREATE INDEX `fk_HISTORICOACESSO_MATERIAL1_idx` ON `biblivirti`.`historicoacesso` (`hanidma` ASC);


-- -----------------------------------------------------
-- Table `biblivirti`.`mensagem`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `biblivirti`.`mensagem` ;

CREATE TABLE IF NOT EXISTS `biblivirti`.`mensagem` (
  `msnid` INT(11) NOT NULL AUTO_INCREMENT,
  `msnidus` INT(11) NOT NULL,
  `msnidgr` INT(11) NOT NULL,
  `msctext` TEXT NOT NULL,
  `mslanex` TINYINT(1) NOT NULL DEFAULT 0,
  `mscanex` VARCHAR(255) NULL DEFAULT NULL,
  `mscstat` CHAR(1) NOT NULL,
  `msdevdt` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `msdredt` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`msnid`),
  CONSTRAINT `FKMSGR`
    FOREIGN KEY (`msnidgr`)
    REFERENCES `biblivirti`.`grupo` (`grnid`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `FKMSUS`
    FOREIGN KEY (`msnidus`)
    REFERENCES `biblivirti`.`usuario` (`usnid`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;

CREATE INDEX `fk_MENSAGEM_USUARIO1_idx` ON `biblivirti`.`mensagem` (`msnidus` ASC);

CREATE INDEX `fk_MENSAGEM_GRUPO1_idx` ON `biblivirti`.`mensagem` (`msnidgr` ASC);


-- -----------------------------------------------------
-- Table `biblivirti`.`parametros`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `biblivirti`.`parametros` ;

CREATE TABLE IF NOT EXISTS `biblivirti`.`parametros` (
  `prnnal` INT(11) NOT NULL DEFAULT '5',
  `prnnqe` INT(11) NOT NULL DEFAULT '10',
  `prntmax` INT(11) NOT NULL DEFAULT '16',
  `prnnadm` INT(11) NOT NULL DEFAULT '1')
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `biblivirti`.`recuperarsenha`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `biblivirti`.`recuperarsenha` ;

CREATE TABLE IF NOT EXISTS `biblivirti`.`recuperarsenha` (
  `rsnid` INT(11) NOT NULL AUTO_INCREMENT,
  `rsnidus` INT(11) NOT NULL,
  `rsctokn` VARCHAR(32) NOT NULL,
  `rscstat` CHAR(1) NOT NULL DEFAULT 'A',
  `rsdcadt` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `rsdaldt` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`rsnid`),
  CONSTRAINT `fkrsus`
    FOREIGN KEY (`rsnidus`)
    REFERENCES `biblivirti`.`usuario` (`usnid`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 18
DEFAULT CHARACTER SET = utf8;

CREATE UNIQUE INDEX `RSCTOKN_UNIQUE` ON `biblivirti`.`recuperarsenha` (`rsctokn` ASC);

CREATE INDEX `fk_RECUPERARSENHA_USUARIO1_idx` ON `biblivirti`.`recuperarsenha` (`rsnidus` ASC);


-- -----------------------------------------------------
-- Table `biblivirti`.`resposta`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `biblivirti`.`resposta` ;

CREATE TABLE IF NOT EXISTS `biblivirti`.`resposta` (
  `renid` INT(11) NOT NULL AUTO_INCREMENT,
  `renidav` INT(11) NOT NULL,
  `renidqe` INT(11) NOT NULL,
  `renidal` INT(11) NOT NULL,
  `rettemp` TIME NOT NULL,
  PRIMARY KEY (`renid`),
  CONSTRAINT `FKREAL`
    FOREIGN KEY (`renidal`)
    REFERENCES `biblivirti`.`alternativa` (`alnid`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `FKREAV`
    FOREIGN KEY (`renidav`)
    REFERENCES `biblivirti`.`avaliacao` (`avnid`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `FKREQE`
    FOREIGN KEY (`renidqe`)
    REFERENCES `biblivirti`.`questao` (`qenid`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE INDEX `fk_RESPOSTA_AVALIACAO1_idx` ON `biblivirti`.`resposta` (`renidav` ASC);

CREATE INDEX `fk_RESPOSTA_QUESTAO1_idx` ON `biblivirti`.`resposta` (`renidqe` ASC);

CREATE INDEX `fk_RESPOSTA_ALTERNATIVA1_idx` ON `biblivirti`.`resposta` (`renidal` ASC);


-- -----------------------------------------------------
-- Table `biblivirti`.`questaosimulado`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `biblivirti`.`questaosimulado` ;

CREATE TABLE IF NOT EXISTS `biblivirti`.`questaosimulado` (
  `qsnidma` INT NOT NULL,
  `qsnidqe` INT NOT NULL,
  PRIMARY KEY (`qsnidma`, `qsnidqe`),
  CONSTRAINT `FKQSMA`
    FOREIGN KEY (`qsnidma`)
    REFERENCES `biblivirti`.`material` (`manid`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `FKQSQE`
    FOREIGN KEY (`qsnidqe`)
    REFERENCES `biblivirti`.`questao` (`qenid`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_questaosimulado_questao1_idx` ON `biblivirti`.`questaosimulado` (`qsnidqe` ASC);


-- -----------------------------------------------------
-- Table `biblivirti`.`notificacao`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `biblivirti`.`notificacao` ;

CREATE TABLE IF NOT EXISTS `biblivirti`.`notificacao` (
  `nonid` INT NOT NULL AUTO_INCREMENT,
  `nonidgr` INT NOT NULL,
  `nonidus` INT NOT NULL,
  `noctext` TEXT NOT NULL,
  `noctipo` CHAR(1) NOT NULL,
  `nonidma` INT NULL,
  `nonidco` INT NULL,
  `nonidce` INT NULL,
  `nonidms` INT NULL,
  `noniddv` INT NULL,
  `noniddr` INT NULL,
  `nodcadt` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `nocstat` CHAR(1) NOT NULL DEFAULT 'A',
  PRIMARY KEY (`nonid`),
  CONSTRAINT `fknous`
    FOREIGN KEY (`nonidus`)
    REFERENCES `biblivirti`.`usuario` (`usnid`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fknoma`
    FOREIGN KEY (`nonidma`)
    REFERENCES `biblivirti`.`material` (`manid`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fknoco`
    FOREIGN KEY (`nonidco`)
    REFERENCES `biblivirti`.`conteudo` (`conid`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fknoce`
    FOREIGN KEY (`nonidce`)
    REFERENCES `biblivirti`.`comentario` (`cenid`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fknoms`
    FOREIGN KEY (`nonidms`)
    REFERENCES `biblivirti`.`mensagem` (`msnid`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fknodv`
    FOREIGN KEY (`noniddv`)
    REFERENCES `biblivirti`.`duvida` (`dvnid`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fknodr`
    FOREIGN KEY (`noniddr`)
    REFERENCES `biblivirti`.`duvidaresposta` (`drnid`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;

CREATE INDEX `fk_notificacao_usuario1_idx` ON `biblivirti`.`notificacao` (`nonidus` ASC);

CREATE INDEX `fk_notificacao_material1_idx` ON `biblivirti`.`notificacao` (`nonidma` ASC);

CREATE INDEX `fk_notificacao_conteudo1_idx` ON `biblivirti`.`notificacao` (`nonidco` ASC);

CREATE INDEX `fk_notificacao_comentario1_idx` ON `biblivirti`.`notificacao` (`nonidce` ASC);

CREATE INDEX `fk_notificacao_mensagem1_idx` ON `biblivirti`.`notificacao` (`nonidms` ASC);

CREATE INDEX `fk_notificacao_duvida1_idx` ON `biblivirti`.`notificacao` (`noniddv` ASC);

CREATE INDEX `fk_notificacao_duvidaresposta1_idx` ON `biblivirti`.`notificacao` (`noniddr` ASC);


-- -----------------------------------------------------
-- Table `biblivirti`.`confirmaremail`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `biblivirti`.`confirmaremail` ;

CREATE TABLE IF NOT EXISTS `biblivirti`.`confirmaremail` (
  `canid` INT NOT NULL AUTO_INCREMENT,
  `canidus` INT NOT NULL,
  `cactokn` VARCHAR(32) NOT NULL,
  `cacstat` CHAR(1) NOT NULL DEFAULT 'I',
  `cadcadt` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `cadaldt` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`canid`),
  CONSTRAINT `FKCAUS`
    FOREIGN KEY (`canidus`)
    REFERENCES `biblivirti`.`usuario` (`usnid`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;

CREATE UNIQUE INDEX `cactokn_UNIQUE` ON `biblivirti`.`confirmaremail` (`cactokn` ASC);

CREATE INDEX `fk_confirmaremail_usuario1_idx` ON `biblivirti`.`confirmaremail` (`canidus` ASC);


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

-- -----------------------------------------------------
-- Data for table `biblivirti`.`areainteresse`
-- -----------------------------------------------------
START TRANSACTION;
USE `biblivirti`;
INSERT INTO `biblivirti`.`areainteresse` (`ainid`, `aicdesc`, `aidcadt`, `aidaldt`) VALUES (1, 'Computação', NULL, NULL);
INSERT INTO `biblivirti`.`areainteresse` (`ainid`, `aicdesc`, `aidcadt`, `aidaldt`) VALUES (2, 'Matemática', NULL, NULL);
INSERT INTO `biblivirti`.`areainteresse` (`ainid`, `aicdesc`, `aidcadt`, `aidaldt`) VALUES (3, 'Física', NULL, NULL);
INSERT INTO `biblivirti`.`areainteresse` (`ainid`, `aicdesc`, `aidcadt`, `aidaldt`) VALUES (4, 'Química', NULL, NULL);
INSERT INTO `biblivirti`.`areainteresse` (`ainid`, `aicdesc`, `aidcadt`, `aidaldt`) VALUES (5, 'Engenharia', NULL, NULL);

COMMIT;


-- -----------------------------------------------------
-- Data for table `biblivirti`.`usuario`
-- -----------------------------------------------------
START TRANSACTION;
USE `biblivirti`;
INSERT INTO `biblivirti`.`usuario` (`usnid`, `uscfbid`, `uscnome`, `uscmail`, `usclogn`, `uscsenh`, `uscfoto`, `uscstat`, `usdcadt`, `usdaldt`) VALUES (2, NULL, 'Djalmo Cruz Jr', 'djalmo.cruz@gmail.com', 'djalmocruzjr', '851e09523550c697260ff6820cea3d28', NULL, 'A', NULL, NULL);
INSERT INTO `biblivirti`.`usuario` (`usnid`, `uscfbid`, `uscnome`, `uscmail`, `usclogn`, `uscsenh`, `uscfoto`, `uscstat`, `usdcadt`, `usdaldt`) VALUES (3, NULL, 'Cynara Carvalho Lira', 'cynaracarvalho@yahoo.com.br', 'cynaracarvalho', '6f15f21a7457ca703b70f7ad196e3418', NULL, 'A', NULL, NULL);
INSERT INTO `biblivirti`.`usuario` (`usnid`, `uscfbid`, `uscnome`, `uscmail`, `usclogn`, `uscsenh`, `uscfoto`, `uscstat`, `usdcadt`, `usdaldt`) VALUES (4, NULL, 'Luiz Mario Cavalcante', 'luizmario72@hotmail.com', 'luizmariodev', 'b0a34919896a856d85ec1fd1bf4ed3c4', NULL, 'A', NULL, NULL);
INSERT INTO `biblivirti`.`usuario` (`usnid`, `uscfbid`, `uscnome`, `uscmail`, `usclogn`, `uscsenh`, `uscfoto`, `uscstat`, `usdcadt`, `usdaldt`) VALUES (5, NULL, 'Vania Lasalvia', 'vania.lasalvia@gmail.com', 'vanialasalvia', 'd6d06ce5e334f03e18a6945a55674d51', NULL, 'A', NULL, NULL);
INSERT INTO `biblivirti`.`usuario` (`usnid`, `uscfbid`, `uscnome`, `uscmail`, `usclogn`, `uscsenh`, `uscfoto`, `uscstat`, `usdcadt`, `usdaldt`) VALUES (6, NULL, 'Alynne Raquel', 'alynneraquel@hotmail.com', 'alynneraquel', '82b03863ae235f0c8dbfc8632a20312f', NULL, 'A', NULL, NULL);
INSERT INTO `biblivirti`.`usuario` (`usnid`, `uscfbid`, `uscnome`, `uscmail`, `usclogn`, `uscsenh`, `uscfoto`, `uscstat`, `usdcadt`, `usdaldt`) VALUES (7, NULL, 'Aurélio Souza', 'aureliosm@gmail.com', 'aureliosm', 'bd44670340339bc8edbd3343797628a5', NULL, 'A', NULL, NULL);
INSERT INTO `biblivirti`.`usuario` (`usnid`, `uscfbid`, `uscnome`, `uscmail`, `usclogn`, `uscsenh`, `uscfoto`, `uscstat`, `usdcadt`, `usdaldt`) VALUES (8, NULL, 'Caíque Rodrigues', 'caiqueadesouza@gmail.com', 'caiqueadesouza', '7f35e980c27c35ce1700928d96b216dd', NULL, 'A', NULL, NULL);
INSERT INTO `biblivirti`.`usuario` (`usnid`, `uscfbid`, `uscnome`, `uscmail`, `usclogn`, `uscsenh`, `uscfoto`, `uscstat`, `usdcadt`, `usdaldt`) VALUES (9, NULL, 'Carlos Alberto Batista', 'carlos36_batista@hotmail.com', 'carlosbatistateixeira', 'f92b045a3e8c69574f01606e69d89414', NULL, 'A', NULL, NULL);
INSERT INTO `biblivirti`.`usuario` (`usnid`, `uscfbid`, `uscnome`, `uscmail`, `usclogn`, `uscsenh`, `uscfoto`, `uscstat`, `usdcadt`, `usdaldt`) VALUES (10, NULL, 'Cláudio Henrique', 'claudiiinho@gmail.com', 'claudiohenrique', 'aecb9f303cbf7b3b03f3f242f2c2f02d', NULL, 'A', NULL, NULL);
INSERT INTO `biblivirti`.`usuario` (`usnid`, `uscfbid`, `uscnome`, `uscmail`, `usclogn`, `uscsenh`, `uscfoto`, `uscstat`, `usdcadt`, `usdaldt`) VALUES (1, NULL, 'Sysmob, Inc', 'contato@sysmob.com.br', 'sysmob', '3d80c26f7c953dcda96ea257f2c31883', NULL, 'A', NULL, NULL);

COMMIT;


-- -----------------------------------------------------
-- Data for table `biblivirti`.`grupo`
-- -----------------------------------------------------
START TRANSACTION;
USE `biblivirti`;
INSERT INTO `biblivirti`.`grupo` (`grnid`, `grnidai`, `grcnome`, `grcfoto`, `grctipo`, `grdcadt`, `grdaldt`) VALUES (1, 1, 'Sysmob, Inc', NULL, 'F', NULL, NULL);

COMMIT;


-- -----------------------------------------------------
-- Data for table `biblivirti`.`grupousuario`
-- -----------------------------------------------------
START TRANSACTION;
USE `biblivirti`;
INSERT INTO `biblivirti`.`grupousuario` (`gunidgr`, `gunidus`, `guladm`, `gucstat`, `gudcadt`, `gudaldt`) VALUES (1, 1, 0, 'A', NULL, NULL);
INSERT INTO `biblivirti`.`grupousuario` (`gunidgr`, `gunidus`, `guladm`, `gucstat`, `gudcadt`, `gudaldt`) VALUES (1, 2, 0, 'A', NULL, NULL);
INSERT INTO `biblivirti`.`grupousuario` (`gunidgr`, `gunidus`, `guladm`, `gucstat`, `gudcadt`, `gudaldt`) VALUES (1, 3, 1, 'A', NULL, NULL);
INSERT INTO `biblivirti`.`grupousuario` (`gunidgr`, `gunidus`, `guladm`, `gucstat`, `gudcadt`, `gudaldt`) VALUES (1, 4, 0, 'A', NULL, NULL);
INSERT INTO `biblivirti`.`grupousuario` (`gunidgr`, `gunidus`, `guladm`, `gucstat`, `gudcadt`, `gudaldt`) VALUES (1, 6, 0, 'A', NULL, NULL);
INSERT INTO `biblivirti`.`grupousuario` (`gunidgr`, `gunidus`, `guladm`, `gucstat`, `gudcadt`, `gudaldt`) VALUES (1, 7, 0, 'A', NULL, NULL);
INSERT INTO `biblivirti`.`grupousuario` (`gunidgr`, `gunidus`, `guladm`, `gucstat`, `gudcadt`, `gudaldt`) VALUES (1, 8, 0, 'A', NULL, NULL);
INSERT INTO `biblivirti`.`grupousuario` (`gunidgr`, `gunidus`, `guladm`, `gucstat`, `gudcadt`, `gudaldt`) VALUES (1, 9, 0, 'A', NULL, NULL);

COMMIT;


-- -----------------------------------------------------
-- Data for table `biblivirti`.`parametros`
-- -----------------------------------------------------
START TRANSACTION;
USE `biblivirti`;
INSERT INTO `biblivirti`.`parametros` (`prnnal`, `prnnqe`, `prntmax`, `prnnadm`) VALUES (5, 10, 16, 1);

COMMIT;

