<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Ad_Component_Controller_Admincp_Invoice
 */
class Ad_Component_Controller_Admincp_Invoice extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        if (($iId = $this->request()->getInt('delete'))) {
            if (Phpfox::getService('ad.process')->deleteInvoice($iId)) {
                $this->url()->send('admincp.ad.invoice', null, _p('invoice_successfully_deleted'));
            }
        }

        $iPage = $this->request()->getInt('page');

        $aPages = array(5, 10, 15, 20);
        $aDisplays = array();
        foreach ($aPages as $iPageCnt) {
            $aDisplays[$iPageCnt] = _p('per_page', array('total' => $iPageCnt));
        }

        $aSorts = array(
            'time_stamp' => _p('recently_added')
        );

        $aFilters = array(
            'status' => array(
                'type' => 'select',
                'options' => array(
                    '1' => _p('paid'),
                    '2' => _p('pending_payment'),
                    '3' => _p('cancelled')
                ),
                'add_any' => true
            ),
            'display' => array(
                'type' => 'select',
                'options' => $aDisplays,
                'default' => '10'
            ),
            'sort' => array(
                'type' => 'select',
                'options' => $aSorts,
                'default' => 'ad_id'
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

        $oSearch = Phpfox_Search::instance()->set(array(
                'type' => 'invoices',
                'filters' => $aFilters,
                'search' => 'search'
            )
        );

        $sStatus = $oSearch->get('status');
        switch ($sStatus) {
            case '1':
                $oSearch->setCondition('ai.status = \'completed\'');
                break;
            case '2':
                $oSearch->setCondition('(ai.status = \'pending\' OR ' . Phpfox_Database::instance()->isNull('ai.status') . ')');
                break;
            case '3':
                $oSearch->setCondition('ai.status = \'cancel\'');
                break;
            default:

                break;
        }

        $iLimit = $oSearch->getDisplay();

        list($iCnt, $aInvoices) = Phpfox::getService('ad')->getInvoices($oSearch->getConditions(), $oSearch->getSort(),
            $oSearch->getPage(), $iLimit);

        Phpfox_Pager::instance()->set(array('page' => $iPage, 'size' => $iLimit, 'count' => $oSearch->getSearchTotal($iCnt)));

        $this->template()->setTitle(_p('ad_invoices'))
            ->setBreadCrumb(_p('invoices'))
            ->assign(array(
                    'aInvoices' => $aInvoices
                )
            );
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('ad.component_controller_admincp_invoice_clean')) ? eval($sPlugin) : false);
    }
}
