<?php

namespace Apps\Core_Photos\Service;

use Phpfox;
use Phpfox_Error;
use Phpfox_Plugin;
use Phpfox_Service;

class Process extends Phpfox_Service
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('photo');
    }

    /**
     * @param $iId
     * @return bool
     */
    public function makeProfilePicture($iId)
    {
        $aPhoto = db()->select('p.destination, p.server_id')
            ->from(Phpfox::getT('photo'), 'p')
            ->where('p.photo_id = ' . (int)$iId)
            ->execute('getSlaveRow');

        if (empty($aPhoto) || !isset($aPhoto['destination'])) {
            return false;
        }
        $aPhoto['destination'] = str_replace(array('{', '}'), '', $aPhoto['destination']);
        $sTempName = Phpfox::getParam('photo.dir_photo') . sprintf($aPhoto['destination'], '');
        if (!file_exists($sTempName)) {
            $sTempName = Phpfox::getParam('photo.dir_photo') . sprintf($aPhoto['destination'], '_500');
        }
        if (!file_exists($sTempName) && Phpfox::getParam('core.allow_cdn')) {
            $sActualFile = Phpfox::getLib('image.helper')->display(array(
                    'server_id' => $aPhoto['server_id'],
                    'path' => 'photo.url_photo',
                    'file' => $aPhoto['destination'],
                    'suffix' => '_1024',
                    'return_url' => true
                )
            );
            file_put_contents($sTempName, fox_get_contents($sActualFile));
            register_shutdown_function(function () use ($sTempName) {
                @unlink($sTempName);
            });
        }
        define('PHPFOX_USER_PHOTO_IS_COPY', true);
        $aRet = Phpfox::getService('user.process')->uploadImage(Phpfox::getUserId(), true, $sTempName, false, $iId);

        if ($sPlugin = Phpfox_Plugin::get('photo.service_process_make_profile_picture__end')) {
            eval($sPlugin);
        }

        return (isset($aRet['user_image']) && !empty($aRet['user_image']));
    }

    /**
     * @param $iId
     * @return bool
     */
    public function makeCoverPicture($iId)
    {
        $aPhoto = db()->select('p.destination, p.server_id, pi.file_name, pi.file_size, pi.mime_type, pi.extension')
            ->from(Phpfox::getT('photo'), 'p')
            ->leftJoin(Phpfox::getT('photo_info'), 'pi', 'pi.photo_id = p.photo_id')
            ->where('p.photo_id = ' . (int)$iId)
            ->executeRow();

        if (empty($aPhoto) || !isset($aPhoto['destination'])) {
            return false;
        }
        $aPhoto['destination'] = str_replace(array('{', '}'), '', $aPhoto['destination']);
        $sTempName = Phpfox::getParam('photo.dir_photo') . sprintf($aPhoto['destination'], '');
        if (!file_exists($sTempName)) {
            $sTempName = Phpfox::getParam('photo.dir_photo') . sprintf($aPhoto['destination'], '_1024');
        }
        if (!file_exists($sTempName) && Phpfox::getParam('core.allow_cdn')) {
            $sActualFile = Phpfox::getLib('image.helper')->display(array(
                    'server_id' => $aPhoto['server_id'],
                    'path' => 'photo.url_photo',
                    'file' => $aPhoto['destination'],
                    'suffix' => '_1024',
                    'return_url' => true
                )
            );
            file_put_contents($sTempName, fox_get_contents($sActualFile));
            register_shutdown_function(function () use ($sTempName) {
                @unlink($sTempName);
            });
        }
        $oFile = \Phpfox_File::instance();
        $aImage = [
            'description' => null,
            'type_id' => 0,
            "name" => $aPhoto['file_name'],
            'type' => $aPhoto['mime_type'],
            'size' => $aPhoto['file_size'],
            'ext' => $aPhoto['extension']
        ];

        if ($iPhotoId = $this->add(Phpfox::getUserId(), $aImage)) {
            // Move the uploaded image and return the full path to that image.
            $sFileName = $oFile->upload($sTempName, Phpfox::getParam('photo.dir_photo'), $iPhotoId);
            // Get the original image file size.
            $iFileSizes = filesize(Phpfox::getParam('photo.dir_photo') . sprintf($sFileName, ''));
            //Create thumbnail for new cover photo
            $oImage = Phpfox::getLib('image');
            $sFile = Phpfox::getParam('photo.dir_photo') . sprintf($sFileName, '');
            list(, $height, ,) = getimagesize($sFile);
            foreach(Phpfox::getService('photo')->getPhotoPicSizes() as $iSize)
            {
                $oImage->createThumbnail($sFile, Phpfox::getParam('photo.dir_photo') . sprintf($sFileName, '_' . $iSize), $iSize, $height, true, ((Phpfox::getParam('photo.enabled_watermark_on_photos') && Phpfox::getParam('core.watermark_option') != 'none') ? (Phpfox::getParam('core.watermark_option') == 'image' ? 'force_skip' : true) : false));
                $iFileSizes += filesize(Phpfox::getParam('photo.dir_photo') . sprintf($sFileName,
                        '_' . $iSize));
            }
            // Update the user space usage
            Phpfox::getService('user.space')->update(Phpfox::getUserId(), 'photo', $iFileSizes);

            // Get the current image width/height
            $aSize = getimagesize(Phpfox::getParam('photo.dir_photo') . sprintf($sFileName, ''));

            // Update the image with the full path to where it is located.
            $aUpdate = array(
                'destination' => $sFileName,
                'width' => $aSize[0],
                'height' => $aSize[1],
                'server_id' => \Phpfox_Request::instance()->getServer('PHPFOX_SERVER_ID'),
                'allow_rate' => 1,
                'description' => null
            );
            $this->update(Phpfox::getUserId(), $iPhotoId, $aUpdate);
            return Phpfox::getService('user.process')->updateCoverPhoto($iPhotoId);
        }
        return false;
    }

    /**
     * Adding a new photo.
     *
     * @param int $iUserId User ID of the user that the photo belongs to.
     * @param array $aVals Array of the post data being passed to insert.
     * @param boolean $bIsUpdate True if we plan to update the entry or false to insert a new entry in the database.
     * @param boolean $bAllowTitleUrl Set to true to allow the editing of the SEO url. remove in v4.6
     *
     * @return int ID of the newly added photo or the ID of the current photo we are editing.
     */
    public function add($iUserId, $aVals, $bIsUpdate = false, $bAllowTitleUrl = false)
    {
        $oParseInput = Phpfox::getLib('parse.input');

        // Create the fields to insert.
        $aFields = array();

        if ($sPlugin = Phpfox_Plugin::get('photo.service_process_add__start')) {
            eval($sPlugin);
        }

        if (isset($aVals['type_id']) && $aVals['type_id'] == 1 && empty($aVals['parent_user_id'])) {
            $iTimelineAlbumId = db()->select('album_id')
                ->from(Phpfox::getT('photo_album'))
                ->where('timeline_id=' . (int)$iUserId)
                ->execute('getSlaveField');
            if (empty($iTimelineAlbumId)) {
                $iTimelineAlbumId = db()->insert(Phpfox::getT('photo_album'), array(
                    'privacy' => '0',
                    'privacy_comment' => '0',
                    'user_id' => (int)$iUserId,
                    'name' => "{_p var='timeline_photos'}",
                    'time_stamp' => PHPFOX_TIME,
                    'timeline_id' => $iUserId,
                    'total_photo' => 0
                ));
                db()->insert(Phpfox::getT('photo_album_info'), array('album_id' => $iTimelineAlbumId));
            }
            db()->update(Phpfox::getT('photo'), array('is_cover' => 0), 'album_id=' . (int)$iTimelineAlbumId);
            db()->updateCounter('photo_album', 'total_photo', 'album_id', $iTimelineAlbumId);
            $aVals['album_id'] = $iTimelineAlbumId;
            $aFields['is_cover'] = 'int';
            $aVals['is_cover'] = 1;
        }

        // Make sure we are updating the album ID
        (!empty($aVals['album_id']) ? $aFields['album_id'] = 'int' : null);

        // Is this an update?
        if ($bIsUpdate) {
            // Make sure we only update the fields that the user is allowed to
            (Phpfox::getUserParam('photo.can_add_mature_images') ? $aFields['mature'] = 'int' : null);
            $aFields['allow_comment'] = 'int';
            $aFields['allow_rate'] = null;
            (!empty($aVals['destination']) ? $aFields[] = 'destination' : null);

            // Check if we really need to update the title
            if (!empty($aVals['title'])) {
                $aFields[] = 'title';

                $bWindows = false;
                if (stristr(PHP_OS, "win")) {
                    $bWindows = true;
                } else {
                    $aVals['original_title'] = $aVals['title'];
                }

                // Clean the title for any sneaky attacks
                $aVals['title'] = $oParseInput->clean($aVals['title'], 255);

                if (Phpfox::getParam('photo.rename_uploaded_photo_names', 0)) {
                    $aFields[] = 'destination';

                    $aPhoto = db()->select('destination')
                        ->from($this->_sTable)
                        ->where('photo_id = ' . $aVals['photo_id'])
                        ->execute('getSlaveRow');

                    $sNewName = preg_replace("/^(.*?)-(.*?)%(.*?)$/",
                        "$1-" . str_replace('%', '', ($bWindows ? $aVals['title'] : $aVals['original_title'])) . "%$3",
                        $aPhoto['destination']);
                    $sNewName = preg_replace('/&#/i', 'u', $oParseInput->convert($sNewName));

                    $aVals['destination'] = $sNewName;

                    if (file_exists(Phpfox::getParam('photo.dir_photo') . sprintf($aPhoto['destination'], ''))) {
                        Phpfox::getLib('file')->rename(Phpfox::getParam('photo.dir_photo') . sprintf($aPhoto['destination'],
                                ''), Phpfox::getParam('photo.dir_photo') . sprintf($sNewName, ''));
                    }

                    // Create thumbnails with different sizes depending on the global param.
                    foreach (Phpfox::getService('photo')->getPhotoPicSizes() as $iSize) {
                        Phpfox::getLib('file')->rename(Phpfox::getParam('photo.dir_photo') . sprintf($aPhoto['destination'],
                                '_' . $iSize), Phpfox::getParam('photo.dir_photo') . sprintf($sNewName, '_' . $iSize));
                    }
                }
            }

            $iAlbumId = (int)(empty($aVals['move_to']) ? (isset($aVals['album_id']) ? $aVals['album_id'] : 0) : $aVals['move_to']);

            if (!empty($aVals['set_album_cover'])) {
                $aFields['is_cover'] = 'int';
                $aVals['is_cover'] = '1';

                db()->update(Phpfox::getT('photo'), array('is_cover' => '0'), 'album_id = ' . (int)$iAlbumId);
            }

            $iOldAlbumId = 0;
            $bIsCoverPhoto = false;
            if (!empty($aVals['move_to'])) {
                $aPhoto = Phpfox::getService('photo')->getPhotoItem($aVals['photo_id']);
                if ($aPhoto) {
                    $iOldAlbumId = $aPhoto['album_id'];
                    if ($aPhoto['is_cover'] == 1) {
                        $bIsCoverPhoto = true;
                    }
                }
                $aFields['album_id'] = 'int';
                $aVals['album_id'] = (int)$aVals['move_to'];

                $aAlbum = Phpfox::getService('photo.album')->getForEdit($aVals['move_to']);
                if ($aAlbum) {
                    $aFields['module_id'] = '';
                    $aVals['module_id'] = $aAlbum['module_id'];

                    $aFields['group_id'] = 'int';
                    $aVals['group_id'] = (int)$aAlbum['group_id'];
                }

                if (!isset($aVals['is_cover'])) {
                    $aFields['is_cover'] = 'int';
                    $aVals['is_cover'] = '0';
                }
            }

            if (isset($aVals['privacy'])) {
                $aFields['privacy'] = 'int';
                $aFields['privacy_comment'] = 'int';
            }

            if (!isset($aVals['allow_download'])) {
                $aVals['allow_download'] = 0;
            }
            $aFields['allow_download'] = 'int';
            // Update the data into the database.
            db()->process($aFields, $aVals)->update($this->_sTable, 'photo_id = ' . (int)$aVals['photo_id']);

            // Check if we need to update the description of the photo
            $aFieldsInfo = array(
                'description'
            );

            // Clean the data before we add it into the database
            $aVals['description'] = (empty($aVals['description']) ? null : $this->preParse()->prepare($aVals['description']));

            (!empty($aVals['width']) ? $aFieldsInfo[] = 'width' : 0);
            (!empty($aVals['height']) ? $aFieldsInfo[] = 'height' : 0);

            // Check if we have anything to add into the photo_info table
            if (isset($aFieldsInfo)) {
                db()->process($aFieldsInfo, $aVals)->update(Phpfox::getT('photo_info'),
                    'photo_id = ' . (int)$aVals['photo_id']);
            }

            if (!empty($aVals['location'])) {
                $aLocation = [
                    'location_name' => !empty($aVals['location']['name']) ? Phpfox::getLib('parse.input')->clean($aVals['location']['name']) : null,
                    'location_latlng' => null
                ];
                if ((!empty($aVals['location']['latlng']))) {
                    $aMatch = explode(',', $aVals['location']['latlng']);
                    $aMatch['latitude'] = floatval($aMatch[0]);
                    $aMatch['longitude'] = floatval($aMatch[1]);
                    $aLocation['location_latlng'] = json_encode(array(
                        'latitude' => $aMatch['latitude'],
                        'longitude' => $aMatch['longitude']
                    ));
                }
                db()->update(Phpfox::getT('photo_info'), $aLocation, 'photo_id =' . (int)$aVals['photo_id']);
            }
            // Add tags for the photo
            if (Phpfox::isModule('tag')) {
                if (Phpfox::getParam('tag.enable_hashtag_support')) {
                    Phpfox::getService('tag.process')->update('photo', $aVals['photo_id'], $iUserId,
                        (!empty($aVals['description']) ? $aVals['description'] : null), true);
                }
                if (Phpfox::getParam('tag.enable_tag_support')) {
                    Phpfox::getService('tag.process')->update('photo', $aVals['photo_id'], $iUserId,
                        (!empty($aVals['tag_list']) ? $aVals['tag_list'] : null));
                }
            }

            // Make sure if we plan to add categories for this image that there is something to add
            db()->delete(Phpfox::getT('photo_category_data'), 'photo_id = ' . (int)$aVals['photo_id']);
            if (isset($aVals['category_id']) && count($aVals['category_id'])) {
                if (!is_array($aVals['category_id'])) {
                    $aVals['category_id'] = array($aVals['category_id']);
                }
                // Loop through all the categories

                foreach ($aVals['category_id'] as $iCategory) {
                    // Add each of the categories
                    if ((int)$iCategory) {
                        Phpfox::getService('photo.category.process')->updateForItem($aVals['photo_id'], $iCategory);
                    }
                }
            }

            $iId = $aVals['photo_id'];

            if (Phpfox::isModule('privacy') && isset($aVals['privacy'])) {
                if ($aVals['privacy'] == '4') {
                    Phpfox::getService('privacy.process')->update('photo', $iId,
                        (isset($aVals['privacy_list']) ? $aVals['privacy_list'] : array()));
                } else {
                    Phpfox::getService('privacy.process')->delete('photo', $iId);
                }
            }

            if (!empty($iAlbumId)) {
                $aAlbum = Phpfox::getService('photo.album')->getAlbum($iUserId, $iAlbumId, true);
                if (!empty($aAlbum['privacy'])) {
                    $aVals['privacy'] = $aAlbum['privacy'];
                }
                if (!empty($aAlbum['privacy_comment'])) {
                    $aVals['privacy_comment'] = $aAlbum['privacy_comment'];
                }
                $this->database()->update(Phpfox::getT('photo'),
                    [
                        'privacy' => (!empty($aAlbum['privacy']) ? $aAlbum['privacy'] : 0),
                        'privacy_comment' => (!empty($aAlbum['privacy_comment']) ? $aAlbum['privacy_comment'] : 0)
                    ],
                    'photo_id = ' . (int)$iId);
                if (isset($aAlbum['privacy']) && $aAlbum['privacy'] == 4) {
                    $aPrivacy = Phpfox::getService('privacy')->get('photo_album', $aAlbum['album_id']);
                    Phpfox::getService('privacy.process')->delete('photo', $iId);
                    $aList = [];
                    foreach ($aPrivacy as $privacy) {
                        $aList[] = $privacy['friend_list_id'];
                    }
                    Phpfox::getService('privacy.process')->add('photo', $iId, $aList);
                }
            }

            if (!isset($aVals['privacy'])) {
                $aVals['privacy'] = 0;
            }

            if (!isset($aVals['privacy_comment'])) {
                $aVals['privacy_comment'] = 0;
            }

            (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->update('photo', $iId, $aVals['privacy'],
                $aVals['privacy_comment']) : null);

            if (!empty($aVals['move_to'])) {
                // check before move feed
                $iFeedId = db()->select('feed_id')
                    ->from(':feed')
                    ->where('type_id = \'photo\' AND item_id = ' . (int)$aVals['photo_id'])
                    ->execute('getSlaveField');

                if ($iFeedId) {
                    $iPhotoId = db()->select('photo_id')
                        ->from(':photo_feed')
                        ->where('feed_id = ' . (int)$iFeedId)
                        ->limit(1)
                        ->execute('getSlaveField');
                    if ($iPhotoId) {
                        db()->update(Phpfox::getT('feed'), ['item_id' => $iPhotoId], ['feed_id' => (int)$iFeedId]);
                        // delete the photo feed
                        db()->delete(Phpfox::getT('photo_feed'), 'photo_id = ' . $iPhotoId);
                    } else {
                        (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->delete('photo',
                            $aVals['photo_id']) : null);
                    }
                }

                Phpfox::getService('photo.album.process')->updateCounter($aVals['move_to'], 'total_photo');
                if ($iOldAlbumId) {
                    Phpfox::getService('photo.album.process')->updateCounter($iOldAlbumId, 'total_photo', true);
                    if ($bIsCoverPhoto) {
                        $iNewCoverPhotoId = db()->select('photo_id')
                            ->from(Phpfox::getT('photo'))
                            ->where('album_id = ' . (int)$iOldAlbumId)
                            ->order('time_stamp DESC')
                            ->execute('getSlaveField');
                        if ($iNewCoverPhotoId) {
                            db()->update(Phpfox::getT('photo'), ['is_cover' => 1],
                                ['photo_id' => (int)$iNewCoverPhotoId]);
                        }
                    }
                }
            }
        } else {
            if (!empty($aVals['callback_module'])) {
                $aVals['module_id'] = $aVals['callback_module'];
            }

            // Define all the fields we need to enter into the database
            $aFields['user_id'] = 'int';
            $aFields['parent_user_id'] = 'int';
            $aFields['type_id'] = 'int';
            $aFields['allow_download'] = 'int';
            $aFields['time_stamp'] = 'int';
            $aFields['server_id'] = 'int';
            $aFields['view_id'] = 'int';
            $aFields['group_id'] = 'int';
            $aFields[] = 'module_id';
            $aFields[] = 'title';

            if (isset($aVals['privacy'])) {
                $aFields['privacy'] = 'int';
                $aFields['privacy_comment'] = 'int';
            }

            // Define all the fields we need to enter into the photo_info table
            $aFieldsInfo = array(
                'photo_id' => 'int',
                'file_name',
                'mime_type',
                'extension',
                'file_size' => 'int',
                'description',
                'location_name',
                'location_latlng'
            );

            // Clean and prepare the title and SEO title
            $aVals['title'] = $oParseInput->clean(rtrim(preg_replace("/^(.*?)\.(jpg|jpeg|gif|png)$/i", "$1",
                rawurldecode($aVals['name']))), 255);

            // Add the user_id
            $aVals['user_id'] = $iUserId;

            // Add the original server ID for LB.
            $aVals['server_id'] = Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID');

            // Add the time stamp.
            $aVals['time_stamp'] = PHPFOX_TIME;

            $aVals['view_id'] = (Phpfox::getUserParam('photo.photo_must_be_approved') ? '1' : '0');
            if (!isset($aVals['allow_download'])) {
                $aVals['allow_download'] = 1;
            }
            // Insert the data into the database.
            $iId = db()->process($aFields, $aVals)->insert($this->_sTable);

            // Prepare the data to enter into the photo_info table
            $aInfo = array(
                'photo_id' => $iId,
                'file_name' => Phpfox::getLib('parse.input')->clean($aVals['name'], 100),
                'extension' => strtolower($aVals['ext']),
                'file_size' => $aVals['size'],
                'mime_type' => $aVals['type'],
                'description' => (empty($aVals['description']) ? null : $this->preParse()->prepare($aVals['description'])),
                'location_name' => (!empty($aVals['location']['name'])) ? Phpfox::getLib('parse.input')->clean($aVals['location']['name']) : null,
                'location_latlng' => null
            );
            if ((!empty($aVals['location']['latlng']))) {
                $aMatch = explode(',', $aVals['location']['latlng']);
                $aMatch['latitude'] = floatval($aMatch[0]);
                $aMatch['longitude'] = floatval($aMatch[1]);
                $aInfo['location_latlng'] = json_encode(array(
                    'latitude' => $aMatch['latitude'],
                    'longitude' => $aMatch['longitude']
                ));
            }
            // Insert the data into the photo_info table
            db()->process($aFieldsInfo, $aInfo)->insert(Phpfox::getT('photo_info'));

            if (!Phpfox::getUserParam('photo.photo_must_be_approved')) {
                // Update user activity
                Phpfox::getService('user.activity')->update($iUserId, 'photo');
            }

            // Make sure if we plan to add categories for this image that there is something to add
            if (isset($aVals['category_id']) && count($aVals['category_id'])) {
                // Loop thru all the categories
                foreach ($aVals['category_id'] as $iCategory) {
                    // Add each of the categories
                    if ((int)$iCategory) {
                        Phpfox::getService('photo.category.process')->updateForItem($iId, $iCategory);
                    }
                }
            }

            if (isset($aVals['privacy'])) {
                if ($aVals['privacy'] == '4') {
                    Phpfox::getService('privacy.process')->add('photo', $iId,
                        (isset($aVals['privacy_list']) ? $aVals['privacy_list'] : array()));
                }
            }

            if (Phpfox::isModule('tag') && Phpfox::getParam('tag.enable_hashtag_support') && !empty($aVals['description'])) {
                Phpfox::getService('tag.process')->add('photo', $iId, $iUserId, $aVals['description'], true);
            }
        }

        // Plugin call
        if ($sPlugin = Phpfox_Plugin::get('photo.service_process_add__end')) {
            eval($sPlugin);
        }

        // Return the photo ID#
        return $iId;
    }

    /**
     * Updating a new photo. We piggy back on the add() method so we don't have to do the same code twice.
     *
     * @param int $iUserId User ID of the user that the photo belongs to.
     * @param int $iId
     * @param array $aVals Array of the post data being passed to insert.
     * @param boolean $bAllowTitleUrl Set to true to allow the editing of the SEO url.
     *
     * @return int ID of the newly added photo or the ID of the current photo we are editing.
     */
    public function update($iUserId, $iId, $aVals, $bAllowTitleUrl = false)
    {
        $aVals['photo_id'] = $iId;
        if (Phpfox::getParam('feed.cache_each_feed_entry')) {
            $this->cache()->remove(array('feeds', 'photo_' . $iId));
        }
        return $this->add($iUserId, $aVals, true, $bAllowTitleUrl);
    }

    /**
     * Used to delete a photo.
     * @param int $iId ID of the photo we want to delete.
     * @param bool $bPass
     * @param string $sView
     * @param int $iUserId
     * @return boolean We return true since if nothing fails we were able to delete the image.
     */
    public function delete($iId, $bPass = false, $sView = '', $iUserId = 0)
    {
        // Get the image ID and full path to the image.
        $aPhoto = db()->select('user_id, module_id, group_id, is_sponsor, is_featured, album_id, photo_id, destination, server_id, is_cover, type_id, parent_user_id')
            ->from($this->_sTable)
            ->where('photo_id = ' . (int)$iId)
            ->execute('getSlaveRow');
        if (!isset($aPhoto['user_id'])) {
            return false;
        }
        // check current page to redirect when delete success
        $sParentReturn = true;
        if ($aPhoto['module_id'] == 'pages' && Phpfox::getService('pages')->isAdmin($aPhoto['group_id'])) {
            $sParentReturn = Phpfox::getService('pages')->getUrl($aPhoto['group_id']) . 'photo/';
            $bPass = true; // is owner of page
        } elseif ($aPhoto['module_id'] == 'groups' && Phpfox::getService('groups')->isAdmin($aPhoto['group_id'])) {
            $sParentReturn = Phpfox::getService('groups')->getUrl($aPhoto['group_id']) . 'photo/';
            $bPass = true; // is owner of group
        } elseif ($aPhoto['type_id'] == 1 && Phpfox::getUserId() == $aPhoto['parent_user_id'] && $aPhoto['parent_user_id'] != 0 && $aPhoto['group_id'] == 0) {
            $sParentReturn = Phpfox::getService('user')->getLink($aPhoto['parent_user_id']);
            $bPass = true; // is owner of wall
        }
        if (!empty($sView)) {
            if ($sView != 'view' && $sView != 'profile') {
                $sParentReturn = Phpfox::getLib('url')->makeUrl('photo', ['view' => $sView]);
            } elseif ($sView == 'profile' && $iUserId) {
                $sParentReturn = Phpfox::getService('user')->getLink($iUserId) . 'photo/';
            }
        }

        if ($bPass === false && !Phpfox::getService('user.auth')->hasAccess('photo', 'photo_id', $iId,
                'photo.can_delete_own_photo', 'photo.can_delete_other_photos', $aPhoto['user_id'], false)
        ) {
            return false;
        }

        if (!empty($aPhoto['destination'])) {
            $this->deleteFiles($aPhoto['destination'], $aPhoto['user_id'], $aPhoto['server_id']);
        }

        // Delete this entry from the database
        db()->delete($this->_sTable, 'photo_id = ' . $aPhoto['photo_id']);
        db()->delete(Phpfox::getT('photo_info'), 'photo_id = ' . $aPhoto['photo_id']);
        // delete the photo tags
        db()->delete(Phpfox::getT('photo_tag'), 'photo_id = ' . $aPhoto['photo_id']);
        // delete the category_data
        db()->delete(Phpfox::getT('photo_category_data'), 'photo_id = ' . $aPhoto['photo_id']);

        (($sPlugin = Phpfox_Plugin::get('photo.service_process_delete__1')) ? eval($sPlugin) : false);

        //close all sponsorships
        (Phpfox::isModule('ad') ? Phpfox::getService('ad.process')->closeSponsorItem('photo', (int)$iId) : null);

        (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->delete('photo', $iId) : null);

        (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->delete('user_photo', $iId) : null);
        (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->delete('user_cover', $iId) : null);
        (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->delete('comment_photo', $iId) : null);

        (Phpfox::isModule('comment') ? Phpfox::getService('comment.process')->deleteForItem($aPhoto['user_id'], $aPhoto['photo_id'],
            'photo') : null);

        (Phpfox::isModule('tag') ? Phpfox::getService('tag.process')->deleteForItem($aPhoto['user_id'], $iId,
            'photo') : null);

        (Phpfox::isModule('like') ? Phpfox::getService('like.process')->delete('photo', $iId, 0, true) : null);
        (Phpfox::isModule('notification') ? Phpfox::getService('notification.process')->deleteAllOfItem([
            'photo_like',
            'photo_approved',
            'photo_feed_profile',
            'photo_tag',
            'photo_feed_tag'
        ], (int)$iId) : null);

        Phpfox::getService('user.activity')->update($aPhoto['user_id'], 'photo', '-');

        if ($aPhoto['album_id'] > 0) {
            Phpfox::getService('photo.album.process')->updateCounter($aPhoto['album_id'], 'total_photo', true);
        }

        //if deleting photo is cover, set other photo to cover
        if (isset($aPhoto['is_cover']) && $aPhoto['is_cover'] && isset($aPhoto['album_id']) && $aPhoto['album_id']) {
            //Select random photo from this album
            $iNewCoverPhotoId = db()->select('photo_id')
                ->from(':photo')
                ->where('album_id = ' . (int)$aPhoto['album_id'].' AND view_id = 0')
                ->order('time_stamp DESC')
                ->execute('getSlaveField');
            if ($iNewCoverPhotoId) {
                db()->update(':photo', ['is_cover' => 1], 'photo_id=' . (int)$iNewCoverPhotoId);
            }
        }


        //delete user profile photo
        $iAvatarId = ((Phpfox::isUser()) ? storage()->get('user/avatar/' . Phpfox::getUserId()) : null);
        if ($iAvatarId) {
            $iAvatarId = $iAvatarId->value;
        }
        if ($iAvatarId && $iAvatarId == $iId) {
            Phpfox::getService('user.process')->removeProfilePic(Phpfox::getUserId());
            storage()->del('user/avatar/' . Phpfox::getUserId());
        }

        //delete user cover photo
        $iCoverId = ((Phpfox::isUser()) ? storage()->get('user/cover/' . Phpfox::getUserId()) : null);
        if ($iCoverId) {
            $iCoverId = $iCoverId->value;
        }
        if ($iCoverId && $iCoverId == $iId) {
            Phpfox::getService('user.process')->removeLogo(Phpfox::getUserId());
            storage()->del('user/cover/' . Phpfox::getUserId());
        }

        if ($aPhoto['module_id'] && $aPhoto['group_id'] && Phpfox::hasCallback($aPhoto['module_id'], 'onDeletePhoto')) {
            Phpfox::callback($aPhoto['module_id'] . '.onDeletePhoto', $aPhoto);
        }

        return $sParentReturn;
    }

    /**
     * Update the photo counters.
     *
     * @param int $iId ID# of the photo
     * @param string $sCounter Field we plan to update
     * @param boolean $bMinus True increases to the count and false decreases the count
     */
    public function updateCounter($iId, $sCounter, $bMinus = false)
    {
        db()->update($this->_sTable, array(
            $sCounter => array('= ' . $sCounter . ' ' . ($bMinus ? '-' : '+'), 1)
        ), 'photo_id = ' . (int)$iId
        );
    }

    public function approve($iId, $iTimeStamp = 0)
    {
        $aPhoto = db()->select('p.*, pi.description, ' . Phpfox::getUserField())
            ->from($this->_sTable, 'p')
            ->join(Phpfox::getT('photo_info'), 'pi', 'pi.photo_id = p.photo_id')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')
            ->where('p.photo_id = ' . (int)$iId)
            ->execute('getSlaveRow');

        if (!isset($aPhoto['photo_id'])) {
            return false;
        }
        if ($aPhoto['view_id'] == '0') {
            return true;
        }

        $aCallback = (!empty($aPhoto['module_id']) ? Phpfox::callback($aPhoto['module_id'] . '.addPhoto',
            $aPhoto['photo_id']) : null);

        db()->update($this->_sTable, array('view_id' => 0, 'time_stamp' => PHPFOX_TIME),
            'photo_id = ' . $aPhoto['photo_id']);

        Phpfox::getService('user.activity')->update($aPhoto['user_id'], 'photo');

        if ($aPhoto['album_id'] > 0) {
            Phpfox::getService('photo.album.process')->updateCounter($aPhoto['album_id'], 'total_photo');
            // Check if we already have an album cover
            if (!Phpfox::getService('photo.album.process')->hasCover($aPhoto['album_id'])) {
                // Set the album cover
                Phpfox::getService('photo.album.process')->setCover($aPhoto['album_id'], $iId);
            }
        }

        if (Phpfox::isModule('notification')) {
            Phpfox::getService('notification.process')->add('photo_approved', $aPhoto['photo_id'], $aPhoto['user_id']);
            $this->notifyTaggedInFeed($aPhoto['description'], $aPhoto['photo_id'], $aPhoto['user_id']);
        }
        if ($iTimeStamp && !empty($_SESSION['approve_photo_feed_' . $aPhoto['user_id'] . '_' . $aPhoto['album_id'] . '_' . $iTimeStamp])) {
            $iFeedId = $_SESSION['approve_photo_feed_' . $aPhoto['user_id'] . '_' . $aPhoto['album_id'] . '_' . $iTimeStamp];
            $aCallback = ($aPhoto['module_id'] ? Phpfox::callback($aPhoto['module_id'] . '.addPhoto',
                $aPhoto['group_id']) : null);
            db()->insert(Phpfox::getT('photo_feed'), array(
                    'feed_id' => $iFeedId,
                    'photo_id' => $aPhoto['photo_id'],
                    'feed_table' => (empty($aCallback['table_prefix']) ? 'feed' : $aCallback['table_prefix'] . 'feed')
                )
            );
        } else {
            (Phpfox::isModule('feed') ? $iFeedId = Phpfox::getService('feed.process')->callback($aCallback)->add('photo',
                $aPhoto['photo_id'], $aPhoto['privacy'], $aPhoto['privacy_comment'],
                (!empty($aPhoto['group_id']) ? (int)$aPhoto['group_id'] : 0), $aPhoto['user_id']) : null);
            $_SESSION['approve_photo_feed_' . $aPhoto['user_id'] . '_' . $aPhoto['album_id'] . '_' . $iTimeStamp] = $iFeedId;
        }
        $sLink = Phpfox::permalink('photo', $aPhoto['photo_id'], $aPhoto['title']);

        (($sPlugin = Phpfox_Plugin::get('photo.service_process_approve__1')) ? eval($sPlugin) : false);

        Phpfox::getLib('mail')->to($aPhoto['user_id'])
            ->subject(array('photo.your_photo_title_has_been_approved', array('title' => $aPhoto['title'])))
            ->message(_p('your_photo_has_been_approved_message', array('sLink' => $sLink, 'title' => $aPhoto['title'])))
            ->send();

        return true;
    }

    public function feature($iId, $sType)
    {
        return db()->update($this->_sTable, array('is_featured' => ($sType == '1' ? 1 : 0)), 'view_id = 0 AND photo_id = ' . (int)$iId);
    }

    public function sponsor($iId, $sType)
    {
        if (!Phpfox::getUserParam('photo.can_sponsor_photo') && !Phpfox::getUserParam('photo.can_purchase_sponsor') && !defined('PHPFOX_API_CALLBACK')) {
            return Phpfox_Error::set(_p('hack_attempt'));
        }

        $iType = (int)$sType;
        if ($iType != 0 && $iType != 1) {
            return false;
        }
        db()->update($this->_sTable, array('is_sponsor' => $iType), 'photo_id = ' . (int)$iId);
        if ($sPlugin = Phpfox_Plugin::get('photo.service_process_sponsor__end')) {
            eval($sPlugin);
        }
        return true;
    }

    public function rotate($iId, $sCmd)
    {
        $aPhoto = db()->select('user_id, title, photo_id, destination, server_id')
            ->from($this->_sTable)
            ->where('photo_id = ' . (int)$iId)
            ->execute('getSlaveRow');

        if (!isset($aPhoto['photo_id'])) {
            return Phpfox_Error::set(_p('unable_to_find_the_photo_you_plan_to_edit'));
        }

        if (($aPhoto['user_id'] == Phpfox::getUserId() && Phpfox::getUserParam('photo.can_edit_own_photo')) || Phpfox::getUserParam('photo.can_edit_other_photo')) {
            $aSizes = Phpfox::getService('photo')->getPhotoPicSizes();
            $aSizes[] = '';
            $aParts = explode('/', $aPhoto['destination']);
            $sParts = '';
            if (is_array($aParts)) {
                foreach ($aParts as $sPart) {
                    if (!empty($sPart)) {
                        if (!preg_match('/jpg|gif|png|jpeg/i', $sPart)) {
                            $sParts .= $sPart . '/';
                        }
                    }
                }
            }

            foreach ($aSizes as $iSize) {
                $sFile = Phpfox::getParam('photo.dir_photo') . sprintf($aPhoto['destination'],
                        (empty($iSize) ? '' : '_') . $iSize);
                if (file_exists($sFile) || Phpfox::getParam('core.allow_cdn')) {
                    $sActualFile = Phpfox::getLib('image.helper')->display(array(
                            'server_id' => $aPhoto['server_id'],
                            'path' => 'photo.url_photo',
                            'file' => $aPhoto['destination'],
                            'suffix' => (empty($iSize) ? '' : '_') . $iSize,
                            'return_url' => true
                        )
                    );

                    $aExts = preg_split("/[\/\\.]/", $sActualFile);
                    $iCnt = count($aExts) - 1;
                    $sExt = strtolower($aExts[$iCnt]);

                    $sFile = Phpfox::getParam('photo.dir_photo') . $sParts . md5($aPhoto['destination']) . (empty($iSize) ? '' : '_') . $iSize . '.' . $sExt;

                    // fix issue allow_url_fopen = Off
                    file_put_contents($sFile, fox_get_contents($sActualFile));
                    Phpfox::getLib('image')->rotate($sFile, $sCmd, null, $aPhoto['server_id']);
                } else {
                    $sExt = '';
                }


                db()->update(Phpfox::getT('photo'),
                    array('destination' => $sParts . md5($aPhoto['destination']) . '%s.' . $sExt),
                    'photo_id = ' . (int)$aPhoto['photo_id']);
            }

            return $aPhoto;
        }

        return false;
    }

    public function massProcess($aAlbum, $aVals)
    {
        foreach ($aVals as $iPhotoId => $aVal) {
            if (isset($aVals['set_album_cover']) && is_numeric($iPhotoId)) {
                if ($aVals['set_album_cover'] == $iPhotoId && empty($aVal['move_to'])) {
                    db()->update(Phpfox::getT('photo'), array('is_cover' => '1'),
                        "album_id = $aAlbum[album_id] AND photo_id = $iPhotoId");
                } else {
                    db()->update(Phpfox::getT('photo'), array('is_cover' => '0'),
                        "album_id = $aAlbum[album_id] AND photo_id = $iPhotoId");
                }
            }
            if (!is_numeric($iPhotoId)) {
                continue;
            }

            if (isset($aVal['delete_photo'])) {
                if (!$this->delete($iPhotoId)) {
                    return false;
                }

                continue;
            }

            $this->update($aAlbum['user_id'], $iPhotoId, $aVal);
        }

        // if no photo in album is set cover, select first
        $aPhotos = db()->select('*')->from($this->_sTable)->where(['album_id' => $aAlbum['album_id']])->executeRows();
        if (count($aPhotos)) {
            $bNoCover = true;
            foreach ($aPhotos as $aPhoto) {
                if ($aPhoto['is_cover']) {
                    $bNoCover = false;
                    break;
                }
            }
            if ($bNoCover) {
                $iPhotoId = db()->select('photo_id')->from($this->_sTable)->where(['album_id' => $aAlbum['album_id']])->executeField();
                db()->update($this->_sTable, ['is_cover' => '1'], "photo_id=$iPhotoId");
            }
        }
        return true;
    }

    /**
     * @param $sContent
     * @param $iItemId
     * @param $iOwnerId
     * @return array|void
     */
    public function notifyTaggedInFeed($sContent, $iItemId, $iOwnerId)
    {
        $iCount = preg_match_all('/\[user=(\d+)\].+?\[\/user\]/i', $sContent, $aResult);
        if ($iCount < 1) {
            return array();
        }
        $aMatches = array();
        if (Phpfox::isModule('friend')) {
            /* Filter out non friends */
            $oFriend = Phpfox::getService('friend');
            foreach ($aResult[1] as $iKey => $iUserId) {
                if ($oFriend->isFriend($iOwnerId, $iUserId)) {
                    $aMatches[] = $iUserId;
                }
            }
        }
        $aChecked = array();
        foreach ($aMatches as $iKey => $iUserId) {
            if (in_array($iUserId, $aChecked)) {
                continue;
            }
            $aChecked[] = $iUserId;
        }
        $aMatches = $aChecked;

        if (empty($aMatches)) {
            return;
        }
        $sUsers = implode(',', $aMatches);
        $aPerms = $this->database()->select('user_id, user_value')->from(Phpfox::getT('user_privacy'))->where('user_id in (' . $sUsers . ' ) AND user_privacy = \'user.can_i_be_tagged\'')->execute('getSlaveRows');
        foreach ($aPerms as $aRow) {
            foreach ($aMatches as $iIndex => $iUserId) {
                if ($iUserId == $aRow['user_id'] && $aRow['user_value'] == 4) {
                    unset($aMatches[$iIndex]);
                }
            }
        }
        if (Phpfox::isModule('notification')) {
            $sName = 'photo_feed_tag';

            foreach ($aMatches as $iIndex => $iUserId) {

                Phpfox::getService('notification.process')->add($sName, $iItemId, $iUserId, $iOwnerId, true);
            }
        }
    }

    /**
     * @param string $sName
     * @param null $iUserId
     * @param int $iServerId
     * @return bool
     */
    public function deleteFiles($sName = '', $iUserId = null, $iServerId = 0) {
        if (empty($sName)) {
            return false;
        }

        if (!$iUserId) {
            $iUserId = Phpfox::getUserId();
        }

        $aParams = Phpfox::getService('photo')->getUploadParams();
        $aParams['type'] = 'photo';
        $aParams['path'] = $sName;
        $aParams['user_id'] = $iUserId;
        $aParams['update_space'] = ($iUserId ? true : false);
        $aParams['server_id'] = $iServerId;
        $aParams['thumbnail_sizes'] = Phpfox::getService('photo')->getPhotoPicSizes();

        return Phpfox::getService('user.file')->remove($aParams);
    }

    /**
     * Remove temporary photos after expired time
     * @param int $iExpiredTime
     */
    public function removeTemporaryPhotos($iExpiredTime = 86400)
    {
        $aAllPhotos = db()->select('photo_id, time_stamp')->from(':photo')->where(['is_temp' => 1])->executeRows();

        foreach ($aAllPhotos as $aPhoto) {
            if (time() - $aPhoto['time_stamp'] < $iExpiredTime) {
                continue;
            }
            // delete temporary photo older than one day
            Phpfox::getService('photo.process')->delete($aPhoto['photo_id']);
        }
    }

    /**
     * If a call is made to an unknown method attempt to connect
     * it to a specific plug-in with the same name thus allowing
     * plug-in developers the ability to extend classes.
     *
     * @param string $sMethod is the name of the method
     * @param array $aArguments is the array of arguments of being passed
     * @return mixed
     */
    public function __call($sMethod, $aArguments)
    {
        /**
         * Check if such a plug-in exists and if it does call it.
         */
        if ($sPlugin = Phpfox_Plugin::get('photo.service_process__call')) {
            eval($sPlugin);
            return null;
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }
}