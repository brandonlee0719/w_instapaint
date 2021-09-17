<?php

$path = flavor()->active->path;
$sThemeFile = $path . 'html/' . $sTemplate . PHPFOX_TPL_SUFFIX;
if (file_exists($sThemeFile)) {
	$sFile = $sThemeFile;
}
