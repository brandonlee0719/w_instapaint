<?php
/**
 * [PHPFOX_HEADER]
 */

namespace Apps\Core_Music\Block;

defined('PHPFOX') or exit('NO DICE!');

class TrackBlock extends \Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        if (!$this->getParam('inline_album')) {
            return false;
        }

        if ($this->getParam('album_user_id', null) === null) {
            return false;
        }

        $aSongs = \Phpfox::getService('music.album')->getTracks($this->getParam('album_user_id'),
            $this->getParam('album_id'), $this->getParam('album_view_all', false));

        $this->template()->assign(array(
                'aTracks' => $aSongs,
                'iTotalSong' => count($aSongs),
                'bIsMusicPlayer' => ($this->getParam('is_player') ? true : false)
            )
        );
        return null;
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = \Phpfox_Plugin::get('music.component_block_track_clean')) ? eval($sPlugin) : false);

        $this->clearParam('inline_album');
    }
}