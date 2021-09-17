<?php

namespace Apps\Core_RSS\Block;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

class InfoBlock extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        if (!Phpfox::isUser()) {
            return false;
        }

        $aUser = Phpfox::getService('user')->get(Phpfox::getUserId());

        $this->template()->assign(array(
                'sHeader' => _p('subscribers'),
                'iRssCount' => $aUser['rss_count']
            )
        );

        return 'block';
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('rss.component_block_info_clean')) ? eval($sPlugin) : false);
    }
}
