INSERT INTO `phpfox_instapaint_frame_size` (`frame_size_id`, `name_phrase`, `description_phrase`, `price_usd`)
VALUES
  (1, 'Small', '10&quot; x 10&quot; size', 99.00),
  (2, 'Medium', '15&quot; x 15&quot; size', 149.00),
  (3, 'Large', '22&quot; x 22&quot; size', 199.00);

INSERT INTO `phpfox_instapaint_frame_type` (`frame_type_id`, `name_phrase`, `description_phrase`, `price_usd`)
VALUES
  (1, 'Gallery', 'Gallery', 49.00),
  (2, 'Rolled', 'Rolled', 0.00);

INSERT INTO `phpfox_instapaint_shipping_type` (`shipping_type_id`, `name_phrase`, `description_phrase`, `price_usd`)
VALUES
  (1, 'Normal', 'Free shipping', 0.00),
  (2, 'Rush', 'Rush shipping', 30.00);

INSERT INTO `phpfox_instapaint_package` (`frame_type_id`, `frame_size_id`, `shipping_type_id`)
VALUES
  (1, 1, 1),
  (1, 1, 2),
  (2, 1, 1),
  (2, 1, 2),
  (1, 2, 1),
  (1, 2, 2),
  (2, 2, 1),
  (2, 2, 2),
  (1, 3, 1),
  (1, 3, 2),
  (2, 3, 1),
  (2, 3, 2);
