<?php

return function (Phpfox_Installer $Installer) {
    //Remove setting
    $Installer->db->delete(':setting','module_id="feed" AND var_name="timeline_optional"');
};