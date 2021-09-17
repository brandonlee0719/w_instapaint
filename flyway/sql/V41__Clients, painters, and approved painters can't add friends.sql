# Clients, painters, and approved painters can't add friends
INSERT INTO `phpfox_user_setting` (`user_group_id`, `setting_id`, `value_actual`)
VALUES
  (6, 53, '0'),
  (6, 54, '1'),
  (6, 55, '10'),
  (6, 56, '1'),
  (7, 53, '0'),
  (7, 54, '1'),
  (7, 55, '10'),
  (7, 56, '1'),
  (8, 53, '0'),
  (8, 54, '1'),
  (8, 55, '10'),
  (8, 56, '1');
