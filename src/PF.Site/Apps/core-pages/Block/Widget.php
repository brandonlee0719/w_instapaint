<?php

namespace Apps\Core_Pages\Block;

use Phpfox;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

class Widget extends \Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        $aWidgetBlocks = Phpfox::getService('pages')->getWidgetBlocks();

        if (!count($aWidgetBlocks)) {
            return false;
        }

        $this->template()->assign(array(
                'aWidgetBlocks' => $aWidgetBlocks
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
        (($sPlugin = Phpfox_Plugin::get('pages.component_block_widget_clean')) ? eval($sPlugin) : false);
    }
}
