<?php

namespace Apps\Core_Photos\Block;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

class Newest extends Phpfox_Component
{
    public function process()
    {
        $this->template()->assign(array(
                'aPhotos' => Phpfox::getService('photo')->getNew()
            )
        );
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('photo.component_block_new_clean')) ? eval($sPlugin) : false);
    }
}