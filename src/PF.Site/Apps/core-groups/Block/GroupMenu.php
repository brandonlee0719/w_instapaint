<?php

namespace Apps\PHPfox_Groups\Block;

defined('PHPFOX') or exit('NO DICE!');

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

class GroupMenu extends Phpfox_Component
{
    public function process()
    {
        $aPage = $this->getParam('aPage');
        $aPageUser = Phpfox::getService('user')->getUser($aPage['page_user_id']);

        $this->template()->assign(compact('aPageUser'));
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('groups.component_block_menu_clean')) ? eval($sPlugin) : false);
    }
}
