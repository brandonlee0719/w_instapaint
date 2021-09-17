<?php

namespace Apps\PHPfox_Videos\Installation\Version;

use Phpfox;

class v452
{

    public function __construct()
    {
    }

    public function process()
    {
        // add status_info field for video
        if (!db()->isField(Phpfox::getT('video'), 'status_info')) {
            db()->query("ALTER TABLE  `" . Phpfox::getT('video') . "` ADD `status_info` mediumtext NULL DEFAULT NULL");
        }

        // add page_user_id field for video
        if (!db()->isField(Phpfox::getT('video'), 'page_user_id')) {
            db()->query("ALTER TABLE  `" . Phpfox::getT('video') . "` ADD `page_user_id` int(10) unsigned NOT NULL DEFAULT '0'");
            $aVideos = db()
                ->select('video_id, item_id, module_id')
                ->from(Phpfox::getT('video'))
                ->where('module_id IN ("pages","groups")')
                ->execute('getSlaveRows');
            if (count($aVideos)) {
                foreach ($aVideos as $aVideo) {
                    $iPageUserId = db()->select('user_id')->from(Phpfox::getT('user'))->where('profile_page_id = ' . (int)$aVideo['item_id'] . ' AND view_id = 7')->execute('getSlaveField');
                    if ($iPageUserId) {
                        db()->update(Phpfox::getT('video'), ['page_user_id' => $iPageUserId],
                            ['video_id' => (int)$aVideo['video_id']]);

                        if ($aVideo['module_id'] == 'pages') {
                            $iItemType = db()
                                ->select('item_type')
                                ->from(Phpfox::getT('pages'))
                                ->where('page_id = ' . (int)$aVideo['item_id'])
                                ->execute('getSlaveField');
                            if ($iItemType) {
                                db()->update(Phpfox::getT('video'), ['module_id' => 'groups'],
                                    ['video_id' => (int)$aVideo['video_id']]);
                            }
                        }
                    }
                }
            }
        }

        // delete group/page's videos deleted (fix user was upgraded to 4.5.2
//        if(count($aVideos)) {
//            foreach ($aVideos as $aVideo) {
//                $iPageUserId = db()->select('user_id')->from(Phpfox::getT('user'))->where('profile_page_id = ' . (int)$aVideo['item_id'] . ' AND view_id = 7')->execute('getSlaveField');
//                if(!$iPageUserId) {
//                    db()->delete(Phpfox::getT('video'), '`video_id` = ' . (int)$aVideo['video_id']);
//                }
//            }
//        }

        // add activity video
        if (!db()->isField(Phpfox::getT('user_activity'), 'activity_v')) {
            db()->query("ALTER TABLE  `" . Phpfox::getT('user_activity') . "` ADD `activity_v` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0'");
        }

        // add statistics total video
        if (!db()->isField(Phpfox::getT('user_field'), 'total_video')) {
            db()->query("ALTER TABLE  `" . Phpfox::getT('user_field') . "` ADD `total_video` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0'");
        }

        // remove v main url
        db()->delete(Phpfox::getT('menu'), "`module_id` = 'v' AND `var_name` = 'menu_videos' AND `url_value` = '/v'");
        db()->delete(Phpfox::getT('menu'),
            "`module_id` = 'v' AND `var_name` = 'menu_videos' AND `url_value` = '/video'");

        db()->query("ALTER TABLE `" . Phpfox::getT('video') . "` CHANGE `destination` `destination` VARCHAR(255)");
        db()->query("ALTER TABLE `" . Phpfox::getT('video') . "` CHANGE `image_path` `image_path` VARCHAR(255)");

        // add rewrite url
        $aRewrite = db()
            ->select('*')
            ->from(Phpfox::getT('rewrite'))
            ->where('url = \'v\' AND replacement = \'video\'')
            ->execute('getSlaveRow');

        if (empty($aRewrite)) {
            db()->insert(Phpfox::getT('rewrite'), ['url' => 'v', 'replacement' => 'video']);
        }

        // move category from cash to video category
        $aCategories = storage()->all('pf_video_category');
        if ($aCategories) {
            foreach ($aCategories as $oCategory) {
                $iCategoryId = db()->insert(Phpfox::getT('video_category'), array(
                        'parent_id' => 0,
                        'name' => $oCategory->value,
                        'time_stamp' => PHPFOX_TIME,
                        'ordering' => $oCategory->order,
                        'is_active' => 1
                    )
                );
                storage()->set('pf_video_categories_map', $iCategoryId, $oCategory->id);
            }
            storage()->del('pf_video_category');
        } else { // or add default category
            $iTotalCategory = db()
                ->select('COUNT(category_id)')
                ->from(Phpfox::getT('video_category'))
                ->execute('getField');

            if ($iTotalCategory == 0) {
                $aCategories = ['Gaming', 'Film & Entertainment', 'Comedy', 'Music'];
                foreach ($aCategories as $sCategory) {
                    db()->insert(Phpfox::getT('video_category'), array(
                            'parent_id' => 0,
                            'name' => $sCategory,
                            'time_stamp' => PHPFOX_TIME,
                            'ordering' => 99,
                            'is_active' => 1
                        )
                    );
                }
            }
        }

        // migrate track from V3
        if (db()->tableExists(Phpfox::getT('video_track'))) {
            $aVideoTracks = db()
                ->select('user_id, item_id, ip_address, time_stamp')
                ->from(Phpfox::getT('video_track'))
                ->limit(10000)
                ->execute('getSlaveRows');
            if (count($aVideoTracks)) {
                foreach ($aVideoTracks as $aVideoTrack) {
                    db()->insert(Phpfox::getT('track'), array(
                            'item_id' => $aVideoTrack['item_id'],
                            'user_id' => $aVideoTrack['user_id'],
                            'ip_address' => $aVideoTrack['ip_address'],
                            'time_stamp' => $aVideoTrack['time_stamp'],
                            'type_id' => 'v'
                        )
                    );
                }
            }
            db()->query("DROP TABLE `" . Phpfox::getT('video_track') . "`");
        }
        // drop table not use
        if (db()->tableExists(Phpfox::getT('video_rating'))) {
            db()->query("DROP TABLE `" . Phpfox::getT('video_rating') . "`");
        }
        if (db()->tableExists(Phpfox::getT('video_custom'))) {
            db()->query("DROP TABLE `" . Phpfox::getT('video_custom') . "`");
        }
    }
}
