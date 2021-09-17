<?php
$installer = new Core\App\Installer();
$installer->onInstall(function() use ($installer) {
    (new \Apps\PHPfox_Facebook\Installation\Version\v453())->process();
});
