<?php
/**
 * [PHPFOX_HEADER]
 */

namespace Apps\Core_Marketplace\Controller\Invoice;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

define('PHPFOX_SKIP_POST_PROTECTION', true);


class IndexController extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        Phpfox::isUser(true);

        $aCond = array();

        $aCond[] = 'AND mi.user_id = ' . Phpfox::getUserId();

        list(, $aInvoices) = Phpfox::getService('marketplace')->getInvoices($aCond);


        $this->template()->setTitle(_p('marketplace_invoices'))
            ->setBreadCrumb(_p('marketplace'), $this->url()->makeUrl('marketplace'))
            ->setBreadCrumb(_p('invoices'), $this->url()->makeUrl('marketplace.invoice'))
            ->assign(array(
                    'aInvoices' => $aInvoices
                )
            );

        Phpfox::getService('marketplace')->buildSectionMenu();
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('marketplace.component_controller_invoice_index_clean')) ? eval($sPlugin) : false);
    }
}