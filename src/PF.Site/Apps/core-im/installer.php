<?php
$installer = new Core\App\Installer();
$installer->onInstall(function() use ($installer) {
    $installer->db->update(':apps', ['apps_alias' => 'im'], ['apps_id' => 'PHPfox_IM']);
    $installer->db->update(':language_phrase', [
        'text'         => 'Provide your Node JS server key (Ignore this setting if you are using phpFox IM hosting service)',
        'text_default' => 'Provide your Node JS server key (Ignore this setting if you are using phpFox IM hosting service)'
    ], [
        'product_id' => 'phpfox',
        'var_name'   => 'setting_phrase_pf_im_node_server_key'
    ]);
});