DROP TABLE phpfox_instapaint_order;

CREATE TABLE `phpfox_instapaint_order` (
  `order_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_status_id` int(10) unsigned NOT NULL,
  `created_timestamp` int(10) unsigned NOT NULL,
  `updated_timestamp` int(10) unsigned DEFAULT NULL,
  `client_user_id` int(10) unsigned NOT NULL,
  `updater_user_id` int(10) unsigned DEFAULT NULL,
  `shipping_address_id` int(10) unsigned NOT NULL,
  `package_id` int(10) unsigned NOT NULL,
  `order_details` varchar(1024) NOT NULL DEFAULT '',
  `image_path` varchar(1024) NOT NULL DEFAULT '',
  `server_id` int(10) unsigned NOT NULL,
  `order_notes` varchar(1024) DEFAULT NULL,
  PRIMARY KEY (`order_id`),
  KEY `fk_phpfox_instapaint_order_phpfox_instapaint_order_status1_idx` (`order_status_id`),
  KEY `fk_phpfox_instapaint_order_phpfox_instapaint_address1_idx` (`shipping_address_id`),
  KEY `fk_phpfox_instapaint_order_phpfox_user1_idx` (`client_user_id`),
  KEY `fk_phpfox_instapaint_order_phpfox_user2_idx` (`updater_user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=latin1;