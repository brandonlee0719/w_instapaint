<?php

return function (Phpfox_Installer $Installer) {
    //Remove setting
    $Installer->db->delete(':setting','module_id="comment" AND var_name="load_delayed_comments_items"');
    
    //Remove block
    $Installer->db->delete(':block', 'module_id="feed" AND component="time"');
    
    //remove component
    $Installer->db->delete(':component', 'module_id="feed" AND component="time"');
};