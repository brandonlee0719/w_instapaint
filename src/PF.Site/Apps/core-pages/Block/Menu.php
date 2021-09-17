<?php

namespace Apps\Core_Pages\Block;

use Phpfox;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

class Menu extends \Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        $aPage = $this->getParam('aPage');

        if (!$aPage || Phpfox::getService('pages')->getUserId($aPage['page_id']) == Phpfox::getUserId()) {
            return false;
        }

        $aCoverPhoto = ($aPage['cover_photo_id'] ? Phpfox::getService('photo')->getCoverPhoto($aPage['cover_photo_id']) : false);

        $this->template()->assign([
            'aPage' => $aPage,
            'aPageUser' => Phpfox::getService('user')->getUser($aPage['page_user_id']),
            'aCoverPhoto' => $aCoverPhoto
        ]);

        return 'block';
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('pages.component_block_menu_clean')) ? eval($sPlugin) : false);
    }
}
