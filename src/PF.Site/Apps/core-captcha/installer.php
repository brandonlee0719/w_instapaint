<?php

use \Apps\Core_Captcha\Installation\Version\v453;

$installer = new Core\App\Installer();
$installer->onInstall(function () use ($installer) {
    (new v453())->process();
});
