# Insert custom field: User Type
INSERT INTO `phpfox_custom_field` (`field_id`, `field_name`, `module_id`, `product_id`, `user_group_id`, `type_id`, `group_id`, `phrase_var_name`, `type_name`, `var_type`, `is_active`, `is_required`, `has_feed`, `on_signup`, `ordering`, `is_search`)
VALUES
  (2, 'user_type', 'custom', 'phpfox', 0, 'user_main', 1, 'custom.custom_user_type', 'VARCHAR(150)', 'select', 1, 1, 0, 1, 0, 0);


# Insert custom field options
INSERT INTO `phpfox_custom_option` (`option_id`, `field_id`, `phrase_var_name`)
VALUES
  (1, 2, 'custom.cf_option_2_1'),
  (2, 2, 'custom.cf_option_2_2');


# Insert phrases
INSERT INTO `phpfox_language_phrase` (`language_id`, `module_id`, `product_id`, `version_id`, `var_name`, `text`, `text_default`, `added`)
VALUES
  ('en', NULL, 'phpfox', NULL, 'cf_option_2_1', 'Client', 'Client', 1518041507),
  ('en', NULL, 'phpfox', NULL, 'cf_option_2_2', 'Painter', 'Painter', 1518041507),
  ('en', NULL, 'phpfox', NULL, 'custom_user_type', 'User Type', 'User Type', 1518041507);
