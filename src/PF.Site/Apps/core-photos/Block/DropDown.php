<?php

namespace Apps\Core_Photos\Block;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

class DropDown extends Phpfox_Component
{
    public function process()
    {
        $this->template()->assign(array(
                'sCategories' => Phpfox::getService('photo.category')->get(false, true, false),
                'bMultiple' => $this->getParam('multiple', true)
            )
        );
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('photo.component_block_drop_down_clean')) ? eval($sPlugin) : false);
    }
}