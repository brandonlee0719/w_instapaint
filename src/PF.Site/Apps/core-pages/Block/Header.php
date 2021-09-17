<?php

namespace Apps\Core_Pages\Block;

use Phpfox;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

class Header extends \Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        $aPage = $this->getParam('aPage');

        if (!isset($aPage['page_id']) || (isset($aPage['use_timeline']) && $aPage['use_timeline'])) {
            return false;
        }

        if ($this->getParam('bIsPagesViewSection')) {
            $aMenus = Phpfox::callback($this->getParam('sCurrentPageModule') . '.getPageSubMenu', $aPage);
            $this->template()->assign(array(
                    'aSubPageMenus' => $aMenus
                )
            );
        }

        return true;
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('pages.component_block_header_clean')) ? eval($sPlugin) : false);
    }
}
