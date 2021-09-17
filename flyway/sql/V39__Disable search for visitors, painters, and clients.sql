# Disable search for visitors
INSERT INTO `phpfox_user_setting` (`user_group_id`, `setting_id`, `value_actual`)
VALUES
  (3, 79, '0');

# Disable search for clients
INSERT INTO `phpfox_user_setting` (`user_group_id`, `setting_id`, `value_actual`)
VALUES
  (6, 79, '0');

# Disable search for painters
INSERT INTO `phpfox_user_setting` (`user_group_id`, `setting_id`, `value_actual`)
VALUES
  (7, 79, '0');

# Disable search for approved painters
INSERT INTO `phpfox_user_setting` (`user_group_id`, `setting_id`, `value_actual`)
VALUES
  (8, 79, '0');