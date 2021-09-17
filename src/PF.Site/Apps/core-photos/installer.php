<?php
$installer = new Core\App\Installer();
$installer->onInstall(function() use ($installer) {
    (new \Apps\Core_Photos\Installation\Version\v453())->process();
    (new \Apps\Core_Photos\Installation\Version\v460())->process();
});