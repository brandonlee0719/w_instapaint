UPDATE phpfox_instapaint_address a
INNER JOIN phpfox_country c
ON a.dial_code = c.dial_code
SET a.dial_code = c.country_iso;

ALTER TABLE `instapaint`.`phpfox_instapaint_address` CHANGE COLUMN `dial_code` `dial_code_iso` VARCHAR(2) NULL DEFAULT NULL ;