<?php

return function(Phpfox_Installer $Installer) {
    $columns =  Phpfox::getLib('database.support')->getColumns(Phpfox::getT('feed'));
    if (!array_key_exists('total_view', $columns))
        $Installer->db->query("ALTER TABLE " . Phpfox::getT('feed') . " ADD total_view INT UNSIGNED NOT NULL DEFAULT '0' AFTER content");
};