<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Ad_Component_Block_Inner
 */
class Ad_Component_Block_Inner extends Phpfox_Component
{
    /**
     * Controller
     * This block shows an ad inside another block
     */
    public function process()
    {
        (($sPlugin = Phpfox_Plugin::get('ad.component_block_inner_process__start')) ? eval($sPlugin) : false);

        if (!Phpfox::getParam('ad.enable_ads')) {
            return false;
        }

        if ($this->getParam('sClass', '') == '') {
            return false;
        }


        $aAd = Phpfox::getService('ad')->getForLocation($this->getParam('sClass'));

        if (!is_array($aAd)) {
            return false;
        }

        if (is_array($aAd) && empty($aAd)) {
            return false;
        }

        $this->template()->assign(array(
                'aAd' => $aAd
            )
        );

        (($sPlugin = Phpfox_Plugin::get('ad.component_block_inner_process__end')) ? eval($sPlugin) : false);
        return null;
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('ad.component_block_display_clean')) ? eval($sPlugin) : false);
    }
}
