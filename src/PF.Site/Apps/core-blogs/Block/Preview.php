<?php

namespace Apps\Core_Blogs\Block;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Preview
 * @package Apps\Core_Blogs\Block
 */
class Preview extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        $this->template()->assign(array(
                'sText' => Phpfox::getLib('parse.input')->prepare($this->getParam('sText'))
            )
        );

        (($sPlugin = Phpfox_Plugin::get('blog.component_block_preview_process')) ? eval($sPlugin) : false);
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('blog.component_block_preview_clean')) ? eval($sPlugin) : false);
    }
}
