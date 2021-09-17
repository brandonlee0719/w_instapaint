<?php

namespace Apps\Core_Photos\Block;

use Phpfox_Component;
use Phpfox_Plugin;

class Warning extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        $this->template()->assign('sLink', $this->request()->get('link'));
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('photo.component_block_warning_clean')) ? eval($sPlugin) : false);
    }
}