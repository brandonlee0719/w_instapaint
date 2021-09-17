# Insert Instapaint app
INSERT INTO `phpfox_apps` (`apps_key`, `apps_id`, `apps_dir`, `apps_name`, `version`, `apps_alias`, `author`, `vendor`, `description`, `apps_icon`, `type`, `is_active`)
VALUES
  (4, 'Instapaint', 'instapaint', 'Instapaint', '4.6.0', 'instapaint', 'Ivan', '', NULL, NULL, 2, 1);

# Insert "My Dashboard" menu
INSERT INTO `phpfox_menu` (`menu_id`, `parent_id`, `page_id`, `m_connection`, `module_id`, `product_id`, `var_name`, `is_active`, `ordering`, `url_value`, `disallow_access`, `version_id`, `mobile_icon`)
VALUES
  (40, 0, 0, 'main', 'instapaint', 'phpfox', 'menu_core_my_dashboard_1748a98d3e3f7e0ddabfe90220e14f25', 1, 2, 'my-dashboard', NULL, '4.6.0', 'line-chart');

# Insert "My Dashboard" menu phrase
INSERT INTO `phpfox_language_phrase` (`phrase_id`, `language_id`, `module_id`, `product_id`, `version_id`, `var_name`, `text`, `text_default`, `added`)
VALUES
  (7570, 'en', NULL, 'phpfox', NULL, 'menu_core_my_dashboard_1748a98d3e3f7e0ddabfe90220e14f25', 'My Dashboard', 'My Dashboard', 1518157947);

# Insert instapaint module
INSERT INTO `phpfox_module` (`module_id`, `product_id`, `is_core`, `is_active`, `is_menu`, `menu`, `phrase_var_name`, `version`, `author`, `vendor`, `description`, `apps_icon`)
VALUES
  ('instapaint', 'phpfox', 0, 1, 0, '', 'module_apps', '4.0.1', 'n/a', '', NULL, '');
