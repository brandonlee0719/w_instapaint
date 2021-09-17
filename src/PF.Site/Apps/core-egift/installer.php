<?php

use \Apps\Core_eGifts\Installation\Version\v460 as v460;

$installer = new Core\App\Installer();
$installer->onInstall(function () use ($installer) {
    (new v460())->process();
});
