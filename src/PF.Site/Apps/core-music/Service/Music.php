<?php
/**
 * [PHPFOX_HEADER]
 */

namespace Apps\Core_Music\Service;

use Phpfox;
use Phpfox_File;
use Phpfox_Template;

defined('PHPFOX') or exit('NO DICE!');


class Music extends \Phpfox_Service
{
    private $_aMimeTypes = array('audio/mpeg', 'application/octet-stream');

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('music_song');
    }

    public function getMimeTypes()
    {
        return $this->_aMimeTypes;
    }

    public function getSongs($iUserId, $iAlbumId = null, $iLimit = null, $bCanViewAll = false, $sExtraConds = '')
    {
        $aSongs = $this->database()->select('ms.*, ma.name AS album_url, u.user_name, mp.play_id AS is_on_profile, mp.user_id AS profile_user_id, ' . Phpfox::getUserField())
            ->from($this->_sTable, 'ms')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = ms.user_id')
            ->leftJoin(Phpfox::getT('music_album'), 'ma', 'ma.album_id = ms.album_id')
            ->leftJoin(Phpfox::getT('music_profile'), 'mp',
                'mp.song_id = ms.song_id AND mp.user_id = ' . Phpfox::getUserId())
            ->where(($bCanViewAll === false ? 'ms.view_id = 0 AND' : '') . ($iAlbumId === null ? '' : ' ms.album_id = ' . (int)$iAlbumId . ' AND') . ' ms.user_id = ' . (int)$iUserId. ((!empty($sExtraConds)) ? $sExtraConds : ''))
            ->order('ms.ordering ASC, ms.time_stamp DESC')
            ->limit($iLimit)
            ->execute('getSlaveRows');

        foreach ($aSongs as $iKey => $aSong) {
            $this->getPermissions($aSongs[$iKey]);
            $aSongs[$iKey]['song_path'] = $this->getSongPath($aSong['song_path'], $aSong['server_id']);
        }

        return $aSongs;
    }

    public function getForManage($iUserId, $iAlbumId = null, $iLimit = null, $iPage = 1)
    {
        $iCnt = $this->database()->select('COUNT(*)')
            ->from($this->_sTable, 'ms')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = ms.user_id')
            ->join(Phpfox::getT('music_album'), 'ma', 'ma.album_id = ms.album_id')
            ->where('ms.album_id = ' . (int)$iAlbumId.' AND ms.user_id ='. (int)$iUserId)
            ->execute('getSlaveField');
        $aSongs = [];
        if ($iCnt) {
            $aSongs = $this->database()->select('ms.*, ma.name AS album_url, u.user_name, mp.play_id AS is_on_profile, mp.user_id AS profile_user_id, ' . Phpfox::getUserField())
                ->from($this->_sTable, 'ms')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = ms.user_id')
                ->join(Phpfox::getT('music_album'), 'ma', 'ma.album_id = ms.album_id')
                ->leftJoin(Phpfox::getT('music_profile'), 'mp',
                    'mp.song_id = ms.song_id AND mp.user_id = ' . Phpfox::getUserId())
                ->where('ms.album_id = ' . (int)$iAlbumId.' AND ms.user_id ='. (int)$iUserId)
                ->order('ms.ordering ASC, ms.time_stamp DESC')
                ->limit($iPage, $iLimit, $iCnt)
                ->execute('getSlaveRows');

            foreach ($aSongs as $iKey => $aSong) {
                $this->getPermissions($aSong);
                $aSongs[$iKey] = $aSong;
                $aSongs[$iKey]['song_path'] = $this->getSongPath($aSong['song_path'], $aSong['server_id']);
                $aSongs[$iKey]['genres'] = Phpfox::getService('music.genre')->getGenreDetailBySong($aSong['song_id']);
            }
        }

        return array($iCnt, $aSongs);
    }

    public function getForProfile($iUserId, $iLimit = null)
    {
        $aSongs = $this->database()->select('ms.song_id, ms.user_id, ms.album_id, ms.title, ms.total_play, ms.song_path, ms.is_featured, ms.view_id, ms.server_id, ms.explicit, ms.duration, ms.time_stamp, ma.name AS album_url, u.user_name, mp.play_id AS is_on_profile, mp.user_id AS profile_user_id, ' . Phpfox::getUserField())
            ->from(Phpfox::getT('music_profile'), 'mp')
            ->join($this->_sTable, 'ms', 'ms.song_id = mp.song_id AND ms.view_id = 0 AND ms.privacy = 0')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = ms.user_id')
            ->leftJoin(Phpfox::getT('music_album'), 'ma', 'ma.album_id = ms.album_id')
            ->where('mp.user_id = ' . (int)$iUserId)
            ->order('ms.time_stamp DESC')
            ->limit($iLimit)
            ->execute('getSlaveRows');

        foreach ($aSongs as $iKey => $aSong) {
            $aSongs[$iKey]['song_path'] = $this->getSongPath($aSong['song_path'], $aSong['server_id']);
        }

        return $aSongs;
    }

    public function getForEdit($iId, $bForce = false)
    {
        $aRow = $this->database()->select('*')
            ->from($this->_sTable)
            ->where('song_id = ' . (int)$iId)
            ->execute('getSlaveRow');

        $aGenres = (array)db()->select('genre_id')
            ->from(':music_genre_data')
            ->where('song_id =' . (int)$iId)
            ->execute('getSlaveRows');
        if (count($aGenres)) {
            foreach ($aGenres as $aGenre) {
                $aRow['genres'][] = $aGenre['genre_id'];
            }
        } else {
            $aRow['genres'] = [];
        }

        if (!isset($aRow['song_id'])) {
            return \Phpfox_Error::display(_p('unable_to_find_the_song_you_are_looking_for_dot'));
        }

        if (!empty($aRow['image_path'])) {
            $aRow['current_image'] = Phpfox::getLib('image.helper')->display(array(
                    'server_id' => $aRow['image_server_id'],
                    'path' => 'music.url_image',
                    'file' => $aRow['image_path'],
                    'suffix' => '_200_square',
                    'return_url' => true
                )
            );
        }

        $aRow['params'] = [
            'id' => $aRow['song_id'],
        ];
        if ((($aRow['user_id'] == Phpfox::getUserId() && Phpfox::getUserParam('music.can_edit_own_song')) || Phpfox::getUserParam('music.can_edit_other_song')) || $bForce === true) {
            return $aRow;
        }

        return \Phpfox_Error::display(_p('unable_to_edit_this_song_dot'));
    }

    public function getSong($iSongId, $bFullPath = true)
    {
        if (Phpfox::isModule('track')) {
            $sJoinQuery = Phpfox::isUser() ? 'pt.user_id = ' . Phpfox::getUserBy('user_id') : 'pt.ip_address = \'' . $this->database()->escape(Phpfox::getIp()) . '\'';
            $this->database()->select('pt.item_id AS is_viewed, ')
                ->leftJoin(Phpfox::getT('track'), 'pt',
                'pt.item_id = ms.song_id AND pt.type_id=\'music_song\' AND ' . $sJoinQuery);
        }
        if (Phpfox::isModule('like')) {
            $this->database()->select('lik.like_id AS is_liked, ')
                ->leftJoin(Phpfox::getT('like'), 'lik',
                    'lik.type_id = \'music_song\' AND lik.item_id = ms.song_id AND lik.user_id = ' . Phpfox::getUserId());
        }
        if (Phpfox::isModule('friend')) {
            $this->database()->select('f.friend_id AS is_friend, ')->leftJoin(Phpfox::getT('friend'), 'f',
                "f.user_id = ms.user_id AND f.friend_user_id = " . Phpfox::getUserId());
        }

        $aSong = $this->database()->select('ms.*,' . (Phpfox::getParam('core.allow_html') ? 'ms.description_parsed' : 'ms.description') . ' AS description, ms.total_comment as song_total_comment, ms.total_play as song_total_play, ms.time_stamp as song_time_stamp, ms.is_sponsor AS song_is_sponsor, ma.name AS album_url, mp.play_id AS is_on_profile, mp.user_id AS profile_user_id, ' . Phpfox::getUserField())
            ->from($this->_sTable, 'ms')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = ms.user_id')
            ->leftJoin(Phpfox::getT('music_album'), 'ma', 'ma.album_id = ms.album_id')
            ->leftJoin(Phpfox::getT('music_profile'), 'mp',
                'mp.song_id = ms.song_id AND mp.user_id = ' . Phpfox::getUserId())
            ->where('ms.song_id = ' . (int)$iSongId)
            ->execute('getSlaveRow');

        if (!isset($aSong['song_id'])) {
            return false;
        }

        $aSong['genres'] = Phpfox::getService('music.genre')->getGenreDetailBySong($aSong['song_id']);
        if ($bFullPath) {
            $aSong['song_path'] = $this->getSongPath($aSong['song_path'], $aSong['server_id']);
        }
        $aSong['bookmark'] = \Phpfox_Url::instance()->permalink('music', $aSong['song_id'], $aSong['title']);
        if (!isset($aSong['song_total_comment'])) {
            $aSong['song_total_comment'] = 0;
        }
        if (!isset($aSong['is_liked'])) {
            $aSong['is_liked'] = false;
        }
        if (!isset($aSong['is_viewed'])) {
            $aSong['is_viewed'] = 0;
        }
        if (!isset($aSong['is_friend'])) {
            $aSong['is_friend'] = 0;
        }
        return $aSong;
    }

    public function getSongPath($sSong, $iServerId = null)
    {
        if (preg_match("/\{file\/music_folder\/(.*)\.mp3\}/i", $sSong, $aMatches)) {
            return Phpfox::getParam('core.path') . str_replace(array('{', '}'), '', $aMatches[0]);
        }
        $sSong = Phpfox::getParam('music.url') . sprintf($sSong, '');

        $sTempSong = Phpfox::getLib('cdn')->getUrl($sSong, $iServerId);
        if (!empty($sTempSong)) {
            $sSong = $sTempSong;
        }

        return $sSong;
    }

    public function getLatestSongs()
    {
        $aSongs = $this->database()->select('ms.song_id, ms.user_id, ms.album_id, ms.title, ms.song_path, ms.server_id, ms.explicit, ms.duration, ms.time_stamp, ma.name AS album_url, u.user_name, mp.play_id AS is_on_profile, mp.user_id AS profile_user_id, ' . Phpfox::getUserField())
            ->from($this->_sTable, 'ms')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = ms.user_id')
            ->leftJoin(Phpfox::getT('music_album'), 'ma', 'ma.album_id = ms.album_id')
            ->leftJoin(Phpfox::getT('music_profile'), 'mp',
                'mp.song_id = ms.song_id AND mp.user_id = ' . Phpfox::getUserId())
            ->where('ms.view_id = 0')
            ->order('ms.time_stamp DESC')
            ->limit(10)
            ->execute('getSlaveRows');

        foreach ($aSongs as $iKey => $aSong) {
            $aSongs[$iKey]['song_path'] = $this->getSongPath($aSong['song_path'], $aSong['server_id']);
        }

        return $aSongs;
    }

    public function getFeaturedSongs($iLimit = 4, $iCacheTime = 5)
    {
        $sCacheId = $this->cache()->set('music_song_featured');

        if (!($sSongIds = $this->cache()->get($sCacheId, $iCacheTime))) {
            $sConds = 'ms.view_id = 0 AND ms.is_featured = 1';
            $sConds .= $this->getConditionsForSettingPageGroup('ms');
            $aSongIds = $this->database()->select('ms.song_id')
                ->from($this->_sTable, 'ms')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = ms.user_id')
                ->leftJoin(Phpfox::getT('music_album'), 'ma', 'ma.album_id = ms.album_id')
                ->where($sConds)
                ->order('rand()')
                ->limit(Phpfox::getParam('core.cache_total'))
                ->execute('getSlaveRows');

            foreach ($aSongIds as $key => $aId) {
                if ($key != 0) {
                    $sSongIds .= ',' . $aId['song_id'];
                } else {
                    $sSongIds = $aId['song_id'];
                }
            }
            if ($iCacheTime) {
                $this->cache()->save($sCacheId, $sSongIds);
            }
        }
        if (empty($sSongIds)) {
            return [];
        }
        $aSongIds = explode(',', $sSongIds);
        shuffle($aSongIds);
        $aSongIds = array_slice($aSongIds, 0, round($iLimit * Phpfox::getParam('core.cache_rate')));
        $aSongs = $this->database()->select('ms.song_id, ms.user_id, ms.album_id, ms.title, ms.total_play, ms.total_view, ms.song_path, ms.server_id, ms.explicit, ms.duration, ms.time_stamp, ms.image_path, ms.image_server_id, ' . Phpfox::getUserField())
            ->from($this->_sTable, 'ms')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = ms.user_id')
            ->leftJoin(Phpfox::getT('music_album'), 'ma', 'ma.album_id = ms.album_id')
            ->where('ms.song_id IN (' . implode(',', $aSongIds) . ')')
            ->limit($iLimit)
            ->execute('getSlaveRows');

        if (!is_array($aSongs)) {
            return array();
        }
        shuffle($aSongs);
        foreach ($aSongs as $iKey => $aSong) {
            $aSongs[$iKey]['url'] = \Phpfox_Url::instance()->permalink('music', $aSong['song_id'], $aSong['title']);
            $aSongs[$iKey]['song_path'] = $this->getSongPath($aSong['song_path'], $aSong['server_id']);
        }


        return $aSongs;
    }


    public function getRandomSponsoredSongs($iLimit = 4, $iCacheTime = 5)
    {
        $sCacheId = $this->cache()->set('music_song_sponsored');
        if (!($sSongIds = $this->cache()->get($sCacheId, $iCacheTime))) {
            $sConds = 'ms.view_id = 0 AND ms.is_sponsor = 1 AND s.module_id = "music_song" AND s.is_active = 1';
            $sConds .= $this->getConditionsForSettingPageGroup('ms');
            $aSongIds = $this->database()->select('ms.song_id')
                ->from($this->_sTable, 'ms')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = ms.user_id')
                ->join(Phpfox::getT('ad_sponsor'), 's', 's.item_id = ms.song_id')
                ->where($sConds)
                ->order('rand()')
                ->limit(Phpfox::getParam('core.cache_total'))
                ->execute('getSlaveRows');
            foreach ($aSongIds as $key => $aId) {
                if ($key != 0) {
                    $sSongIds .= ',' . $aId['song_id'];
                } else {
                    $sSongIds = $aId['song_id'];
                }
            }
            if ($iCacheTime) {
                $this->cache()->save($sCacheId, $sSongIds);
            }
        }
        if (empty($sSongIds)) {
            return [];
        }
        $aSongIds = explode(',', $sSongIds);
        shuffle($aSongIds);
        $aSongIds = array_slice($aSongIds, 0, round($iLimit * Phpfox::getParam('core.cache_rate')));

        $aSongs = $this->database()->select(Phpfox::getUserField().', ms.*, u.user_name, s.*, ms.server_id as song_server_id, ms.total_view as total_view_song')
            ->from($this->_sTable, 'ms')
            ->leftJoin(Phpfox::getT('music_album'), 'ma', 'ma.album_id = ms.album_id')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = ms.user_id')
            ->join(Phpfox::getT('ad_sponsor'), 's', 's.item_id = ms.song_id AND s.module_id = \'music_song\'')
            ->where('ms.song_id IN (' . implode(',', $aSongIds) . ')')
            ->limit($iLimit)
            ->execute('getSlaveRows');

        if (!isset($aSongs[0]) || empty($aSongs[0])) {
            return array();
        }
        if (Phpfox::isModule('ad')) {
            $aSongs = Phpfox::getService('ad')->filterSponsor($aSongs);
        }

        shuffle($aSongs);
        if (count($aSongs)) {
            foreach ($aSongs as $key => $aSong) {
                $aSongs[$key]['total_view'] = $aSong['total_view_song'];
                $aSongs[$key]['song_path'] = $this->getSongPath($aSong['song_path'], $aSong['song_server_id']);
            }
        }
        return $aSongs;
    }

    public function getRandomSponsoredAlbum($iLimit = 4, $iCacheTime = 5)
    {
        $sCacheId = $this->cache()->set('music_album_sponsored');
        if (!($sAlbumIds = $this->cache()->get($sCacheId, $iCacheTime))) {
            $sConds = 'm.view_id = 0 AND m.is_sponsor = 1 AND s.module_id = "music_album" AND s.is_active = 1';
            $sConds .= $this->getConditionsForSettingPageGroup('m');
            $aAlbumIds = $this->database()->select('m.album_id')
                ->from(Phpfox::getT('music_album'), 'm')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = m.user_id')
                ->join(Phpfox::getT('ad_sponsor'), 's', 's.item_id = m.album_id')
                ->where($sConds)
                ->order('rand()')
                ->limit(Phpfox::getParam('core.cache_total'))
                ->execute('getSlaveRows');
            foreach ($aAlbumIds as $key => $aId) {
                if ($key != 0) {
                    $sAlbumIds .= ',' . $aId['album_id'];
                } else {
                    $sAlbumIds = $aId['album_id'];
                }
            }
            if ($iCacheTime) {
                $this->cache()->save($sCacheId, $sAlbumIds);
            }
        }
        if (empty($sAlbumIds)) {
            return [];
        }
        $aAlbumIds = explode(',', $sAlbumIds);
        shuffle($aAlbumIds);
        $aAlbumIds = array_slice($aAlbumIds, 0, round($iLimit * Phpfox::getParam('core.cache_rate')));
        $aAlbums = $this->database()->select(Phpfox::getUserField() . ', s.*, m.name, m.year, m.total_track, m.total_play, m.total_view as total_view_album, m.server_id, m.image_path, m.user_id')
            ->from(Phpfox::getT('music_album'), 'm')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = m.user_id')
            ->join(Phpfox::getT('ad_sponsor'), 's', 's.item_id = m.album_id AND s.module_id = \'music_album\'')
            ->where('m.album_id IN (' . implode(',', $aAlbumIds) . ')')
            ->limit($iLimit)
            ->execute('getSlaveRows');
        if (empty($aAlbums) || !is_array($aAlbums)) {
            return array();
        }
        if (Phpfox::isModule('ad')) {
            $aAlbums = Phpfox::getService('ad')->filterSponsor($aAlbums);
        }
        shuffle($aAlbums);
        foreach ($aAlbums as $key => $aAlbum) {
            $aAlbums[$key]['total_view'] = $aAlbum['total_view_album'];
        }

        return $aAlbums;
    }

    public function getPendingTotal()
    {
        return (int)$this->database()->select('COUNT(*)')
            ->from($this->_sTable)
            ->where('view_id = 1')
            ->execute('getSlaveField');
    }

    public function getSectionMenu()
    {

        $aFilterMenu = array();
        if (!defined('PHPFOX_IS_USER_PROFILE')) {
            $iMySongsTotal = $this->getMySongTotal();
            $iMyAlbumsTotal = Phpfox::getService('music.album')->getMyAlbumTotal();
            $aFilterMenu = array(
                _p('all_songs') => '',
                _p('my_songs') . ($iMySongsTotal ? ('<span class="my count-item">' . (($iMySongsTotal > 99) ? '99+' : $iMySongsTotal) . '</span>') : '') => 'my',
                _p('all_albums') => 'music.browse.album',
                _p('my_albums') . ($iMyAlbumsTotal ? ('<span class="my count-item">' . (($iMyAlbumsTotal > 99) ? '99+' : $iMyAlbumsTotal) . '</span>') : '') => 'music.browse.album.view_my-album',
            );

            if (Phpfox::isModule('friend') && !Phpfox::getParam('core.friends_only_community')) {

                $aFilterMenu[_p('friends_songs')] = 'friend';
            }
            $aFilterMenu[] = true;

            if (Phpfox::getUserParam('music.can_approve_songs')) {
                $iPendingTotal = \Phpfox::getService('music')->getPendingTotal();

                if ($iPendingTotal) {
                    $aFilterMenu[_p('pending_songs') . '<span class="pending count-item">' . (($iPendingTotal > 99) ? '99+' : $iPendingTotal) . '</span>'] = 'pending';
                }
            }
        }
        Phpfox_Template::instance()->buildSectionMenu('music', $aFilterMenu);
    }

    /**
     * @deprecated This function will be removed in 4.6.0
     * @param $aItem
     * @return array|int|string
     */
    public function getInfoForAction($aItem)
    {
        // now we check if its a music-album or a music-song
        if ($aItem['item_type_id'] == 'music_song') {
            $aRow = $this->database()->select('ms.song_id, ms.title, ms.user_id, u.gender, u.full_name')
                ->from(Phpfox::getT('music_song'), 'ms')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = ms.user_id')
                ->where('ms.song_id = ' . (int)$aItem['item_id'])
                ->execute('getSlaveRow');
            $aRow['link'] = \Phpfox_Url::instance()->permalink('music', $aRow['song_id'], $aRow['title']);
            return $aRow;
        }

        // else its a music-album
        $aRow = $this->database()->select('ma.album_id, ma.name as title, ma.user_id, u.gender, u.full_name')
            ->from(Phpfox::getT('music_album'), 'ma')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = ma.user_id')
            ->where('ma.album_id = ' . (int)$aItem['item_id'])
            ->execute('getSlaveRow');
        $aRow['link'] = \Phpfox_Url::instance()->permalink('music.album', $aRow['album_id'], $aRow['title']);
        return $aRow;

    }

    /**
     * @return int
     */
    public function getMySongTotal()
    {
        $sWhere = 'user_id = ' . Phpfox::getUserId();
        $aModules = ['user'];
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

    /**
     * @param $aRow
     */
    public function getPermissions(&$aRow)
    {
        $aRow['canEdit'] = (($aRow['user_id'] == Phpfox::getUserId() && Phpfox::getUserParam('music.can_edit_own_song')) || Phpfox::getUserParam('music.can_edit_other_song'));
        $aRow['canDelete'] = $this->canDelete($aRow);
        $aRow['canSponsorInFeed'] = (Phpfox::isModule('ad') && $aRow['user_id'] == Phpfox::getUserId() && Phpfox::isModule('feed') && (Phpfox::getUserParam('feed.can_purchase_sponsor') || Phpfox::getUserParam('feed.can_sponsor_feed')) && (Phpfox::getService('feed')->canSponsoredInFeed('music_song',
                $aRow['song_id'])));
        $aRow['iSponsorInFeedId'] = Phpfox::getService('feed')->canSponsoredInFeed('music_song', $aRow['song_id']);
        $aRow['canSponsor'] = (Phpfox::isModule('ad') && Phpfox::getUserParam('music.can_sponsor_song'));
        $aRow['canPurchaseSponsor'] = (Phpfox::isModule('ad') && $aRow['user_id'] == Phpfox::getUserId() && Phpfox::getUserParam('music.can_purchase_sponsor_song'));
        $aRow['canApprove'] = (Phpfox::getUserParam('music.can_approve_songs') && $aRow['view_id'] == 1);
        $aRow['canFeature'] = (Phpfox::getUserParam('music.can_feature_songs') && $aRow['view_id'] == 0);
        $aRow['canDownload'] = Phpfox::getUserParam('music.can_download_songs');
        $aRow['hasPermission'] = ($aRow['canEdit'] || $aRow['canDelete'] || $aRow['canSponsor'] || $aRow['canApprove'] || $aRow['canFeature'] || $aRow['canPurchaseSponsor'] || $aRow['canSponsorInFeed']);
    }

    public function canDelete($aRow)
    {
        $bCanDelete = (($aRow['user_id'] == Phpfox::getUserId() && user('music.can_delete_own_track')) || user('music.can_delete_other_tracks'));
        if (!$bCanDelete && Phpfox::isModule($aRow['module_id'])) {
            if ($aRow['module_id'] == 'pages' && Phpfox::getService('pages')->isAdmin($aRow['item_id'])) {
                $bCanDelete = true; // is owner of page
            } elseif ($aRow['module_id'] == 'groups' && Phpfox::getService('groups')->isAdmin($aRow['item_id'])) {
                $bCanDelete = true; // is owner of group
            }
        }
        return $bCanDelete;
    }

    public function getSuggestSongs($aSong, $iLimit = 4)
    {
        $sWhere = 'ms.view_id = 0 AND ms.privacy = 0 AND ms.song_id <> '.$aSong['song_id'];
        $aGenreIds = [];
        if (isset($aSong['genres']) && count($aSong['genres'])) {
            foreach ($aSong['genres'] as $aGenre) {
                $aGenreIds[] = $aGenre['genre_id'];
            }
            $sWhere .= ' AND mgd.genre_id IN (' . implode(',', $aGenreIds) . ')';
        }
        $sWhere .= $this->getConditionsForSettingPageGroup('ms');
        $aSongs = $this->database()->select('DISTINCT ms.song_id, ms.user_id, ms.album_id, ms.title, ms.total_play, ms.total_view, ms.song_path, ms.server_id, ms.explicit, ms.duration, ms.time_stamp, ms.image_path, ms.image_server_id, ' . Phpfox::getUserField())
            ->from($this->_sTable, 'ms')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = ms.user_id')
            ->leftJoin(Phpfox::getT('music_album'), 'ma', 'ma.album_id = ms.album_id')
            ->join(':music_genre_data', 'mgd', 'mgd.song_id = ms.song_id')
            ->where($sWhere)
            ->limit($iLimit)
            ->order('ms.time_stamp DESC')
            ->execute('getSlaveRows');
        foreach ($aSongs as $iKey => $aSong) {
            $aSongs[$iKey]['url'] = \Phpfox_Url::instance()->permalink('music', $aSong['song_id'], $aSong['title']);
            $aSongs[$iKey]['song_path'] = $this->getSongPath($aSong['song_path'], $aSong['server_id']);
        }

        if (!is_array($aSongs)) {
            return array();
        }

        shuffle($aSongs);


        return $aSongs;
    }

    /**
      * Apply settings show music of pages / groups
     * @param string $sPrefix
     * @return string
     */
    public function getConditionsForSettingPageGroup($sPrefix = 'ms')
    {
        $aModules = [];
        // Apply settings show music of pages / groups
        if (Phpfox::getParam('music.music_display_music_created_in_group') && Phpfox::isModule('groups')) {
            $aModules[] = 'groups';
        }
        if (Phpfox::getParam('music.music_display_music_created_in_page') && Phpfox::isModule('pages')) {
            $aModules[] = 'pages';
        }
        if(count($aModules))
        {
            return ' AND ('.$sPrefix.'.module_id IN (\'' . implode('\',\'', $aModules) . '\') OR '.$sPrefix.'.module_id is NULL)';
        }
        else{
            return ' AND '.$sPrefix.'.module_id is NULL';
        }
    }

    /**
     * @return array
     */
    public function getUploadParams() {
        $iMaxFileSize = Phpfox::getUserParam('music.music_max_file_size');
        $iMaxFileSize = Phpfox::getLib('file')->getLimit($iMaxFileSize);

        $iMaxFileUpload = Phpfox::getUserParam('music.max_songs_per_upload');

        $aEvents = [
            'sending'=> '$Core.music.dropzoneOnSending',
            'success' => '$Core.music.dropzoneOnSuccess',
            'error' => '$Core.music.dropzoneOnError',
            'addedfile' => '$Core.music.dropzoneAddedFile',
            'queuecomplete' => '$Core.music.dropzoneQueueComplete',
        ];

        return [
            'max_size' => ($iMaxFileSize === 0 ? null : $iMaxFileSize),
            'upload_url' => Phpfox::getLib('url')->makeUrl('music.frame'),
            'component_only' => true,
            'max_file' => $iMaxFileUpload,
            'js_events' => $aEvents,
            'upload_now' => "true",
            'first_description' => _p('drag_and_drop_to_upload_songs'),
            'upload_dir' => Phpfox::getParam('music.dir'),
            'upload_path' => Phpfox::getParam('music.url'),
            'update_space' => true,
            'type_list' => ['mp3'],
            'type_description' => $iMaxFileUpload ? _p('maximum_number_of_songs_you_can_upload_each_time_is_max', ['max' => $iMaxFileUpload]) : '',
            'max_size_description' => _p('select_an_mp3') . ($iMaxFileSize ? ' ' . _p('the_file_size_limit_is_size', ['size' => Phpfox_File::instance()->filesize($iMaxFileSize*1048576)]): ''),
            'type_list_string' => 'audio/mpeg3, audio/mp3, audio/mpeg',
            'upload_icon' => 'ico ico-upload-cloud',
            'preview_template' => '<div style="display:none"></div>',
            'keep_form' => true,
            'style' => ''
        ];
    }

    /**
     * @param null $aParams
     * @return array
     */
    public function getUploadPhotoParams($aParams = null) {
        $iMaxFileSize = Phpfox::getUserParam('photo.photo_max_upload_size');
        $iMaxFileSize = $iMaxFileSize > 0 ? $iMaxFileSize/1024 : 0;
        $iMaxFileSize = Phpfox::getLib('file')->getLimit($iMaxFileSize);
        $aCallback = [
            'max_size' => ($iMaxFileSize === 0 ? null : $iMaxFileSize),
            'type_list' => ['jpg', 'jpeg', 'gif', 'png'],
            'upload_dir' => Phpfox::getParam('music.dir_image'),
            'upload_path' => Phpfox::getParam('music.url_photo'),
            'thumbnail_sizes' => Phpfox::getParam('music.thumbnail_sizes'),
            'label' => _p('photo')
        ];
        if (isset($aParams['id'])) {
            $aCallback['type'] = 'music_song_'.$aParams['id'];
        }
        return $aCallback;
    }
    /**
     * Check if current user is admin of photo's parent item
     * @param $iSongId
     * @return bool|mixed
     */
    public function isAdminOfParentItem($iSongId)
    {
        $aSong = db()->select('song_id, module_id, item_id')->from($this->_sTable)->where('song_id = '.(int)$iSongId)->execute('getRow');
        if (!$aSong) {
            return false;
        }
        if ($aSong['module_id'] && Phpfox::hasCallback($aSong['module_id'], 'isAdmin')) {
            return Phpfox::callback($aSong['module_id'] . '.isAdmin', $aSong['item_id']);
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
        if ($sPlugin = \Phpfox_Plugin::get('music.service_music__call')) {
            eval($sPlugin);
            return null;
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        \Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }
}