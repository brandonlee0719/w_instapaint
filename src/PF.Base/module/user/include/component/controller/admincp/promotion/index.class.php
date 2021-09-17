<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class User_Component_Controller_Admincp_Promotion_Index
 */
class User_Component_Controller_Admincp_Promotion_Index extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        if (($iDeleteId = $this->request()->getInt('delete')) && Phpfox::getService('user.promotion.process')->delete($iDeleteId)) {
            $this->url()->send('admincp.user.promotion', null, _p('promotion_successfully_deleted'));
        }

        // process sort params
        $sSort = str_replace('+', ' ', $this->request()->get('sort', ''));
        $aPromotions = Phpfox::getService('user.promotion')->get($sSort);

        $this->template()
            ->setBreadCrumb(_p('Members'),'#')
            ->setSectionTitle(_p('promotions'))
            ->setActiveMenu('admincp.member.promotion')
            ->setActionMenu([
                _p('create_a_promotion') => [
                    'url'   => $this->url()->makeUrl('admincp.user.promotion.add'),
                    'class' => 'popup',
                ],
            ])
            ->setTitle(_p('promotions'))
            ->setBreadCrumb(_p('promotions'), '')
            ->assign([
                'aPromotions' => $aPromotions,
                'sCurrent'    => $sSort,
            ]);
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('user.component_controller_admincp_promotion_index_clean')) ? eval($sPlugin) : false);
    }
}
