<?php

$path = flavor()->active->path;
$template = $path . 'html/' . $sTemplate . '.html';
if (file_exists($template)) {
	$vars = $this->getVar();
	$content = view('@Flavor/' . $sTemplate . '.html', $vars);
	echo $content;

	$skip_layout = true;

	Phpfox_Template::instance()->clean([
		'sHeader'
	]);
}