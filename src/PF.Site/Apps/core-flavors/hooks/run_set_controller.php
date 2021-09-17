<?php

if (!defined('PHPFOX_INSTALLER') && !Phpfox::isAdminPanel()) {
	Phpfox_Template::instance()->setStyle([
		'theme_folder_name' => flavor()->active->legacy->theme,
		'style_folder_name' => flavor()->active->legacy->flavor,
		'theme_parent_id' => 0
	]);
}