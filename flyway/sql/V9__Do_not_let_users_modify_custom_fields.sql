# Don't let these user groups modify custom fields:
# Registered User, Client, Painter, Approved Painter
INSERT INTO `phpfox_user_setting` (`user_group_id`, `setting_id`, `value_actual`)
VALUES
  (2, 36, '0'),
  (6, 36, '0'),
  (7, 36, '0'),
  (8, 36, '0');
