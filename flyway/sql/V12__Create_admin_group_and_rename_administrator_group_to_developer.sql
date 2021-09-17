# Insert Admin user group
INSERT INTO `phpfox_user_group` (`user_group_id`, `inherit_id`, `title`, `is_special`, `prefix`, `suffix`, `icon_ext`)
VALUES
  (9, 2, 'user_group_title_d05ffc7bb6a9bdb3995a00b6d6fb17c3', 0, '', '', NULL);

# Insert Admin user group name phrase
INSERT INTO `phpfox_language_phrase` (`phrase_id`, `language_id`, `module_id`, `product_id`, `version_id`, `var_name`, `text`, `text_default`, `added`)
VALUES
  (7571, 'en', NULL, 'phpfox', NULL, 'user_group_title_d05ffc7bb6a9bdb3995a00b6d6fb17c3', 'Admin', 'Admin', 1518218974);

# Rename Administrator group to Developer
UPDATE `phpfox_user_group`
SET `title` = 'administrator'
WHERE `user_group_id` = 1;

UPDATE `phpfox_language_phrase`
SET `text` = 'Developer'
WHERE `phrase_id` = 2605;
