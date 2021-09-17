# Create package table
CREATE TABLE IF NOT EXISTS `instapaint`.`phpfox_instapaint_package` (
  `package_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `frame_type_id` INT NOT NULL,
  `frame_size_id` INT NOT NULL,
  `shipping_type_id` INT NOT NULL,
  PRIMARY KEY (`package_id`),
  INDEX `fk_phpfox_instapaint_package_phpfox_instapaint_frame_type1_idx` (`frame_type_id` ASC),
  INDEX `fk_phpfox_instapaint_package_phpfox_instapaint_frame_size1_idx` (`frame_size_id` ASC),
  INDEX `fk_phpfox_instapaint_package_phpfox_instapaint_shipping_typ_idx` (`shipping_type_id` ASC))
ENGINE = InnoDB;