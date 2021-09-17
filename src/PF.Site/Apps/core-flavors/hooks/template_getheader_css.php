<?php

if (!defined('PHPFOX_INSTALLER') && !Phpfox::isAdminPanel()) {
	$Theme->folder = flavor()->active->legacy->theme;
	$Theme->flavor_folder = flavor()->active->legacy->flavor;
}