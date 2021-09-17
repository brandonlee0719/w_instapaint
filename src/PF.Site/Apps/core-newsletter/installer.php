<?php

use \Apps\Core_Newsletter\Installation\Version\v453 as v453;

$installer = new Core\App\Installer();
$installer->onInstall(function () use ($installer) {
    (new v453())->process();
});
