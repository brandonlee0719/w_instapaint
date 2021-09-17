<?php
$installer = new Core\App\Installer();
$installer->OnUninstall(function () use ($installer) {
    Phpfox::getLib('database')->update(':setting', ['value_actual' => 0],
        'var_name="allow_html" AND module_id="core"');
});
