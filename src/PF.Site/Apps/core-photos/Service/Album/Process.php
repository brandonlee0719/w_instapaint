<?php

namespace Apps\Core_Photos\Service\Album;

use Phpfox;
use Phpfox_Error;
use Phpfox_Plugin;
use Phpfox_Service;

class Process extends Phpfox_Service
{
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('photo_album');
    }

    /**
     * Add a new photo album.
     *
     * @param array $aVals $_POST data array.
     * @param boolean $bIsUpdate True for INSERT, false for UPDATE.
     *
     * @return int ID of the item we inserted/updated.
     */
    public function add($aVals, $bIsUpdate = false)
    {
        // Get the parser object.
        $oParseInput = Phpfox::getLib('parse.input');

        // Create the fields to insert
        $aFields = array(
            'name',
            'privacy' => 'int',
            'privacy_comment' => 'int'
        );

        // Create the fields to insert
        $aFieldsInfo = array(
            'description'
        );

        Phpfox::getService('ban')->checkAutomaticBan($aVals['name'] . ' ' . $aVals['description']);
        // Clean album name
        $aVals['name'] = $oParseInput->clean($aVals['name'], 255);

        // Prepare description.
        if (!empty($aVals['description'])) {
            $aVals['description'] = $oParseInput->clean($aVals['description']);
        }

        if ($bIsUpdate) {

            // Insert the data into the database.
            db()->process($aFields, $aVals)->update($this->_sTable, 'album_id = ' . $aVals['album_id']);

            // Insert album info.
            db()->process($aFieldsInfo, $aVals)->update(Phpfox::getT('photo_album_info'),
                'album_id = ' . $aVals['album_id']);

            $iId = $aVals['album_id'];

            if (!isset($aVals['privacy'])) {
                $aVals['privacy'] = 0;
            }
            $aVals['privacy_comment'] = 0;

            $this->setPrivacy($iId, $aVals['privacy'], $aVals['privacy_comment']);

            (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->update('photo_album', $iId,
                $aVals['privacy'], $aVals['privacy_comment']) : null);

            if (Phpfox::isModule('privacy')) {
                if (isset($aVals['privacy']) && $aVals['privacy'] == '4') {
                    Phpfox::getService('privacy.process')->update('photo_album', $iId,
                        (isset($aVals['privacy_list']) ? $aVals['privacy_list'] : array()));
                } else {
                    Phpfox::getService('privacy.process')->delete('photo_album', $iId);
                }
            }
            // Add tags for the album
            if (Phpfox::isModule('tag')) {
                if (Phpfox::getParam('tag.enable_hashtag_support') && !empty($aVals['description'])) {
                    Phpfox::getService('tag.process')->update('photo_album', $aVals['album_id'], Phpfox::getUserId(),
                        $aVals['description'], true);
                } else {
                    if (isset($aVals['tag_list'])) {
                        if (!empty($aVals['tag_list'])) {
                            Phpfox::getService('tag.process')->update('photo_album', $aVals['album_id'],
                                Phpfox::getUserId(), $aVals['tag_list']);
                        } else {
                            Phpfox::getService('tag.process')->deleteForItem($aVals['user_id'], Phpfox::getUserId(),
                                'photo_album');
                        }
                    }
                }
            }
        } else {
            $aFields[] = 'module_id';
            $aFields['group_id'] = 'int';
            $aFields['user_id'] = 'int';
            $aFields[] = 'time_stamp';

            if (!empty($aVals['callback_module'])) {
                $aVals['module_id'] = $aVals['callback_module'];
            }

            // Add the users ID to the fields array
            $aVals['user_id'] = Phpfox::getUserId();

            // Add a time_stamp
            $aVals['time_stamp'] = PHPFOX_TIME;

            // Insert the data into the database.
            $iId = db()->process($aFields, $aVals)->insert($this->_sTable);

            $aFieldsInfo['album_id'] = 'int';

            $aVals['album_id'] = $iId;

            // Insert album info.
            db()->process($aFieldsInfo, $aVals)->insert(Phpfox::getT('photo_album_info'));

            //Add feed for album
            $aCallback = (!empty($aVals['module_id']) ? Phpfox::callback($aVals['module_id'] . '.addPhoto',
                $aVals['album_id']) : null);
            (Phpfox::isModule('feed') ? $iFeedId = Phpfox::getService('feed.process')->callback($aCallback)->add('photo_album',
                $iId, (isset($aVals['privacy']) ? $aVals['privacy'] : 0),
                (isset($aVals['privacy_comment']) ? $aVals['privacy_comment'] : 0),
                (!empty($aVals['group_id']) ? (int)$aVals['group_id'] : 0), $aVals['user_id']) : null);

            if (Phpfox::isModule('privacy')) {
                if (isset($aVals['privacy']) && $aVals['privacy'] == '4') {
                    Phpfox::getService('privacy.process')->add('photo_album', $iId,
                        (isset($aVals['privacy_list']) ? $aVals['privacy_list'] : array()));
                }
            }
            if (Phpfox::isModule('tag') && Phpfox::getParam('tag.enable_hashtag_support') && !empty($aVals['description'])) {
                Phpfox::getService('tag.process')->add('photo_album', $iId, $aVals['user_id'], $aVals['description'],
                    true);
            }

        }

        return $iId;
    }

    /**
     * @param $iAlbumId
     * @param string $sView , deprecated from 4.6.0
     * @param int $iUserId , deprecated from 4.6.0
     * @param bool $bPass
     * @return bool|string
     * @throws \Exception
     */
    public function delete($iAlbumId, $sView = '', $iUserId = 0, $bPass = false)
    {
        $aAlbum = db()->select('album_id, user_id, module_id, group_id')
            ->from($this->_sTable)
            ->where('album_id = ' . (int)$iAlbumId)
            ->execute('getSlaveRow');

        if (!isset($aAlbum['album_id'])) {
            return $bPass ? false : Phpfox_Error::set(_p('not_a_valid_photo_album_to_delete'));
        }
        // check current page to redirect when delete success
        $sParentReturn = true;
        if ($aAlbum['module_id'] == 'pages' && Phpfox::getService('pages')->isAdmin($aAlbum['group_id'])) {
            $sParentReturn = Phpfox::getService('pages')->getUrl($aAlbum['group_id']) . 'photo/albums/';
            $bPass = true; // is owner of page
        } elseif ($aAlbum['module_id'] == 'groups' && Phpfox::getService('groups')->isAdmin($aAlbum['group_id'])) {
            $sParentReturn = Phpfox::getService('groups')->getUrl($aAlbum['group_id']) . 'photo/albums/';
            $bPass = true; // is owner of group
        }
        
        if (!$bPass && !Phpfox::getService('user.auth')->hasAccess('photo_album', 'album_id', $iAlbumId,
                'photo.can_delete_own_photo_album', 'photo.can_delete_other_photo_albums', $aAlbum['user_id'])
        ) {
            return Phpfox_Error::set(_p('you_do_not_have_sufficient_permission_to_delete_this_photo_album'));
        }
        $aPhotos = db()->select('photo_id')
            ->from(Phpfox::getT('photo'))
            ->where('album_id = ' . $aAlbum['album_id'])
            ->execute('getSlaveRows');

        foreach ($aPhotos as $aPhoto) {
            Phpfox::getService('photo.process')->delete($aPhoto['photo_id'], true);
        }

        (($sPlugin = Phpfox_Plugin::get('photo.service_album_process_delete__1')) ? eval($sPlugin) : false);

        db()->delete($this->_sTable, 'album_id = ' . $aAlbum['album_id']);
        db()->delete(Phpfox::getT('photo_album_info'), 'album_id = ' . $aAlbum['album_id']);
        (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->delete('photo_album',
            $aAlbum['album_id']) : null);
        (Phpfox::isModule('comment') ? Phpfox::getService('comment.process')->deleteForItem($aAlbum['user_id'], $aAlbum['album_id'],
            'photo_album') : null);

        (Phpfox::isModule('notification') ? Phpfox::getService('notification.process')->deleteAllOfItem([
            'photo_album_like',
            'comment_photo_album'
        ], (int)$aAlbum['album_id']) : null);

        return $sParentReturn;
    }

    /**
     * @param $iAlbumId
     * @param $aVals
     * @return int
     */
    public function update($iAlbumId, $aVals)
    {
        $aVals['album_id'] = $iAlbumId;

        return $this->add($aVals, true);
    }

    public function setPrivacy($iAlbumId, $iPrivacy = null, $iPrivacyComment = null)
    {
        if ($iPrivacy === null) {
            $aAlbum = db()->select('privacy, privacy_comment')
                ->from($this->_sTable)
                ->where('album_id = ' . (int)$iAlbumId)
                ->execute('getSlaveRow');

            $iPrivacy = $aAlbum['privacy'];
            $iPrivacyComment = $aAlbum['privacy_comment'];
        }

        db()->update(Phpfox::getT('photo'),
            array('privacy' => (int)$iPrivacy, 'privacy_comment' => (int)$iPrivacyComment),
            'album_id = ' . (int)$iAlbumId);

        if ($iPrivacy == '4') {
            $aList = array();
            $aPrivacyLists = db()->select('*')
                ->from(Phpfox::getT('privacy'))
                ->where('module_id = \'photo_album\' AND item_id = ' . (int)$iAlbumId)
                ->execute('getSlaveRows');

            foreach ($aPrivacyLists as $aPrivacyList) {
                $aList[] = $aPrivacyList['friend_list_id'];
            }
        }

        $aPhotos = db()->select('photo_id')
            ->from(Phpfox::getT('photo'))
            ->where('album_id = ' . (int)$iAlbumId)
            ->execute('getSlaveRows');
        foreach ($aPhotos as $aPhoto) {
            if (Phpfox::isModule('feed')) {
                Phpfox::getService('feed.process')->update('photo', $aPhoto['photo_id'], $iPrivacy, $iPrivacyComment);
            }
            if (Phpfox::isModule('privacy')) {
                if ($iPrivacy == '4') {
                    Phpfox::getService('privacy.process')->update('photo', $aPhoto['photo_id'], $aList);
                } else {
                    Phpfox::getService('privacy.process')->delete('photo', $aPhoto['photo_id']);
                }
            }
        }

        return true;
    }

    public function hasCover($iAlbumId)
    {
        return db()->select('COUNT(*)')
            ->from(Phpfox::getT('photo'))
            ->where('album_id = ' . (int)$iAlbumId . ' AND is_cover = 1')
            ->execute('getSlaveField');
    }

    public function setCover($iAlbumId, $iPhotoId)
    {
        db()->update(Phpfox::getT('photo'), array('is_cover' => 0), 'album_id = ' . (int)$iAlbumId);
        db()->update(Phpfox::getT('photo'), array('is_cover' => 1), 'photo_id = ' . (int)$iPhotoId);

        return true;
    }

    public function autoCover($iAlbumId)
    {
        $aPhoto = db()->select('destination, photo_id, server_id, mature')
                        ->from(':photo')
                        ->where('is_cover = 0 AND album_id = '.(int)$iAlbumId)
                        ->order('time_stamp DESC')
                        ->execute('getRow');
        if ($aPhoto) {
            $this->setCover($iAlbumId, $aPhoto['photo_id']);
        }
        return $aPhoto;
    }
    /**
     * Update the album counters.
     *
     * @param int $iId ID# of the album
     * @param string $sCounter Field we plan to update
     * @param boolean $bMinus True increases to the count and false decreases the count | remove in v4.6
     * @param mixed $sValue Pass a null to use 1 or pass an int value to define how many should we plus/minus | remove in v4.6
     */
    public function updateCounter($iId, $sCounter, $bMinus = false, $sValue = null)
    {
        $iTotal = db()->select('COUNT(*)')
            ->from(Phpfox::getT('photo'))
            ->where('album_id = ' . (int)$iId . ' AND view_id = 0')
            ->execute('getSlaveField');

        db()->update($this->_sTable, array($sCounter => $iTotal), 'album_id = ' . (int)$iId);
    }

    public function updateTitle($iAlbumId, $sTitle)
    {
        Phpfox::getService('ban')->checkAutomaticBan($sTitle);
        db()->update($this->_sTable, array('name' => Phpfox::getLib('parse.input')->clean($sTitle, 255)),
            'album_id = ' . (int)$iAlbumId);

        return true;
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
        if ($sPlugin = Phpfox_Plugin::get('photo.service_album_process__call')) {
            eval($sPlugin);
            return null;
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }
}