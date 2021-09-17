<?php

namespace Apps\Core_Pages\Block;

use Phpfox;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

class Login extends \Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        // get the total pages, and the pages
        list($iTotal, $aPages) = Phpfox::getService('pages')->getMyLoginPages();

        $this->template()->assign(array(
                'aPages' => $aPages,
                'sLink' => $this->url()->makeUrl('pages.add'),
                'iTotal' => $iTotal,
            )
        );
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('pages.component_block_login_clean')) ? eval($sPlugin) : false);
    }
}
