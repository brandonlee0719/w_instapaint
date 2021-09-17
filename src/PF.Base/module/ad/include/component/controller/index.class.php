<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Ad_Component_Controller_Index
 */
class Ad_Component_Controller_Index extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        (($sPlugin = Phpfox_Plugin::get('ad.component_controller_process__start')) ? eval($sPlugin) : false);
        if (($iAd = $this->request()->getInt('id'))) {
            if (($sUrl = Phpfox::getService('ad')->getAdRedirect($iAd))) {
                $this->url()->forward($sUrl);
            }
        }

        $this->url()->send('ad.manage');

        Phpfox::getService('ad')->getSectionMenu();

        $this->template()->setTitle(_p('advertise'))
            ->setBreadCrumb(_p('advertise'), $this->url()->makeUrl('ad'));

        (($sPlugin = Phpfox_Plugin::get('ad.component_controller_process__start')) ? eval($sPlugin) : false);
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('ad.component_controller_index_clean')) ? eval($sPlugin) : false);
    }
}
