<?php
/**
 * [PHPFOX_HEADER]
 */

namespace Apps\Core_Music\Controller;

use Phpfox;

defined('PHPFOX') or exit('NO DICE!');

class DownloadController extends \Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        Phpfox::getUserParam('music.can_download_songs', true);

        $iSongId = $this->request()->getInt('id');
        if (!$iSongId) {
            exit;
        }
        $aSong = Phpfox::getService('music')->getSong($iSongId, false);
        if (!isset($aSong['song_id'])) {
            exit;
        }

        if (!empty($aSong['song_path'])) {
            $sFullPath = Phpfox::getParam('core.dir_file') . 'music' . PHPFOX_DS . sprintf($aSong['song_path'], '');
        } else {
            exit;
        }
        // Download the song
        Phpfox::getLib('file')->forceDownload($sFullPath, $aSong['title'] . '.mp3', 'audio/mpeg', '',
            $aSong['server_id']);

        // We are done, lets get out of here
        exit;

    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = \Phpfox_Plugin::get('music.component_controller_download_clean')) ? eval($sPlugin) : false);
    }
}