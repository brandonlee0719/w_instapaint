<?php
/**
 * [PHPFOX_HEADER]
 */

namespace Apps\Core_Music\Service\Album;

use Phpfox;

defined('PHPFOX') or exit('NO DICE!');

class Browse extends \Phpfox_Service
{
    public function processRows(&$aRows)
    {
        foreach ($aRows as $iKey => $aAlbum) {
            Phpfox::getService('music.album')->getPermissions($aAlbum);
            $aRows[$iKey] = $aAlbum;
            $aRows[$iKey]['aFeed'] = array(
                'feed_display' => 'mini',
                'comment_type_id' => 'music_album',
                'privacy' => $aAlbum['privacy'],
                'comment_privacy' => $aAlbum['privacy_comment'],
                'like_type_id' => 'music_album',
                'feed_is_liked' => (isset($aAlbum['is_liked']) ? $aAlbum['is_liked'] : false),
                'feed_is_friend' => (isset($aAlbum['is_friend']) ? $aAlbum['is_friend'] : false),
                'item_id' => $aAlbum['album_id'],
                'user_id' => $aAlbum['user_id'],
                'total_comment' => $aAlbum['total_comment'],
                'feed_total_like' => $aAlbum['total_like'],
                'total_like' => $aAlbum['total_like'],
                'feed_link' => \Phpfox_Url::instance()->permalink('music.album', $aAlbum['album_id'], $aAlbum['name']),
                'feed_title' => $aAlbum['name']
            );
        }
    }

    public function query()
    {
        if (Phpfox::isModule('like')) {
            $this->database()->select('lik.like_id AS is_liked, ')
                ->leftJoin(Phpfox::getT('like'), 'lik',
                    'lik.type_id = \'music_album\' AND lik.item_id = m.album_id AND lik.user_id = ' . Phpfox::getUserId());
        }
    }

    public function getQueryJoins($bIsCount = false, $bNoQueryFriend = false)
    {
        if (Phpfox::isModule('friend') && \Phpfox::getService('friend')->queryJoin($bNoQueryFriend)) {
            $this->database()->join(Phpfox::getT('friend'), 'friends',
                'friends.user_id = m.user_id AND friends.friend_user_id = ' . Phpfox::getUserId());
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
        if ($sPlugin = \Phpfox_Plugin::get('marketplace.service_browse__call')) {
            eval($sPlugin);
            return null;
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        \Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }
}