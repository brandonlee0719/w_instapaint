<?php
/**
 * [PHPFOX_HEADER]
 */
namespace Apps\Core_Music\Controller;

use Phpfox;

defined('PHPFOX') or exit('NO DICE!');

class ProfileController extends \Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        $this->setParam('bIsProfile', true);
        if ($this->request()->get('req3') == 'album') {
            $this->template()->assign('sReq3', 'album');
            Phpfox::getComponent('music.browse.album', array('bNoTemplate' => true), 'controller');
        } else {
            $this->template()->assign('sReq3', 'song');
            Phpfox::getComponent('music.index', array('bNoTemplate' => true), 'controller');
        }
        $aUser = $this->getParam('aUser');
        $bShowSongs = $this->request()->get('req3') != 'album';

        $this->template()->assign(array(
            'bShowSongs' => $bShowSongs,
            'sSongLink' => $this->url()->makeUrl($aUser['user_name'] . '.music'),
            'sAlbumLink' => $this->url()->makeUrl($aUser['user_name'] . '.music.album')
        ));
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = \Phpfox_Plugin::get('music.component_controller_profile_clean')) ? eval($sPlugin) : false);
    }
}