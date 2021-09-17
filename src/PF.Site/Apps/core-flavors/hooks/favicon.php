<?php

$f = flavor()->active->favicon_url();
if ($f) {
	$favicon = $f . '?v=' . $oTpl->getStaticVersion();
}