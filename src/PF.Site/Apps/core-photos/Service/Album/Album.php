<?php

namespace Apps\Core_Photos\Service\Album;

use Phpfox;
use Phpfox_Error;
use Phpfox_Plugin;
use Phpfox_Service;

class Album extends Phpfox_Service
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('photo_album');
    }

    /**
     * Get all albums based on filters we passed via the params.
     *
     * @param array $mConditions SQL Conditions
     * @param string $sOrder SQL Ordering
     * @param mixed $iPage Current page we are on
     * @param mixed $iPageSize Define how many photos we can display at one time
     *
     * @return array Return an array of the total album count and the albums
     */
    public function get($mConditions = array(), $sOrder = 'pa.time_stamp DESC', $iPage = '', $iPageSize = '')
    {
        $aAlbums = array();

        (($sPlugin = Phpfox_Plugin::get('photo.service_album_album_get_count')) ? eval($sPlugin) : false);

        $iCnt = db()->select('COUNT(DISTINCT pa.name)')
            ->from($this->_sTable, 'pa')
            ->leftJoin(Phpfox::getT('photo'), 'p', 'p.album_id = pa.album_id')
            ->where($mConditions)
            ->execute('getSlaveField');

        if ($iCnt) {
            (($sPlugin = Phpfox_Plugin::get('photo.service_album_album_get_query')) ? eval($sPlugin) : false);

            $aAlbums = db()->select('pa.*, p.destination, p.server_id, p.mature, ' . Phpfox::getUserField())
                ->from($this->_sTable, 'pa')
                ->leftJoin(Phpfox::getT('photo'), 'p', 'p.album_id = pa.album_id AND pa.view_id = 0 AND p.is_cover = 1')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = pa.user_id')
                ->where($mConditions)
                ->order($sOrder)
                ->limit($iPage, $iPageSize, $iCnt)
                ->execute('getSlaveRows');
        }

        return array($iCnt, $aAlbums);
    }

    /**
     * Get all the albums for a specific user.
     *
     * @param int $iUserId User ID.
     * @param boolean $sModule
     * @param boolean $iItem
     *
     * @return array Return the array of albums.
     */
    public function getAll($iUserId, $sModule = false, $iItem = false)
    {
        (($sPlugin = Phpfox_Plugin::get('photo.service_album_album_getall')) ? eval($sPlugin) : false);

        return db()->select('album_id, name, profile_id, cover_id, timeline_id')
            ->from($this->_sTable)
            ->where(($sModule === false ? 'module_id IS NULL AND group_id = 0 AND ' : 'module_id = \'' . $this->database()->escape($sModule) . '\' AND group_id = ' . (int)$iItem . ' AND ') . 'user_id = ' . (int)$iUserId)
            ->execute('getSlaveRows');
    }

    /**
     * Get the total count of albums for a specific user.
     *
     * @param int $iUserId User ID.
     *
     * @return int Return the total number of albums.
     */
    public function getAlbumCount($iUserId)
    {
        (($sPlugin = Phpfox_Plugin::get('photo.service_album_album_getalbumcount')) ? eval($sPlugin) : false);
        $bIsDisplayProfile = Phpfox::getParam('photo.display_profile_photo_within_gallery');
        $bIsDisplayCover = Phpfox::getParam('photo.display_cover_photo_within_gallery');
        $bIsDisplayTimeline = Phpfox::getParam('photo.display_timeline_photo_within_gallery');
        $aReturn = db()->select('COUNT(*)')
            ->from($this->_sTable)
            ->where('
			    view_id = 0
			    AND group_id = 0' .
                ($bIsDisplayProfile ? '' : ' AND profile_id=0') .
                ($bIsDisplayCover ? '' : ' AND cover_id=0') .
                ($bIsDisplayTimeline ? '' : ' AND timeline_id=0') .
                ' AND user_id = ' . (int)$iUserId
            )
            ->execute('getSlaveField');
        return $aReturn;
    }

    /**
     * Get a specific album based on the user ID and album ID or album title.
     *
     * @param int $iUserId User ID this album belongs to
     * @param mixed $mId Album ID or Album title
     * @param boolean $bUseId True to use an album ID call or else it is an album title
     *
     * @return array Array of the album
     */
    public function getAlbum($iUserId, $mId, $bUseId = false)
    {
        (($sPlugin = Phpfox_Plugin::get('photo.service_album_album_getalbum')) ? eval($sPlugin) : false);

        return db()->select('pa.*, pai.*')
            ->from($this->_sTable, 'pa')
            ->join(Phpfox::getT('photo_album_info'), 'pai', 'pai.album_id = pa.album_id')
            ->where('pa.user_id = ' . (int)$iUserId . ' AND ' . ($bUseId === true ? 'pa.album_id = ' . (int)$mId : '1'))
            ->execute('getSlaveRow');
    }

    /**
     * @param $iId
     * @param bool $bIsProfile
     * @return array|bool|int|string
     */
    public function getForView($iId, $bIsProfile = false)
    {
        if (Phpfox::isModule('friend')) {
            db()->select('f.friend_id AS is_friend, ')->leftJoin(Phpfox::getT('friend'), 'f',
                "f.user_id = pa.user_id AND f.friend_user_id = " . Phpfox::getUserId());
        }

        if (Phpfox::isModule('like')) {
            db()->select('l.like_id AS is_liked, ')
                ->leftJoin(Phpfox::getT('like'), 'l',
                    'l.type_id = \'photo_album\' AND l.item_id = pa.album_id AND l.user_id = ' . Phpfox::getUserId());
        }

        $aAlbum = db()->select('pa.*, pai.*, ' . Phpfox::getUserField())
            ->from($this->_sTable, 'pa')
            ->join(Phpfox::getT('photo_album_info'), 'pai', 'pai.album_id = pa.album_id')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = pa.user_id')
            ->where(($bIsProfile ? 'pa.profile_id = ' . (int)$iId : 'pa.album_id = ' . (int)$iId))
            ->execute('getSlaveRow');
        if (!isset($aAlbum['album_id'])) {
            return false;
        }

        if (!isset($aAlbum['is_friend'])) {
            $aAlbum['is_friend'] = $aAlbum['privacy'] == 0;
        }
        if (!isset($aAlbum['is_liked'])) {
            $aAlbum['is_liked'] = false;
        }
        return $aAlbum;
    }

    /**
     * @param int $iId
     *
     * @return bool|array
     */
    public function getCoverForView($iId)
    {

        if (Phpfox::isModule('friend')) {
            db()->select('f.friend_id AS is_friend, ')->leftJoin(Phpfox::getT('friend'), 'f',
                "f.user_id = pa.user_id AND f.friend_user_id = " . Phpfox::getUserId());
        }

        if (Phpfox::isModule('like')) {
            db()->select('l.like_id AS is_liked, ')
                ->leftJoin(Phpfox::getT('like'), 'l',
                    'l.type_id = \'photo_album\' AND l.item_id = pa.album_id AND l.user_id = ' . Phpfox::getUserId());
        }

        $aAlbum = db()->select('pa.*, pai.*, ' . Phpfox::getUserField())
            ->from($this->_sTable, 'pa')
            ->join(Phpfox::getT('photo_album_info'), 'pai', 'pai.album_id = pa.album_id')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = pa.user_id')
            ->where('pa.cover_id = ' . (int)$iId)
            ->execute('getSlaveRow');

        if (!isset($aAlbum['album_id'])) {
            return false;
        }

        if (!isset($aAlbum['is_friend'])) {
            $aAlbum['is_friend'] = $aAlbum['privacy'] == 0;
        }
        if (!isset($aAlbum['is_liked'])) {
            $aAlbum['is_liked'] = false;
        }
        return $aAlbum;
    }

    /**
     * @param $iProfileId
     * @param bool $bForceCreation
     * @return array|bool|int|string
     */
    public function getForProfileView($iProfileId, $bForceCreation = false)
    {
        $aAlbum = $this->getForView($iProfileId, true);
        if (!isset($aAlbum['album_id']) || $bForceCreation === true) {
            $aUser = db()->select(Phpfox::getUserField())
                ->from(Phpfox::getT('user'), 'u')
                ->where('u.user_id = ' . (int)$iProfileId)
                ->execute('getSlaveRow');
            $sModuleId = null;
            if ($aUser['profile_page_id']) {
                $iItemType = db()->select('item_type')->from(':pages')->where(['page_id' => $aUser['profile_page_id']])->executeField();
                $sModuleId = ($iItemType == 0) ? 'pages' : 'groups';
            }
            if (!isset($aUser['user_id'])) {
                return false;
            }
            if (!isset($aAlbum['album_id'])) {
                $aInsert = array(
                    'privacy'         => '0',
                    'privacy_comment' => '0',
                    'user_id'         => $aUser['user_id'],
                    'name'            => "{_p var='profile_pictures'}",
                    'time_stamp'      => PHPFOX_TIME,
                    'profile_id'      => $aUser['user_id'],
                    'total_photo'     => 1
                );
                if ($aUser['profile_page_id']) {
                    $aInsert['module_id'] = $sModuleId;
                    $aInsert['group_id'] = $aUser['profile_page_id'];
                }
                $iId = $this->database()->insert(Phpfox::getT('photo_album'), $aInsert);
                db()->insert(Phpfox::getT('photo_album_info'), array('album_id' => $iId));
            } else {
                $iId = $aAlbum['album_id'];
            }

            if (!empty($aUser['user_image']) && file_exists(Phpfox::getParam('core.dir_user') . sprintf($aUser['user_image'],
                        ''))
            ) {
                $aImage = getimagesize(Phpfox::getParam('core.dir_user') . sprintf($aUser['user_image'], ''));
                $iFileSize = filesize(Phpfox::getParam('core.dir_user') . sprintf($aUser['user_image'], ''));
                // update cover of album to 0, new image is 1
                $this->database()->update(Phpfox::getT('photo'), array('is_cover' => '0'), 'album_id = ' . (int) $iId);

                $aInsert = array(
                    'album_id' => $iId,
                    'title' => date('F j, Y'),
                    'user_id' => $aUser['user_id'],
                    'server_id' => $aUser['user_server_id'],
                    'time_stamp' => PHPFOX_TIME,
                    'is_cover' => '1',
                    'is_profile_photo' => '1'
                );
                if (defined('PHPFOX_FORCE_PHOTO_VERIFY_EMAIL')) {
                    $aInsert['view_id'] = 3;
                }
                if ($aUser['profile_page_id']) {
                    $aInsert['module_id'] = $sModuleId;
                    $aInsert['group_id'] = $aUser['profile_page_id'];
                }
                $iPhotoInsert = db()->insert(Phpfox::getT('photo'), $aInsert);

                $aExts = preg_split("/[\/\\.]/", sprintf($aUser['user_image'], ''));
                $iCnt = count($aExts) - 1;
                $sExt = strtolower($aExts[$iCnt]);

                db()->insert(Phpfox::getT('photo_info'), array(
                        'photo_id' => $iPhotoInsert,
                        'file_name' => sprintf($aUser['user_image'], ''),
                        'mime_type' => $aImage['mime'],
                        'extension' => $sExt,
                        'width' => $aImage[0],
                        'height' => $aImage[1],
                        'file_size' => $iFileSize
                    )
                );

                $sFileName = md5($iPhotoInsert) . '%s.' . $sExt;

                db()->update(Phpfox::getT('photo'), array('destination' => $sFileName),
                    'photo_id = ' . (int)$iPhotoInsert);

                storage()->del('user/avatar/' . $iProfileId);
                storage()->set('user/avatar/' . $iProfileId, $iPhotoInsert);

                copy(Phpfox::getParam('core.dir_user') . sprintf($aUser['user_image'], ''),
                    Phpfox::getParam('photo.dir_photo') . sprintf($sFileName, ''));
                //push to cdn
                if (Phpfox::getParam('core.allow_cdn')) {
                    Phpfox::getLib('cdn')->put(Phpfox::getParam('photo.dir_photo') . sprintf($sFileName, ''));
                }

                $oImage = Phpfox::getLib('image');
                foreach (Phpfox::getService('photo')->getPhotoPicSizes() as $iSize) {
                    // Create the thumbnail
                    if ($oImage->createThumbnail(Phpfox::getParam('photo.dir_photo') . sprintf($sFileName, ''),
                            Phpfox::getParam('photo.dir_photo') . sprintf($sFileName, '_' . $iSize), $iSize, $iSize,
                            true,
                            ((Phpfox::getParam('photo.enabled_watermark_on_photos') && Phpfox::getParam('core.watermark_option') != 'none') ? (Phpfox::getParam('core.watermark_option') == 'image' ? 'force_skip' : true) : false)) === false
                    ) {
                        continue;
                    }

                    if (Phpfox::getParam('photo.enabled_watermark_on_photos')) {
                        $oImage->addMark(Phpfox::getParam('photo.dir_photo') . sprintf($sFileName, '_' . $iSize));
                    }
                }

                if (Phpfox::getParam('photo.enabled_watermark_on_photos')) {
                    $oImage->addMark(Phpfox::getParam('photo.dir_photo') . sprintf($sFileName, ''));
                }

                Phpfox::getService('user.activity')->update($aUser['user_id'], 'photo');
            }

            $aAlbum = $this->getForView($iProfileId, true);
        }

        if ($bForceCreation) {
            $aAlbum['photo_id'] = $iPhotoInsert;
        }
        return $aAlbum;
    }

    /**
     * @param int $iProfileId
     *
     * @return array|bool
     */
    public function getForCoverView($iProfileId)
    {
        $aAlbum = $this->getCoverForView($iProfileId);
        return $aAlbum;
    }

    /**
     * @param $iId
     * @param bool $bForce
     * @return array|bool|int|string
     */
    public function getForEdit($iId, $bForce = false)
    {
        (($sPlugin = Phpfox_Plugin::get('photo.service_album_album_getforedit')) ? eval($sPlugin) : false);

        $aAlbum = db()->select('pa.*, pai.*, ' . Phpfox::getUserField())
            ->from($this->_sTable, 'pa')
            ->join(Phpfox::getT('photo_album_info'), 'pai', 'pai.album_id = pa.album_id')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = pa.user_id')
            ->where('pa.album_id = ' . (int)$iId)
            ->execute('getSlaveRow');

        if (!isset($aAlbum['album_id'])) {
            return false;
        }

        if ($bForce || (Phpfox::getUserId() == $aAlbum['user_id'] && Phpfox::getUserParam('photo.can_edit_own_photo_album')) || Phpfox::getUserParam('photo.can_edit_other_photo_albums')) {
            return $aAlbum;
        }

        return false;
    }

    public function inThisAlbum($iAlbumId, $iLimit = 8, $iPage = 1)
    {
        $aRows = db()->select(Phpfox::getUserField())
            ->from(Phpfox::getT('photo_tag'), 'pt')
            ->innerJoin(Phpfox::getT('photo'), 'p', 'p.album_id = ' . (int)$iAlbumId)
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = pt.tag_user_id')
            ->where('pt.photo_id = p.photo_id')
            ->group('u.user_id', true)
            ->limit($iPage, $iLimit)
            ->forCount()
            ->execute('getSlaveRows');

        $iCnt = db()->getCount();

        return array($iCnt, $aRows);
    }

    /**
     * @return int
     */
    public function getMyAlbumTotal()
    {
        $sWhere = 'user_id = ' . Phpfox::getUserId();
        $aModules = [];
        if (!Phpfox::isModule('groups')) {
            $aModules[] = 'groups';
        }
        if (!Phpfox::isModule('pages')) {
            $aModules[] = 'pages';
        }
        if (count($aModules)) {
            $sWhere .= ' AND (module_id NOT IN ("' . implode('","', $aModules) . '") OR module_id IS NULL)';
        }
        $sWhere .= ' AND (((profile_id > 0 OR cover_id > 0 OR timeline_id > 0) AND total_photo > 0) OR (profile_id = 0 AND cover_id = 0 AND timeline_id = 0))';
        return db()->select('COUNT(*)')
            ->from($this->_sTable)
            ->where($sWhere)
            ->execute('getSlaveField');
    }

    /**
     * @param $aRow
     */
    public function getPermissions(&$aRow)
    {
        $aRow['canEdit'] = (($aRow['user_id'] == Phpfox::getUserId() && Phpfox::getUserParam('photo.can_edit_own_photo_album')) || Phpfox::getUserParam('photo.can_edit_other_photo_albums'));
        $aRow['canUpload'] = ($aRow['user_id'] == Phpfox::getUserId() && $aRow['profile_id'] == '0' && $aRow['cover_id'] == '0' && $aRow['timeline_id'] == '0');
        $aRow['canDelete'] = $this->_canDelete($aRow);
        $aRow['hasPermission'] = ($aRow['canEdit'] || $aRow['canDelete'] || $aRow['canUpload']);
    }

    private function _canDelete($aRow)
    {
        $bCanDelete = ($aRow['profile_id'] == '0' && $aRow['cover_id'] == '0' && $aRow['timeline_id'] == '0' && (($aRow['user_id'] == Phpfox::getUserId() && Phpfox::getUserParam('photo.can_delete_own_photo_album')) || Phpfox::getUserParam('photo.can_delete_other_photo_albums')));
        if (!$bCanDelete && Phpfox::isModule($aRow['module_id'])) {
            if ($aRow['module_id'] == 'pages' && Phpfox::getService('pages')->isAdmin($aRow['group_id'])) {
                $bCanDelete = true; // is owner of page
            } elseif ($aRow['module_id'] == 'groups' && Phpfox::getService('groups')->isAdmin($aRow['group_id'])) {
                $bCanDelete = true; // is owner of group
            }
        }
        return $bCanDelete;
    }
    /**
     * @param $iAlbumId
     * @return array|int|string
     */
    public function getTotalPendingInAlbum($iAlbumId)
    {
        return db()->select('COUNT(*)')
                    ->from(':photo','p')
                    ->join($this->_sTable,'pa','pa.album_id = p.album_id')
                    ->where('p.view_id = 1 AND pa.album_id ='.(int)$iAlbumId)
                    ->execute('getField');
    }
    /**
     * Check if current user is admin of photo's parent item
     * @param $iAlbumId
     * @return bool|mixed
     */
    public function isAdminOfParentItem($iAlbumId)
    {
        $aAlbum = db()->select('album_id, module_id, group_id')->from($this->_sTable)->where('album_id = '.(int)$iAlbumId)->execute('getRow');
        if (!$aAlbum) {
            return false;
        }
        if ($aAlbum['module_id'] && Phpfox::hasCallback($aAlbum['module_id'], 'isAdmin')) {
            return Phpfox::callback($aAlbum['module_id'] . '.isAdmin', $aAlbum['group_id']);
        }
        return false;
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
        if ($sPlugin = Phpfox_Plugin::get('photo.service_album__call')) {
            eval($sPlugin);
            return null;
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }
}