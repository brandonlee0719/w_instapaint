<?php

$path = flavor()->active->path;
$sThemeFile = $path . 'html/' . $sName . PHPFOX_TPL_SUFFIX;
if (file_exists($sThemeFile)) {
	$sFile = $sThemeFile;
}