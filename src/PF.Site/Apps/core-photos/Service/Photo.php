<?php

namespace Apps\Core_Photos\Service;

use Friend_Service_Friend;
use Friend_Service_List_List;
use Phpfox;
use Phpfox_Error;
use Phpfox_Plugin;
use Phpfox_Service;
use Phpfox_Url;

class Photo extends Phpfox_Service
{
    private $_bIsTagSearch = false;
    private $_aPhotoPicSizes =  [75,100,150,240,500,1024];

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('photo');
    }

    public function getCoverPhoto($iPhotoId)
    {
        $aRow = db()->select('*')
            ->from(Phpfox::getT('photo'))
            ->where('photo_id = ' . (int)$iPhotoId)
            ->execute('getSlaveRow');
        if (isset($aRow['photo_id'])) {
            return $aRow;
        }
        return false;
    }

    public function isTagSearch($bIsTagSearch = false)
    {
        $this->_bIsTagSearch = $bIsTagSearch;

        return $this;
    }

    /**
     * Get all photos based on filters we passed via the params.
     *
     * @param array $mConditions SQL Conditions
     * @param string $sOrder SQL Ordering
     * @param mixed $iPage Current page we are on
     * @param mixed $iPageSize Define how many photos we can display at one time
     * @param array $aCallback remove in v4.6
     *
     * @return array Return an array of the total photo count and the photos
     */
    public function get(
        $mConditions = array(),
        $sOrder = 'p.time_stamp DESC',
        $iPage = '',
        $iPageSize = '',
        $aCallback = null
    ) {
        $aPhotos = array();
        if ($this->_bIsTagSearch !== false) {
            db()->innerJoin(Phpfox::getT('tag'), 'tag', "tag.item_id = p.photo_id AND tag.category_id = 'photo'");
        }

        $iCnt = db()->select('COUNT(*)')
            ->from(Phpfox::getT('photo'), 'p')
            ->where($mConditions)
            ->execute('getSlaveField');

        if ($iCnt) {
            if ($this->_bIsTagSearch !== false) {
                db()->innerJoin(Phpfox::getT('tag'), 'tag', "tag.item_id = p.photo_id AND tag.category_id = 'photo'");
            }

            if (Phpfox::isModule('like')) {
                db()->select('l.like_id as is_liked, ')
                    ->leftJoin(Phpfox::getT('like'), 'l',
                        'l.type_id = "photo" AND l.item_id = p.photo_id AND l.user_id = ' . Phpfox::getUserId() . '');
            }

            $aPhotos = db()->select(Phpfox::getUserField() . ', p.*, pa.name AS album_url, pi.*')
                ->from(Phpfox::getT('photo'), 'p')
                ->leftJoin(Phpfox::getT('photo_info'), 'pi', 'pi.photo_id = p.photo_id')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')
                ->leftJoin(Phpfox::getT('photo_album'), 'pa', 'pa.album_id = p.album_id')
                ->where($mConditions)
                ->order($sOrder)
                ->limit($iPage, $iPageSize, $iCnt)
                ->execute('getSlaveRows');

            foreach ($aPhotos as $iKey => $aPhoto) {
                $sCategoryList = '';
                $aCategories = (array)db()->select('category_id')
                    ->from(Phpfox::getT('photo_category_data'))
                    ->where('photo_id = ' . (int)$aPhoto['photo_id'])
                    ->execute('getSlaveRows');

                foreach ($aCategories as $aCategory) {
                    $sCategoryList .= $aCategory['category_id'] . ',';
                }

                $aPhoto['link'] = Phpfox::permalink('photo', $aPhoto['photo_id'], $aPhoto['title']);
                $aPhoto['category_list'] = rtrim($sCategoryList, ',');
                $aPhoto['destination'] = $this->getPhotoUrl($aPhoto);
                $this->getPermissions($aPhoto);
                $aPhotos[$iKey] = $aPhoto;
            }
        }

        return array($iCnt, $aPhotos);
    }

    public function getForEdit($iId)
    {
        $aPhoto = db()->select('p.*, pi.*, pa.name AS album_url, pa.name AS album_title, ' . Phpfox::getUserField())
            ->from($this->_sTable, 'p')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')
            ->leftJoin(Phpfox::getT('photo_info'), 'pi', 'pi.photo_id = p.photo_id')
            ->leftJoin(Phpfox::getT('photo_album'), 'pa', 'pa.album_id = p.album_id')
            ->where('p.photo_id = ' . (int)$iId)
            ->execute('getSlaveRow');

        $aPhoto['categories'] = Phpfox::getService('photo.category')->getCategoriesById($aPhoto['photo_id']);

        if (Phpfox::isModule('tag')) {
            $aTags = Phpfox::getService('tag')->getTagsById('photo', $aPhoto['photo_id']);
            if (isset($aTags[$aPhoto['photo_id']])) {
                $aPhoto['tag_list'] = '';
                foreach ($aTags[$aPhoto['photo_id']] as $aTag) {
                    $aPhoto['tag_list'] .= ' ' . $aTag['tag_text'] . ',';
                }
                $aPhoto['tag_list'] = trim(trim($aPhoto['tag_list'], ','));
            }
        }

        $sCategoryList = '';
        $aCategories = (array)db()->select('category_id')
            ->from(Phpfox::getT('photo_category_data'))
            ->where('photo_id = ' . (int)$aPhoto['photo_id'])
            ->execute('getSlaveRows');

        foreach ($aCategories as $aCategory) {
            $sCategoryList .= $aCategory['category_id'] . ',';
        }

        $aPhoto['category_list'] = rtrim($sCategoryList, ',');

        if (!empty($aPhoto['description'])) {
            $aPhoto['description'] = str_replace('<br />', "\n", $aPhoto['description']);
        }

        return $aPhoto;
    }

    public function getForProcess($iId, $iUserId = 0)
    {
        return db()->select('user_id, photo_id, server_id, title, album_id, group_id, destination, privacy, privacy_comment, view_id')
            ->from($this->_sTable)
            ->where('photo_id = ' . (int)$iId . ' AND user_id = ' . ($iUserId ? $iUserId : Phpfox::getUserId()))
            ->execute('getSlaveRow');
    }

    public function getApprovalPhotosCount()
    {
        return db()->select('COUNT(*)')
            ->from($this->_sTable)
            ->where('view_id = 1')
            ->execute('getSlaveField');
    }

    public function getPhotoByDestination($sName)
    {
        $aPhoto = db()->select('p.*, pi.*, pa.name AS album_title, ' . Phpfox::getUserField())
            ->from($this->_sTable, 'p')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')
            ->leftJoin(Phpfox::getT('photo_info'), 'pi', 'pi.photo_id = p.photo_id')
            ->leftJoin(Phpfox::getT('photo_album'), 'pa', 'pa.album_id = p.album_id')
            ->where('p.destination = \'' . db()->escape($sName) . '\'')
            ->execute('getSlaveRow');

        if (!isset($aPhoto['photo_id'])) {
            return false;
        }

        return $aPhoto;
    }

    /**
     * @param $sId
     * @return array|bool|int|string
     */
    public function getPhoto($sId)
    {
        if (Phpfox::isModule('like')) {
            db()->select('lik.like_id AS is_liked, ')
                ->leftJoin(Phpfox::getT('like'), 'lik',
                    'lik.type_id = \'photo\' AND lik.item_id = p.photo_id AND lik.user_id = ' . Phpfox::getUserId());
        }
        if (Phpfox::isModule('friend')) {
            db()->select('f.friend_id AS is_friend, ')->leftJoin(Phpfox::getT('friend'), 'f',
                "f.user_id = p.user_id AND f.friend_user_id = " . Phpfox::getUserId());
        }
        if (Phpfox::isModule('track')) {
            $sJoinQuery = Phpfox::isUser() ? 'pt.user_id = ' . Phpfox::getUserBy('user_id') : 'pt.ip_address = \'' . $this->database()->escape(Phpfox::getIp()) . '\'';
            db()->select('pt.item_id AS is_viewed, ')
                ->leftJoin(Phpfox::getT('track'), 'pt',
                'pt.item_id = p.photo_id AND pt.type_id=\'photo\' AND '.$sJoinQuery);
        }
        db()->where('p.photo_id = ' . (int)$sId);

        $aPhoto = db()->select('' . Phpfox::getUserField() . ', p.*, pi.*, pa.name AS album_url, pa.name AS album_title, pa.profile_id AS album_profile_id, pa.cover_id AS album_cover_id, pa.timeline_id AS album_timeline_id')
            ->from($this->_sTable, 'p')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')
            ->leftJoin(Phpfox::getT('photo_info'), 'pi', 'pi.photo_id = p.photo_id')
            ->leftJoin(Phpfox::getT('photo_album'), 'pa', 'pa.album_id = p.album_id')
            ->execute('getSlaveRow');
        if (!isset($aPhoto['photo_id'])) {
            return false;
        }

        if (!isset($aPhoto['is_liked'])) {
            $aPhoto['is_liked'] = false;
        }
        if (!isset($aPhoto['is_viewed'])) {
            $aPhoto['is_viewed'] = 0;
        }
        if (!isset($aPhoto['is_friend'])) {
            $aPhoto['is_friend'] = 0;
        }
        if (Phpfox::isModule('tag')) {
            $aTags = Phpfox::getService('tag')->getTagsById('photo', $aPhoto['photo_id']);
            if (isset($aTags[$aPhoto['photo_id']])) {
                $aPhoto['tag_list'] = $aTags[$aPhoto['photo_id']];
            }
        }

        $aPhoto['categories'] = Phpfox::getService('photo.category')->getCategoriesById($aPhoto['photo_id']);
        $aPhoto['category_list'] = Phpfox::getService('photo.category')->getCategoryIds($aPhoto['photo_id']);

        if (empty($aPhoto['album_id'])) {
            $aPhoto['album_url'] = 'view';
        }

        $aPhoto['original_destination'] = $aPhoto['destination'];
        $aPhoto['destination'] = $this->getPhotoUrl($aPhoto);

        if ($aPhoto['album_id'] > 0) {
            if ($aPhoto['album_profile_id'] > 0) {
                $aPhoto['album_title'] = _p('user_profile_pictures', ['full_name' => $aPhoto['full_name']]);;
                $aPhoto['album_url'] = Phpfox::permalink('photo.album.profile', $aPhoto['user_id'],
                    $aPhoto['user_name']);
            }
            if ($aPhoto['album_cover_id'] > 0) {
                $aPhoto['album_title'] = _p('user_cover_photo', ['full_name' => $aPhoto['full_name']]);
                $aPhoto['album_url'] = Phpfox::permalink('photo.album.cover', $aPhoto['user_id'], $aPhoto['user_name']);
            }
            if ($aPhoto['album_timeline_id'] > 0) {
                $aPhoto['album_title'] = _p('user_timeline_photos', ['full_name' => $aPhoto['full_name']]);
                $aPhoto['album_url'] = Phpfox::permalink('photo.album', $aPhoto['album_id'], $aPhoto['album_title']);
            } else {
                $aPhoto['album_url'] = Phpfox::permalink('photo.album', $aPhoto['album_id'], $aPhoto['album_title']);
            }
        }
        $aPhoto['location_latlng'] = json_decode($aPhoto['location_latlng'], true);
        $aPhoto['link'] = Phpfox::permalink('photo', $aPhoto['photo_id'], $aPhoto['title']);
        $this->getPermissions($aPhoto);

        return $aPhoto;
    }

    /**
     * @param $sId
     * @return \Phpfox_Database_Dba
     */
    public function getPhotoItem($sId)
    {
        $aPhoto = db()->select('p.*,  pi.*')
            ->from($this->_sTable, 'p')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')
            ->leftJoin(Phpfox::getT('photo_info'), 'pi', 'pi.photo_id = p.photo_id')
            ->where('p.photo_id = ' . (int)$sId)
            ->execute('getSlaveRow');
        if (count($aPhoto)) {
            $aPhoto['original_destination'] = $aPhoto['destination'];
            $aPhoto['destination'] = $this->getPhotoUrl($aPhoto);
        }
        return $aPhoto;
    }

    /**
     * We get and return the latest images we uploaded. The reason we run
     * this check is so we only return images that belong to the user that is loggeed in
     * and not someone else images.
     *
     * @param int $iUserId User ID of the user the images belong to.
     * @param array $aIds Array of photo IDS
     *
     * @return array Array of user images.
     */
    public function getNewImages($iUserId, $aIds)
    {
        // We run an INT check just in case someone is trying to be funny.
        $sIds = '';
        foreach ($aIds as $iKey => $sId) {
            if (!is_numeric($sId)) {
                continue;
            }
            $sIds .= $sId . ',';
        }
        $sIds = rtrim($sIds, ',');

        // Lets the new images and return them.
        return db()->select('p.photo_id, p.album_id, p.destination, p.server_id, p.view_id, pa.privacy, p.title')
            ->from($this->_sTable, 'p')
            ->leftJoin(Phpfox::getT('photo_album'), 'pa', 'pa.album_id = p.album_id')
            ->where('p.photo_id IN(' . $sIds . ') AND p.user_id = ' . (int)$iUserId)
            ->order('p.photo_id DESC')
            ->execute('getSlaveRows');
    }

    public function getRandomSponsored($iLimit = 4, $iCacheTime = 5)
    {
        $sCacheId = $this->cache()->set('photo_sponsored');
        if (!($sPhotoIds = $this->cache()->get($sCacheId, $iCacheTime))) {
            $sWhere = 'p.view_id = 0 AND p.is_sponsor = 1 AND s.module_id = \'photo\' AND s.is_active = 1 AND s.is_custom = 3';
            $sWhere .= $this->getConditionsForSettingPageGroup();

            $aPhotoIds = db()->select('p.photo_id')
                ->from($this->_sTable, 'p')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')
                ->join(Phpfox::getT('photo_info'), 'pi', 'pi.photo_id = p.photo_id')
                ->join(Phpfox::getT('ad_sponsor'), 's', 's.item_id = p.photo_id')
                ->leftJoin(Phpfox::getT('photo_album'), 'pa', 'pa.album_id = p.album_id')
                ->where($sWhere)
                ->order('rand()')
                ->limit(Phpfox::getParam('core.cache_total'))
                ->execute('getSlaveRows');
            foreach ($aPhotoIds as $key => $aId) {
                if ($key != 0) {
                    $sPhotoIds .= ',' . $aId['photo_id'];
                } else {
                    $sPhotoIds = $aId['photo_id'];
                }
            }
            if ($iCacheTime) {
                $this->cache()->save($sCacheId, $sPhotoIds);
            }
        }
        if (empty($sPhotoIds)) {
            return [];
        }
        $aPhotoIds = explode(',', $sPhotoIds);
        shuffle($aPhotoIds);
        $aPhotoIds = array_slice($aPhotoIds, 0, round($iLimit * Phpfox::getParam('core.cache_rate')));
        $aRows = db()->select('s.*, pi.width, pi.height, u.user_name, p.mature, p.total_like, p.total_view as total_view_photo, p.time_stamp, pi.file_size, p.photo_id, p.destination, p.server_id, p.title, p.album_id')
            ->from($this->_sTable, 'p')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')
            ->join(Phpfox::getT('photo_info'), 'pi', 'pi.photo_id = p.photo_id')
            ->join(Phpfox::getT('ad_sponsor'), 's',
                's.item_id = p.photo_id AND s.module_id = \'photo\' AND s.is_active = 1')
            ->leftJoin(Phpfox::getT('photo_album'), 'pa', 'pa.album_id = p.album_id')
            ->where('p.photo_id IN (' . implode(',', $aPhotoIds) . ')')
            ->limit($iLimit)
            ->execute('getSlaveRows');
        if (!isset($aRows[0]) || empty($aRows[0])) {
            return array();
        }

        shuffle($aRows);
        if (Phpfox::isModule('ad')) {
            $aRows = Phpfox::getService('ad')->filterSponsor($aRows);
        }

        $aOut = array();
        for ($i = 0; ($i < $iLimit) && !empty($aRows); ++$i) {
            $aRow = array_pop($aRows);
            if (Phpfox::isModule('ad')) {
                Phpfox::getService('ad.process')->addSponsorViewsCount($aRow['sponsor_id'], 'photo');
            }

            $aRow['details'] = array(
                _p('submitted') => Phpfox::getTime(Phpfox::getParam('core.global_update_time'), $aRow['time_stamp']),
                _p('file_size') => \Phpfox_File::instance()->filesize($aRow['file_size']),
                _p('resolution') => $aRow['width'] . '×' . $aRow['height'],
                _p('views') => $aRow['total_view_photo']
            );
            $aRow['total_view'] = $aRow['total_view_photo'];
            $aRow['can_view'] = true;
            if ($aRow['user_id'] != Phpfox::getUserId() && $aRow['mature'] != 0 && Phpfox::getUserParam(array(
                    'photo.photo_mature_age_limit' => array(
                        '>',
                        (int)Phpfox::getUserBy('age')
                    )
                ))
            ) {
                $aRow['can_view'] = false;
            }
            $aRow['link'] = Phpfox::getLib('url')->makeUrl('ad.sponsor').'view_'.$aRow['sponsor_id'];
            $aOut[] = $aRow;
        }

        return $aOut;
    }

    /**
     * @param $iPhotoId
     * @return bool
     */
    public function isSponsoredInFeed($iPhotoId)
    {
        if (!Phpfox::isModule('ad') || !Phpfox::isModule('feed')) {
            return false;
        }
        //Get Feed ID of Photo
        $iFeedId = db()->select('feed_id')
            ->from(':feed')
            ->where('type_id="photo" AND item_id=' . (int)$iPhotoId)
            ->execute('getSlaveField');
        if (!$iFeedId) {
            return false;
        }
        $iCnt = db()->select('DISTINCT item_id')
            ->from(Phpfox::getT('ad_sponsor'))
            ->where('module_id = "feed" AND item_id=' . (int)$iFeedId)
            ->execute('getSlaveField');
        return ($iCnt) ? false : true;
    }

    /**
     * @param int $iLimit
     * @return array|int|string
     */
    public function getNew($iLimit = 3)
    {
        $aPhotos = db()->select('p.destination, p.server_id, p.title, p.photo_id, p.mature, p.album_id, ' . Phpfox::getUserField())
            ->from($this->_sTable, 'p')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')
            ->leftJoin(Phpfox::getT('photo_album'), 'pa', 'pa.album_id = p.album_id')
            ->where('p.view_id = 0 AND p.group_id = 0 AND p.privacy = 0')
            ->order('p.time_stamp DESC')
            ->limit($iLimit)
            ->execute('getSlaveRows');

        foreach ($aPhotos as $iKey => $aPhoto) {
            $aPhotos[$iKey]['link'] = Phpfox::permalink('photo', $aPhoto['photo_id'], $aPhoto['title']);
        }

        return $aPhotos;
    }

    /* This function is used in the converting controller to get the image that needs to have its thumbnails created.*/
    public function getForConverting($iUserId, $iLimit = 1)
    {
        $aPhoto = db()->select('p.photo_id, p.destination, p.server_id, p.title, p.mature, p.album_id, ' . Phpfox::getUserField())
            ->from($this->_sTable, 'p')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')
            ->leftJoin(Phpfox::getT('photo_album'), 'pa', 'pa.album_id = p.album_id')
            ->where('p.view_id = 0 AND p.user_id = ' . (int)$iUserId)
            ->order('p.time_stamp DESC')
            ->limit($iLimit)
            ->execute('getSlaveRows');
        return $aPhoto;
    }

    public function getForProfile($iUserId, $iLimit = 3)
    {
        $aPhotos = db()->select(Phpfox::getUserField() . ',p.*, p.destination, p.server_id, p.title, p.mature, p.album_id, pa.name AS album_name')
            ->from($this->_sTable, 'p')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')
            ->leftJoin(Phpfox::getT('photo_album'), 'pa', 'pa.album_id = p.album_id')
            ->where('p.view_id = 0 AND p.group_id = 0 AND p.user_id = ' . (int)$iUserId . ((!Phpfox::getParam('photo.display_profile_photo_within_gallery')) ? ' AND p.is_profile_photo IN (0)' : '') . ((!Phpfox::getParam('photo.display_cover_photo_within_gallery')) ? ' AND p.is_cover_photo IN (0)' : '') . ((!Phpfox::getParam('photo.display_timeline_photo_within_gallery')) ? ' AND type_id = 0' : '') )
            ->order('p.time_stamp DESC')
            ->limit($iLimit)
            ->execute('getSlaveRows');
        foreach ($aPhotos as $iKey => $aPhoto) {
            $aPhotos[$iKey]['link'] = Phpfox::permalink('photo', $aPhoto['photo_id'], $aPhoto['title']);
            if (!Phpfox::getService('privacy')->check('photo', $aPhoto['photo_id'], $iUserId, $aPhoto['privacy'], null,
                true)
            ) {
                unset($aPhotos[$iKey]);
            }
            else {
                $aPhotos[$iKey]['can_view'] = true;
                if ($aPhoto['user_id'] != Phpfox::getUserId() && $aPhoto['mature'] != 0 && Phpfox::getUserParam(array(
                        'photo.photo_mature_age_limit' => array(
                            '>',
                            (int)Phpfox::getUserBy('age')
                        )
                    ))
                ) {
                    $aPhotos[$iKey]['can_view'] = false;
                }
            }
        }

        return $aPhotos;
    }

    /**
     * @param $iGroupId
     * @param $sGroupUrl remove in v4.6
     * @return array|int|string
     */
    public function getForGroup($iGroupId, $sGroupUrl)
    {
        $aPhotos = db()->select('p.destination, p.server_id, p.title, p.mature, p.album_id, ' . Phpfox::getUserField())
            ->from($this->_sTable, 'p')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')
            ->leftJoin(Phpfox::getT('photo_album'), 'pa', 'pa.album_id = p.album_id')
            ->where('p.view_id = 0 AND p.group_id = ' . $iGroupId . ' AND p.privacy = 0')
            ->order('p.time_stamp DESC')
            ->limit(3)
            ->execute('getSlaveRows');

        foreach ($aPhotos as $iKey => $aPhoto) {
            $aPhotos[$iKey]['link'] = Phpfox::permalink('photo', $aPhoto['photo_id'], $aPhoto['title']);
        }

        return $aPhotos;
    }

    /**
     * Return the featured time stamp in milliseconds
     *
     * @return int Time stamp in milliseconds
     */
    public function getFeaturedRefreshTime()
    {
        // Get the refresh setting
        $sTime = Phpfox::getUserParam('photo.refresh_featured_photo');

        // Match the minutes or seconds
        preg_match("/(.*?)(min|sec)$/i", $sTime, $aMatches);

        // Make sure we have a match
        if (isset($aMatches[1]) && isset($aMatches[2])) {
            // Trim the matched time stamp
            $aMatches[2] = trim($aMatches[2]);

            // If we want to work with minutes
            if ($aMatches[2] == 'min') {
                // Convert to milliseconds
                return (int)($aMatches[1] * 60000);
            } // If we want to work with seconds
            elseif ($aMatches[2] == 'sec') {
                // Convert to milliseconds
                return (int)($aMatches[1] * 1000);
            }
        }

        // Return the default value (60 seconds)
        return 60000;
    }

    /**
     * @return int
     */
    public function getMyPhotoTotal()
    {
        $sWhere = '(type_id = 0 OR (type_id = 1 AND (parent_user_id = 0 OR group_id))) AND user_id = ' . Phpfox::getUserId();
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

        if (!Phpfox::getParam('photo.display_profile_photo_within_gallery')) {
            $sWhere .= ' AND is_profile_photo IN (0)';
        }
        if (!Phpfox::getParam('photo.display_cover_photo_within_gallery')) {
            $sWhere .= ' AND is_cover_photo IN (0)';
        }
        if (!Phpfox::getParam('photo.display_timeline_photo_within_gallery')) {
            $sWhere .= ' AND (type_id = 0 OR (type_id = 1 AND group_id != 0))';
        }

        return db()->select('COUNT(*)')
            ->from($this->_sTable)
            ->where($sWhere)
            ->execute('getSlaveField');
    }

    /**
     * @return int
     */
    public function getPendingTotal()
    {
        $sWhere = 'view_id = 1';
        $aModules = [];
        if (!Phpfox::isModule('groups')) {
            $aModules[] = 'groups';
        }
        if (!Phpfox::isModule('pages')) {
            $aModules[] = 'pages';
        }
        $sWhere .= ' AND (module_id NOT IN ("' . implode('","', $aModules) . '") OR module_id is NULL)';

        return db()->select('COUNT(*)')
            ->from($this->_sTable)
            ->where($sWhere)
            ->execute('getSlaveField');
    }

    public function getPhotoUrl($aPhoto)
    {
        $sUrl = $aPhoto['destination'];
        if (Phpfox::getParam('photo.rename_uploaded_photo_names', 0)) {
            if (preg_match('/(.*)\/(.*)\%s\.(.*)/i', $aPhoto['destination'],
                    $aMatches) && isset($aMatches[2]) && (int)strlen($aMatches[2]) == 32
            ) {
                $sUrl = '[PHPFOX_CUSTOM_URL]' . $aMatches[1] . '/' . $aMatches[2] . '-' . Phpfox::getLib('parse.input')->cleanFileName($aPhoto['title']) . '%s.' . $aMatches[3];
            }
        }

        return $sUrl;
    }

    /**
     * @param int $iLimit
     * @param int $iCacheTime
     * @return array
     */
    public function getFeatured($iLimit = 4, $iCacheTime = 5)
    {
        $sCacheId = $this->cache()->set('photo_featured');
        if (!($sPhotoIds = $this->cache()->get($sCacheId, $iCacheTime))) {
            $sWhere = 'p.view_id = 0 AND p.is_featured = 1';
            $sWhere .= $this->getConditionsForSettingPageGroup();
            if (!Phpfox::getParam('photo.display_profile_photo_within_gallery')) {
                $sWhere .= ' AND p.is_profile_photo IN (0)';
            }

            if (!Phpfox::getParam('photo.display_cover_photo_within_gallery')) {
                $sWhere .= ' AND p.is_cover_photo IN (0)';
            }

            if (!Phpfox::getParam('photo.display_timeline_photo_within_gallery')) {
                $sWhere .= ' AND (p.type_id = 0 OR (p.type_id = 1 AND p.group_id != 0))';
            }
            $aPhotoIds = db()->select('p.photo_id')
                ->from(Phpfox::getT('photo'), 'p')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')
                ->where($sWhere)
                ->order('rand()')
                ->limit(Phpfox::getParam('core.cache_total'))
                ->execute('getSlaveRows');

            foreach ($aPhotoIds as $key => $aId) {
                if ($key != 0) {
                    $sPhotoIds .= ',' . $aId['photo_id'];
                } else {
                    $sPhotoIds = $aId['photo_id'];
                }
            }
            if ($iCacheTime) {
                $this->cache()->save($sCacheId, $sPhotoIds);
            }
        }
        if (empty($sPhotoIds)) {
            return array(0,[]);
        }
        $aPhotoIds = explode(',', $sPhotoIds);
        shuffle($aPhotoIds);
        $aPhotoIds = array_slice($aPhotoIds, 0, round($iLimit * Phpfox::getParam('core.cache_rate')));
        $aRows = db()->select('p.*, ' . Phpfox::getUserField())
            ->from(Phpfox::getT('photo'), 'p')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')
            ->where('p.photo_id IN (' . implode(',', $aPhotoIds) . ')')
            ->limit($iLimit)
            ->execute('getSlaveRows');
        foreach ($aRows as $key => $aRow) {
            $aRows[$key]['can_view'] = true;
            if ($aRow['user_id'] != Phpfox::getUserId() && $aRow['mature'] != 0 && Phpfox::getUserParam(array(
                    'photo.photo_mature_age_limit' => array(
                        '>',
                        (int)Phpfox::getUserBy('age')
                    )
                ))
            ) {
                $aRows[$key]['can_view'] = false;
            }
            $aRows[$key]['link'] = Phpfox::getLib('url')->permalink('photo',$aRow['photo_id'],$aRow['title']);
        }
        shuffle($aRows);

        return array(count($aRows), $aRows);
    }

    /**
     * Build sub menu
     */
    public function buildMenu()
    {
        $aFilterMenu = array();
        if (!defined('PHPFOX_IS_USER_PROFILE')) {
            $sAllPhotosKey = _p('all_photos');

            $iMyPhotosTotal = $this->getMyPhotoTotal();
            $sMyPhotosKey = _p('my_photos') . ($iMyPhotosTotal ? '<span class="my count-item">' . ($iMyPhotosTotal > 99 ? '99+' : $iMyPhotosTotal) . '</span>' : '');

            $sFriendPhotosKey = _p('friends_photos');

            $sAllAlbumsKey = _p('all_albums');

            $iMyAlbumsTotal = Phpfox::getService('photo.album')->getMyAlbumTotal();
            $sMyAlbumsKey = _p('my_albums') . ($iMyAlbumsTotal ? '<span class="my count-item">' . ($iMyAlbumsTotal > 99 ? '99+' : $iMyAlbumsTotal) . '</span>' : '');

            if (Phpfox::getParam('photo.in_main_photo_section_show',
                    'photos') == 'albums' && Phpfox::getUserParam('photo.can_view_photo_albums')
            ) {
                $aFilterMenu[$sAllAlbumsKey] = '';
                $aFilterMenu[$sMyAlbumsKey] = 'photo.albums.view_myalbums';
                $aFilterMenu[] = true;
            }

            if (Phpfox::getParam('core.friends_only_community') || !Phpfox::isModule('friend')) {
                $aFilterMenu[$sAllPhotosKey] = 'photos';
                $aFilterMenu[$sMyPhotosKey] = 'my';
            } else {
                if (Phpfox::getParam('photo.in_main_photo_section_show', 'photos') == 'albums') {
                    $aFilterMenu[$sAllPhotosKey] = 'photo.view_photos';
                } else {
                    $aFilterMenu[$sAllPhotosKey] = '';
                }
                $aFilterMenu[$sMyPhotosKey] = 'my';
                $aFilterMenu[$sFriendPhotosKey] = 'friend';
            }

            if (Phpfox::getUserParam('photo.can_approve_photos')) {
                $iPendingTotal = $this->getPendingTotal();
                if ($iPendingTotal) {
                    $aFilterMenu[_p('pending_photos') . '<span id="photo_pending" class="pending count-item">' . ($iPendingTotal > 99 ? '99+' : $iPendingTotal) . '</span>'] = 'pending';
                }
            }

            if (Phpfox::getParam('photo.in_main_photo_section_show',
                    'photos') != 'albums' && Phpfox::getUserParam('photo.can_view_photo_albums')
            ) {
                $aFilterMenu[] = true;
                $aFilterMenu[$sAllAlbumsKey] = 'photo.albums';
                $aFilterMenu[$sMyAlbumsKey] = 'photo.albums.view_myalbums';
            }
        }

        Phpfox::getLib('template')->buildSectionMenu('photo', $aFilterMenu);
    }

    /**
     * @param $iFeedId
     * @param null $iLimit
     * @param string $sFeedTablePrefix
     * @return array|int|string
     */
    public function getFeedPhotos($iFeedId, $iLimit = null, $sFeedTablePrefix = '')
    {
        $aFeed = Phpfox::getService('feed')->getFeed($iFeedId, $sFeedTablePrefix);
        if (!$aFeed) {
            return [];
        }
        if ($iLimit) {
            $aPhotos = db()
                ->select('p.photo_id, p.album_id, p.user_id, p.title, p.server_id, p.destination, p.mature')
                ->from(Phpfox::getT('photo'), 'p')
                ->leftJoin(Phpfox::getT('photo_feed'), 'pfeed', 'p.photo_id = pfeed.photo_id')
                ->where('(pfeed.feed_id = ' . $iFeedId . ' AND  pfeed.feed_table = \'' . $sFeedTablePrefix . 'feed\') OR p.photo_id = ' . $aFeed['item_id'])
                ->limit($iLimit)
                ->order('pfeed.feed_id ASC, p.time_stamp DESC')
                ->execute('getSlaveRows');
        } else {
            $aPhotos = db()
                ->select('p.photo_id, p.album_id, p.user_id, p.title, p.server_id, p.destination, p.mature')
                ->from(Phpfox::getT('photo'), 'p')
                ->leftJoin(Phpfox::getT('photo_feed'), 'pfeed', 'p.photo_id = pfeed.photo_id')
                ->where('(pfeed.feed_id = ' . $iFeedId . ' AND  pfeed.feed_table = \'' . $sFeedTablePrefix . 'feed\') OR p.photo_id = ' . $aFeed['item_id'])
                ->order('pfeed.feed_id ASC, p.time_stamp DESC')
                ->execute('getSlaveRows');
        }
        foreach ($aPhotos as $key => $aPhoto) {
            $aPhotos[$key]['html'] = '<span style="background-image: url(\''.
                    Phpfox::getLib('image.helper')->display([
                        'server_id' => $aPhoto['server_id'],
                        'path' => 'photo.url_photo',
                        'file' => $this->getPhotoUrl($aPhoto),
                        'suffix' => '_500',
                        'userid' => $aPhoto['user_id'],
                        'return_url' => true
                    ]
            ).'\')";></span>';
            $aPhotos[$key]['link'] = Phpfox::permalink('photo', $aPhoto['photo_id'], $aPhoto['title']) .'feed_'.$iFeedId;
        }
        return $aPhotos;
    }

    /**
     * @param int $iAlbumId
     * @param int $iUserId
     * @param int $iOwnerId
     * @param null $aPhoto
     * @param int $bMyPhotos
     * @return array
     */
    public function getPhotos($iAlbumId = 0, $iUserId = 0, $iOwnerId = 0, $aPhoto = null, $bMyPhotos = 0)
    {
        $sQuery = '';
        if (!$iAlbumId && !$bMyPhotos) {
            $sQuery = 'AND photo.type_id = 0';
        }
        if (!$bMyPhotos && !$iAlbumId && isset($aPhoto['module_id'])) {
            $sQuery .= ' AND photo.module_id = \'' . db()->escape($aPhoto['module_id']) . '\' AND photo.group_id = ' . (int)$aPhoto['group_id'];

        }
        if (!($iOwnerId > 0 && Phpfox::getUserId() == $iOwnerId)) {
            $sQuery .= $this->getConditionsForSettingPageGroup('photo');
            if (!Phpfox::getUserParam('privacy.can_view_all_items')) {
                $sQuery .= ' AND photo.privacy IN(%PRIVACY%)';
            }
        }
        $bIsProfilePhotoAlbum = false;
        if ($iAlbumId > 0 && ($aAlbum = db()->select('user_id, profile_id')->from(Phpfox::getT('photo_album'))->where('album_id = ' . (int)$iAlbumId)->execute('getSlaveRow')) && ($aAlbum['user_id'] == $aAlbum['profile_id'])) {
            $bIsProfilePhotoAlbum = true;
        }
        $bIsCoverPhotoAlbum = false;
        if ($iAlbumId > 0 && ($aAlbum = db()->select('user_id, cover_id')->from(Phpfox::getT('photo_album'))->where('album_id = ' . (int)$iAlbumId)->execute('getSlaveRow')) && ($aAlbum['user_id'] == $aAlbum['cover_id'])) {
            $bIsCoverPhotoAlbum = true;
        }
        $bIsTimelinePhotoAlbum = false;
        if ($iAlbumId > 0 && ($aAlbum = db()->select('user_id, timeline_id')->from(Phpfox::getT('photo_album'))->where('album_id = ' . (int)$iAlbumId)->execute('getSlaveRow')) && ($aAlbum['user_id'] == $aAlbum['timeline_id'])) {
            $bIsTimelinePhotoAlbum = true;
        }
        if (!Phpfox::getParam('photo.display_profile_photo_within_gallery') && !$bIsProfilePhotoAlbum) {
            $sQuery .= ' AND photo.is_profile_photo IN (0)';
        }
        if (!Phpfox::getParam('photo.display_cover_photo_within_gallery') && !$bIsCoverPhotoAlbum) {
            $sQuery .= ' AND photo.is_cover_photo IN (0)';
        }
        if (!Phpfox::getParam('photo.display_timeline_photo_within_gallery') && !$bIsTimelinePhotoAlbum) {
            $sQuery .= ' AND (photo.type_id = 0 OR (photo.type_id = 1 AND photo.group_id != 0))';
        }
        if ($iAlbumId > 0 && !$bMyPhotos) {
            $sQuery .= ' AND photo.album_id = ' . (int)$iAlbumId;
        }
        if ($iUserId > 0) {
            $sQuery .= ' AND photo.user_id = ' . (int)$iUserId;
        }
        $aPhotos = $this->_getPhotos($sQuery, 'DESC');
        foreach ($aPhotos as $key => $aPhoto) {
            $aPhotos[$key]['html'] = '<span style="background-image: url(\''.
                    Phpfox::getLib('image.helper')->display([
                        'server_id' => $aPhoto['server_id'],
                        'path' => 'photo.url_photo',
                        'file' => $this->getPhotoUrl($aPhoto),
                        'suffix' => '_500',
                        'userid' => $aPhoto['user_id'],
                        'return_url' => true
                    ]
            ).'\')";></span>';
            $aPhotos[$key]['link'] = Phpfox::permalink('photo', $aPhoto['photo_id'], $aPhoto['title']);
            if($iAlbumId > 0) {
                $aPhotos[$key]['link'] .= 'albumid_' . $iAlbumId . '/';
            }
        }
        return $aPhotos;
    }

    /**
     * @param $sCondition
     * @param $sOrder
     * @param bool $bNoPrivacy
     * @param null $iCategory
     * @return array
     * @deprecated from 4.6.0
     */
    private function _getPhoto($sCondition, $sOrder, $bNoPrivacy = false, $iCategory = null)
    {
        if ($bNoPrivacy === true) {
            $iCategoryChecked = null;
            if ($iCategory !== null) {
                $iCategoryChecked = (int)$iCategory;
            } else {
                if (Phpfox::getCookie('photo_category')) {
                    $iCategoryChecked = Phpfox::getCookie('photo_category');
                } else {
                    if ((isset($_SESSION['photo_category']) && $_SESSION['photo_category'] != '')) {
                        $iCategoryChecked = $_SESSION['photo_category'];
                    }
                }
            }

            if ($iCategoryChecked !== null) {
                $this->database()->join(Phpfox::getT('photo_category_data'), 'pcd',
                    'pcd.photo_id = photo.photo_id AND pcd.category_id = ' . ((int)$iCategoryChecked));
            }
            $iPreviousCnt = $this->database()->select('COUNT(*)')
                ->from(Phpfox::getT('photo'), 'photo')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = photo.user_id')
                ->where(array($sCondition))
                ->execute('getSlaveField');

            if ($iCategoryChecked !== null) {
                $this->database()->select('pcd.category_id,')->join(Phpfox::getT('photo_category_data'), 'pcd',
                    'pcd.photo_id = photo.photo_id AND pcd.category_id = ' . (int)$iCategoryChecked);
            }
            $aPrevious = (array)$this->database()->select('photo.*')
                ->from(Phpfox::getT('photo'), 'photo')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = photo.user_id')
                ->where(array($sCondition))
                ->order('photo.photo_id ' . $sOrder)
                ->execute('getSlaveRow');

            if (!empty($aPrevious['photo_id'])) {
                $aPrevious['link'] = Phpfox_Url::instance()->permalink('photo', $aPrevious['photo_id'],
                        $aPrevious['title']) . ($iCategoryChecked !== null ? 'category_' . $iCategoryChecked : '');
            }

            return array($iPreviousCnt, $aPrevious);
        }

        $aBrowseParams = array(
            'module_id' => 'photo',
            'alias' => 'photo',
            'field' => 'photo_id',
            'table' => Phpfox::getT('photo'),
            'hide_view' => array('pending', 'my')
        );

        $this->search()->set(array(
                'type' => 'photo',
                'filters' => array(
                    'display' => array('type' => 'option', 'default' => '1'),
                    'sort' => array('type' => 'option', 'default' => 'photo_id'),
                    'sort_by' => array('type' => 'option', 'default' => $sOrder)
                )
            )
        );

        $this->search()->setCondition($sCondition);
        $this->search()->setCondition('AND photo.view_id = 0 AND photo.group_id = 0 AND photo.type_id = 0 AND photo.privacy IN(%PRIVACY%)');

        $this->search()->browse()->params($aBrowseParams)->execute();
        $iPreviousCnt = $this->search()->browse()->getCount();
        $aPreviousRows = $this->search()->browse()->getRows();

        $this->search()->browse()->reset();

        $aPrevious = array();
        if (isset($aPreviousRows[0])) {
            $aPrevious = $aPreviousRows[0];
        }

        return array($iPreviousCnt, $aPrevious);
    }

    /**
     * @param $sCondition
     * @param $sOrder
     * @return array
     */
    private function _getPhotos($sCondition, $sOrder)
    {
        $aBrowseParams = array(
            'module_id' => 'photo',
            'alias' => 'photo',
            'field' => 'photo_id',
            'table' => Phpfox::getT('photo'),
            'hide_view' => array('pending', 'my')
        );

        $this->search()->set(array(
                'type' => 'photo',
                'filters' => array(
                    'display' => array('type' => 'option', 'default' => '500'),
                    'sort' => array('type' => 'option', 'default' => 'photo_id'),
                    'sort_by' => array('type' => 'option', 'default' => $sOrder)
                )
            )
        );
        if (!empty($sCondition)) {
            $this->search()->setCondition($sCondition);
        }
        $this->search()->browse()->params($aBrowseParams)->execute();
        $aPhotos = $this->search()->browse()->getRows();
        $this->search()->browse()->reset();

        return $aPhotos;
    }

    /**
     * @param $sDes
     * @return null
     */
    public function cropMaxWidth($sDes)
    {
        $oImage = Phpfox::getLib('image');
        list($width, $height, , ) = @getimagesize($sDes);
        if ($width == 0 || $height == 0) {
            return null;
        }
        $iWidth = (int)Phpfox::getUserParam('photo.maximum_image_width_keeps_in_server');
        if ($iWidth < $width) {
            $bIsCropped = $oImage->createThumbnail($sDes, $sDes, $iWidth, $height, true,
                ((Phpfox::getParam('photo.enabled_watermark_on_photos') && Phpfox::getParam('core.watermark_option') != 'none') ? (Phpfox::getParam('core.watermark_option') == 'image' ? 'force_skip' : true) : false));
            if ($bIsCropped !== false && Phpfox::getParam('photo.enabled_watermark_on_photos')) {
                $oImage->addMark($sDes);
            }
        }
    }

    /**
     * @description: check permission to view a photo
     * @param int $iId
     * @param bool $bReturnItem
     *
     * @return array|bool|int|string
     */
    public function canViewItem($iId, $bReturnItem = false)
    {
        if (!Phpfox::getUserParam('photo.can_view_photos')) {
            Phpfox_Error::set(_p('You don\'t have permission to {{ action }} {{ items }}.',
                ['action' => _p('view__l'), 'items' => _p('photos__l')]));
            return false;
        }

        $aPhoto = $this->getPhoto($iId);

        // No photo founds lets get out of here
        if (!isset($aPhoto['photo_id'])) {
            Phpfox_Error::set(_p('This {{ item }} cannot be found.', ['item' => _p('photo__l')]));
            return false;
        }

        if (Phpfox::isUser() && Phpfox::getService('user.block')->isBlocked(null, $aPhoto['user_id'])) {
            Phpfox_Error::set(_p('Sorry, this content isn\'t available right now'));
            return false;
        }

        if (Phpfox::isModule('privacy')) {
            if (!Phpfox::getService('privacy')->check('photo', $aPhoto['photo_id'], $aPhoto['user_id'],
                $aPhoto['privacy'], $aPhoto['is_friend'], true)
            ) {
                return false;
            }
        }

        if ($aPhoto['mature'] != 0) {
            if (Phpfox::getUserId()) {
                if ($aPhoto['user_id'] != Phpfox::getUserId()) {
                    if ($aPhoto['mature'] == 2 && Phpfox::getUserParam(array(
                            'photo.photo_mature_age_limit' => array(
                                '>',
                                (int)Phpfox::getUserBy('age')
                            )
                        ))
                    ) {
                        return Phpfox_Error::display(_p('sorry_this_photo_can_only_be_viewed_by_those_older_then_the_age_of_limit',
                            array('limit' => Phpfox::getUserParam('photo.photo_mature_age_limit'))));
                    }
                }
            } else {
                Phpfox_Error::set(_p('You don\'t have permission to {{ action }} this {{ item }}.',
                    ['action' => _p('view__l'), 'item' => _p('photo__l')]));
                return false;
            }
        }
        if (!empty($aPhoto['module_id']) && $aPhoto['module_id'] != 'photo') {
            if ($aCallback = Phpfox::callback($aPhoto['module_id'] . '.getPhotoDetails', $aPhoto)) {
                if (Phpfox::isModule($aPhoto['module_id']) && Phpfox::hasCallback($aPhoto['module_id'],
                        'checkPermission')
                ) {
                    if (!Phpfox::callback($aPhoto['module_id'] . '.checkPermission', $aCallback['item_id'],
                        'photo.view_browse_photos')
                    ) {
                        Phpfox_Error::display(_p('unable_to_view_this_item_due_to_privacy_settings'));
                        return false;
                    }
                }
            }
        }

        if (!$bReturnItem) {
            return true;
        }

        $aPhoto['bookmark_url'] = Phpfox::getLib('url')->permalink('photo', $aPhoto['photo_id'], $aPhoto['title']);
        $aPhoto['photo_url'] = Phpfox::getLib('image.helper')->display(array(
            'server_id' => $aPhoto['server_id'],
            'path' => 'photo.url_photo',
            'file' => $aPhoto['destination'],
            'suffix' => '_1024',
            'return_url' => true
        ));

        return $aPhoto;
    }

    /**
     * @param $aRow
     */
    public function getPermissions(&$aRow)
    {
        $aRow['can_view'] = true;
        if ($aRow['user_id'] != Phpfox::getUserId() && $aRow['mature'] != 0 && Phpfox::getUserParam(array(
                'photo.photo_mature_age_limit' => array(
                    '>',
                    (int)Phpfox::getUserBy('age')
                )
            ))
        ) {
            $aRow['can_view'] = false;
        }
        $aRow['canEdit'] = (($aRow['user_id'] == Phpfox::getUserId() && Phpfox::getUserParam('photo.can_edit_own_photo')) || Phpfox::getUserParam('photo.can_edit_other_photo'));
        $aRow['canDelete'] = $this->canDelete($aRow);
        $aRow['canSponsorInFeed'] = (Phpfox::isModule('ad') && $aRow['user_id'] == Phpfox::getUserId() && Phpfox::isModule('feed') && (Phpfox::getUserParam('feed.can_purchase_sponsor') || Phpfox::getUserParam('feed.can_sponsor_feed')) && (Phpfox::getService('feed')->canSponsoredInFeed('photo',
                $aRow['photo_id'])));
        $aRow['iSponsorInFeedId'] = Phpfox::getService('feed')->canSponsoredInFeed('photo', $aRow['photo_id']);
        $aRow['canSponsor'] = $aRow['view_id'] == 0 && (Phpfox::isModule('ad') && Phpfox::getUserParam('photo.can_sponsor_photo'));
        $aRow['canPurchaseSponsor'] = $aRow['view_id'] == 0 && (Phpfox::isModule('ad') && $aRow['user_id'] == Phpfox::getUserId() && Phpfox::getUserParam('photo.can_purchase_sponsor'));
        $aRow['canApprove'] = (Phpfox::getUserParam('photo.can_approve_photos') && $aRow['view_id'] == 1);
        $aRow['canFeature'] = (Phpfox::getUserParam('photo.can_feature_photo') && $aRow['view_id'] == 0);
        $aRow['hasPermission'] = ($aRow['canEdit'] || $aRow['canDelete'] || $aRow['canSponsorInFeed'] || $aRow['canSponsor'] || $aRow['canApprove'] || $aRow['canFeature'] || $aRow['canPurchaseSponsor']);
    }

    private function canDelete($aRow)
    {
        $bCanDelete = (($aRow['user_id'] == Phpfox::getUserId() && Phpfox::getUserParam('photo.can_delete_own_photo')) || Phpfox::getUserParam('photo.can_delete_other_photos'));
        if (!$bCanDelete) {
            $aErrors = Phpfox_Error::get();

            if ($aRow['module_id'] == 'pages' && Phpfox::isModule('pages') && Phpfox::getService('pages')->isAdmin($aRow['group_id'])) {
                $bCanDelete = true; // is owner of page
            } elseif ($aRow['module_id'] == 'groups' && Phpfox::isModule('groups') && Phpfox::getService('groups')->isAdmin($aRow['group_id'])) {
                $bCanDelete = true; // is owner of group
            } elseif ($aRow['type_id'] == 1 && Phpfox::getUserId() == $aRow['parent_user_id'] && $aRow['parent_user_id'] != 0 && $aRow['group_id'] == 0) {
                $bCanDelete = true; // is owner of profile
            }

            Phpfox_Error::reset();
            foreach ($aErrors as $sError) {
                Phpfox_Error::set($sError);
            }
        }

        return $bCanDelete;
    }

    /**
     * @return array
     */
    public function getPhotoPicSizes()
    {

        (($sPlugin = Phpfox_Plugin::get('photo.service_photo_getphotopicsizes')) ? eval($sPlugin) : false);

        return $this->_aPhotoPicSizes;
    }

    /**
     * Get the next photo based on the current photo and album we are viewing.
     *
     * @param int $iPhotoId ID of the current photo we are viewing
     * @param string $sType
     * @param int $iItemId
     * @param array $aCallback remove in v4.6
     * @param int $iUserId
     * @return array Array of the next photo
     * @deprecated from 4.6.0
     */
    public function getPreviousPhotos($iPhotoId, $sType = null, $iItemId = null, $aCallback = null, $iUserId = 0)
    {
        (($sPlugin = Phpfox_Plugin::get('photo.service_album_album_getnextphoto')) ? eval($sPlugin) : false);

        $sView = 'p.view_id = 0';
        if ($iUserId == Phpfox::getUserId() || Phpfox::getUserParam('photo.can_approve_photos')) {
            $sView = 'p.view_id IN(0,1)';
        }

        $aCond = array();
        if ($sType !== null) {
            if ($sType == 'album') {
                $aCond[] = 'p.photo_id > ' . (int)$iPhotoId . ' AND p.album_id = ' . (int)$iItemId . ' AND p.group_id = 0 AND ' . $sView;
            } elseif ($sType == 'group') {
                $aCond[] = 'p.photo_id > ' . (int)$iPhotoId . ' AND p.group_id = ' . (int)$iItemId . ' AND ' . $sView;
            }
        }

        $aPhoto = $this->database()->select(Phpfox::getUserField() . ', p.photo_id,  p.destination, p.server_id, p.title, p.mature, p.album_id')
            ->from($this->_sTable, 'p')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')
            ->leftJoin(Phpfox::getT('photo_album'), 'pa', 'pa.album_id = p.album_id')
            ->where($aCond)
            ->order('p.photo_id ASC')
            ->execute('getSlaveRow');

        if (!isset($aPhoto['photo_id'])) {
            return false;
        }

        $aPhoto['link'] = Phpfox::permalink('photo', $aPhoto['photo_id'], $aPhoto['title']);

        return $aPhoto;
    }

    /**
     * Get the previous photo based on the current photo and album we are viewing.
     *
     * @param int $iPhotoId ID of the current photo we are viewing
     * @param string $sType
     * @param int $iItemId
     * @param array $aCallback remove in v4.6
     * @param int $iUserId
     * @return array|bool Array of the previous photo
     * @deprecated from 4.6.0
     */
    public function getNextPhotos($iPhotoId, $sType = null, $iItemId = null, $aCallback = null, $iUserId = 0)
    {
        (($sPlugin = Phpfox_Plugin::get('photo.service_album_album_getpreviousphoto')) ? eval($sPlugin) : false);

        $sView = 'p.view_id = 0';
        if ($iUserId == Phpfox::getUserId() || Phpfox::getUserParam('photo.can_approve_photos')) {
            $sView = 'p.view_id IN(0,1)';
        }

        $aCond = array();
        if ($sType !== null) {
            if ($sType == 'album') {
                $aCond[] = 'p.photo_id < ' . (int)$iPhotoId . ' AND p.album_id = ' . (int)$iItemId . ' AND p.group_id = 0 AND ' . $sView;
            } elseif ($sType == 'group') {
                $aCond[] = 'p.photo_id < ' . (int)$iPhotoId . ' AND p.group_id = ' . (int)$iItemId . ' AND ' . $sView;
            }
        }

        $aPhoto = $this->database()->select(Phpfox::getUserField() . ', p.photo_id, p.destination, p.server_id, p.title, p.mature, p.album_id')
            ->from($this->_sTable, 'p')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')
            ->leftJoin(Phpfox::getT('photo_album'), 'pa', 'pa.album_id = p.album_id')
            ->where($aCond)
            ->order('p.photo_id DESC')
            ->execute('getSlaveRow');

        if (!isset($aPhoto['photo_id'])) {
            return false;
        }

        $aPhoto['link'] = Phpfox::permalink('photo', $aPhoto['photo_id'], $aPhoto['title']);

        return $aPhoto;
    }

    /**
     * @param $iPhotoId
     * @param int $iAlbumId
     * @param null $aCallback
     * @param int $iUserId
     * @param null $iCategory
     * @param int $iOwnerId
     * @return array
     * @deprecated from 4.6.0
     */
    public function getPhotoStream(
        $iPhotoId,
        $iAlbumId = 0,
        $aCallback = null,
        $iUserId = 0,
        $iCategory = null,
        $iOwnerId = 0
    ) {
        if (isset($aCallback['module_id'])) {
            $sQuery = ' AND photo.module_id = \'' . $this->database()->escape($aCallback['module_id']) . '\' AND photo.group_id = ' . (int)$aCallback['item_id'];
        } else {
            $sQuery = ' AND photo.group_id = 0 AND photo.type_id = 0 ';
        }

        if ($iAlbumId > 0) {
            $sQuery = ' AND photo.album_id = ' . (int)$iAlbumId;
        }
        if ($iUserId > 0) {
            $sQuery .= ' AND photo.user_id = ' . (int)$iUserId;
        }

        $bIsProfilePhotoAlbum = false;
        if ($iAlbumId > 0 && ($aAlbum = $this->database()->select('user_id, profile_id')->from(Phpfox::getT('photo_album'))->where('album_id = ' . (int)$iAlbumId)->execute('getSlaveRow')) && $aAlbum['user_id'] == $aAlbum['profile_id']) {
            $bIsProfilePhotoAlbum = true;
        }
        if (!Phpfox::getParam('photo.display_profile_photo_within_gallery') && !$bIsProfilePhotoAlbum) {
            $sQuery .= ' AND photo.is_profile_photo IN (0)';
        }

        // Check permissions
        if ($iAlbumId > 0 && $iOwnerId > 0 && Phpfox::getUserId() == $iOwnerId) {

        } elseif (!Phpfox::isAdmin()) {

            /*
                4 => Custom
                3 => Only Me
                2 => Friends of Friends
                1 => Friends
                0 => Everyone
            */
            $sQuery .= (empty($sQuery) ? '' : ' AND ') . '(';
            $sQuery .= '(photo.privacy = 0)';
            if (Phpfox::getParam('core.section_privacy_item_browsing')) {
                $sQuery .= ' OR ';
                // 3 - "Only me" privacy
                $sQuery .= ' (photo.privacy = 3 AND photo.user_id = ' . Phpfox::getUserId() . ') ';

                // Can view Pending-Approval photos
                if (Phpfox::getUserParam('photo.can_approve_photos') == false) {
                    $sQuery .= ' AND photo.view_id = 0';
                }
                $iCnt = 0;
                $aFriends = array();
                if (Phpfox::isModule('friend')) {
                    list($iCnt, $aFriends) = Friend_Service_Friend::instance()->get(array('AND friend.user_id = ' . (int)Phpfox::getUserId()),
                        '', '', false);
                }
                if ($iCnt > 0) {
                    // 1 - Friends
                    $sFriendsIn = '(';
                    foreach ($aFriends as $aFriend) {
                        $sFriendsIn .= $aFriend['friend_user_id'] . ',';
                    }
                    $sFriendsIn = rtrim($sFriendsIn, ',') . ')';

                    $sQuery .= ' OR (photo.privacy = 1 AND photo.user_id IN ' . $sFriendsIn . ')';

                    if (Phpfox::isModule('friend')) {
                        // 2 - Friends of Friends
                        $aFriendsOfFriends = Friend_Service_Friend::instance()->getFriendsOfFriends($sFriendsIn);
                        if (!empty($aFriendsOfFriends)) {
                            $sIn = implode(',', $aFriendsOfFriends);
                            $sQuery .= ' OR (photo.privacy = 2 AND photo.user_id IN (' . $sIn . '))';
                        }
                        $aInList = Friend_Service_List_List::instance()->getUsersInAnyList();
                        if (!empty($aInList)) {
                            $sIn = implode(',', $aInList);
                            $sQuery .= ' OR (photo.privacy = 4 AND photo.user_id IN (' . $sIn . '))';
                        }
                    }
                } else {
                    $sQuery .= ') AND (photo.photo_id = 0';
                }
            } else {
                $sQuery .= ' AND photo.privacy = 0 AND photo.view_id = 0';
            }
            $sQuery .= ')';
        }

        list($iPreviousCnt, $aPrevious) = $this->_getPhoto('AND photo.photo_id > ' . (int)$iPhotoId . $sQuery, 'ASC',
            (empty($sQuery) ? false : true), $iCategory);
        list($iNextCount, $aNext) = $this->_getPhoto('AND photo.photo_id < ' . (int)$iPhotoId . $sQuery, 'DESC',
            (empty($sQuery) ? false : true), $iCategory);

        return array(
            'total' => ($iNextCount + $iPreviousCnt + 1),
            'current' => ($iPreviousCnt + 1),
            'previous' => $aPrevious,
            'next' => $aNext
        );
    }

    /**
     * @param $aItem
     * @return array|int|string
     * @deprecated from 4.6.0
     */
    public function getInfoForAction($aItem)
    {
        if (is_numeric($aItem)) {
            $aItem = array('item_id' => $aItem);
        }
        $aRow = $this->database()->select('p.photo_id, p.title, p.user_id, u.gender, u.full_name')
            ->from(Phpfox::getT('photo'), 'p')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')
            ->where('p.photo_id = ' . (int)$aItem['item_id'])
            ->execute('getSlaveRow');

        if (empty($aRow)) {
            d($aRow);
            d($aItem);
        }

        $aRow['link'] = Phpfox_Url::instance()->permalink('photo', $aRow['photo_id'], $aRow['title']);
        return $aRow;
    }

    /**
     * @param bool $bInFeed
     * @return array
     */
    public function getUploadParams($bInFeed = false) {
        $iMaxFileSize = Phpfox::getUserParam('photo.photo_max_upload_size');
        $iMaxFileSize = $iMaxFileSize > 0 ? $iMaxFileSize/1024 : 0;
        $iMaxFileSize = Phpfox::getLib('file')->getLimit($iMaxFileSize);
        $aEvents = $bInFeed ? [
            'sending' => '$Core.Photo.dropzoneOnSendingInFeed',
            'success' => '$Core.Photo.dropzoneOnSuccessInFeed',
            'queuecomplete' => '$Core.Photo.dropzoneOnCompleteInFeed',
            'removedfile' => '$Core.Photo.dropzoneOnRemovedFileInFeed',
            'error' => '$Core.Photo.dropzoneOnErrorInFeed'
        ] : [
            'sending' => '$Core.Photo.dropzoneOnSending',
            'success' => '$Core.Photo.dropzoneOnSuccess',
            'removedfile' => '$Core.Photo.dropzoneOnRemovedFileInFeed',
            'queuecomplete' => '$Core.Photo.dropzoneOnComplete',
            'addedfile' => '$Core.Photo.dropzoneOnAddedFile',
            'error' => '$Core.Photo.dropzoneOnError'
        ];
        return [
            'max_size' => ($iMaxFileSize === 0 ? null : $iMaxFileSize),
            'upload_url' => Phpfox::getLib('url')->makeUrl('photo.frame-drag-drop'),
            'component_only' => true,
            'max_file' => Phpfox::getUserParam('photo.max_images_per_upload'),
            'js_events' => $aEvents,
            'upload_now' => "false",
            'submit_button' => $bInFeed ? '#activity_feed_submit' : '',
            'first_description' => _p('drag_n_drop_multi_photos_here_to_upload'),
            'upload_dir' => Phpfox::getParam('photo.dir_photo'),
            'upload_path' => Phpfox::getParam('photo.url_photo'),
            'update_space' => true,
            'type_list' => ['jpg', 'gif', 'png'],
            'on_remove' => $bInFeed ? '' : 'photo.removePhoto',
            'style' => '',
            'extra_description' => [
                _p('maximum_number_of_images_you_can_upload_each_time_is').' '.Phpfox::getUserParam('photo.max_images_per_upload')
            ]
        ];
    }
    /**
     * Apply settings show photo of pages / groups
     * @param $sPrefix
     * @return string
     */
    public function getConditionsForSettingPageGroup($sPrefix = 'p')
    {
        $aModules = [];
        if (Phpfox::getParam('photo.display_photo_album_created_in_group') && Phpfox::isModule('groups')) {
            $aModules[] = 'groups';
        }
        if (Phpfox::getParam('photo.display_photo_album_created_in_page') && Phpfox::isModule('pages')) {
            $aModules[] = 'pages';
        }

        if (count($aModules)) {
            return ' AND ('.$sPrefix.'.module_id IN ("' . implode('","', $aModules) . '") OR '.$sPrefix.'.module_id is NULL)';
        } else {
            return ' AND '.$sPrefix.'.module_id is NULL';
        }
    }
    /**
     * Check if current user is admin of photo's parent item
     * @param $iPhotoId
     * @return bool|mixed
     */
    public function isAdminOfParentItem($iPhotoId)
    {
        $aPhoto = db()->select('photo_id, module_id, group_id')->from($this->_sTable)->where('photo_id = '.(int)$iPhotoId)->execute('getRow');
        if (!$aPhoto) {
            return false;
        }
        if ($aPhoto['module_id'] && Phpfox::hasCallback($aPhoto['module_id'], 'isAdmin')) {
            return Phpfox::callback($aPhoto['module_id'] . '.isAdmin', $aPhoto['group_id']);
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
        if ($sPlugin = Phpfox_Plugin::get('photo.service_photo__call')) {
            eval($sPlugin);
            return null;
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }
}