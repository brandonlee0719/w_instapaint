<?php

namespace Apps\PHPfox_Twemoji_Awesome\Block;

defined('PHPFOX') or exit('NO DICE!');

class Share extends \Phpfox_Component
{
    public function process()
    {
        $bShowEmoji = $this->getParam('attachment_share', null) !== null;

        if ($sPlugin = \Phpfox_Plugin::get('PHPfox_Twemoji_Awesome.component_block_show_emoji')) {
            eval($sPlugin);
        }

        if (!$bShowEmoji) {
            return false;
        }

        $this->template()->assign('id', $this->getParam('id'));

        return null;
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = \Phpfox_Plugin::get('PHPfox_Twemoji_Awesome.component_block_share_clean')) ? eval($sPlugin) : false);
    }
}
