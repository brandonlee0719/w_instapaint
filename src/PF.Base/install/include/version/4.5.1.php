<?php
return function (Phpfox_Installer $Installer) {
    $Installer->db->addField([
        'table' => Phpfox::getT('custom_field'),
        'field' => 'is_search',
        'type' => 'TINT:1',
        'default' => '1',
        'null' => true
    ]);

    $Installer->db->update(':setting', [
        'value_actual' => 'auto_responder_subject',
        'value_default' => 'auto_responder_subject',
    ], [
        'var_name' => 'auto_responder_subject',
        'module_id' => 'contact'
    ]);
    $Installer->db->update(':setting', [
        'value_actual' => 'auto_responder_message',
        'value_default' => 'auto_responder_message',
    ], [
        'var_name' => 'auto_responder_message',
        'module_id' => 'contact'
    ]);

    $Installer->db->update(':module', [
        'author' => 'phpFox',
        'vendor' => 'https://store.phpfox.com'
    ], [
        'module_id' => [
            'in' => '"ad", "admincp", "announcement", "api","attachment","ban","blog","captcha","comment","contact","core","custom","egift","error","event","feed","forum","friend","invite","language","like","link","log","mail","marketplace","music","newsletter","notification","page","pages","photo","poke","poll","privacy","profile","quiz","report","request","rss","search","share","subscribe","tag","theme","track","user"'
        ]
    ]);

    $Installer->db->delete(':module', [
        'module_id' => 'facebook'
    ]);

    //change field type of the table user_delete_feedback
    $Installer->db->changeField(Phpfox::getT('user_delete_feedback'), 'feedback_text', ['type' => 'TEXT']);

    //delete block profile.logo in pages.view
    $Installer->db->delete(Phpfox::getT('block'), [
        'm_connection' => 'pages.view',
        'module_id' => 'profile',
        'component' => 'logo'
    ]);

    $items  = $Installer->db->select('*')
        ->from(':apps')
        ->where("apps_dir is NULL or apps_dir = ''")
        ->execute('getSlaveRows');

    foreach($items as $item){
        $fromDirName  =  realpath(PHPFOX_PARENT_DIR . '/PF.Site/Apps/'. $item['apps_id']);
        $path =  $fromDirName  .'/Install.php';

        if(!$fromDirName or !file_exists($path)){
            continue;
        }
        $appClassName  = 'Apps\\'.$item['apps_id'] .'\\Install';

        include_once $path;

        if(!class_exists($appClassName))
            continue;

        /** @var \Core\App\App $appInfo */
        $appInfo = new $appClassName;

        if(empty($appInfo->_apps_dir)){
            continue;
        }

        if(!isset($appInfo->path) || empty($appInfo->path)){
            continue;
        }

        $toDirName  = $appInfo->path;

        if(is_dir($toDirName)){
            continue;
        }

        $toDirName = rtrim($toDirName);

        $result  =  rename($fromDirName, $toDirName);

        if(!$result){
            exit("Can not rename $fromDirName => $toDirName");
        }

        $appIcon = $appInfo->icon;

        $appIcon =  str_replace($appInfo->id, $appInfo->_apps_dir .'/', $appIcon);

        $Installer->db
            ->update(':apps',['apps_dir'=>$appInfo->_apps_dir,'apps_icon'=>$appIcon],['apps_id'=>$appInfo->id]);

    }
};