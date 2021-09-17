<?php
/**
 * [PHPFOX_HEADER]
 */

namespace Apps\Core_Music\Service\Song;

use Phpfox;

defined('PHPFOX') or exit('NO DICE!');


class Process extends \Phpfox_Service
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('music_song');
    }

    public function setName($iSong, $sTitle, $bReturnNewTitle = false)
    {
        $iSong = (int)$iSong;
        \Phpfox::getService('ban')->checkAutomaticBan($sTitle);
        $sTitle = Phpfox::getLib('parse.input')->clean($sTitle);

        $this->database()->update(Phpfox::getT('music_song'), array('title' => $sTitle),
            'song_id = ' . $iSong . ' AND user_id = ' . Phpfox::getUserId());
        if ($bReturnNewTitle) {
            return Phpfox::getLib('parse.input')->prepareTitle('music', $sTitle, 'title', Phpfox::getUserId(),
                Phpfox::getT('music_song'));
        }
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
        if ($sPlugin = \Phpfox_Plugin::get('music.service_process__call')) {
            eval($sPlugin);
            return null;
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        \Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }
}