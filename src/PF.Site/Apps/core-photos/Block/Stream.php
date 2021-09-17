<?php

namespace Apps\Core_Photos\Block;

use Phpfox_Component;
use Phpfox_Plugin;

class Stream extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {

    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('photo.component_block_stream_clean')) ? eval($sPlugin) : false);

        $this->template()->clean(array(
                'aStreams'
            )
        );
    }
}