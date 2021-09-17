<?php

namespace Apps\Core_Photos\Installation\Version;

use Phpfox;
use Phpfox_Url;
use \Core\Lib as Lib;

class v460
{
    public function __construct()
    {

    }

    public function process()
    {
        // add change album description
        if (db()->isField(':photo_album_info', 'description')) {
            db()->query("ALTER TABLE  `" . Phpfox::getT('photo_album_info') . "` CHANGE  `description`  `description` MEDIUMTEXT");
        }

        // this cron remove temporary photos every 1 day
        $aPhotoCron = db()->select('*')->from(':cron')->where(['module_id' => 'photo'])->executeRow();
        if (!$aPhotoCron) {
            db()->insert(':cron', [
                'module_id' => 'photo',
                'type_id' => 3,
                'every' => 1,
                'is_active' => 1,
                'php_code' => '(new \Apps\Core_Photos\Service\Process)->removeTemporaryPhotos();'
            ]);
        }

        //Deprecated setting "photo_pic_sizes", remove this settings in v4.7.0
        db()->update(':setting', ['is_hidden' => 1], ['var_name' => 'photo_pic_sizes', 'module_id' => 'photo']);
        $iFeedShare = db()->select('COUNT(*)')
            ->from(':feed_share')
            ->where('module_id = \'photo\'')
            ->execute('getField');
        if (!$iFeedShare) {
            db()->insert(':feed_share', [
                'module_id' => 'photo',
                'title' => '{_p var=\'photo\'}',
                'description' => '{_p var=\'say_something_about_this_photo\'}',
                'block_name' => 'share',
                'no_input' => 0,
                'is_frame' => 1,
                'ajax_request' => '',
                'no_profile' => 0,
                'icon' => 'photo.png',
                'ordering' => 1
            ]);
        }
        $aUpdatePhrase = [
            'user_setting_can_download_user_photos' => 'Can download other users photos?',
            'user_setting_max_number_of_albums' => 'Define the total number of photo albums a user within this user group can create. 
Notice: Leave this empty will allow them to create an unlimited amount of photo albums. Setting this value to 0 will not allow them the ability to create photo albums.'
        ];
        Phpfox::getService('language.phrase.process')->updatePhrases($aUpdatePhrase);

        $iSettingsId = db()->select('setting_id')->from(':user_group_setting')->where('product_id = \'Core_Photos\' AND name = \'max_number_of_albums\'')->execute('getField');
        if ($iSettingsId) {
            db()->update(':user_setting',['value_actual' => ''],'value_actual = \'null\' AND setting_id ='.$iSettingsId);
            db()->update(':user_group_setting',['default_admin' => ''],'setting_id ='.$iSettingsId);
        }
    }
}