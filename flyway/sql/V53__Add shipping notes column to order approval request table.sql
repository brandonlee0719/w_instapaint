DROP TABLE phpfox_instapaint_order_approval_request;

CREATE TABLE `phpfox_instapaint_order_approval_request` (
  `order_approval_request_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) unsigned NOT NULL,
  `painter_user_id` int(10) unsigned NOT NULL,
  `is_approved` tinyint(1) unsigned DEFAULT NULL,
  `is_denied` tinyint(1) unsigned DEFAULT NULL,
  `request_timestamp` int(10) unsigned NOT NULL,
  `reviewed_timestamp` int(10) unsigned DEFAULT NULL,
  `feedback` tinytext NOT NULL,
  `finished_painting_path` varchar(1024) NOT NULL DEFAULT '',
  `is_shipped` tinyint(1) unsigned DEFAULT NULL,
  `shipped_timestamp` int(10) unsigned DEFAULT NULL,
  `shipping_notes` tinytext,
  PRIMARY KEY (`order_approval_request_id`)
);