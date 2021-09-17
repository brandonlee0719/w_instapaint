<?php
return function (Phpfox_Installer $Installer) {

    $Installer->db->insert(':component',[
        'component'=>'fe-site-stat',
        'm_connection'=>'',
        'module_id'=>'core',
        'product_id'=>'phpfox',
        'is_controller'=>0,
        'is_block'=>1,
        'is_active'=>1,
    ]);
    

    // import phrase for theme-material.
    if(file_exists($filename = PHPFOX_DIR_SITE . 'flavors/material/phrase.json')){
        \Core\Lib::phrase()->addPhrase(json_decode(file_get_contents($filename),true));
    }
};
