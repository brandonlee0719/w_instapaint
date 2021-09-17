<?php

$logo = flavor()->active->logo_url();
if ($logo) {
	$this->template()->assign([
		'logo' => $logo
	]);
}