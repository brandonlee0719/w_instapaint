<?php

namespace Apps\PHPfox_Groups\Installation\Version;

use Phpfox;

class v460
{
    public function process()
    {
        $this->_updateProfileCoverPhotos();
        $this->_updateSettings();
        $this->_updateUserGroupSettings();
        $this->_insertUpdateDatabase();
    }

    /**
     * Update settings
     */
    private function _updateSettings()
    {
        // move show admin to block setting
        $sBlockSettings = db()->select('params')->from(':block')->where([
            'module_id' => 'groups',
            'product_id' => 'phpfox',
            'component' => 'admin'
        ])->executeField();

        if (!$sBlockSettings && Phpfox::getService('admincp.setting')->isSetting('pf_group_show_admins')) {
            db()->update(':block', [
                'params' => json_encode(['is_show' => Phpfox::getParam('groups.pf_group_show_admins')])
            ], [
                'module_id' => 'groups',
                'product_id' => 'phpfox',
                'component' => 'admin'
            ]);
        }
    }

    /**
     * Update user groups settings
     */
    private function _updateUserGroupSettings()
    {
        // apply moderator role for edit/delete
        $iEditSettingId = db()->select('setting_id')->from(':user_group_setting')
            ->where(['module_id' => 'groups', 'name' => 'can_edit_all_groups'])
            ->executeField();
        $iDeleteSettingId = db()->select('setting_id')->from(':user_group_setting')
            ->where(['module_id' => 'groups', 'name' => 'can_delete_all_groups'])
            ->executeField();
        $iApproveSettingId = db()->select('setting_id')->from(':user_group_setting')
            ->where(['module_id' => 'groups', 'name' => 'can_approve_groups'])
            ->executeField();
        for ($i = 1; $i < 6; $i++) {
            $bUserGroupModerate = Phpfox::getService('user.group.setting')->getGroupParam($i, 'pf_group_moderate');

            if ($bUserGroupModerate == null) {
                continue;
            }

            Phpfox::getService('user.group.setting.process')->update($i, [
                'value_actual' => [
                    $iEditSettingId => $bUserGroupModerate
                ]
            ]);
            Phpfox::getService('user.group.setting.process')->update($i, [
                'value_actual' => [
                    $iDeleteSettingId => $bUserGroupModerate
                ]
            ]);
            Phpfox::getService('user.group.setting.process')->update($i, [
                'value_actual' => [
                    $iApproveSettingId => $bUserGroupModerate
                ]
            ]);
        }

        // delete user group setting "Can moderate groups?"
        $iModerateSettingId = db()->select('setting_id')->from(':user_group_setting')
            ->where(['module_id' => 'groups', 'name' => 'pf_group_moderate'])
            ->executeField();
        db()->delete(':user_setting', ['setting_id' => $iModerateSettingId]);
        db()->delete(':user_group_setting', ['setting_id' => $iModerateSettingId]);
    }

    /**
     * Insert and update database
     */
    private function _insertUpdateDatabase()
    {
        // insert default categories
        $sDefaultDir = str_replace(PHPFOX_ROOT, '',
            dirname(dirname(__DIR__)) . PHPFOX_DS . 'assets' . PHPFOX_DS . 'img' . PHPFOX_DS . 'default-category' . PHPFOX_DS);
        $aGroupCategories = [
            [
                'name' => 'Sports',
                'image_path' => $sDefaultDir . 'sport.jpg',
                'image_server_id' => 0
            ],
            [
                'name' => 'Food',
                'image_path' => $sDefaultDir . 'food.jpg',
                'image_server_id' => 0
            ],
            [
                'name' => 'Travel',
                'image_path' => $sDefaultDir . 'travel.jpg',
                'image_server_id' => 0
            ],
            [
                'name' => 'Photography',
                'image_path' => $sDefaultDir . 'photography.jpg',
                'image_server_id' => 0
            ]
        ];

        /**
         * Change condition to `item_type = 1` inorder to solve the case:
         *  Admin upgrade Groups and old Groups deleted "Sport" category
         *  => Admin will not want this category appear again
         */
        if (!db()->select('COUNT(*)')->from(':pages_type')->where(['item_type' => 1])->executeField()) {
            $iCnt = 0;
            foreach ($aGroupCategories as $aCategory) {
                // translate category name
                $sCategoryVarName = strtolower($aCategory['name']);
                if (!Phpfox::getService('language.phrase')->isPhrase($sCategoryVarName)) {
                    Phpfox::getService('language.phrase.process')->add([
                        'var_name' => $sCategoryVarName,
                        'text' => [
                            'en' => $aCategory['name']
                        ]
                    ]);
                }

                db()->insert(':pages_type', [
                    'is_active' => '1',
                    'item_type' => '1',
                    'name' => $sCategoryVarName,
                    'image_path' => $aCategory['image_path'],
                    'image_server_id' => $aCategory['image_server_id'],
                    'time_stamp' => PHPFOX_TIME,
                    'ordering' => ++$iCnt
                ]);
            }
        } else {
            foreach ($aGroupCategories as $aCategory) {
                if (isset($aCategory['image_path'])) {
                    db()->update(':pages_type', [
                        'image_path' => $aCategory['image_path'],
                        'image_server_id' => $aCategory['image_server_id']
                    ], ['name' => $aCategory['name']]);
                }
            }
        }
    }

    /**
     * Update profile photos, cover photos
     */
    private function _updateProfileCoverPhotos()
    {
        // update group's profile photo album, cover photo album
        $aPages = db()->select('*')->from(':pages')
            ->where('(image_path IS NOT NULL OR cover_photo_id IS NOT NULL) AND item_type = 1')
            ->executeRows();
        foreach ($aPages as $aPage) {
            $iUserId = db()->select('user_id')->from(':user')->where(['profile_page_id' => $aPage['page_id']])->executeField();
            // process profile images inorder to show in page's photo tab
            if ($aPage['image_path']) {
                // update profile album
                db()->update(':photo_album', ['module_id' => 'pages', 'group_id' => $aPage['page_id']],
                    ['profile_id' => $iUserId]);
                // update profile images
                db()->update(':photo', ['module_id' => 'pages', 'group_id' => $aPage['page_id']],
                    ['user_id' => $iUserId, 'is_profile_photo' => 1]);
            }

            // process cover image
            if ($aPage['cover_photo_id']) {
                $iTimestamp = db()->select('time_stamp')->from(':photo')->where(['photo_id' => $aPage['cover_photo_id']])
                    ->executeField();
                // create new cover album
                $iCoverAlbumId = db()->insert(':photo_album', [
                    'view_id' => 0,
                    'module_id' => 'pages',
                    'group_id' => $aPage['page_id'],
                    'privacy' => 0,
                    'privacy_comment' => 0,
                    'user_id' => $iUserId,
                    'name' => '{_p var=\'cover_photo\'}',
                    'time_stamp' => $iTimestamp,
                    'total_photo' => 1,
                    'cover_id' => $iUserId
                ]);
                // set cover image to created album
                db()->update(':photo',
                    ['module_id' => 'pages', 'group_id' => $aPage['page_id'], 'album_id' => $iCoverAlbumId],
                    ['photo_id' => $aPage['cover_photo_id']]);
            }
        }
    }
}
