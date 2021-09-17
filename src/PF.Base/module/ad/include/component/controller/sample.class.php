<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Ad_Component_Controller_Sample
 */
class Ad_Component_Controller_Sample extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        define('PHPFOX_IS_AD_SAMPLE', true);
        if ($this->request()->get('no-click') == '1') {
            define('PHPFOX_NO_WINDOW_CLICK', true);
        }
        $this->template()->testStyle();
        $this->template()->bIsSample = true;
        if ($this->request()->get('click')) {
            $this->template()->setHeader('<style type="text/css">.sample { cursor:pointer; }</style>');
        }
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('ad.component_controller_sample_clean')) ? eval($sPlugin) : false);
    }
}
