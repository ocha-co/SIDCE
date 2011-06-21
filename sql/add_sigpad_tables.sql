SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

CREATE SCHEMA IF NOT EXISTS `sidce` DEFAULT CHARACTER SET utf8 ;
USE `sidce` ;

-- -----------------------------------------------------
-- Table `sidce`.`sigpad_id_incident_id`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `sidce`.`sigpad_id_incident_id` (
  `sigpad_id` INT NOT NULL ,
  `incident_id` BIGINT(20) UNSIGNED NOT NULL ,
  PRIMARY KEY (`sigpad_id`, `incident_id`) ,
  INDEX `fk_sidih_id_incident_id_incident1` (`incident_id` ASC) ,
  CONSTRAINT `fk_sidih_id_incident_id_incident1`
    FOREIGN KEY (`incident_id` )
    REFERENCES `sidce`.`incident` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = MyISAM
COMMENT = 'Event ID in SIDIH => Incident ID in SihCesar';


-- -----------------------------------------------------
-- Table `sidce`.`impact_type`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `sidce`.`impact_type` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `impact_type` VARCHAR(45) NULL ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM;


-- -----------------------------------------------------
-- Table `sidce`.`impact`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `sidce`.`impact` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `impact` VARCHAR(255) NOT NULL ,
  `incident_id` BIGINT(20) UNSIGNED NOT NULL ,
  `impact_type_id` INT NOT NULL ,
  INDEX `fk_impact_incident1` (`incident_id` ASC) ,
  INDEX `fk_impact_impact_type1` (`impact_type_id` ASC) ,
  PRIMARY KEY (`id`) ,
  CONSTRAINT `fk_impact_incident1`
    FOREIGN KEY (`incident_id` )
    REFERENCES `sidce`.`incident` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_impact_impact_type1`
    FOREIGN KEY (`impact_type_id` )
    REFERENCES `sidce`.`impact_type` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = MyISAM;


-- -----------------------------------------------------
-- Table `sidce`.`aid_national_item`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `sidce`.`aid_national_item` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `aid_national_item` VARCHAR(100) NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `sidce`.`aid_national`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `sidce`.`aid_national` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `aid_national` BIGINT NULL ,
  `aid_national_date` DATE NULL ,
  `incident_id` BIGINT(20) UNSIGNED NOT NULL ,
  `aid_national_item_id` INT NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_aid_national_incident1` (`incident_id` ASC) ,
  INDEX `fk_aid_national_aid_national_item1` (`aid_national_item_id` ASC) ,
  CONSTRAINT `fk_aid_national_incident1`
    FOREIGN KEY (`incident_id` )
    REFERENCES `sidce`.`incident` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_aid_national_aid_national_item1`
    FOREIGN KEY (`aid_national_item_id` )
    REFERENCES `sidce`.`aid_national_item` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = MyISAM;


CREATE  TABLE IF NOT EXISTS `sidce`.`state` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `state` VARCHAR(100) NULL DEFAULT NULL ,
  `divipola` VARCHAR(5) NULL DEFAULT NULL ,
  `country_id` INT(11) NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_state_country1` (`country_id` ASC) ,
  CONSTRAINT `fk_state_country1`
    FOREIGN KEY (`country_id` )
    REFERENCES `sidce`.`country` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = MyISAM;

ALTER TABLE city ADD COLUMN state_id INT NOT NULL;
ALTER TABLE city ADD COLUMN divipola VARCHAR(5) NOT NULL;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
