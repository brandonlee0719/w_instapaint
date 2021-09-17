<?php

if (!Phpfox::isAdminPanel()) {
	$url = $sBaseUrl . 'PF.Site/flavors/' . flavor()->active->id . '/assets/';
	$sData .= '<link href="' . $url . 'autoload.css?v=' . Phpfox::internalVersion() . '" rel="stylesheet">';
}
