<?php
    return function(Phpfox_Installer $Installer) {
        //Remove setting
        $Installer->db->delete(':setting','module_id="photo" AND var_name="delete_original_after_resize"');
        $Installer->db->delete(':setting','module_id="comment" AND var_name="wysiwyg_comments"');
        //Remove User group setting
        $Installer->db->delete(':user_group_setting', 'module_id="comment" AND name="wysiwyg_on_comments"');
    }
?>