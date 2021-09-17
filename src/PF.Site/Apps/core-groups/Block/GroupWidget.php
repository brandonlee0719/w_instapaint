<?php

namespace Apps\PHPfox_Groups\Block;

use Phpfox_Component;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

class GroupWidget extends Phpfox_Component
{
    public function process()
    {
        $aWidgetBlocks = \Phpfox::getService('groups')->getWidgetBlocks();

        if (!count($aWidgetBlocks)) {
            return false;
        }

        $this->template()->assign([
                'aWidgetBlocks' => $aWidgetBlocks,
            ]
        );

        return null;
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('groups.component_block_widget_clean')) ? eval($sPlugin) : false);
    }
}
