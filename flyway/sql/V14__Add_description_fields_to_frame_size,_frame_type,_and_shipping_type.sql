ALTER TABLE `phpfox_instapaint_frame_size`
ADD COLUMN `description_phrase` varchar(255) DEFAULT NULL AFTER `name_phrase`;

ALTER TABLE `phpfox_instapaint_frame_type`
ADD COLUMN `description_phrase` varchar(255) DEFAULT NULL AFTER `name_phrase`;

ALTER TABLE `phpfox_instapaint_shipping_type`
ADD COLUMN `description_phrase` varchar(255) DEFAULT NULL AFTER `name_phrase`;
