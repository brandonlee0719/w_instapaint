ALTER TABLE `phpfox_instapaint_partial_order`
ADD `faces` tinyint(3) unsigned DEFAULT '1';

ALTER TABLE `phpfox_instapaint_order`
ADD `faces` tinyint(3) unsigned DEFAULT '1';
