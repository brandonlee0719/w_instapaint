CREATE TABLE `phpfox_instapaint_gift_card_purchase` (
  `purchase_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `gift_card_value` float unsigned NOT NULL,
  `gift_card_price` float unsigned NOT NULL,
  `client_name` varchar(256) NOT NULL DEFAULT '',
  `client_email` varchar(512) NOT NULL DEFAULT '',
  `recipient_name` varchar(256) NOT NULL DEFAULT '',
  `recipient_email` varchar(512) NOT NULL DEFAULT '',
  `purchase_timestamp` int(11) unsigned NOT NULL,
  `stripe_customer_id` varchar(256) NOT NULL DEFAULT '',
  `stripe_charge_id` varchar(256) NOT NULL DEFAULT '',
  PRIMARY KEY (`purchase_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
