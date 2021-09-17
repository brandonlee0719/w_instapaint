<?php

$installer = new Core\App\Installer();
$installer->onInstall(function () {
    (new Apps\PHPfox_Groups\Installation\Version\v460())->process();
});
