# Drop table
DROP TABLE `phpfox_instapaint_discount`;


# Create table
CREATE TABLE `phpfox_instapaint_discount` (
  `discount_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `coupon_code` varchar(255) DEFAULT NULL,
  `expiration_timestamp` int(10) unsigned DEFAULT NULL,
  `discount_percentage` tinyint(3) unsigned NOT NULL,
  `user_id` int(11) unsigned DEFAULT '1' COMMENT 'Field necessary for browse service',
  `time_stamp` int(10) unsigned DEFAULT NULL COMMENT 'Field necessary for browse service',
  PRIMARY KEY (`discount_id`),
  KEY `coupon_code_INDEX` (`coupon_code`),
  KEY `expiration_date_INDEX` (`expiration_timestamp`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;

CREATE TABLE `phpfox_instapaint_package_discount` (
  `package_discount_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `discount_id` int(10) unsigned NOT NULL,
  `package_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`package_discount_id`),
  KEY `fk_phpfox_instapaint_frame_size_discount_phpfox_instapaint__idx` (`discount_id`),
  KEY `fk_phpfox_instapaint_frame_size_discount_phpfox_instapaint__idx1` (`package_id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=latin1;