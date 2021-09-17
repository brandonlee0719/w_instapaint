<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Ad_Component_Controller_Invoice_Index
 */
class Ad_Component_Controller_Invoice_Index extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        Phpfox::isUser(true);

        $iSponsorId = $this->request()->get('invoice_id', null);
        if ($this->request()->get('sponsor', false) && $iSponsorId) {
            $aInvoice = db()->select('*')
                ->from(Phpfox::getT('ad_invoice'))
                ->where('invoice_id = ' . $iSponsorId . ' AND is_sponsor = 1')
                ->execute('getSlaveRow');

            $aAd = Phpfox::getService('ad')->getSponsor($aInvoice['ad_id']);
            $sStatus = $aAd['status'];
            if ($aAd['is_custom'] == 2 && $aAd['is_active'] == 0) {
                $sStatus = 'pending_approval';
            }
            if (!empty($aAd['redirect_' . $sStatus])) {
                $sMessage = !empty($aAd['message_' . $sStatus]) ? $aAd['message_' . $sStatus] : null;
                $this->url()->send($aAd['redirect_' . $sStatus], null, $sMessage);
            }
        }

        if (($sId = $this->request()->get('item_number')) != '') {
            define('PHPFOX_SKIP_POST_PROTECTION', true);
            $this->url()->send('ad.invoice', null, 'Payment Completed');
        }
        $aCond = array();
        $aCond[] = 'ai.user_id = ' . Phpfox::getUserId();

        list(, $aInvoices) = Phpfox::getService('ad')->getInvoices($aCond);

        Phpfox::getService('ad')->getSectionMenu();

        $this->template()->setTitle(_p('ad_invoices'))
            ->setBreadCrumb(_p('advertise'), $this->url()->makeUrl('ad'))
            ->setBreadCrumb(_p('invoices'), $this->url()->makeUrl('ad.invoice'), true)
            ->setHeader('cache', array(
                    'table.css' => 'style_css'
                )
            )
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
        (($sPlugin = Phpfox_Plugin::get('ad.component_controller_invoice_index_clean')) ? eval($sPlugin) : false);
    }
}
