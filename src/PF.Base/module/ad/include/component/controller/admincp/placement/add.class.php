<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Ad_Component_Controller_Admincp_Placement_Add
 */
class Ad_Component_Controller_Admincp_Placement_Add extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        $bIsEdit = false;
        if (($iId = $this->request()->getInt('id')) && ($aPlacement = Phpfox::getService('ad')->getPlacement($iId))) {
            $bIsEdit = true;
            $this->setParam('currency_value_val[cost]', unserialize($aPlacement['cost']));
            $this->template()->assign(array(
                    'aForms' => $aPlacement
                )
            );
        }

        if (($aVals = $this->request()->getArray('val'))) {
            if ($bIsEdit) {
                if (Phpfox::getService('ad.process')->updatePlacement($aPlacement['plan_id'], $aVals)) {
                    $this->url()->send('admincp.ad.placement', null, _p('ad_placement_successfully_updated'));
                }
            } else {
                if (Phpfox::getService('ad.process')->addPlacement($aVals)) {
                    $this->url()->send('admincp.ad.placement', null, _p('ad_placement_successfully_added'));
                }
            }
        }

        $aCount = array();
        for ($i = 1; $i <= 12; $i++) {
            $aCount[$i] = $i;
        }

        if ($bIsEdit) {
            $aCount[$aPlacement['block_id']] = $aPlacement['block_id'];
        }

        $this->template()->setTitle(_p('add_ad_placement'))
            ->setBreadCrumb(_p('manage_placements'), $this->url()->makeUrl('admincp.ad.placement'))
            ->setBreadCrumb(($bIsEdit ? _p('edit_ad_placement') : _p('new_placement')), $this->url()->current(), true)
            ->assign(array(
                    'bIsEdit' => $bIsEdit,
                    'aPlanBlocks' => $aCount
                )
            );
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('ad.component_controller_admincp_placement_add_clean')) ? eval($sPlugin) : false);
    }
}
