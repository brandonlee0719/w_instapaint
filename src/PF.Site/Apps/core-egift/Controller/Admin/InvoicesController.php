<?php

namespace Apps\Core_eGifts\Controller\Admin;

use Admincp_Component_Controller_App_Index;
use Phpfox_Plugin;
use Phpfox;

define('PHPFOX_SKIP_POST_PROTECTION', true);

class InvoicesController extends Admincp_Component_Controller_App_Index
{
    /**
     * Controller
     */
    public function process()
    {
        $aInvoices = Phpfox::getService('egift')->getInvoices();

        $this->template()->setTitle(_p('invoices'))
            ->setBreadCrumb(_p('admin_menu_invoices'), null, true)
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
        (($sPlugin = Phpfox_Plugin::get('egift.component_controller_index_clean')) ? eval($sPlugin) : false);
    }
}
