<?php

if (request()->get('force-flavor')) {
	if (!is_array($aParams)) {
		$aParams = [$aParams];
	}
	$aParams['force-flavor'] = request()->get('force-flavor');
}