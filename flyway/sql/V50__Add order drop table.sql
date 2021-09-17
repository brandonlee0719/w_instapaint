CREATE TABLE `phpfox_instapaint_order_drop` (
  `order_drop_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) unsigned NOT NULL,
  `painter_user_id` int(10) unsigned NOT NULL,
  `reason` tinytext NOT NULL,
  `timestamp` int(10) unsigned NOT NULL,
  PRIMARY KEY (`order_drop_id`)
);