<?php
return function (Phpfox_Installer $Installer) {

    $Installer->db->delete(':api_gateway', 'gateway_id="2checkout"');
};
