<?php
/**
 * [PHPFOX_HEADER]
 */

namespace Apps\Core_Music\Service\Album;

use Phpfox;
use Phpfox_Error;
use Phpfox_Plugin;
use Phpfox_Service;

defined('PHPFOX') or exit('NO DICE!');

class Process extends Phpfox_Service
{

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('music_album');
    }

    public function add($aVals)
    {
        if (empty($aVals['privacy'])) {
            $aVals['privacy'] = 0;
        }

        if (empty($aVals['privacy_comment'])) {
            $aVals['privacy_comment'] = 0;
        }
        $bHasAttachments = (!empty($aVals['attachment']));
        Phpfox::getService('ban')->checkAutomaticBan($aVals['name'] . ' ' . $aVals['text']);
        $aInsert = array(
            'view_id' => 0,
            'privacy' => (isset($aVals['privacy']) ? $aVals['privacy'] : '0'),
            'privacy_comment' => (isset($aVals['privacy_comment']) ? $aVals['privacy_comment'] : '0'),
            'user_id' => Phpfox::getUserId(),
            'name' => $this->preParse()->clean($aVals['name'], 255),
            'year' => $aVals['year'],
            'module_id' => (isset($aVals['callback_module']) ? $aVals['callback_module'] : null),
            'item_id' => (isset($aVals['callback_item_id']) ? (int)$aVals['callback_item_id'] : '0'),
            'time_stamp' => PHPFOX_TIME
        );
        if (!empty($aVals['temp_file'])) {
            $aFile = Phpfox::getService('core.temp-file')->get($aVals['temp_file']);
            if (!empty($aFile)) {
                if (!Phpfox::getService('user.space')->isAllowedToUpload($aInsert['user_id'], $aFile['size'])) {
                    Phpfox::getService('core.temp-file')->delete($aVals['temp_file'], true);
                    return false;
                }
                $aInsert['image_path'] = $aFile['path'];
                $aInsert['server_id'] = $aFile['server_id'];
                Phpfox::getService('user.space')->update($aInsert['user_id'], 'music_image', $aFile['size']);
                Phpfox::getService('core.temp-file')->delete($aVals['temp_file']);
            }
        }
        $iId = $this->database()->insert($this->_sTable, $aInsert);

        if (!$iId) {
            return false;
        }

        $this->database()->insert(Phpfox::getT('music_album_text'), array(
                'album_id' => $iId,
                'text' => (empty($aVals['text']) ? null : $this->preParse()->clean($aVals['text'])),
                'text_parsed' => (empty($aVals['text']) ? null : $this->preParse()->prepare($aVals['text']))
            )
        );
        //Add hashtag
        if (Phpfox::isModule('tag') && Phpfox::getParam('tag.enable_hashtag_support')) {
            Phpfox::getService('tag.process')->add('music_album', $iId, Phpfox::getUserId(), $aVals['text'], true);
        } else {
            if (Phpfox::isModule('tag') && isset($aVals['tag_list']) && ((is_array($aVals['tag_list']) && count($aVals['tag_list'])) || (!empty($aVals['tag_list'])))) {
                Phpfox::getService('tag.process')->add('music_album', $iId, Phpfox::getUserId(), $aVals['tag_list']);
            }
        }
        // If we uploaded any attachments make sure we update the 'item_id'
        if ($bHasAttachments) {
            Phpfox::getService('attachment.process')->updateItemId($aVals['attachment'], Phpfox::getUserId(), $iId);
        }

        if ($aVals['privacy'] == '4') {
            Phpfox::getService('privacy.process')->add('music_album', $iId,
                (isset($aVals['privacy_list']) ? $aVals['privacy_list'] : array()));
        }
        $aCallback = null;
        if (!empty($aVals['callback_module']) && Phpfox::hasCallback($aVals['callback_module'], 'uploadSong')) {
            $aCallback = Phpfox::callback($aVals['callback_module'] . '.uploadSong', $aVals['callback_item_id']);
        }
        (Phpfox::isModule('feed') ? $iFeedId = Phpfox::getService('feed.process')->callback($aCallback)->add('music_album',
            $iId, $aVals['privacy'], (isset($aVals['privacy_comment']) ? (int)$aVals['privacy_comment'] : 0),
            (isset($aVals['callback_item_id']) ? (int)$aVals['callback_item_id'] : '0')) : null);
        return $iId;
    }

    /**
     * @todo Security checks. Users perms.
     *
     * @param mixed $iId
     * @param mixed $aVals
     * @return mixed
     */
    public function update($iId, $aVals)
    {
        $aAlbum = $this->database()->select('*')
            ->from($this->_sTable)
            ->where('album_id = ' . (int)$iId)
            ->execute('getSlaveRow');

        if (!isset($aAlbum['album_id'])) {
            return Phpfox_Error::set(_p('unable_to_find_the_album_you_want_to_edit'));
        }

        Phpfox::getService('ban')->checkAutomaticBan($aVals['name'] . ' ' . $aVals['text']);

        if (($aAlbum['user_id'] == Phpfox::getUserId() && Phpfox::getUserParam('music.can_edit_own_albums')) || Phpfox::getUserParam('music.can_edit_other_music_albums')) {
            if (empty($aVals['privacy'])) {
                $aVals['privacy'] = 0;
            }

            if (empty($aVals['privacy_comment'])) {
                $aVals['privacy_comment'] = 0;
            }
            $bHasAttachments = (!empty($aVals['attachment']));
            if ($bHasAttachments) {
                Phpfox::getService('attachment.process')->updateItemId($aVals['attachment'], Phpfox::getUserId(), $iId);
            }
            if (Phpfox::isModule('tag') && Phpfox::getParam('tag.enable_hashtag_support')) {
                Phpfox::getService('tag.process')->update('music_album', $iId, $aAlbum['user_id'], $aVals['text'],
                    true);
            } else {
                if (Phpfox::isModule('tag')) {
                    Phpfox::getService('tag.process')->update('music_album', $iId, $aAlbum['user_id'],
                        (!Phpfox::getLib('parse.format')->isEmpty($aVals['tag_list']) ? $aVals['tag_list'] : null));
                }
            }
            $aUpdate = [
                'privacy' => (isset($aVals['privacy']) ? $aVals['privacy'] : '0'),
                'privacy_comment' => (isset($aVals['privacy_comment']) ? $aVals['privacy_comment'] : '0'),
                'name' => $this->preParse()->clean($aVals['name'], 255),
                'year' => $aVals['year'],
                'total_attachment' => (Phpfox::isModule('attachment') ? Phpfox::getService('attachment')->getCountForItem($iId,
                    'music_album') : '0')
            ];
            if (!empty($aAlbum['image_path']) && (!empty($aVals['temp_file']) || !empty($aVals['remove_photo']))) {
                if ($this->deleteImage($iId)) {
                    $aUpdate['image_path'] = null;
                    $aUpdate['server_id'] = 0;
                }
                else {
                    return false;
                }
            }

            if (!empty($aVals['temp_file'])) {
                $aFile = Phpfox::getService('core.temp-file')->get($aVals['temp_file']);
                if (!empty($aFile)) {
                    if (!Phpfox::getService('user.space')->isAllowedToUpload($aAlbum['user_id'], $aFile['size'])) {
                        Phpfox::getService('core.temp-file')->delete($aVals['temp_file'], true);
                        return false;
                    }
                    $aUpdate['image_path'] = $aFile['path'];
                    $aUpdate['server_id'] = $aFile['server_id'];
                    Phpfox::getService('user.space')->update($aAlbum['user_id'], 'music_image', $aFile['size']);
                    Phpfox::getService('core.temp-file')->delete($aVals['temp_file']);
                }
            }

            $this->database()->update($this->_sTable, $aUpdate, 'album_id = ' . $aAlbum['album_id']);

            $this->database()->update(Phpfox::getT('music_album_text'), array(
                'text' => (empty($aVals['text']) ? null : $this->preParse()->clean($aVals['text'])),
                'text_parsed' => (empty($aVals['text']) ? null : $this->preParse()->prepare($aVals['text']))
            ), 'album_id = ' . $aAlbum['album_id']
            );

            $aSongs = $this->database()->select('song_id, user_id')
                ->from(Phpfox::getT('music_song'))
                ->where('album_id = ' . (int)$aAlbum['album_id'])
                ->execute('getSlaveRows');

            if (count($aSongs)) {
                foreach ($aSongs as $aSong) {
                    $this->database()->update(Phpfox::getT('music_song'), array(
                        'privacy' => (isset($aVals['privacy']) ? $aVals['privacy'] : '0'),
                        'privacy_comment' => (isset($aVals['privacy_comment']) ? $aVals['privacy_comment'] : '0'),
                    ), 'song_id = ' . $aSong['song_id']
                    );

                    (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->update('music_song',
                        $aSong['song_id'], $aVals['privacy'], $aVals['privacy_comment'], 0, $aSong['user_id']) : null);

                    if (Phpfox::isModule('privacy')) {
                        if ($aVals['privacy'] == '4') {
                            Phpfox::getService('privacy.process')->update('music_song', $aSong['song_id'],
                                (isset($aVals['privacy_list']) ? $aVals['privacy_list'] : array()));
                        } else {
                            Phpfox::getService('privacy.process')->delete('music_song', $aSong['song_id']);
                        }
                    }
                }
            }

            if (Phpfox::isModule('privacy')) {
                if ($aVals['privacy'] == '4') {
                    Phpfox::getService('privacy.process')->update('music_album', $iId,
                        (isset($aVals['privacy_list']) ? $aVals['privacy_list'] : array()));
                } else {
                    Phpfox::getService('privacy.process')->delete('music_album', $iId);
                }
            }

            if (!empty($_FILES['mp3']['name'])) {
                if (empty($aVals['title'])) {
                    return Phpfox_Error::set(_p('provide_a_title_for_this_track'));
                }

                if (!Phpfox::getService('music.process')->upload($aVals, $aAlbum['album_id'])) {
                    return false;
                }
            }
            (($sPlugin = Phpfox_Plugin::get('music.service_album_process_update__1')) ? eval($sPlugin) : false);
            return true;
        }

        return Phpfox_Error::set(_p('unable_to_edit_this_album'));
    }

    public function deleteImage($iId, &$aAlbum = null)
    {
        $bSkip = true;
        if ($aAlbum === null) {
            $bSkip = false;
            $aAlbum = $this->database()->select('album_id, user_id, image_path, server_id')
                ->from($this->_sTable)
                ->where('album_id = ' . (int)$iId)
                ->execute('getSlaveRow');

            if (!isset($aAlbum['album_id'])) {
                return false;
            }
        }

        if (empty($aAlbum['image_path'])) {
            return null;
        }

        if ($bSkip || (($aAlbum['user_id'] == Phpfox::getUserId() && Phpfox::getUserParam('music.can_edit_own_albums')) || Phpfox::getUserParam('music.can_edit_other_music_albums'))) {
            $aParams = Phpfox::getService('music')->getUploadPhotoParams();
            $aParams['type'] = 'music_image';
            $aParams['path'] = $aAlbum['image_path'];
            $aParams['user_id'] = $aAlbum['user_id'];
            $aParams['update_space'] = ($aAlbum['user_id'] ? true : false);
            $aParams['server_id'] = $aAlbum['server_id'];

            (($sPlugin = Phpfox_Plugin::get('music.service_album_process_deleteimage__1')) ? eval($sPlugin) : false);
            return Phpfox::getService('user.file')->remove($aParams);
        }

        return Phpfox_Error::set(_p('not_allowed_to_edit_this_photo_album_art'));
    }

    public function delete($iId)
    {
        $bSkip = false;
        $mReturn = true;
        $aAlbum = $this->database()->select('*')
            ->from($this->_sTable)
            ->where('album_id = ' . (int)$iId)
            ->execute('getSlaveRow');

        if (!isset($aAlbum['album_id'])) {
            return Phpfox_Error::set(_p('album_you_are_trying_to_delete_cannot_be_found'));
        }
        if (in_array($aAlbum['module_id'],['pages','groups']) && Phpfox::isModule($aAlbum['module_id']) && Phpfox::getService($aAlbum['module_id'])->isAdmin($aAlbum['item_id'])) {
            $bSkip = true;
            $mReturn = Phpfox::getService($aAlbum['module_id'])->getUrl($aAlbum['item_id']) . 'music/album';
        }

        if ($bSkip || ($aAlbum['user_id'] == Phpfox::getUserId() && Phpfox::getUserParam('music.can_delete_own_music_album')) || Phpfox::getUserParam('music.can_delete_other_music_albums')) {
            $this->deleteImage($aAlbum['album_id'], $aAlbum);

            $aSongs = $this->database()->select('*')
                ->from(Phpfox::getT('music_song'))
                ->where('album_id = ' . $aAlbum['album_id'])
                ->execute('getSlaveRows');

            foreach ($aSongs as $aSong) {
                Phpfox::getService('music.process')->delete($aSong['song_id'], $aSong);
            }
            (Phpfox::isModule('attachment') ? Phpfox::getService('attachment.process')->deleteForItem($aAlbum['user_id'],
                $iId, 'music_album') : null);
            (Phpfox::isModule('comment') ? Phpfox::getService('comment.process')->deleteForItem($aAlbum['user_id'], $aAlbum['album_id'],
                'music_album') : null);
            (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->delete('comment_music_album',
                $iId) : null);
            (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->delete('music_album',
                $iId) : null);
            (Phpfox::isModule('like') ? Phpfox::getService('like.process')->delete('music_album', (int)$iId, 0,
                true) : null);
            (Phpfox::isModule('notification') ? Phpfox::getService('notification.process')->deleteAllOfItem([
                'music_album_like',
                'music_song_album'
            ], (int)$iId) : null);
            if (Phpfox::isModule('tag')) {
                $this->database()->delete(Phpfox::getT('tag'),
                    'item_id = ' . $aAlbum['album_id'] . ' AND category_id = "music_album"', 1);
                $this->cache()->remove();
            }
            //close all sponsorships
            (Phpfox::isModule('ad') ? Phpfox::getService('ad.process')->closeSponsorItem('music_album',
                (int)$iId) : null);

            $this->database()->delete($this->_sTable, 'album_id = ' . $aAlbum['album_id']);
            $this->database()->delete(Phpfox::getT('music_album_text'), 'album_id = ' . $aAlbum['album_id']);

            if ($aAlbum['is_featured'] == 1) {
                $this->cache()->remove('music_album_featured');
            }

            (($sPlugin = Phpfox_Plugin::get('music.service_album_process_delete__1')) ? eval($sPlugin) : false);
        } else {
            $mReturn = false;
        }

        return $mReturn;
    }

    public function feature($iId, $iType)
    {
        Phpfox::isUser(true);
        Phpfox::getUserParam('music.can_feature_music_albums', true);

        $this->database()->update($this->_sTable, array('is_featured' => ($iType ? '1' : '0')),
            'album_id = ' . (int)$iId);

        $this->cache()->remove('music_album_featured');

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
        if ($sPlugin = Phpfox_Plugin::get('music.service_album_process__call')) {
            eval($sPlugin);
            return null;
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }
}