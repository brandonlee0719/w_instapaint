<?php

$installer = new Core\App\Installer();

$installer->onInstall(function () use ($installer) {
    Phpfox::getLib('database')->update(':setting', ['value_actual' => 1],
        'var_name="allow_html" AND module_id="core"');
});
