CREATE TABLE `phpfox_instapaint_partial_order` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `unique_id` varchar(100) NOT NULL DEFAULT '',
  `photo_path` varchar(100) NOT NULL DEFAULT '',
  `thumbnail_path` varchar(500) NOT NULL,
  `order_notes` varchar(500) DEFAULT NULL,
  `package_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
);
