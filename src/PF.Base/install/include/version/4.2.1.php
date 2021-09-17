<?php
defined('PHPFOX') or exit('NO DICE!');

return function(Phpfox_Installer $Installer) {
    $Installer->db->update(':setting',[
        'group_id' => 'registration',
        'is_hidden' => 0
    ], [
        'module_id' => 'user',
        'var_name' => 'new_user_terms_confirmation'
    ]);

    $Installer->db->query("DELETE FROM " . Phpfox::getT('setting') . " WHERE module_id = 'photo' AND var_name='total_tags_on_photos';");
    $Installer->db->query("DELETE FROM " . Phpfox::getT('setting') . " WHERE module_id = 'photo' AND var_name='total_photo_input_bars';");
    $Installer->db->query("DELETE FROM " . Phpfox::getT('user_group_setting') . " WHERE `module_id`='photo' AND `name`='can_password_protect_albums';");

    $Installer->db->delete(':user_group_setting', 'name="can_view_all_photo_sizes" AND module_id="photo"');

    $Installer->db->delete(':language_phrase','module_id="facebook" AND var_name="this_method_has_been_deprecated_since_v2_dot0_dot7_dot"');
};