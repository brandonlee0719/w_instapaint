<?php

namespace Apps\Core_Photos\Service;

use Phpfox;
use Phpfox_Error;
use Phpfox_Plugin;
use \Core\Api\ApiServiceBase as ApiServiceBase;

class Api extends ApiServiceBase
{
    public function __construct()
    {
        $this->setPublicFields([
            'is_liked',
            'user_id',
            'photo_id',
            'album_id',
            'module_id',
            'group_id',
            'privacy',
            'privacy_comment',
            'title',
            'time_stamp',
            'is_featured',
            'is_sponsor',
            'tag_list',
            'categories',
            'bookmark_url',
            'photo_url'
        ]);
    }

    /**
     * @description: get detail info of a photo
     * @param array $params
     * @param array $messages
     *
     * @return array|bool
     */
    public function get($params, $messages = [])
    {
        if (!($aPhoto = Phpfox::getService('photo')->canViewItem($params['id'], true))) {
            return $this->error(_p('You don\'t have permission to {{ action }} this {{ item }}.',
                ['action' => _p('view__l'), 'item' => _p('photo__l')]), true);
        }

        $aItem = $this->getItem($aPhoto);
        return $this->success($aItem, $messages);
    }

    /**
     * @description: update info for a photo
     * @param $params
     *
     * @return array|bool
     */
    public function put($params)
    {
        $this->isUser();
        $aPhoto = Phpfox::getService('photo')->getPhoto($params['id']);

        if (!isset($aPhoto['photo_id'])) {
            return $this->error(_p('This {{ item }} cannot be found.', ['item' => _p('photo__l')]));
        }

        $aVals = $this->request()->getArray('val');
        unset($aPhoto['tag_list']);
        $aVals = array_merge($aPhoto, $aVals);


        if (($iUserId = Phpfox::getService('user.auth')->hasAccess('photo', 'photo_id', $params['id'],
                'photo.can_edit_own_photo',
                'photo.can_edit_other_photo')) && Phpfox::getService('photo.process')->update($iUserId, $params['id'],
                $aVals)
        ) {
            return $this->get(['id' => $params['id']],
                [_p('{{ item }} successfully updated.', ['item' => _p('photo')])]);
        }

        return $this->error(_p('You don\'t have permission to {{ action }} this {{ item }}.',
            ['action' => _p('edit__l'), 'item' => _p('photo__l')]), true);
    }

    /**
     * @description: delete a photo
     * @param $params
     *
     * @return array|bool
     */
    public function delete($params)
    {
        $this->isUser();

        if (Phpfox::getService('photo.process')->delete($params['id'])) {
            return $this->success([], [_p('{{ item }} successfully deleted.', ['item' => _p('photo')])]);
        }

        return $this->error(_p('Cannot {{ action }} this {{ item }}.',
            ['action' => _p('delete__l'), 'item' => _p('photo__l')]), true);
    }

    /**
     * @description: browse photos
     * @return array|bool
     */
    public function gets()
    {
        //check permission
        if (!Phpfox::getUserParam('photo.can_view_photos')) {
            return $this->error(_p('You don\'t have permission to browse {{ items }}.', ['items' => _p('photos__l')]));
        }

        //check for case get profile photos
        $userId = $this->request()->get('user_id', null);
        if ($userId) {
            $aUser = Phpfox::getService('user')->get($userId);
            if (!$aUser) {
                return $this->error('The {{ item }} cannot be found.', ['item' => _p('user__l')]);
            }

            if (Phpfox::isUser() && Phpfox::getService('user.block')->isBlocked(null, $userId)) {
                return $this->error('Sorry, this content isn\'t available right now');
            }

            if (!Phpfox::getService('user.privacy')->hasAccess($aUser['user_id'], 'photo.display_on_profile')) {
                return $this->error(_p('You don\'t have permission to browse {{ items }} on this profile.',
                    ['items' => _p('photos__l')]));
            }

        }

        $this->initSearchParams();
        $sView = $this->request()->get('view', false);

        $aSort = array(
            'latest' => ['photo.photo_id', _p('latest')],
            'most-viewed' => ['photo.total_view', _p('most_viewed')],
            'most-talked' => ['photo.total_comment', _p('most_discussed')]
        );

        $aSearchParam = [
            'type' => 'photo',
            'field' => 'photo.photo_id',
            'ignore_blocked' => true,
            'search_tool' => [
                'table_alias' => 'photo',
                'search' => [
                    'default_value' => _p('search_photos'),
                    'name' => 'search',
                    'field' => 'photo.title'
                ],
                'sort' => $aSort,
                'show' => [$this->getSearchParam('limit')]
            ]
        ];

        if (!Phpfox::getUserParam('photo.can_search_for_photos')) {
            unset($aSearchParam['search_tool']['search']);
        }

        $this->search()->set($aSearchParam);
        $aBrowseParams = array(
            'module_id' => 'photo',
            'alias' => 'photo',
            'field' => 'photo_id',
            'table' => Phpfox::getT('photo'),
            'hide_view' => array('pending', 'my')
        );

        $bCanBrowse = false;
        $moduleId = $this->request()->get('module_id', null);
        $itemId = $this->request()->get('item_id', null);

        switch ($sView) {
            case 'pending':
                if (Phpfox::isUser() && Phpfox::getUserParam('photo.can_approve_photos')) {
                    $bCanBrowse = true;
                    $this->search()->setCondition('AND photo.view_id = 1');
                }
                break;
            case 'my':
                if (Phpfox::isUser()) {
                    $bCanBrowse = true;
                    $this->search()->setCondition('AND photo.user_id = ' . Phpfox::getUserId());
                }
                break;
            default:
                $bCanBrowse = true;
                if ($userId) {
                    $this->search()->setCondition('AND photo.view_id ' . ($aUser['user_id'] == Phpfox::getUserId() ? 'IN(0,2)' : '= 0') . ' AND photo.group_id = 0 AND photo.type_id = 0 AND photo.privacy IN(' . (Phpfox::getParam('core.section_privacy_item_browsing') ? '%PRIVACY%' : Phpfox::getService('core')->getForBrowse($aUser)) . ') AND photo.user_id = ' . (int)$aUser['user_id']);
                } else {
                    if ($moduleId && $itemId) {
                        if (Phpfox::hasCallback($moduleId, 'getItem') && Phpfox::callback($moduleId . '.getItem',
                                $itemId) === false
                        ) {
                            return $this->error(_p('Cannot find the parent item.'));
                        }

                        if (Phpfox::hasCallback($moduleId,
                                'checkPermission') && !Phpfox::callback($moduleId . '.checkPermission', $itemId,
                                'photo.view_browse_photos')
                        ) {
                            return $this->error(_p('You don\'t have permission to browse {{ items }} on this item.',
                                ['items' => _p('photos__l')]));
                        }
                        $this->search()->setCondition('AND photo.view_id = 0 AND photo.module_id = \'' . db()->escape($moduleId) . '\' AND photo.group_id = ' . (int)$itemId . ' AND photo.privacy IN(%PRIVACY%)');
                    } else {
                        $this->search()->setCondition('AND photo.view_id = 0 AND photo.group_id = 0 AND photo.type_id = 0 AND photo.privacy IN(%PRIVACY%)');
                    }
                }
                break;
        }

        if (!$bCanBrowse) {
            return $this->error('You don\'t have permission to browse those {{ items }}.',
                ['items' => _p('photos__l')]);
        }

        $category = $this->request()->get('category', null);
        if ($category) {
            $sWhere = 'AND pcd.category_id = ' . (int)$category;

            // Get sub-categories
            $aSubCategories = Phpfox::getService('photo.category')->getForBrowse($category);

            if (!empty($aSubCategories) && is_array($aSubCategories)) {
                $aSubIds = Phpfox::getService('photo.category')->extractCategories($aSubCategories);
                if (!empty($aSubIds)) {
                    $sWhere = 'AND pcd.category_id IN (' . (int)$category . ',' . join(',', $aSubIds) . ')';
                }
            }

            $this->search()->setCondition($sWhere);
        }

        if (Phpfox::isModule('tag') && !Phpfox::getParam('tag.enable_hashtag_support') && ($tag = $this->request()->get('tag',
                null))
        ) {
            if (!defined('PHPFOX_GET_FORCE_REQ')) {
                define('PHPFOX_GET_FORCE_REQ', true);
            }
            if ($aTag = Phpfox::getService('tag')->getTagInfo('photo', $tag)) {
                $this->search()->setCondition('AND tag.tag_text = \'' . urldecode(db()->escape($aTag['tag_text'])) . '\'');
            } else {
                $this->search()->setCondition('AND 0');
            }
        }

        if ($sView == 'featured') {
            $this->search()->setCondition('AND photo.is_featured = 1');
        }

        if (!Phpfox::getParam('photo.display_profile_photo_within_gallery')) {
            $this->search()->setCondition('AND photo.is_profile_photo IN (0)');
        }
        if (!Phpfox::getParam('photo.display_cover_photo_within_gallery')) {
            $this->search()->setCondition('AND photo.is_cover_photo IN (0)');
        }
        if (!Phpfox::getParam('photo.display_timeline_photo_within_gallery')) {
            $this->search()->setCondition('(photo.type_id = 0 OR (photo.type_id = 1 AND photo.group_id != 0))');
        }

        $this->search()->browse()->params($aBrowseParams)->execute();
        $aItems = $this->search()->browse()->getRows();
        if (Phpfox_Error::isPassed()) {
            $result = [];
            foreach ($aItems as $aItem) {
                if (Phpfox::isModule('tag')) {
                    $aTags = Phpfox::getService('tag')->getTagsById('photo', $aItem['photo_id']);
                    if (isset($aTags[$aItem['photo_id']])) {
                        $aItem['tag_list'] = $aTags[$aItem['photo_id']];
                    }
                }

                $aItem['categories'] = Phpfox::getService('photo.category')->getCategoriesById($aItem['photo_id']);
                $aItem['bookmark_url'] = Phpfox::getLib('url')->permalink('photo', $aItem['photo_id'], $aItem['title']);
                $aItem['photo_url'] = Phpfox::getLib('image.helper')->display(array(
                    'server_id' => $aItem['server_id'],
                    'path' => 'photo.url_photo',
                    'file' => $aItem['destination'],
                    'suffix' => '_1024',
                    'return_url' => true
                ));
                $result[] = $this->getItem($aItem);
            }
            return $this->success($result);
        }
        return $this->error();
    }

    /**
     * @description: upload photos/ upload cover photo
     * @return array|bool
     */
    public function post()
    {
        $this->isUser();
        if (!Phpfox::getUserParam('photo.can_upload_photos')) {
            return $this->error(_p('You don\'t have permission to add new {{ item }}.', ['item' => _p('photo__l')]));
        }
        $aVals = $this->request()->getArray('val');
        if (!empty($aVals['module_id']) && !empty($aVals['item_id'])) {
            if (Phpfox::hasCallback($aVals['module_id'],
                    'getPhotoDetails') && Phpfox::callback($aVals['module_id'] . '.getPhotoDetails',
                    ['group_id' => $aVals['item_id']]) === false
            ) {
                return $this->error(_p('Cannot find the parent item.'));
            }
            if (Phpfox::hasCallback($aVals['module_id'],
                    'checkPermission') && !Phpfox::callback($aVals['module_id'] . '.checkPermission', $aVals['item_id'],
                    'photo.share_photos')
            ) {
                return $this->error(_p('You don\'t have permission to add new {{ item }} on this item.',
                    ['item' => _p('photo__l')]));
            }

            $aVals['parent_user_id'] = $aVals['group_id'] = $aVals['item_id'];
            $aVals['type_id'] = 1;
        }
        if (($iFlood = Phpfox::getUserParam('photo.flood_control_photos')) !== 0) {
            $aFlood = array(
                'action' => 'last_post', // The SPAM action
                'params' => array(
                    'field' => 'time_stamp', // The time stamp field
                    'table' => Phpfox::getT('photo'), // Database table we plan to check
                    'condition' => 'user_id = ' . Phpfox::getUserId(), // Database WHERE query
                    'time_stamp' => $iFlood * 60 // Seconds);
                )
            );

            // actually check if flooding
            if (Phpfox::getLib('spam')->check($aFlood)) {
                return $this->error(_p('uploading_photos_a_little_too_soon') . ' ' . Phpfox::getLib('spam')->getWaitTime());
            }
        }

        $oServicePhotoProcess = Phpfox::getService('photo.process');
        $oFile = \Phpfox_File::instance();
        $oImage = \Phpfox_Image::instance();
        $sFileName = '';
        $iId = 0;
        $aImages = [];
        $iFileSizes = 0;
        foreach ($_FILES['image']['error'] as $iKey => $sError) {
            if ($sError == UPLOAD_ERR_OK) {
                if ($aImage = $oFile->load('image[' . $iKey . ']', [
                    'jpg',
                    'gif',
                    'png'
                ],
                    (Phpfox::getUserParam('photo.photo_max_upload_size') === 0 ? null : (Phpfox::getUserParam('photo.photo_max_upload_size') / 1024)))
                ) {
                    $aVals['type_id'] = !empty($aVals['description']) ? 1 : (isset($aVals['type_id']) ? $aVals['type_id'] : 0);
                    if (!empty($aVals['user_id'])) {
                        $aVals['parent_user_id'] = $aVals['user_id'];
                    }

                    if ($iId = $oServicePhotoProcess->add(Phpfox::getUserId(), array_merge($aVals, $aImage))) {
                        $aPhoto = Phpfox::getService('photo')->getForProcess($iId);

                        // Move the uploaded image and return the full path to that image.
                        $sFileName = $oFile->upload('image[' . $iKey . ']',
                            Phpfox::getParam('photo.dir_photo'),
                            (Phpfox::getParam('photo.rename_uploaded_photo_names',
                                0) ? Phpfox::getUserBy('user_name') . '-' . preg_replace('/&#/i', 'u',
                                    $aPhoto['title']) : $iId),
                            (Phpfox::getParam('photo.rename_uploaded_photo_names', 0) ? array() : true)
                        );

                        if (!$sFileName) {
                            return $this->error(_p('Upload failed.'), true);
                        }

                        // Get the original image file size.
                        $iFileSizes += filesize(Phpfox::getParam('photo.dir_photo') . sprintf($sFileName, ''));

                        // Get the current image width/height
                        $aSize = getimagesize(Phpfox::getParam('photo.dir_photo') . sprintf($sFileName, ''));


                        // Update the image with the full path to where it is located.
                        $aUpdate = array(
                            'destination' => $sFileName,
                            'width' => $aSize[0],
                            'height' => $aSize[1],
                            'server_id' => Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID'),
                            'allow_rate' => (empty($aVals['album_id']) ? '1' : '0'),
                            'description' => (empty($aVals['description']) ? null : $aVals['description'])
                        );

                        if (isset($aVals['category_id'])) {
                            $aUpdate['category_id'] = $aVals['category_id'];
                        }

                        $oServicePhotoProcess->update(Phpfox::getUserId(), $iId, $aUpdate);

                        $aPhoto = Phpfox::getService('photo')->getForProcess($iId);
                        if (Phpfox::getParam('core.allow_cdn')) {
                            Phpfox::getLib('cdn')->setServerId($aPhoto['server_id']);
                        }

                        $sFileName = $aPhoto['destination'];
                        if (!file_exists(Phpfox::getParam('photo.dir_photo') . sprintf($sFileName, ''))
                            && Phpfox::getParam('core.allow_cdn')
                            && !Phpfox::getParam('core.keep_files_in_server')
                        ) {
                            if (Phpfox::getParam('core.allow_cdn') && $aPhoto['server_id'] > 0) {
                                $sActualFile = Phpfox::getLib('image.helper')->display(array(
                                        'server_id' => $aPhoto['server_id'],
                                        'path' => 'photo.url_photo',
                                        'file' => $aPhoto['destination'],
                                        'suffix' => '',
                                        'return_url' => true
                                    )
                                );

                                $aExts = preg_split("/[\/\\.]/", $sActualFile);
                                $iCnt = count($aExts) - 1;
                                $sExt = strtolower($aExts[$iCnt]);

                                $aParts = explode('/', $aPhoto['destination']);
                                $sFile = Phpfox::getParam('photo.dir_photo') . $aParts[0] . '/' . $aParts[1] . '/' . md5($aPhoto['destination']) . '.' . $sExt;

                                // Create a temp copy of the original file in local server, deleted later in line 606
                                copy($sActualFile, $sFile);
                            }
                        }

                        list($width, $height, ,) = getimagesize(Phpfox::getParam('photo.dir_photo') . sprintf($sFileName,
                                ''));

                        foreach (Phpfox::getService('photo')->getPhotoPicSizes() as $iSize) {
                            // Create the thumbnail
                            if ($oImage->createThumbnail(Phpfox::getParam('photo.dir_photo') . sprintf($sFileName, ''),
                                    Phpfox::getParam('photo.dir_photo') . sprintf($sFileName, '_' . $iSize), $iSize,
                                    $height, true,
                                    ((Phpfox::getParam('photo.enabled_watermark_on_photos') && Phpfox::getParam('core.watermark_option') != 'none') ? (Phpfox::getParam('core.watermark_option') == 'image' ? 'force_skip' : true) : false)) === false
                            ) {
                                continue;
                            }

                            if (Phpfox::getParam('photo.enabled_watermark_on_photos')) {
                                $oImage->addMark(Phpfox::getParam('photo.dir_photo') . sprintf($sFileName,
                                        '_' . $iSize));
                            }

                            $iFileSizes += filesize(Phpfox::getParam('photo.dir_photo') . sprintf($sFileName,
                                    '_' . $iSize));

                            if (defined('PHPFOX_IS_HOSTED_SCRIPT')) {
                                unlink(Phpfox::getParam('photo.dir_photo') . sprintf($sFileName, '_' . $iSize));
                            }
                        }

                        //Crop original image
                        $iWidth = (int)Phpfox::getUserParam('photo.maximum_image_width_keeps_in_server');
                        if ($iWidth < $width) {
                            $bIsCropped = $oImage->createThumbnail(Phpfox::getParam('photo.dir_photo') . sprintf($sFileName,
                                    ''), Phpfox::getParam('photo.dir_photo') . sprintf($sFileName, ''), $iWidth,
                                $height, true,
                                ((Phpfox::getParam('photo.enabled_watermark_on_photos') && Phpfox::getParam('core.watermark_option') != 'none') ? (Phpfox::getParam('core.watermark_option') == 'image' ? 'force_skip' : true) : false));
                            if ($bIsCropped !== false) {
                                //Rename file
                                if (Phpfox::getParam('photo.enabled_watermark_on_photos')) {
                                    $oImage->addMark(Phpfox::getParam('photo.dir_photo') . sprintf($sFileName, ''));
                                }
                                $iFileSizes += filesize(Phpfox::getParam('photo.dir_photo') . sprintf($sFileName, ''));
                                if (defined('PHPFOX_IS_HOSTED_SCRIPT')) {
                                    unlink(Phpfox::getParam('photo.dir_photo') . sprintf($sFileName, ''));
                                }
                            }
                        }

                        //End Crop
                        if (Phpfox::getParam('photo.enabled_watermark_on_photos')) {
                            $oImage->addMark(Phpfox::getParam('photo.dir_photo') . sprintf($sFileName, ''));
                        }

                        $aImages[] = $iId;
                    }
                }
            }
        }
        // Make sure we were able to upload some images
        if (count($aImages)) {
            if (defined('PHPFOX_IS_HOSTED_SCRIPT')) {
                unlink(Phpfox::getParam('photo.dir_photo') . sprintf($sFileName, ''));
            }

            // Have we posted an album for these set of photos?
            if (isset($aVals['album_id']) && !empty($aVals['album_id'])) {
                Phpfox::getService('photo.album')->getAlbum(Phpfox::getUserId(), $aVals['album_id'], true);

                // Set the album privacy
                Phpfox::getService('photo.album.process')->setPrivacy($aVals['album_id']);

                // Check if we already have an album cover
                if (!Phpfox::getService('photo.album.process')->hasCover($aVals['album_id'])) {
                    // Set the album cover
                    Phpfox::getService('photo.album.process')->setCover($aVals['album_id'], $iId);
                }

                // Update the album photo count
                if (!Phpfox::getUserParam('photo.photo_must_be_approved')) {
                    Phpfox::getService('photo.album.process')->updateCounter($aVals['album_id'], 'total_photo', false,
                        count($aImages));
                }
            }

            // Update the user space usage
            Phpfox::getService('user.space')->update(Phpfox::getUserId(), 'photo', $iFileSizes);
            $sMessage = (count($aImages) == 1) ? _p('photo_successfully_uploaded') : _p('photos_successfully_uploaded');

            if (count($aImages) == 1 && isset($aVals['is_cover_photo']) && $aVals['is_cover_photo']) {

                $sMessage = _p('Cover photo successfully updated.');

                if (empty($aVals['module_id']) || empty($aVals['item_id'])) {
                    Phpfox::getService('user.process')->updateCoverPhoto($iId);
                }

                (($sPlugin = Phpfox_Plugin::get('photo.set_cover_photo_for_item')) ? eval($sPlugin) : false);

            }
            $aCallback = (!empty($aVals['module_id']) && !empty($aVals['item_id']) && Phpfox::hasCallback($aVals['module_id'],
                    'addPhoto')) ? Phpfox::callback($aVals['module_id'] . '.addPhoto', $aVals['item_id']) : null;
            if (!Phpfox::getUserParam('photo.photo_must_be_approved') && empty($aVals['is_cover_photo'])) {
                if (Phpfox::isModule('feed')) {
                    $iFirstPhotoId = $aImages[0];
                    $aFirstPhoto = Phpfox::getService('photo')->getForProcess($iFirstPhotoId);
                    $iFeedId = Phpfox::getService('feed.process')->callback($aCallback)->add('photo',
                        $aFirstPhoto['photo_id'], $aFirstPhoto['privacy'], $aFirstPhoto['privacy_comment'],
                        (!empty($aVals['parent_user_id']) ? $aVals['parent_user_id'] : 0));
                    if ($aCallback && Phpfox::isModule('notification') && Phpfox::isModule($aCallback['module']) && Phpfox::hasCallback($aCallback['module'],
                            'addItemNotification')
                    ) {
                        Phpfox::callback($aCallback['module'] . '.addItemNotification', [
                            'page_id' => $aCallback['item_id'],
                            'item_perm' => 'photo.view_browse_photos',
                            'item_type' => 'photo',
                            'item_id' => $aFirstPhoto['photo_id'],
                            'owner_id' => $aFirstPhoto['user_id'],
                            'items_phrase' => _p('photos__l')
                        ]);
                    }

                    foreach ($aImages as $iImageId) {
                        if ($iImageId == $aFirstPhoto['photo_id']) {
                            continue;
                        }

                        db()->insert(Phpfox::getT('photo_feed'), array(
                                'feed_id' => $iFeedId,
                                'photo_id' => $iImageId,
                                'feed_table' => (empty($aCallback['table_prefix']) ? 'feed' : $aCallback['table_prefix'] . 'feed')
                            )
                        );
                    }
                }
            }

            $results = [];
            foreach ($aImages as $iImageId) {
                $aPhoto = Phpfox::getService('photo')->canViewItem($iImageId, true);
                if ($aPhoto) {
                    $results[] = $this->getItem($aPhoto);
                }
            }

            return $this->success($results, [$sMessage]);
        }

        return $this->error(_p('Cannot add new {{ item }}.', ['item' => _p('photo__l')]), true);
    }
}