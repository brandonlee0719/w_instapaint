CREATE TABLE `phpfox_instapaint_style` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `style_id` int(11) unsigned NOT NULL,
  `name` varchar(300) NOT NULL DEFAULT '',
  `price` float NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

INSERT INTO `phpfox_instapaint_style` (`id`, `style_id`, `name`, `price`)
VALUES
	(1, 0, 'Artist\'s Choice', 0),
	(2, 5, 'Photorealistic', 0),
	(3, 7, 'Enhanced Colors', 0),
	(4, 11, 'Soft Focus', 0),
	(5, 17, 'Black & White', 0),
	(6, 15, 'Delicate Brushwork', 0),
	(7, 9, 'Natural', 0),
	(8, 18, 'Dramatic Depth', 0),
	(9, 12, 'Structured', 0),
	(10, 13, 'Soft Blur', 0),
	(11, 6, 'Abstract Expressionism', 24.99),
	(12, 2, 'Make Me Royal', 29.99),
	(13, 3, 'Whimsical', 29.99),
	(14, 14, 'Rainbow Brushwork', 29.99),
	(15, 16, '3D Effect', 55);
