<?php

if (version_compare(phpversion(), '5.5', '<') === true) {
    exit('phpFox 4 requires PHP 5.5 or newer.');
}

define('PHPFOX_PARENT_DIR', __DIR__ . DIRECTORY_SEPARATOR);

require('./PF.Base/start.php');
