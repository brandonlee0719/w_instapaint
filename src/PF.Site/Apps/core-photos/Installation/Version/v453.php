<?php

namespace Apps\Core_Photos\Installation\Version;

use Phpfox;
use Phpfox_Url;
use \Core\Lib as Lib;

class v453
{

    private $_aPhotoCategories;

    public function __construct()
    {

        $this->_aPhotoCategories = array(
            'Comedy',
            'Digital Art',
            'Photography',
            'Traditional Art',
            'Film & Animation',
            'Designs & Interfaces',
            'Game Development Art',
            'Artisan Crafts',
            'Customization',
            'Fractal Art',
            'Cartoons & Comics',
            'Contests',
            'Resources & Stock Images',
            'Literature',
            'Fan Art',
            'Anthro',
            'Community Projects',
            'People',
            'Pets & Animals',
            'Science & Technology',
            'Sports'
        );
    }

    public function process()
    {

        // add activity photo
        if (!db()->isField(':user_activity', 'activity_photo')) {
            db()->query("ALTER TABLE  `" . Phpfox::getT('user_activity') . "` ADD `activity_photo` INT(10) UNSIGNED NOT NULL DEFAULT '0'");
        }

        // add statistics total photo
        if (!db()->isField(':user_field', 'total_photo')) {
            db()->query("ALTER TABLE  `" . Phpfox::getT('user_field') . "` ADD `total_photo` INT(10) UNSIGNED NOT NULL DEFAULT '0'");
        }

        // add photo width to photo tag
        if (!db()->isField(':photo_tag', 'photo_width')) {
            db()->query("ALTER TABLE  `" . Phpfox::getT('photo_tag') . "` ADD `photo_width` smallint(4) UNSIGNED NOT NULL DEFAULT '1110'");
        }

        // add timeline album
        if (!db()->isField(':photo_album', 'timeline_id')) {
            db()->query("ALTER TABLE  `" . Phpfox::getT('photo_album') . "` ADD `timeline_id` INT(10) UNSIGNED NOT NULL DEFAULT '0'");
            $aPhotos = db()
                ->select('photo_id, user_id')
                ->from(Phpfox::getT('photo'))
                ->where('album_id = 0 AND type_id = 1 AND parent_user_id = 0')
                ->order('time_stamp DESC')
                ->execute('getSlaveRows');
            if(count($aPhotos)) {
                foreach ($aPhotos as $aPhoto) {
                    //Create timeline photo albums
                    $iTimelineAlbumId = db()->select('album_id')
                        ->from(Phpfox::getT('photo_album'))
                        ->where('timeline_id=' . (int) $aPhoto['user_id'])
                        ->execute('getSlaveField');
                    if (empty($iTimelineAlbumId)){
                        $iTimelineAlbumId = db()->insert(Phpfox::getT('photo_album'), array(
                            'privacy' => '0',
                            'privacy_comment' => '0',
                            'user_id' => (int) $aPhoto['user_id'],
                            'name' => "{_p var='timeline_photos'}",
                            'time_stamp' => PHPFOX_TIME,
                            'timeline_id' => $aPhoto['user_id'],
                            'total_photo' => 0
                        ));
                        db()->insert(Phpfox::getT('photo_album_info'), array('album_id' => $iTimelineAlbumId));
                        db()->update(Phpfox::getT('photo'),['is_cover' => 1], ['photo_id' => (int)$aPhoto['photo_id']]);
                    }
                    db()->update(Phpfox::getT('photo'),['album_id' => $iTimelineAlbumId], ['photo_id' => (int)$aPhoto['photo_id']]);
                    db()->updateCounter('photo_album', 'total_photo', 'album_id', $iTimelineAlbumId);
                }
            }
        }

        // add default category
        $iTotalCategory = db()
            ->select('COUNT(category_id)')
            ->from(':photo_category')
            ->execute('getField');
        if ($iTotalCategory == 0) {
            sort($this->_aPhotoCategories);
            $iCategoryOrder = 0;
            foreach ($this->_aPhotoCategories as $sCategory) {
                $iCategoryOrder++;
                db()->insert(':photo_category', array(
                        'parent_id' => 0,
                        'name' => $sCategory,
                        'time_stamp' => PHPFOX_TIME,
                        'ordering' => $iCategoryOrder,
                        'is_active' => 1
                    )
                );
            }
        }

        // remove settings
        db()->delete(':setting','module_id="photo" AND var_name="photo_image_details_time_stamp"');
        db()->delete(':setting','module_id="photo" AND var_name="html5_upload_photo"');

        // remove user group settings
        db()->delete(':user_group_setting','module_id="photo" AND name="can_add_tags_on_photos"');
        db()->delete(':user_group_setting','module_id="photo" AND name="can_edit_photo_categories"');
        db()->delete(':user_group_setting','module_id="photo" AND name="can_add_public_categories"');
        db()->delete(':user_group_setting','module_id="photo" AND name="total_photo_display_profile"');

        // remove menu add
        db()->delete(':menu', "`module_id` = 'photo' AND `url_value` = 'photo.add'");

        //remove component
        db()->delete(':component', "`module_id` = 'photo' AND `component` = 'stat'");

        // update module is app
        db()->update(':module', ['phrase_var_name' => 'module_apps', 'is_active' => 1], ['module_id' => 'photo']);
        //update phrase setting
        Phpfox::getService('language.phrase.process')->updatePhrases([
            'setting_phrase_display_profile_photo_within_gallery' => 'Display User Profile Photos within Gallery',
            'display_profile_photo_within_gallery' => '<title>Display User Profile Photos within Gallery</title><info>Disable this feature if you do not want to display user profile photos within the photo gallery.</info>'
        ]);
        $aMetaKeys = [
            'photo_meta_description' => array(
                'description' => '<title>Photo Meta Description</title><info>Meta description added to pages related to the Photo app. <a target="_bank" href="'.Phpfox_Url::instance()->makeUrl('admincp.language.phrase').'?q=seo_photo_meta_description">Click here</a> to edit meta description.<span style="float:right;">(SEO) <input style="width:150px;" readonly value="seo_photo_meta_description"></span></info>',
                'meta_value' => 'Share your photos with friends, family, and the world on Site Name.'
            ),
            'photo_meta_keywords' => array(
                'description' => '<title>Photo Meta Keywords</title><info>Meta keywords that will be displayed on sections related to the Photo app. <a target="_bank" href="'.Phpfox_Url::instance()->makeUrl('admincp.language.phrase').'?q=seo_photo_meta_keywords">Click here</a> to edit meta keywords.<span style="float:right;">(SEO) <input style="width:150px;" readonly value="seo_photo_meta_keywords"></span></info>',
                'meta_value' => 'photo, sharing, free, upload'
            ),
        ];
        // We'll update only one time
        foreach ($aMetaKeys as $sSetting => $aMetaKey) {
            $sNewPhrase = 'seo_' . $sSetting;
            if (!Lib::phrase()->isPhrase($sNewPhrase)) {
                $sValue = Phpfox::getParam('photo.' . $sSetting);

                // If old setting is formatted phrase already
                if (preg_match('/\{_p var=(.*)\}/i', $sValue, $aMatches)) {
                    Lib::phrase()->clonePhrase(trim($aMatches[1], '\'\"'), $sNewPhrase);
                } elseif (preg_match('/\{phrase var=(.*)\}/i', $sValue, $aMatches)) {
                    Lib::phrase()->clonePhrase(trim($aMatches[1], '\'\"'), $sNewPhrase);
                } else {
                    $sValue = $aMetaKey['meta_value'];
                    Lib::phrase()->addPhrase($sNewPhrase, $sValue);
                }

                // Update setting value
                db()->update(':setting', array(
                    'value_actual' => '{_p var=\'' . $sNewPhrase . '\'}',
                    'value_default' => '{_p var=\'' . $sNewPhrase . '\'}',
                    'type_id' => ''
                ), 'module_id=\'photo\' AND var_name=\'' . $sSetting . '\'');

                // Update setting description
                db()->update(':language_phrase', array(
                    'text' => $aMetaKey['description'],
                    'text_default' => $aMetaKey['description']
                ), 'var_name = \'' . $sSetting . '\'');
            }
        }
        $aUpdatePhrase = [
                'user_setting_can_tag_other_photos' => 'Can tag all photos?'
        ];
        Phpfox::getService('language.phrase.process')->updatePhrases($aUpdatePhrase);
    }
}