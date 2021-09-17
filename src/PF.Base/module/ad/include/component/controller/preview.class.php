<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Ad_Component_Controller_Preview
 */
class Ad_Component_Controller_Preview extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        define('PHPFOX_IS_AD_PREVIEW', true);
        $this->template()->setTemplate('blank');
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('ad.component_controller_preview_clean')) ? eval($sPlugin) : false);
    }
}
