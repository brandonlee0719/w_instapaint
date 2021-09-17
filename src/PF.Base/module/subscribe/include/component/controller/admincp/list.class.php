<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 *
 * @copyright       [PHPFOX_COPYRIGHT]
 * @author          phpFox
 * @package         Phpfox_Component
 */
class Subscribe_Component_Controller_Admincp_List extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        if (($iDeleteId = $this->request()->getInt('delete'))) {
            if (Phpfox::getService('subscribe.purchase.process')->delete($iDeleteId)) {
                $this->url()->send('admincp.subscribe.list', null, _p('purchase_order_successfully_deleted'));
            }
        }

        $aPages = array(20, 30, 40, 50);
        $aDisplays = array();
        foreach ($aPages as $iPageCnt) {
            $aDisplays[$iPageCnt] = _p('per_page', array('total' => $iPageCnt));
        }

        $sStatus = $this->request()->get('status');

        $aSorts = array(
            'time_stamp' => _p('time'),
            'status' => _p('status'),
            'price' => _p('price')
        );

        $aFilters = array(
            'package' => array(
                'type' => 'input:text',
                'search' => 'AND sp.package_id = \'[VALUE]\''
            ),
            'status' => array(
                'type' => 'select',
                'options' => array(
                    'completed' => _p('active'),
                    'cancel' => _p('canceled'),
                    'pending' => _p('pending_payment'),
                    'pendingaction' => _p('pending_action')
                ),
                'add_any' => true,
                'search' => ($sStatus == 'pendingaction' ? 'AND (sp.status IS NULL OR sp.status = \'\')' : 'AND sp.status = \'[VALUE]\'')
            ),
            'display' => array(
                'type' => 'select',
                'options' => $aDisplays,
                'default' => '12'
            ),
            'sort' => array(
                'type' => 'select',
                'options' => $aSorts,
                'default' => 'time_stamp',
                'alias' => 'sp'
            ),
            'sort_by' => array(
                'type' => 'select',
                'options' => array(
                    'DESC' => _p('descending'),
                    'ASC' => _p('ascending')
                ),
                'default' => 'DESC'
            )
        );

        $oFilter = Phpfox_Search::instance()->live()
            ->setRequests()
            ->set(array(
                    'type' => 'subscribe',
                    'filters' => $aFilters,
                    'redirect' => true,
                    'redirect_url' => 'admincp.subscribe.list'
                )
            );

        $iPage = $this->request()->getInt('page');
        $iPageSize = $oFilter->getDisplay();

        list($iCnt, $aPurchases) = Phpfox::getService('subscribe.purchase')->getSearch($oFilter->getConditions(),
            $oFilter->getSort(), $oFilter->getPage(), $iPageSize);

        $iCnt = $oFilter->getSearchTotal($iCnt);

        Phpfox_Pager::instance()->set(array('page' => $iPage, 'size' => $iPageSize, 'count' => $iCnt));

        $this->template()->setTitle(_p('subscription_purchase_orders'))
            ->setBreadCrumb(_p('Members'),'#')
            ->setBreadCrumb(_p('subscriptions'),$this->url()->makeUrl('admincp.subscribe'))
            ->setBreadCrumb(_p('purchase_orders'), $this->url()->makeUrl('admincp.subscribe.list'))
            ->setActiveMenu('admincp.member.subscribe')
            ->assign(array(
                    'aPurchases' => $aPurchases,
                    'bIsSearching' => $oFilter->isSearching()
                )
            );
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('subscribe.component_controller_admincp_list_clean')) ? eval($sPlugin) : false);
    }
}
