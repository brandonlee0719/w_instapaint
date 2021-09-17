# Insert group name phrases
INSERT INTO `phpfox_language_phrase` (`language_id`, `module_id`, `product_id`, `version_id`, `var_name`, `text`, `text_default`, `added`)
VALUES
  ('en', NULL, 'phpfox', NULL, 'user_group_title_01dc0d4a6f3d002925cbe5292456df8a', 'Client', 'Client', 1518032602),
  ('en', NULL, 'phpfox', NULL, 'user_group_title_a27051d222eacb66877cd0c6e4f20190', 'Painter', 'Painter', 1518032678),
  ('en', NULL, 'phpfox', NULL, 'user_group_title_14ba355a1ca254460a0b75e1bc3fd456', 'Approved Painter', 'Approved Painter', 1518032848);

# Insert user groups
INSERT INTO `phpfox_user_group` (`user_group_id`, `inherit_id`, `title`, `is_special`, `prefix`, `suffix`, `icon_ext`)
VALUES
  (6, 2, 'user_group_title_01dc0d4a6f3d002925cbe5292456df8a', 0, '', '', NULL),
  (7, 2, 'user_group_title_a27051d222eacb66877cd0c6e4f20190', 0, '', '', NULL),
  (8, 2, 'user_group_title_14ba355a1ca254460a0b75e1bc3fd456', 0, '', '', NULL);
