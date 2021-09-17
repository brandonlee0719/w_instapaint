<?php

$out = [];
try {
	if (!file_exists(__DIR__ . '/../file/settings/license.sett.php')) {
		throw new Exception(_p('phpFox does not seem to be installed.'));
	}
	require(__DIR__ . '/../file/settings/license.sett.php');

	if (!file_exists(__DIR__ . '/../file/settings/version.sett.php')) {
		throw new Exception(_p('phpFox is missing version ID'));
	}
	$version = require(__DIR__ . '/../file/settings/version.sett.php');

	if (empty($_REQUEST['license_id']) || empty($_REQUEST['license_key'])) {
		throw new Exception(_p('Missing License ID/Key'));
	}

	if ($_REQUEST['license_id'] != PHPFOX_LICENSE_ID) {
		throw new Exception(_p('License ID does not match.'));
	}

	if ($_REQUEST['license_key'] != PHPFOX_LICENSE_KEY) {
		throw new Exception(_p('License Key does not match.'));
	}

	$out = $version;
} catch (Exception $e) {
	$out = ['error' => $e->getMessage()];
}

header('Content-type: application/json; charset=utf-8');
echo json_encode($out, JSON_PRETTY_PRINT);