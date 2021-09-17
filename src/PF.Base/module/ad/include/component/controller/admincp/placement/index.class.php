<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Ad_Component_Controller_Admincp_Placement_Index
 */
class Ad_Component_Controller_Admincp_Placement_Index extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        if (($iDelete = $this->request()->getInt('delete')) && Phpfox::getService('ad.process')->deletePlacement($iDelete)) {
            $this->url()->send('admincp.ad.placement', null, _p('ad_placement_successfully_deleted'));
        }

        $this->template()->setTitle(_p('manage_ad_placements'))
            ->setBreadCrumb(_p('manage_ad_placements'), $this->url()->makeUrl('ad.placement'))
            ->setActionMenu([
                _p('new_placement') => [
                    'url' => $this->url()->makeUrl('admincp.ad.placement.add'),
                    'class' => 'popup'
                ]
            ])
            ->assign(array(
                    'aPlacements' => Phpfox::getService('ad')->getPlacements()
                )
            );
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('ad.component_controller_admincp_placement_index_clean')) ? eval($sPlugin) : false);
    }
}
