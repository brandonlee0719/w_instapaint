<?php

if (!Phpfox::isAdminPanel()) {
	$url = Phpfox::getLib('cdn')->getUrl(home()) . 'PF.Site/flavors/' . flavor()->active->id . '/assets/';
	$file = PHPFOX_DIR_SITE . 'flavors/' . flavor()->active->id . '/assets/autoload.js';
	if (file_exists($file)) {
		$this->_sFooter .= '<script src="' . $url . 'autoload.js?v=' . Phpfox::internalVersion() . '"></script>';
	}
}
