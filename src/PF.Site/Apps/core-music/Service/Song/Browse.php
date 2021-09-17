<?php
/**
 * [PHPFOX_HEADER]
 */

namespace Apps\Core_Music\Service\Song;

use Phpfox;

defined('PHPFOX') or exit('NO DICE!');

class Browse extends \Phpfox_Service
{
    public function processRows(&$aRows)
    {
        foreach ($aRows as $iKey => $aSong) {
            Phpfox::getService('music')->getPermissions($aSong);
            $aRows[$iKey] = $aSong;
            $aRows[$iKey]['song_path'] = \Phpfox::getService('music')->getSongPath($aSong['song_path'],
                $aSong['server_id']);
            $aRows[$iKey]['genres'] = Phpfox::getService('music.genre')->getGenreDetailBySong($aSong['song_id']);

            $aRows[$iKey]['aFeed'] = array(
                'feed_display' => 'mini',
                'comment_type_id' => 'music_song',
                'privacy' => $aSong['privacy'],
                'comment_privacy' => $aSong['privacy_comment'],
                'like_type_id' => 'music_song',
                'feed_is_liked' => (isset($aSong['is_liked']) ? $aSong['is_liked'] : false),
                'feed_is_friend' => (isset($aSong['is_friend']) ? $aSong['is_friend'] : false),
                'item_id' => $aSong['song_id'],
                'user_id' => $aSong['user_id'],
                'total_comment' => $aSong['total_comment'],
                'feed_total_like' => $aSong['total_like'],
                'total_like' => $aSong['total_like'],
                'feed_link' => \Phpfox_Url::instance()->permalink('music', $aSong['song_id'], $aSong['title']),
                'feed_title' => $aSong['title'],
                'type_id' => 'music_song'
            );
        }
    }

    public function query()
    {
        if (Phpfox::isModule('like')) {
            $this->database()->select('lik.like_id AS is_liked, ')
                ->leftJoin(Phpfox::getT('like'), 'lik',
                    'lik.type_id = \'music_song\' AND lik.item_id = m.song_id AND lik.user_id = ' . Phpfox::getUserId());
        }
        $this->database()->select('ma.name AS album_name, mp.play_id AS is_on_profile, ')
            ->leftJoin(Phpfox::getT('music_album'), 'ma', 'ma.album_id = m.album_id')
            ->leftJoin(Phpfox::getT('music_profile'), 'mp',
                'mp.song_id = m.song_id AND mp.user_id = ' . Phpfox::getUserId())
            ->leftJoin(Phpfox::getT('music_genre_data'), 'mmgd', 'mmgd.song_id = m.song_id')
            ->leftJoin(Phpfox::getT('music_genre'), 'mg', 'mg.genre_id = mmgd.genre_id')
            ->group('m.song_id', true);
    }

    public function getQueryJoins($bIsCount = false, $bNoQueryFriend = false)
    {
        if (Phpfox::isModule('friend') && \Phpfox::getService('friend')->queryJoin($bNoQueryFriend)) {
            $this->database()->join(Phpfox::getT('friend'), 'friends',
                'friends.user_id = m.user_id AND friends.friend_user_id = ' . Phpfox::getUserId());
        }
        if ($this->request()->get('req2') == 'genre' && $this->request()->getInt('req3')) {
            $this->database()->innerJoin(Phpfox::getT('music_genre_data'), 'mgd', 'mgd.song_id = m.song_id');
            if (!$bIsCount) {
                db()->group('m.song_id');
            }
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
        if ($sPlugin = \Phpfox_Plugin::get('music.service_browse__call')) {
            eval($sPlugin);
            return null;
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        \Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }
}